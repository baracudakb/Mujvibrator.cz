<?php

require('../../config/config.inc.php');

error_reporting(1);

/*
  * XML modul cunarny.cz v 1.2
  * Pouze pro XML Import i3.1.2 a vyšší.
  *
  * author & copyright Jiri Kolarik for I'm IN & Get2Shop.com
  *	
  * eShopove sluzby Get2shop - http://get2shop.com | info@get2shop.com
  * Internetove sluzby I'm IN - http://imin.cz | info@imin.cz
  *
  * Kupujici muze tento modul pouzivat a upravovat podle svych potreb
  * Kupujici NESMI ! tento modul sirit dale a poskytovat tretim stranam
*/




//////// - Nacteni parametru z databaze k danemu XML feedu -////////
$parameters = ImINXMLi::getParameters('cunarny');

//////// - Bez spravneho security klice nepusti dal - ////////
if($_GET['security']==$parameters['security_key'])
{
	$id_language = Language::getIdByIso($parameters['language']);
	$parameters['id_lang'] = (int)$id_language;
	

	if ($parameters['categories'] == 1)
	{

		$xmlcat = simplexml_load_file("http://www.cunarny.cz/cs/rss/categories.xml");	
		if(!empty($xmlcat))
		{
			$mycategory = array();
			
			foreach ($xmlcat->CATEGORIES->CATEGORY as $category) 
			{ 	
				$mycategory['id_category'] = $category->ID;
				$mycategory['id_parent'] = $category->PARENT_ID;
				//$mycategory['level'] = $category->Level+1;
				$mycategory['name'] = $category->NAME;	
				$mycategory['description'] = $category->DESCRIPTION;	
				$mycategory['active'] = 1;
			
				$importit = ImINXMLi::checkCategoryByID($mycategory);
			
				if($importit==true)
				{		

					ImINXMLi::insertIntoCategory($mycategory,$parameters);	
				}
			
			}
			
			unset($xmlcat);
			Category::regenerateEntireNtree();
		}
	
	}


	////// - Nacteni XML - bud z administrace PS nebo rucne
	////// Pro nacteni rucne: $xml = simplexml_load_file("http://imin.cz/feed.xml");
	$xml = simplexml_load_file($parameters['source']);
	//$xml = simplexml_load_file("http://mujvibrator.cz/products.xml");	
	
	if(!empty($xml))
	{
		
		///// Pro spousteni kontretnich akci napr import. Pokud nevyplnime nic, automaticky se provede
		if (!isset($_GET['action']) || $_GET['action'] == 'import')
		{
			//////// - Spocitam produkty
			$p_cnt = count($xml->PRODUCTS->SHOPITEM);
			$i = 0;
			
			/////// - Dosadime nemene hodnoty k importu			
			$id_language = Language::getIdByIso($parameters['language']);
			
			$product = array();
			$product['vat'] = 20;
			$product['id_lang'] = (int)$id_language;
			$product['available_for_order'] = 1;
			$product['on_sale']=0;
			$product['online_only']=0;
			$product['minimal_quantity']=1;
			$product['out_of_stock']=2;
			$product['wholesale_price']=0.000000;
			$product['add_shipping_cost']=0.00;
			$product['active'] = 1;
			$product['show_price'] = 1;
			$parameters['no_category']=true;
			
			/////// - Pokud chceme spoustet import davkove, nepoli jen v rozmezi nekolika produktu, prepiseme delku a zacinajici cislo
			if(isset($_GET['start']) && isset($_GET['end']))
			{
				if($_GET['start']=='start'){$i=0;} else{$i=$_GET['start'];} 
				if($_GET['end'] == 'end' || $_GET['end'] > $p_cnt) { $p_cnt = $p_cnt; } else {$p_cnt=$_GET['end'];}
			}
			
			//$p_cnt=100;
			for ($i; $i<$p_cnt; $i++)
			{
				///// - Dosadime hodnoty ktere se u kazdeho produktu meni
				$product['reference'] = $xml->PRODUCTS->SHOPITEM[$i]->ID;
				///// - Zkontrolujeme, zdali se produkty jiz nenachazi v db podle reference nebo ean kodu
				$importit = ImINXMLi::checkProduct($product,$parameters['code_name']);

				if($importit==true)
				{	
				//print_r($xml);
				//echo "<br><br><br><br>"; 
				$product['vat'] = (float)$xml->PRODUCTS->SHOPITEM[$i]->VAT * 100;
				$product['id_tax_rules_group'] = ImINXMLi::returnIdTaxRulesGroup($product['vat'],$parameters['language']);
				$product['name'] = $xml->PRODUCTS->SHOPITEM[$i]->PRODUCTS;
				$product['description'] = $xml->PRODUCTS->SHOPITEM[$i]->PRODUCTSDESCRIPTION;
				//$product['url'] = $xml->PRODUCTS->SHOPITEM[$i]->URL;
				$product['imgurl'] = $xml->PRODUCTS->SHOPITEM[$i]->IMGURL;
				$product['price'] = $xml->PRODUCTS->SHOPITEM[$i]->PRICE;
				//$product['price_vat'] = $xml->PRODUCTS->SHOPITEM[$i]->PRICE_VAT;
				$product['manufacturer'] = $xml->PRODUCTS->SHOPITEM[$i]->MANUFACTURER;
				//$product['category'] = $xml->PRODUCTS->SHOPITEM[$i]->CATEGORYTEXT;
				//$kategorie[] = $xml->PRODUCTS->SHOPITEM[$i]->CATEGORIES->CATEGORY;
				if(!empty($xml->PRODUCTS->SHOPITEM[$i]->EAN)){
				$product['ean13'] = $xml->PRODUCTS->SHOPITEM[$i]->EAN;}
				$product['quantity'] = $xml->PRODUCTS->SHOPITEM[$i]->QUANTITY;
						
				$id_product = ImINXMLi::insertProduct($product,$parameters);	
				foreach($xml->PRODUCTS->SHOPITEM[$i]->CATEGORIES->CATEGORY as $kategorie){
				//echo $kategorie;
				ImINXMLi::checkAndInsertProductInCategory($id_product,$kategorie);
				
				}
				///// - Zde pridat kod pro rozsireni importniho modulu pro dany XML feed $id_product je id naimportovaneho produktu
				////
				///
				//
				
				//ImINXMLi::insertProductInCategory($id_product,$product['id_category']);
				//ImINXMLi::updateDefaultCategoryByIDProduct($id_product,$product['id_category']);
					
				}
			}
			
			unset($product);
			
		}
		
	
		//// - aktualizace ceny, pokud je dovoleno
		if ((!isset($_GET['action']) || $_GET['action'] == 'price') && $parameters['update_price'] == 1)
		{
			$product = array();
		
			foreach ($xml->PRODUCTS->SHOPITEM as $products) 
			{ 
				$product['vat'] = (float)$products->VAT * 100;
				$product['reference'] = $products->ID;
				$product['price'] = $products->PRICE;
				//$product['price_vat'] = $products->PRICE_VAT;
				
				ImINXMLi::updatePrices($product,$parameters);
			}
			
			unset($product);
		
		}
		
		
		//// - aktualizace skladu, pokud je dovoleno
		if ((!isset($_GET['action']) || $_GET['action'] == 'quantity') && $parameters['update_quantity'] == 1)
		{
			$product = array();
		
			foreach ($xml->PRODUCTS->SHOPITEM as $products) 
			{ 
				$product['reference'] = $products->ID;
				//$product['delivery_date'] = $products->DELIVERY_DATE;
				$product['quantity'] = $products->QUANTITY;
				
				ImINXMLi::updateQuantity($product,$parameters);
			}
			
			unset($product);
		
		}
		
		//// - deaktivace produktu, ktere nejsou v XML, pokud je dovoleno
		if ((!isset($_GET['action']) || $_GET['action'] == 'deactivate') && $parameters['deactivate'] == 1)
		{
			$product = array();
			$product['date_upd'] = date("Y-m-d H:i:s");
			
			foreach ($xml->PRODUCTS->SHOPITEM as $products) 
			{ 
				$product['reference'] = $products->ID;
				ImINXMLi::updateProductDate($product,$parameters);
			} 

			ImINXMLi::deactivateProductsNotInXML($parameters);
		
			unset($product);

		}	
	
		
	}
	
	else
	{
		echo "Nepodarilo se nacist XML, bud je nedostupne, nebo mate chybne zadany odkaz v administaci";
	}	
}

else
{
	echo "Security key je spatny";
}




?>
