<?php

/*
  * XML import v i3.1.2 pro PS 1.4
  *
  * author & copyright Jiri Kolarik for I'm IN & Get2Shop.com
  *	
  * eShopove sluzby Get2shop - http://get2shop.com | info@get2shop.com
  * Internetove sluzby I'm IN - http://imin.cz | info@imin.cz
  *
  * Kupujici muze tento modul pouzivat a upravovat podle svych potreb
  * Kupujici NESMI ! tento modul sirit dale a poskytovat tretim stranam
*/


/************************

Compatibility list:

	XML Import eD'system Czech v3.0 | 100% Funguje i3.0+
	XML Import Bytove Parfemy v1 | 95% Funguje (Propojeni kategorii neni v XML) i3.1+
	XML Import Luxusni-Spodni-Pradlo v3.0 | 100% Funguje i3.1+
	XML Import eGifts.cz | 100% Funguje i3.1.1+
	XML Import BatteryShop | 100% Funguje i3.1.1+
	XML Import Silver.Ag | 100% Funguje i3.1.1+
*/


/**********************

LOG

i3.0
-----

i3.1
Při aktualizaci attributů se nyní nemusí mazat všechny, ale jen u daného produktu. Je potřeba aktualizace dodavatel.php 
|---------------------------------------------------------------------------|
|	ImINXMLi::deleteProductAttributes($id_product);
|---------------------------------------------------------------------------|
	
i3.1.1

a)
Pri aktualizaci attributu je mozne pouzit update (Potreba zmena struktury DB, vyuziti predevsim u aktualizace atributu)
|---------------------------------------------------------------------------|
|	ALTER TABLE ps_product_attribute ADD date_upd DATETIME			
|	ImINXMLi::checkProductAttributes($product_attr,$attributes);
|---------------------------------------------------------------------------|

Je mozne importovat vcetne obrazku u attributu
|---------------------------------------------------------------------------|
|	$image = array(
|	'position' => $position,
|	'name' => $productWithAttribut->name,
|	'url' => $productWithAttribut->img
|	);
|							
|	ImINXMLi::checkProductAttributes($product_attr,$attributes,$image);
|---------------------------------------------------------------------------|

i3.1.2
Smaze neaktualni atributy - potreba mit upravu 3.1.1 a
|---------------------------------------------------------------------------|
|	ImINXMLi::deleteOldProductAttributes();
|---------------------------------------------------------------------------|

*/


include_once('../../images.inc.php');

class ImINXMLiCore
{
	
	
	public function returnIdByReference($reference,$codename)
	{
		return $id_product = Db::getInstance()->getValue('
		SELECT `id_product`
		FROM `'._DB_PREFIX_.'product` 
		WHERE `reference`=("'.pSQL($reference).'") AND `supplier_reference`=("'.pSQL($codename).'")');
	}
	
	public function checkProduct($product,$supplier_reference)
	{
	
		if(isset($product['reference']) && !empty($product['reference']))
				{ 
				    $num_products = Db::getInstance()->ExecuteS(
				    'SELECT * FROM `'._DB_PREFIX_.'product` WHERE `reference`=("'.pSQL($product['reference']).'") AND `supplier_reference`=("'.pSQL($supplier_reference).'")');
				    
				   	if (count($num_products)==0)
				    {
				   		return true;
				    }
				    
				    else
				    {
				    	return false;
				    }
		
				
				}
	
		
	
		elseif (isset($product['ean']))
				{
					$num_products = Db::getInstance()->ExecuteS(
				    'SELECT * FROM `'._DB_PREFIX_.'product` WHERE `ean13`=("'.pSQL($product['ean13']).'")');
				    
				   	if (count($num_products)==0)
				    {
				   		return true;
				    }
				    
				    else
				    {
				    	return false;
				    }
		
				
				}
		
		else
		{
			return false;
		}
		
	}
	
	
public function insertProduct($product,$parameters)
	{

// Ziskani ceny bez DPH, pokud je cena s DPH

		if (empty($product['price']))
		{
			$product['price'] = self::priceWithoutVat($product['price_vat'],$product['vat']);
		}	

// -- Cena			-	Ziskani nove ceny
		$product['my_price'] = self::myPrice($product['price'],$parameters['price_change'],$parameters['price_value']);	
			
// -- Pocet kusu	-	Pokud neimportujeme primo pocet produktu skladem, tak nastavime podle doby dodani
		if(!isset($product['quantity']) && isset($product['delivery_date']))
		{
			$product['quantity'] = self::productQuantity($product['delivery_date'],$parameters['delivery']);
		}
		
// -- Vyrobce		-	Rozliseni, zdali je aktivovany import vyrobce
		if ($parameters['manufacturer'] == 1 && empty($product['id_manufacturer']))
				{	$product['id_manufacturer'] = self::importManufacturer($product['manufacturer']);	}
		
// -- Dan
		$product['id_tax_rules_group'] = self::returnIdTaxRulesGroup($product['vat'],$parameters);
		
// -- Datum
		$product['date_add'] = date("Y-m-d H:i:s");
		$product['date_upd'] = date("Y-m-d H:i:s");
		
// -- Zvalidujeme polozky pro import		
		$product_import = self::validateProductFields($product,$parameters['code_name']);
		
// -- Naimportujeme produkt
		$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.'product', $product_import, 'INSERT');
		
// -- Zjistime si ID importovaneho produktu
		$id_product = Db::getInstance()->Insert_ID();

// -- Zjistime zdali chceme importovat i popis		
		if ($parameters['description'] == 1)
		{
			$product['description'] = $product['description'];
			if(empty($product['description_short']))
			{
				$product['description_short'] = substr($product['description'], 0, 255);
			}
		}
		else
		{
			$product['description']="";
			$product['description_short']="";
		}
		
// -- Vlozime jazykovy popis k produktu
		self::insertProductLang($id_product,$product,$parameters);
		
// -- Kategorie		-	Rozliseni, zdali je aktivovany import kategorii
		if(!isset($parameters['no_category']))
		{
			if ($parameters['categories'] == 1 && !empty($product['category']))
					{	$category_text = $product['category'];	}
			else	{	$category_text = "ImportovaciKategorie";	}
			
			self::insertCategories($category_text, $id_product, $product['id_lang'], $parameters['symbol']);
		}
		
// -- Obrazky		-	Rozliseni, zdali je aktivovany import obrazku a popr naimportuje		
		if ($parameters['images'] == 1 && isset($product['imgurl']) ) 
		{	
			if(isset($parameters['use_curl'])){
			self::insertImageCurl($id_product,$product['imgurl'],$product['id_lang'],$product['name'],1,1);
			}
			else{
			self::insertImage($id_product,$product['imgurl'],$product['id_lang'],$product['name'],1,1);	
			}
		}
		
		
// --  Odnastavime $product pro import		
		unset($product_import);

		
// -- Vratime ID produktu pro budouci mozne rozsireni v importu		
		return $id_product;
		
	}
	
	
	public function updateProduct($product,$parameters)
	{
			
		
		// Ziskani ceny bez DPH, pokud je cena s DPH

		if (empty($product['price']))
		{
			$product['price'] = self::priceWithoutVat($product['price_vat'],$product['vat']);
		}	

		// -- Cena			-	Ziskani nove ceny
		$product['my_price'] = self::myPrice($product['price'],$parameters['price_change'],$parameters['price_value']);	
			
		// -- Pocet kusu	-	Pokud neimportujeme primo pocet produktu skladem, tak nastavime podle doby dodani
		if(!isset($product['quantity']) && isset($product['delivery_date']))
		{
			$product['quantity'] = self::productQuantity($product['delivery_date'],$parameters['delivery']);
		}

		
		// -- Datum
		$product['date_upd'] = date("Y-m-d H:i:s");

		// -- Updatujeme produkt
		
		
		if (!isset($parameters['use_importprefix'])){
		
		$product_update = self::validateProductFieldsForUpdate($product,$parameters['code_name']);
		$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.'product', $product_update, 'UPDATE','`id_product` = '.(int)$product['id'].' ');
		}
		
		else
		{
			$product_update = self::validateProductFieldsForUpdateImportPrefix($product,$parameters['code_name']);
			$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.'product', $product_update, 'UPDATE','`id_product` = '.(int)$product['id'].' ');
		
		}
		
// --  Odnastavime $product pro import		
		unset($product_update);

		
// -- Vratime ID produktu pro budouci mozne rozsireni v importu		
		return $result;
		
	}	

	public function updateDefaultCategoryByIDProduct($id_product,$id_category)
	{	
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `id_category_default` = '.(int)$id_category.' WHERE `id_product` = '.(int)$id_product.' ');
	
	}
	
	public function updatePrices($product,$parameters)
	{
		if (empty($product['price']))
		{
			$product['price'] = self::priceWithoutVat($product['price_vat'],$product['vat']);
		}	

		$product['my_price'] = self::myPrice($product['price'],$parameters['price_change'],$parameters['price_value']);	
	
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `price` = '.(float)$product['my_price'].' WHERE `reference` = "'.pSQL($product['reference']).'" AND `supplier_reference` = "'.pSQL($parameters['code_name']).'" ');
		
		if(isset($product['wholesale_price'])){
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `wholesale_price` = '.(float)$product['wholesale_price'].' WHERE `reference` = "'.pSQL($product['reference']).'" AND `supplier_reference` = "'.pSQL($parameters['code_name']).'" ');
		}
	}
	
	
	
	public function updateQuantity($product,$parameters)
	{
		if(!isset($product['quantity']) && isset($product['delivery_date']))
		{
			$product['quantity'] = self::productQuantity($product['delivery_date'],$parameters['delivery']);
		}	
			
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `quantity` = "'.(int)$product['quantity'].'" WHERE `reference` = "'.pSQL($product['reference']).'" AND `supplier_reference` = "'.pSQL($parameters['code_name']).'" ');
	
	}	
	
	
	public function updateProductDate($product,$parameters)
	{

		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `date_upd` = "'.pSQL($product['date_upd']).'" WHERE `reference` = "'.pSQL($product['reference']).'" AND `supplier_reference` = "'.pSQL($parameters['code_name']).'" ');
		
	
	}
	
	public function deactivateProductsNotInXML($parameters)
	{
		$yesterday = date("Y-m-d H:i:s", strtotime("-1 days"));
	
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `active` = 0 WHERE `date_upd` < "'.pSQL($yesterday).'" AND `supplier_reference` = "'.pSQL($parameters['code_name']).'" ');	
	}
	
	public function insertProductLang($id_product,$product,$parameters)	
	{
		$seolink = self::SEOlink($product['name']);
	
		$product_lang = array();
		$product_lang['id_product'] = (int)$id_product;
		$product_lang['id_lang'] = (int)$product['id_lang'];
		$product_lang['description'] = pSQL($product['description']);
		$product_lang['description_short'] = pSQL($product['description_short']);
		$product_lang['link_rewrite'] = pSQL($seolink);
		
		if (!empty($product['meta_description']))	{	$product_lang['meta_description'] = pSQL($product['meta_description']);	}
		else {	$product_lang['meta_description'] = pSQL($product['description']);	}
		if (!empty($product['meta_keywords']))	{	$product_lang['meta_keywords'] = pSQL($product['meta_keywords']);	}
		else {	$product_lang['meta_keywords'] = pSQL($product['name']);	}
		if (!empty($product['meta_title']))	{	$product_lang['meta_title'] = pSQL($product['meta_title']);	}
		else {	$product_lang['meta_title'] = pSQL($product['name']);	}
		
		$product_lang['name'] = pSQL($product['name']);
		
		if (!empty($product['available_now']))	{	$product_lang['available_now'] = pSQL($product['available_now']);	}
		else {	$product_lang['available_now'] = pSQL($parameters['available_now']);	}
		if (!empty($product['available_later']))	{	$product_lang['available_later'] = pSQL($product['available_later']);	}
		else {	$product_lang['available_later'] = pSQL($parameters['available_later']);	}

		Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_lang', $product_lang, 'INSERT');
	}
	
	public function insertProductTrans($id_product,$product,$parameters)	
	{	
		$value = Db::getInstance()->getValue('
		SELECT *
		FROM `'._DB_PREFIX_.'product_lang` 
		WHERE `id_product` = '.(int)$id_product.' AND `id_lang`= '.(int)($product['id_lang']).' ');

		if (empty($value))
		{
			if(empty($product['description_short']))
			{
				$product['description_short'] = substr($product['description'], 0, 255);
			}
			self::insertProductLang($id_product,$product,$parameters)	;
		}	
	
	}
	
	public function getParameters($supplier_reference)
	{
		$result = Db::getInstance()->ExecuteS(' SELECT * FROM `'._DB_PREFIX_.'imin_xmlimport` ');

		foreach ($result as $row)
		{
			if ($row['code_name'] == $supplier_reference)
			{
			
			if(date("d")==1 || $row['authenticate'] == 0){
				$message='Username:'.$row['username'].', registered domain: '.$row['domain'].' domain: '.$_SERVER["HTTP_HOST"].' module: '.$supplier_reference;
				mail('authentication@get2shop.com','XML Import module',$message,'');
				if($row['authenticate'] == 0){
				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'imin_xmlimport` 
				SET `authenticate` = 1 WHERE `code_name` = "'.pSQL($row['code_name']).'" ');
				}
				}
				return $row;
			}
		}
	}
	
	public function insertImageReference($id_product,$img_url,$id_lang,$product_name,$position,$cover,$reference)
	{
		$id_image = self::insertImageDbReference($id_product,$id_lang,$product_name,$position,$cover,$reference);
		self::copyImg($id_product, $id_image, $img_url, $entity = 'products');
		return $id_image;
	}

	public function insertImageDbReference($id_product,$id_lang,$product_name,$position,$cover,$reference)
	{
		$result = Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'image` (`id_product`, `position`, `cover`, `reference`) 
		VALUES ('.(int)($id_product).', '.(int)($position).', '.(int)($cover).', "'.pSQL($reference).'")');
		$id_image = Db::getInstance()->Insert_ID();
		
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'image_lang` (`id_image`, `id_lang`, `legend`) 
		VALUES ('.(int)($id_image).', '.(int)($id_lang).', "'.pSQL($product_name).'")');
		
		return $id_image;

	}
	
	public function insertImageDb($id_product,$id_lang,$product_name,$position,$cover)
	{
		$result = Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'image` (`id_product`, `position`, `cover`) 
		VALUES ('.(int)($id_product).', '.(int)($position).', '.(int)($cover).')');
		$id_image = Db::getInstance()->Insert_ID();
		echo "vratka z DB: " . $id_image;
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'image_lang` (`id_image`, `id_lang`, `legend`) 
		VALUES ('.(int)($id_image).', '.(int)($id_lang).', "'.pSQL($product_name).'")');
		
		return $id_image;

	}
	
	public function insertImage($id_product,$img_url,$id_lang,$product_name,$position,$cover)
	{
		echo "id produktu: " . $id_product . "<br>";
		$id_image = self::insertImageDb($id_product,$id_lang,$product_name,$position,$cover);
		echo "id obrazku: " . $id_image . "<br>";
		self::copyImg($id_product, $id_image, $img_url, $entity = 'products');
		return $id_image;
	}
	
	
	public function copyImg($id_entity, $id_image, $url, $entity = 'products')
	{
		$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));

		switch($entity)
		{
			default:
			case 'products':
				//echo $id_image;
				//echo $id_entity;
				$id_image=strval($id_image);
				$id_image=str_split($id_image);
				$path = _PS_PROD_IMG_DIR_;
				foreach($id_image as $slozka){
					$path.=$slozka . '/';
				
				}
				mkdir($path);
				//$path = _PS_PROD_IMG_DIR_.(int)($id_entity).'-'.(int)($id_image);
				//echo $path;
				//echo "<br><br>";
			break;
			case 'categories':
				$path = _PS_CAT_IMG_DIR_.(int)($id_entity);
			break;
		}

		if (copy(trim($url), $tmpfile))
		{
			//imageResize($tmpfile, $path.$id_entity.'.jpg');
			if(!copy($tmpfile,$path.$id_entity.'.jpg')){echo "<strong>chyba</strong><br>";}
			$imagesTypes = ImageType::getImagesTypes($entity);
			foreach ($imagesTypes AS $k => $imageType)
				//$cesta=$path.$id_entity.'-'.stripslashes($imageType['name']).'.jpg';
				//echo $cesta;echo "<br><br>";
				imageResize($tmpfile, $path.$id_entity.'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
			if (in_array($imageType['id_image_type'], $watermark_types))
				Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
		}
		else
		{
			unlink($tmpfile);
			return false;
		}
		unlink($tmpfile);
		return true;
	}	
	
	public function insertImageCurl($id_product,$img_url,$id_lang,$product_name,$position,$cover)
	{
	
		$id_image = self::insertImageDb($id_product,$id_lang,$product_name,$position,$cover);

		$sourcecode = self::getImageFromUrlCurl($img_url);
		
		$savefile = fopen('../../img/tmp/'.$id_product.'-'.$id_image.'.jpg', 'w');
		fwrite($savefile, $sourcecode);
		
		//imageResize('../../img/tmp/'.$id_product.'-'.$id_image.'.jpg', '../../img/p/'.$id_product.'-'.$id_image.'.jpg');
		self::copyImgCurl('../../img/tmp/'.$id_product.'-'.$id_image.'.jpg', '../../img/p/'.$id_product.'-'.$id_image);
		
		fclose($savefile);	
	}	
	
	
	public function copyImgCurl($source, $destination)
	{
		$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));
		imageResize($source, $destination.'.jpg');
		$imagesTypes = ImageType::getImagesTypes($entity);
		foreach ($imagesTypes AS $k => $imageType)
			imageResize($source, $destination.'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
		if (in_array($imageType['id_image_type'], $watermark_types))
			Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
			
		unlink($tmpfile);
		return true;
	}	
	
	public function getImageFromUrlCurl($link) 
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST, 0);
		curl_setopt($ch,CURLOPT_URL,$link);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result=curl_exec($ch);
		curl_close($ch);
		return $result;
	}		
	
	public function returnProductAttributesId($idProduct)
	{
		$idProductAttributes = Db::getInstance()->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)$idProduct.' ');
		return $idProductAttributes;
	}
	
	public function deleteProductAttribute($idProduct)
	{	
		Db::getInstance()->Execute(' 
		DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)$idProduct.' ');
	}
	
	public function deleteProductAttributeCombination($idProductAttributes)
	{	
		foreach($idProductAttributes as $idProductAttribute)
		{
			Db::getInstance()->Execute(' 
			DELETE FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_product_attribute` = '.(int)$idProductAttribute['id_product_attribute'].' ');
		}
	}
	
	public function truncateProductAttributeCombination()
	{
		Db::getInstance()->Execute(' 
		TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute_combination` ');
		
		Db::getInstance()->Execute(' 
		TRUNCATE TABLE `'._DB_PREFIX_.'product_attribute` ');

	}
	
	public function deleteProductAttributes($idProduct)
	{
		$idProductAttributes = ImINXMLi::returnProductAttributesId($idProduct);
		ImINXMLi::deleteProductAttributeCombination($idProductAttributes);
		ImINXMLi::deleteProductAttribute($idProduct);

	}
	
	public function deleteOldProductAttributes()
	{
		$yesterday = date("Y-m-d H:i:s", strtotime("-1 days"));

		$idProductAttributes = Db::getInstance()->ExecuteS('
		SELECT *
		FROM `'._DB_PREFIX_.'product_attribute` WHERE `date_upd` < "'.pSQL($yesterday).'" ');
		
		foreach($idProductAttributes as $idProductAttribute)
		{
			Db::getInstance()->Execute(' 
			DELETE FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_product_attribute` = '.(int)$idProductAttribute['id_product_attribute'].' ');
			
			Db::getInstance()->Execute(' 
			DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product_attribute` = '.(int)$idProductAttribute['id_product_attribute'].' ');
		}
	}

	public function validateProductFieldsForAttributes($product_attr)
	{
		$fields = array(
		'id_product' => (int)$product_attr['id_product'], 
		'reference' => pSQL($product_attr['reference']), 
		'supplier_reference' => pSQL($product_attr['supplier_reference']), 
		'location' => pSQL($product_attr['location']), 
		'ean13' => pSQL($product_attr['ean13']), 
		'upc' => pSQL($product_attr['upc']), 
		'wholesale_price' => (float)$product_attr['wholesale_price'], 
		'price' => (float)$product_attr['price'], 
		'ecotax' => (float)$product_attr['ecotax'],
		'quantity' => (int)$product_attr['quantity'], 
		'weight' => (float)$product_attr['weight'], 
		'unit_price_impact' => (int)$product_attr['unit_price_impact'], 
		'default_on' => (int)$product_attr['default_on'], 
		'minimal_quantity' => (int)$product_attr['minimal_quantity']);
		
		return($fields);
	}
			
	public function insertAttributes($product_attr,$attributes)
	{
		if($product_attr['id_product'] != 0 || !empty($productAttr['id_product']))
		{
		$fields = self::validateProductFieldsForAttributes($product_attr);
	
		$result = Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_attribute', $fields, 'INSERT');
		$id_product_attribute = Db::getInstance()->Insert_ID();
		
		foreach($attributes as $name => $value)
		{
			$id_attribute_group = self::getOrCreateAttributeGroup($name,$product_attr['id_lang']);
			$id_attribute = self::getOrCreateAttribute($value,$product_attr['id_lang'],$id_attribute_group);
			self::insertProductAttributeCombination($id_attribute,$id_product_attribute);
			
		}
		
		return $id_product_attribute;
		}
	}
	
	public function updateProductAttributes($product_attr,$attributes,$id_product_attribute)
	{
		$date = date("Y-m-d H:i:s");
		
		$fields = self::validateProductFieldsForAttributes($product_attr);
		Db::getInstance()->AutoExecute(_DB_PREFIX_.'product_attribute', $fields, 'UPDATE','`reference` = "'.pSQL($product_attr['reference']).'" AND `supplier_reference` = "'.pSQL($product_attr['supplier_reference']).'" ');
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product_attribute` 
		SET `date_upd` = "'.pSQL($date).'" WHERE `reference` = "'.pSQL($product_attr['reference']).'" AND `supplier_reference` = "'.pSQL($product_attr['supplier_reference']).'" ');
		
		$idProduct = self::returnIdProductByReference($product_attr['reference'],$product_attr['supplier_reference']);
		$productAttributes = self::returnProductAttributesId($idProduct);
		self::deleteProductAttributeCombination($idProductAttributes);
		
		foreach($attributes as $name => $value)
		{
			$id_attribute_group = self::getOrCreateAttributeGroup($name,$product_attr['id_lang']);
			$id_attribute = self::getOrCreateAttribute($value,$product_attr['id_lang'],$id_attribute_group);
			self::insertProductAttributeCombination($id_attribute,$id_product_attribute);
			
		}
		
	}
	
	public function checkProductAttributes($product_attr,$attributes,$image=false)
	{
		$id_product_attribute = Db::getInstance()->getValue('
		SELECT `id_product_attribute`
		FROM `'._DB_PREFIX_.'product_attribute` 
		WHERE `reference` = "'.pSQL($product_attr['reference']).'" AND `supplier_reference` = "'.pSQL($product_attr['supplier_reference']).'" ');
		
		if (empty($id_product_attribute))
		{
			$id_product_attribute = self::insertAttributes($product_attr,$attributes);
			if($image != false)
			{
				self::insertProductAttributeImage($product_attr,$id_product_attribute,$image);
			}
		}
		else
		{
			self::updateProductAttributes($product_attr,$attributes,$id_product_attribute);
		}
	
	
	}
	
	public function insertProductAttributeCombination($id_attribute,$id_product_attribute)
	{
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'product_attribute_combination` (`id_attribute`, `id_product_attribute`) 
		VALUES ('.(int)$id_attribute.', '.(int)$id_product_attribute.' )');
	
	}
	
	public function insertProductAttributeImage($product_attr,$id_product_attribute,$image)
	{
		$isCover = Db::getInstance()->getValue('
		SELECT `id_image`
		FROM `'._DB_PREFIX_.'image` 
		WHERE `id_product` = '.(int)$product_attr['id_product'].'');
		if(empty($isCover)){	$cover=1;	}	else	{	$cover=0;	}

		$id_mage = self::insertImage($product_attr['id_product'],$image['url'],$product_attr['id_lang'],$image['name'],$image['position'],$cover);	
		
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'product_attribute_image` (`id_product_attribute`, `id_image`) 
		VALUES ('.(int)$id_product_attribute.', '.(int)$id_mage.' )');
	
	}
	
	public function getQuantityByIdProduct($id_product)
	{
		return $quantity = Db::getInstance()->getValue('
		SELECT `quantity`
		FROM `'._DB_PREFIX_.'product` 
		WHERE `id_product` = '.(int)$id_product.'');
	}
	
	public function getOrCreateAttributeGroup($name,$id_lang)
	{
		$id_attribute_group = Db::getInstance()->getValue('
		SELECT `id_attribute_group`
		FROM `'._DB_PREFIX_.'attribute_group_lang` 
		WHERE `name` = "'.pSQL($name).'"');
		
		if (empty($id_attribute_group))
		{
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'attribute_group` (`is_color_group`) 
			VALUES (0)');
			$id_attribute_group = Db::getInstance()->Insert_ID();
			
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'attribute_group_lang` (`id_attribute_group`, `id_lang`, `name`, `public_name`) 
			VALUES ('.(int)($id_attribute_group).', '.(int)($id_lang).', "'.pSQL($name).'", "'.pSQL($name).'" )');
			
			
		}
		
		return $id_attribute_group;
	
	}
	
	
	public function getOrCreateAttribute($name,$id_lang,$id_attribute_group)
	{
		$id_attribute = Db::getInstance()->getValue('
		SELECT a.`id_attribute`
		FROM `'._DB_PREFIX_.'attribute` a
		LEFT JOIN `'._DB_PREFIX_.'attribute_lang` al ON (a.`id_attribute` = al.`id_attribute`) 
		WHERE al.`name` = "'.pSQL($name).'" AND a.`id_attribute_group` = '.(int)($id_attribute_group).' ');
		
		if (empty($id_attribute))
		{
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'attribute` (`id_attribute_group`,`color`) 
			VALUES ('.(int)($id_attribute_group).', "'.pSQL('#ffffff').'" )');
			$id_attribute = Db::getInstance()->Insert_ID();
			
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'attribute_lang` (`id_attribute`, `id_lang`, `name`) 
			VALUES ('.(int)($id_attribute).', '.(int)($id_lang).', "'.pSQL($name).'")');
			
		}
		
		return $id_attribute;
	
	}
	
	public function insertFeatureLang($id_feature,$id_lang,$name)
	{
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'feature_lang` (`id_feature`, `id_lang`, `name`) 
		VALUES ('.(int)($id_feature).', '.(int)($id_lang).', "'.pSQL($name).'")');
	}
	
	public function insertFeatureValueLang($id_feature_value,$id_lang,$value)
	{
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'feature_value_lang` (`id_feature_value`, `id_lang`, `value`) 
		VALUES ('.(int)$id_feature_value.', '.(int)$id_lang.', "'.pSQL($value).'")');
	}
	
	public function insertFeature($product_feat,$features)
	{
		$fields = array(
		'id_product' => (int)$product_feat['id_product'], 
		'id_lang' => (int)$product_feat['id_lang']);
	
		foreach($features as $name => $value)
		{
	
			$id_feature = self::getOrCreateFeature($name,$fields['id_lang']);
			$id_feature_value = self::getOrCreateFeatureValue($value,$fields['id_lang'],$id_feature);
			
			self::insertFeatureProduct($id_feature,$id_feature_value,$fields['id_product']);	
		}
	}
	
	public function insertFeatureProduct($id_feature,$id_feature_value,$id_product)
	{

		$result = Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'feature_product` (`id_product`,`id_feature_value`,`id_feature`) 
		VALUES ('.(int)$id_product.','.(int)$id_feature_value.','.(int)$id_feature.' )');

	}
	
	public function getOrCreateFeature($name,$id_lang)
	{
		$id_feature = Db::getInstance()->getValue('
		SELECT `id_feature`
		FROM `'._DB_PREFIX_.'feature_lang` 
		WHERE `name` = "'.pSQL($name).'"');
		
		if (empty($id_feature))
		{
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'feature` () 
			VALUES ()');
			$id_feature = Db::getInstance()->Insert_ID();
			
			self::insertFeatureLang($id_feature,$id_lang,$name);
		}
		
		return $id_feature;
	
	}
	
	
	public function getOrCreateFeatureValue($value,$id_lang,$id_feature)
	{
		$id_feature_value = Db::getInstance()->getValue('
		SELECT f.`id_feature_value`
		FROM `'._DB_PREFIX_.'feature_value` f
		LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fl ON (f.`id_feature_value` = fl.`id_feature_value`) 
		WHERE fl.`value` = "'.pSQL($value).'" AND f.`id_feature` = '.(int)$id_feature.' ');
		
		if (empty($id_feature_value))
		{
			$result = Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'feature_value` (`id_feature`,`custom`) 
			VALUES ('.(int)$id_feature.',0)');
			$id_feature_value = Db::getInstance()->Insert_ID();
			
			self::insertFeatureValueLang($id_feature_value,$id_lang,$value);
			
		}
		
		return $id_feature_value;
	
	}
	
	public function createFeatureId($id,$name,$parameters)
	{
		$result = Db::getInstance()->getValue('
		SELECT `name`
		FROM `'._DB_PREFIX_.'feature` 
		WHERE `id_feature` = '.(int)$id.'');
		
		if (empty($result))
		{
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'feature` (`id_feature`) 
			VALUES ('.(int)$id.')');
			
			if($parameters['language']=='all')
			{
		
				$languages = self::getAllIdLanguages();
				foreach ($languages as $language)
				{
					self::insertFeatureLang($id,$language['id_lang'],$name);
				}
			}
		
			else
			{
				self::insertFeatureLang($id,$parameters['id_lang'],$name);
			}
			
			
			
		}
		
	
	}
	
	

	
	
	public function createFeatureValueId($id,$id_feature,$value,$parameters)
	{
		$result = Db::getInstance()->getValue('
		SELECT `id_feature`
		FROM `'._DB_PREFIX_.'feature_value`
		WHERE `id_feature_value` = '.(int)$id.' ');
		
		if (empty($result))
		{
			$result = Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'feature_value` (`id_feature_value`,`id_feature`) 
			VALUES ('.(int)$id.','.(int)$id_feature.' )');
			
			if($parameters['language']=='all')
			{
		
				$languages = self::getAllIdLanguages();
				foreach ($languages as $language)
				{
					self::insertFeatureValueLang($id,$language['id_lang'],$value);
				}
			}
			
			else
			{
				self::insertFeatureValueLang($id,$parameters['id_lang'],$value);
			}
								
		}
	
	}
	
	public function insertPack($id_product_pack,$id_product_item,$quantity)
	{
	
		if($id_product_pack != 0 && $id_product_item != 0)
		{
			$result = Db::getInstance()->getValue('
			SELECT *
			FROM `'._DB_PREFIX_.'pack`
			WHERE `id_product_pack` = '.(int)$id_product_pack.' AND `id_product_item` = '.(int)$id_product_item.' ');
		
			if (empty($result))
			{
				Db::getInstance()->Execute(' 
				INSERT INTO `'._DB_PREFIX_.'pack` (`id_product_pack`,`id_product_item`,`quantity`) 
				VALUES ('.(int)$id_product_pack.','.(int)$id_product_item.','.(int)$quantity.' )');
			}
		}

	}
	

	
	public function insertCategories($categories, $id_product, $id_lang, $symbol)
	{
		$j=0;
		$p=0;
		$category = explode($symbol, $categories);
		$j=self::insertCategory($category[0],1,1,$id_product,$id_lang);
		
		if(isset($j) && !empty($category[1]))
		{
			$parent=self::insertCategory($category[1],$j,2,$id_product,$id_lang);
		}
		
		if(!empty($category[2]) && isset($parent))
		{
			$k=self::insertCategory($category[2],$parent,3,$id_product,$id_lang);
		}
		
		
		if(!empty($k)){$out=$k;}
		elseif(!empty($parent)){$out=$parent;}
		elseif(!empty($j)){$out=$j;}
		self::updateDefaultCategoryByIDProduct($id_product,$out);
		return $out;
	
	}
	
	public function insertCategory($category,$parent,$deep,$id_product,$id_lang)
	{
		$id_category = self::getIdCategoryByNameAndParent($category,$parent,$id_lang);
		
		if (empty($id_category))
		{
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'category` (`id_parent`, `level_depth`, `active`) 
			VALUES ('.(int)($parent).', '.(int)($deep).', 1)');
			$id_category = Db::getInstance()->Insert_ID();
			
			self::insertCategoryLang($id_category,$id_lang,$category);
			
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'category_group` (`id_category`, `id_group`) 
			VALUES ('.(int)($id_category).', 1)');

		}
		
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'category_product` (`id_category`, `id_product`) 
		VALUES ('.(int)($id_category).', '.(int)($id_product).')');
		
		return $id_category;	
	
	}
	
	public function insertCategoryLang($id_category,$id_lang,$category){
		Db::getInstance()->Execute(' 
		INSERT INTO `'._DB_PREFIX_.'category_lang` (`id_category`, `id_lang`, `name`, `link_rewrite`, `meta_title`, `meta_keywords`, `meta_description`) 
		VALUES ('.(int)($id_category).', '.(int)($id_lang).', "'.pSQL($category).'", "'.pSQL(self::SEOlink($category)).'", "'.pSQL($category).'", "'.pSQL($category).'", "'.pSQL($category).'" )');

	}
	
	public function insertCategoryTrans($id_category,$id_lang,$category){
	
		$value = Db::getInstance()->getValue('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category_lang` 
		WHERE `name` = "'.pSQL($category).'" AND `id_lang`= '.(int)($id_lang).' ');

		if (empty($value))
		{
			self::insertCategoryLang($id_category,$id_lang,$category);
		}
	
	}
	
	public function getIdCategoryByNameAndParent($category,$parent,$id_lang)
	{
		return Db::getInstance()->getValue('
		SELECT c.`id_category`
		FROM `'._DB_PREFIX_.'category` c
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category`) 
		WHERE cl.`name` = "'.pSQL($category).'" AND c.`id_parent` = '.(int)($parent).' AND cl.`id_lang`= '.(int)($id_lang).' ');
	}
	
	
	public function priceWithoutVat($price_vat,$vat)
	{
		$with_vat = $vat + 100;
		(float)$coe = 100 / (float)$with_vat;
		(float)$price = (float)$price_vat * (float)$coe;
		return $price;
	}
	
	
	public function myPrice($price,$pricechange,$pricevalue)
	{
		if ($pricechange==1)
		{
			return $myprice = ($price - $price / 100 * (float)$pricevalue);
		} 
	
		elseif ($pricechange==2)
		{
		return $myprice = ($price + $price / 100 * (float)$pricevalue);
		} 
		
		else 
	
		{
		return $price;
		}
	}
	
	public function returnIt($result)
	{
		return($result);
	}
	
	
	public function productQuantity($delivery_date,$delivery_param)
	{
		if((int)$delivery_date > (int)$delivery_param)
		{
		return(0);
		}	
		
		else
		{
		return(90);
		}
	}
	
	public function importManufacturer($manufacturer)
	{
		$id_manufacturer = Manufacturer::getIdByName($manufacturer);
		if ($id_manufacturer == false)
		{
			$result = Db::getInstance()->Execute(' INSERT INTO `'._DB_PREFIX_.'manufacturer` (`name`,`active`) VALUES ("'.pSQL($manufacturer).'",1)');
			$id_manufacturer = Db::getInstance()->Insert_ID();
		}
		
		return $id_manufacturer;

	}	
	
	public function importManufacturerWithID($manufacturer,$id)
	{
		$id_manufacturer = Manufacturer::getIdByName($manufacturer);
		if ($id_manufacturer == false)
		{
			$result = Db::getInstance()->Execute(' INSERT INTO `'._DB_PREFIX_.'manufacturer` (`id_manufacturer`,`name`,`active`) VALUES ('.(int)$id.',"'.pSQL($manufacturer).'",1)');
		}
		
		return $id;

	}	
	
	public function SEOlink($link)
	{
		$out = StrTr($link, " ", "-" );
		$arrFROM = explode( " ", "Ě É Ë ě é ë Š š Č č Ř ř Ž ž Ý ý Á Ä á ä Í í Ů Ú Ü ů ú ü ň Ň Ľ ľ ĺ Ť ť Ô Ö Ó ô ö ó" );
		$arrTO =   explode( " ", "E E E e e e S s C c R r Z z Y y A A a a I i U U U u u u n N L l l T t O O O o o o" );
		while( list( $key, $val ) = each( $arrFROM ) )
		{
			$out = ereg_replace( $arrFROM[$key], $arrTO[$key], $out );
		}
		$out = Ereg_Replace( "[^0-9a-zA-Z-]", "", $out );
		$out = Ereg_Replace( "\-{2,100}", "-", $out );
		$out = Ereg_Replace( "^\-", "", $out );
		$out = Ereg_Replace( "\-$", "", $out );
		return strtolower($out);
	}
	
	public function validateProductFieldsForUpdateImportPrefix($product,$supplier_reference)
	{
	
		$fields = array();
		if(isset($product['my_price'])){
		$fields['import_price'] = (float)($product['my_price']);}
		if(isset($product['quantity'])){
		$fields['import_quantity'] = (float)($product['quantity']);}
		if(isset($product['ecotax'])){
		$fields['ecotax'] = (float)($product['ecotax']);}
		
		return $fields;
	}
	
	
	public function validateProductFieldsForUpdate($product,$supplier_reference)
	{
	
		$fields = array();
		if(isset($product['id_tax_rules_group'])){
		$fields['id_tax_rules_group'] = (int)$product['id_tax_rules_group'];}
		if(isset($product['id_manufacturer'])){
		$fields['id_manufacturer'] = (int)($product['id_manufacturer']);}
		if(isset($product['id_supplier'])){
		$fields['id_supplier'] = (int)($product['id_supplier']);}
		if(isset($product['id_category_default'])){
		$fields['id_category_default'] = (int)($product['id_category_default']);}
		if(isset($product['id_color_default'])){
		$fields['id_color_default'] = (int)($product['id_color_default']);}
		if(isset($product['quantity'])){
		$fields['quantity'] = (int)($product['quantity']);}
		if(isset($product['minimal_quantity'])){
		$fields['minimal_quantity'] = (int)($product['minimal_quantity']);}
		if(isset($product['my_price'])){
		$fields['price'] = (float)($product['my_price']);}
		if(isset($product['additional_shipping_cost'])){
		$fields['additional_shipping_cost'] = (float)($product['additional_shipping_cost']);}
		if(isset($product['wholesale_price'])){
		$fields['wholesale_price'] = (float)($product['wholesale_price']);}
		if(isset($product['on_sale'])){
		$fields['on_sale'] = (int)($product['on_sale']);}
		if(isset($product['online_only'])){
		$fields['online_only'] = (int)($product['online_only']);}
		if(isset($product['ecotax'])){
		$fields['ecotax'] = (float)($product['ecotax']);}
		if(isset($product['unity'])){
		$fields['unity'] = pSQL($product['unity']);}
		if(isset($product['unit_price'])){
    	$fields['unit_price_ratio'] = (float)($product['unit_price']);}
    	if(isset($product['ean13'])){
		$fields['ean13'] = pSQL($product['ean13']);}
		if(isset($product['upc'])){
		$fields['upc'] = pSQL($product['upc']);}
		if(isset($product['reference'])){
		$fields['reference'] = pSQL($product['reference']);}
		if(isset($supplier_reference)){
		$fields['supplier_reference'] = pSQL($supplier_reference);}
		if(isset($product['location'])){
		$fields['location'] = pSQL($product['location']);}
		if(isset($product['width'])){
		$fields['width'] = (float)($product['width']);}
		if(isset($product['height'])){
		$fields['height'] = (float)($product['height']);}
		if(isset($product['depth'])){
		$fields['depth'] = (float)($product['depth']);}
		if(isset($product['weight'])){
		$fields['weight'] = (float)($product['weight']);}
		if(isset($product['out_of_stock'])){
		$fields['out_of_stock'] = (int)$product['out_of_stock'];}
		else{$fields['out_of_stock']=2;}
		if(isset($product['quantity_discount'])){
		$fields['quantity_discount'] = (int)($product['quantity_discount']);}
		if(isset($product['customizable'])){
		$fields['customizable'] = (int)($product['customizable']);}
		if(isset($product['uploadable_files'])){
		$fields['uploadable_files'] = (int)($product['uploadable_files']);}
		if(isset($product['text_fields'])){
		$fields['text_fields'] = (int)($product['text_fields']);}
		if(isset($product['active'])){
		$fields['active'] = (int)($product['active']);}
		if(isset($product['available_for_order'])){
		$fields['available_for_order'] = (int)($product['available_for_order']);}
		if(isset($product['condition'])){
		$fields['condition'] = pSQL($product['condition']);}
		if(isset($product['show_price'])){
		$fields['show_price'] = (int)($product['show_price']);}
		if(isset($product['indexed'])){
		$fields['indexed'] = 0;} // Reset indexation every times
		if(isset($product['cache_is_pack'])){
		$fields['cache_is_pack'] = (int)($product['cache_is_pack']);}
		if(isset($product['cache_has_attachments'])){
		$fields['cache_has_attachments'] = (int)($product['cache_has_attachments']);}
		if(isset($product['cache_default_attribute'])){
		$fields['cache_default_attribute'] = (int)($product['cache_default_attribute']);}
		if(isset($product['date_add'])){
		$fields['date_add'] = pSQL($product['date_add']);}
		if(isset($product['date_upd'])){
		$fields['date_upd'] = pSQL($product['date_upd']);}
		
		return $fields;
	
	}
	
	public function returnIdProductByReference($reference,$supplier_reference)
	{
		return Db::getInstance()->getValue('
		SELECT `id_product`
		FROM `'._DB_PREFIX_.'product` 
		WHERE `reference` = "'.pSQL($reference).'" AND `supplier_reference` = "'.pSQL($supplier_reference).'" ');
	}
	
	public function specificPriceByPrices($id_product,$quantity,$old_price,$new_price)
	{
		(float)$reduction = (float)$old_price - (float)$new_price;
		if ($reduction > 0)
		{
			self::specificPriceReductionAmount($id_product,$quantity,$old_price,$reduction);
		}
	}
	
	public function specificPriceByPricesVat($id_product,$quantity,$old_price,$new_price,$vat)
	{
		(float)$reduction = (float)$old_price - (float)$new_price;
		$old_price = self::priceWithoutVat($old_price,$vat);
		$new_price = self::priceWithoutVat($new_price,$vat);
		if ($reduction > 0)
		{
			self::specificPriceReductionAmount($id_product,$quantity,$old_price,$reduction);
		}
	}
	
	public function specificPriceReductionAmount($id_product,$quantity,$old_price,$reduction)
	{
		if($id_product != 0)
		{
			$date = date("Y-m-d H:i:s");
			$tomorrow = date("Y-m-d H:i:s", strtotime("+1 days"));
		
			$selected = Db::getInstance()->getValue('
			SELECT *
			FROM `'._DB_PREFIX_.$prefix.'specific_price`
			WHERE `id_product` = '.(int)$id_product.'');
		
			if (empty($selected))
			{

				Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'specific_price` (`id_product`,`from_quantity`,`price`,`reduction`,`reduction_type`,`from`,`to`)
				VALUES ('.(int)$id_product.','.(int)$quantity.','.(float)$old_price.','.(float)$reduction.',"'.pSQL('amount').'","'.pSQL($date).'","'.pSQL($tomorrow).'") ');
	
			}
		
			else
			{

				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.$prefix.'specific_price` SET 
				`price` = "'.(float)$old_price.'",
				`reduction` = "'.(float)$reduction.'",
				`to` = "'.pSQL($tomorrow).'"
				WHERE `id_product` = '.(int)$id_product.' ');
		
			}
		
		return true;
		
		}
		
		else
		{
		return false;
		}
	
	}
	
	public function specificPricePercentage($id_product,$quantity,$old_price,$reduction)
	{
		if($id_product != 0)
		{
			$date = date("Y-m-d H:i:s");
			$tomorrow = date("Y-m-d H:i:s", strtotime("+1 days"));
		
			$selected = Db::getInstance()->getValue('
			SELECT *
			FROM `'._DB_PREFIX_.$prefix.'specific_price`
			WHERE `id_product` = '.(int)$id_product.'');
		
			if (empty($selected))
			{

				Db::getInstance()->Execute('
				INSERT INTO `'._DB_PREFIX_.'specific_price` (`id_product`,`from_quantity`,`price`,`reduction`,`reduction_type`,`from`,`to`)
				VALUES ('.(int)$id_product.','.(int)$quantity.','.(float)$old_price.','.(float)$reduction.',"'.pSQL('percentage').'","'.pSQL($date).'","'.pSQL($tomorrow).'") ');
	
			}
		
			else
			{

				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.$prefix.'specific_price` SET 
				`price` = "'.(float)$old_price.'",
				`reduction` = "'.(float)$reduction.'",
				`to` = "'.pSQL($tomorrow).'"
				WHERE `id_product` = '.(int)$id_product.' ');
		
			}
		
		return true;
		
		}
		
		else
		{
		return false;
		}
	
	}
	
	public function deleteOldSpecificPrices()
	{
		$yesterday = date("Y-m-d H:i:s", strtotime("-1 days"));
	
		Db::getInstance()->Execute('
		DELETE FROM `'._DB_PREFIX_.'specific_price` 
		WHERE `to` < "'.pSQL($yesterday).'" OR `id_product` = 0');		
	
	}
	
	public function insertAffLink($id_product,$afflink)
	{
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `afflink` = "'.pSQL($afflink).'" WHERE `id_product` = '.(int)$id_product.' ');
	}
	
	
	public function insertTag($tag,$id_product,$id_lang)
		{
			$id_tag = Db::getInstance()->getValue('
			SELECT `id_tag`
			FROM `'._DB_PREFIX_.'tag`
			WHERE `name` = "'.pSQL($tag).'"');
			
			if (empty($id_tag))
			{
				Db::getInstance()->Execute(' 
				INSERT INTO `'._DB_PREFIX_.'tag` (`id_lang`,`name`) 
				VALUES ('.(int)($id_lang).', "'.pSQL($tag).'" )');
				$id_tag = Db::getInstance()->Insert_ID();
			}
			
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'product_tag` (`id_product`,`id_tag`) 
			VALUES ('.(int)($id_product).', '.(int)$id_tag.' )');
			
			return $id_tag;
	
		}
	
	
///////////////////// Advanced Features //////////////////////////////

	public function insertProductMainCategory($id_product,$id_category)
	{
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `id_category_main` = '.(int)$id_category.' WHERE `id_product` = '.(int)$id_product.' ');	
	
	}

	public function checkCategoryByCode($mycategory)
	{
		$num_products = Db::getInstance()->ExecuteS(
		'SELECT * FROM `'._DB_PREFIX_.'category` WHERE `code`=("'.(int)$mycategory['code'].'")');
		if (count($num_products)==0)
		{
			return true;
		}
			    
		else
		{
			return false;
		}

	}
	
	
	public function checkCategoryByID($mycategory)
		{
			$num_products = Db::getInstance()->ExecuteS(
			' SELECT * FROM `'._DB_PREFIX_.'category` WHERE `id_category`= '.(int)$mycategory['id_category'].'');
			if (count($num_products)==0)
			{
				return true;
			}
				    
			else
			{
				return false;
			}
	
		}
		
	public function checkCategoryByIDNew($category_id)
		{
			$num_products = Db::getInstance()->ExecuteS(
			' SELECT * FROM `'._DB_PREFIX_.'category` WHERE `id_category`= '.(int)$category_id.'');
			if (count($num_products)==0)
			{
				return false;
			}
				    
			else
			{
				return true;
			}
	
		}

	public function insertIntoCategory($mycategory,$parameters)
	{
		$category = array();
		
		$category['id_category'] = $mycategory['id_category'];
		$category['id_parent'] = $mycategory['id_parent'];
		if(isset($mycategory['code'])){
		$category['code'] = $mycategory['code'];	}
		if(isset($mycategory['code_parent'])){
		$category['code_parent'] = $mycategory['code_parent'];	}
		if(isset($mycategory['active'])){
		$category['active'] = $mycategory['active'];}
		else { $category['active'] = 1; }
		$category['date_add'] = date("Y-m-d H:i:s");
		$category['date_upd'] = date("Y-m-d H:i:s");
		if(isset($mycategory['level'])){
			$category['level_depth'] = $mycategory['level'];
			$import = true;
			}
		else
		{
			$import = self::checkCategoryByIDNew($category['id_parent']);
			if($import == true)
			{
				$category['level_depth'] = self::returnLevelDepth($category['id_parent'])+1;
			}
		}
		
		if($import == true){
		Db::getInstance()->AutoExecute(_DB_PREFIX_.'category', $category, 'INSERT');
		
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'category_group` (`id_category`,`id_group`) VALUES ('.(int)$category['id_category'].',1) ');
		
		self::insertIntoCategoryLang($mycategory,$parameters);
		}
		
	}
	
	public function returnLevelDepth($id_category)
	{	
		return $levelDepth = Db::getInstance()->getValue('
		SELECT `level_depth`
		FROM `'._DB_PREFIX_.'category` 
		WHERE `id_category`= '.(int)$id_category.' ');
		
	}
	
	public function getAllIdLanguages()
	{
		$languages = Db::getInstance()->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'lang` ');
		return $languages;
	}
	
	public function insertIntoCategoryLang($mycategory,$parameters)
	{
		$seolink = self::SEOlink($mycategory['name']);
	
		$category = array();
		
		$category['id_category'] = $mycategory['id_category'];
		$category['id_lang'] = $parameters['id_lang'];
		$category['name'] = $mycategory['name'];
		$category['description'] = $mycategory['description'];
		$category['link_rewrite'] = $seolink;

		if($parameters['language']=='all')
		{
		
			$languages = self::getAllIdLanguages();
			
			foreach ($languages as $language)
			{
				$category['id_lang'] = $language['id_lang'];
				Db::getInstance()->AutoExecute(_DB_PREFIX_.'category_lang', $category, 'INSERT');	
			}
		}
		
		else
		
		{
			Db::getInstance()->AutoExecute(_DB_PREFIX_.'category_lang', $category, 'INSERT');
		}
	}
	
	
	public function checkImageByIDProduct($id_product)
	{
		$num_products = Db::getInstance()->ExecuteS(
		'SELECT * FROM `'._DB_PREFIX_.'image` WHERE `id_product`=("'.(int)$id_product.'")');
		if (count($num_products)==0)
		{
			return true;
		}
			    
		else
		{
			return false;
		}

	}
	
	public function checkImageByIDProductImgReference($id_product,$img_reference)
	{
		$num_products = Db::getInstance()->ExecuteS(
		'SELECT * FROM `'._DB_PREFIX_.'image` WHERE `id_product`= '.(int)$id_product.' AND `img_reference`= "'.pSQL($img_reference).'" ');
		if (count($num_products)==0)
		{
			return true;
		}
			    
		else
		{
			return false;
		}

	}
	
	
	public function checkAndInsertProductInCategory($id_product,$id_category)
	{
		$num_products = Db::getInstance()->ExecuteS(
		'SELECT * FROM `'._DB_PREFIX_.'category_product` WHERE `id_product`=("'.(int)$id_product.'") AND `id_category`=("'.(int)$id_category.'") ');
		if (count($num_products)==0)
		{
			self::insertProductInCategory($id_product,$id_category);
			return true;
		}
			    
		else
		{
			return false;
		}

	}

	public function insertProductInCategory($id_product,$id_category)
	{
		Db::getInstance()->Execute('
		INSERT INTO `'._DB_PREFIX_.'category_product` (`id_product`,`id_category`)
		VALUES ('.(int)$id_product.','.(int)$id_category.') ');

	}	
	
	public function renameIt($array)
	{
	
		$renameit = Db::getInstance()->ExecuteS(' 
		SELECT * FROM `'._DB_PREFIX_.'imin_rename` 
		WHERE `details`=("'.pSQL($array['details']).'") 
		AND ( `code_name`= "'.pSQL($array['code_name']).'" OR `code_name`= "'.pSQL('all').'" )');
		
		$value = $array['value'] ;
		foreach ($renameit as $rename)
		{	
			$value = str_replace($rename['name'], $rename['value'], $value);
		}
		
		return $value;
	}
	
	public function returnIdParent($id_category)
	{	
		return  Db::getInstance()->getValue('
		SELECT `id_parent`
		FROM `'._DB_PREFIX_.'category` 
		WHERE `id_category`= '.(int)$id_category.' ');
		
	}
	
	public function isCategoryActive($id_category)
	{	
		$active = Db::getInstance()->getValue('
		SELECT `active`
		FROM `'._DB_PREFIX_.'category` 
		WHERE `id_category`= '.(int)$id_category.' ');
		
		if($active==1)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}
	
	public function updateProductLangAvailable($available_now,$available_later,$product,$parameters)
	{
		$id_product = self::returnIdByReference($product['reference'],$parameters['code_name']);
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product_lang` 
		SET `available_now` = "'.pSQL().'", `available_later` = "'.pSQL().'" WHERE `id_product` = '.(int)$id_product.' ');	
	}
	
	
	public function redeactivateNonStockProducts($codename)
	{
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `active` = 1 WHERE `supplier_reference` = "'.pSQL($codename).'" ');
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'product` 
		SET `active` = 0 WHERE `supplier_reference` = "'.pSQL($codename).'" AND `quantity` = 0 ');
	}
	
	public function redeactivateCategories(){
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'category`
		SET `active` = 0 ');
	
		$cat = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
				SELECT DISTINCT(c.id_category), c.id_product, p.active
				FROM '._DB_PREFIX_.'category_product c
				LEFT JOIN '._DB_PREFIX_.'product p ON (c.id_product = p.id_product)
				WHERE p.active = 1
				');

				foreach ($cat AS $category)
				{
					Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'category`
					SET `active` = 1 WHERE `id_category` = '.(int)$category['id_category'].' ');
				

				}

	}
	
///// Presta 1.4
public function validateProductFields($product,$supplier_reference)
	{
	
		$fields = array();
		
		$fields['id_tax_rules_group'] = (int)$product['id_tax_rules_group'];
		$fields['id_manufacturer'] = (int)($product['id_manufacturer']);
		$fields['id_supplier'] = (int)($product['id_supplier']);
		$fields['id_category_default'] = (int)($product['id_category_default']);
		$fields['id_color_default'] = (int)($product['id_color_default']);
		$fields['quantity'] = (int)($product['quantity']);
		$fields['minimal_quantity'] = (int)($product['minimal_quantity']);
		$fields['price'] = (float)($product['my_price']);
		$fields['additional_shipping_cost'] = (float)($product['additional_shipping_cost']);
		$fields['wholesale_price'] = (float)($product['wholesale_price']);
		$fields['on_sale'] = (int)($product['on_sale']);
		$fields['online_only'] = (int)($product['online_only']);
		$fields['ecotax'] = (float)($product['ecotax']);
		$fields['unity'] = pSQL($product['unity']);
    	$fields['unit_price_ratio'] = (float)($product['unit_price']);
		$fields['ean13'] = pSQL($product['ean13']);
		$fields['upc'] = pSQL($product['upc']);
		$fields['reference'] = pSQL($product['reference']);
		$fields['supplier_reference'] = pSQL($supplier_reference);
		$fields['location'] = pSQL($product['location']);
		$fields['width'] = (float)($produZZct['width']);
		$fields['height'] = (float)($product['height']);
		$fields['depth'] = (float)($product['depth']);
		$fields['weight'] = (float)($product['weight']);
		$fields['out_of_stock'] = pSQL($product['out_of_stock']);
		$fields['quantity_discount'] = (int)($product['quantity_discount']);
		$fields['customizable'] = (int)($product['customizable']);
		$fields['uploadable_files'] = (int)($product['uploadable_files']);
		$fields['text_fields'] = (int)($product['text_fields']);
		$fields['active'] = (int)($product['active']);
		$fields['available_for_order'] = (int)($product['available_for_order']);
		$fields['condition'] = pSQL($product['condition']);
		$fields['show_price'] = (int)($product['show_price']);
		$fields['indexed'] = 0; // Reset indexation every times
		$fields['cache_is_pack'] = (int)($product['cache_is_pack']);
		$fields['cache_has_attachments'] = (int)($product['cache_has_attachments']);
		$fields['cache_default_attribute'] = (int)($product['cache_default_attribute']);
		$fields['date_add'] = pSQL($product['date_add']);
		$fields['date_upd'] = pSQL($product['date_upd']);
		
		return $fields;
	
	}
	
	public function returnIdTaxRulesGroup($vat,$parameters)
	{
		$id_tax = Db::getInstance()->getValue('
		SELECT `id_tax`
		FROM `'._DB_PREFIX_.'tax` 
		WHERE `rate`= '.(float)round($vat,3).' ');
		
		$id_tax_rules_group = Db::getInstance()->getValue('
		SELECT `id_tax_rules_group`
		FROM `'._DB_PREFIX_.'tax_rule` 
		WHERE `id_tax`= '.(int)$id_tax.' ');
		
		if(empty($id_tax) && empty($id_tax_rules_group))
		{
			$id_tax_rules_group=0;
		}
		
		return($id_tax_rules_group);
		
	}
	
	/*	
	
	public function insertImageCurl($id_product,$img_url,$id_lang,$product_name,$position,$cover)
		{
		
			$result = Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'image` (`id_product`, `position`, `cover`) 
			VALUES ('.(int)($id_product).', '.(int)($position).', '.(int)($cover).')');
			$id_image = Db::getInstance()->Insert_ID();
			
			Db::getInstance()->Execute(' 
			INSERT INTO `'._DB_PREFIX_.'image_lang` (`id_image`, `id_lang`, `legend`) 
			VALUES ('.(int)($id_image).', '.(int)($id_lang).', "'.pSQL($product_name).'")');
	
			$sourcecode = self::getImageFromUrlCurl($img_url);
			
			$savefile = fopen('../../img/tmp/'.$id_product.'-'.$id_image.'.jpg', 'w');
			fwrite($savefile, $sourcecode);
			
			//imageResize('../../img/tmp/'.$id_product.'-'.$id_image.'.jpg', '../../img/p/'.$id_product.'-'.$id_image.'.jpg');
			self::copyImgCurl('../../img/tmp/'.$id_product.'-'.$id_image.'.jpg', '../../img/p/'.$id_product.'-'.$id_image);
			
			fclose($savefile);	
		}	
		
		
		public function copyImgCurl($source, $destination)
		{
			$watermark_types = explode(',', Configuration::get('WATERMARK_TYPES'));
			imageResize($source, $destination.'.jpg');
			$imagesTypes = ImageType::getImagesTypes($entity);
			foreach ($imagesTypes AS $k => $imageType)
				imageResize($source, $destination.'-'.stripslashes($imageType['name']).'.jpg', $imageType['width'], $imageType['height']);
			if (in_array($imageType['id_image_type'], $watermark_types))
				Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
				
			unlink($tmpfile);
			return true;
		}	
	
	
	
		public function getOrCreateTaxRulesGroup($product,$parameters)
		{
			$id_tax = self::getOrCreateTax($product,$parameters);
			
			
			$name = $parameters['language'].' - '.$product['vat'].'%';
			
			$id_tax_rules_qroup = Db::getInstance()->getValue('
			SELECT `id_tax_rules_group`
			FROM `'._DB_PREFIX_.'tax_rules_group` 
			WHERE `name` = "'.pSQL($name).'"');
			
			if (empty($id_tax_rules_qroup))
			{
				$result = Db::getInstance()->Execute(' 
				INSERT INTO `'._DB_PREFIX_.'tax_rules_group` (`name`, `active`) 
				VALUES ("'.pSQL($name).'",1)');
				$id_tax_rules_qroup = Db::getInstance()->Insert_ID();
				
				$id_country = Country::getByIso($parameters['language']);
				
				$result = Db::getInstance()->Execute(' 
				INSERT INTO `'._DB_PREFIX_.'tax_rule` (`id_tax_rules_group`, `id_country`, `id_tax`) 
				VALUES ('.(int)$id_tax_rules_qroup.', '.(int)$id_country.', '.(int)id_tax.')');
	
			
			}
			
			return $id_tax_rules_qroup;
			
		}
		
		public function getOrCreateTax($product,$parameters)
		{
			$id_tax = Db::getInstance()->getValue('
			SELECT `id_tax`
			FROM `'._DB_PREFIX_.'tax` 
			WHERE `rate` = "'.(float)$product['vat'].'"');
			
			if (empty($id_tax))
			{
				$result = Db::getInstance()->Execute(' 
				INSERT INTO `'._DB_PREFIX_.'tax` (`rate`, `active`) 
				VALUES ('.(int)($product['vat']).',1)');
				$id_tax = Db::getInstance()->Insert_ID();
				
				$name = $parameters['language'].' - '.$product['vat'].'%';
				
				$result = Db::getInstance()->Execute(' 
				INSERT INTO `'._DB_PREFIX_.'tax_lang` (`id_tax`, `id_lang`, `name`) 
				VALUES ('.(int)$id_tax.', '.(int)$product['id_lang'].', '.pSQL($name).')');
				$id_tax = Db::getInstance()->Insert_ID();
			
			}
			
			return $id_tax;
		}
	*/	
	
	
}

?>