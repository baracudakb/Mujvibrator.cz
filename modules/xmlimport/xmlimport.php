<?php
class xmlimport extends Module
{
	private $_html = '';
	private $_postErrors = array();
	
	function __construct()
	{
		$this->name = 'xmlimport';
		// The parent construct is required for translations
		parent::__construct();
		
		$this->tab = 'Im IN';
		$this->version = 'i3 PS 1.4+';
		
		$this->displayName = $this->l('XML Import');
		$this->description = $this->l('Import products from XML');
	}

	public function install()
	{
	
		if (!parent::install())
		return false;

		//Db::getInstance()->Execute(' ALTER TABLE `'._DB_PREFIX_.'product` CHANGE `reference` `reference` varchar(128) NULL DEFAULT NULL');
		
		return Db::getInstance()->Execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'imin_xmlimport` (
			`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`code_name` VARCHAR(100) NOT NULL,
			`source` VARCHAR(100) NOT NULL,
			`security_key` VARCHAR(100),
			`unique_field` VARCHAR(40),
			`language` VARCHAR(4) NOT NULL,
			`available_now` VARCHAR(100),
			`available_later` VARCHAR(100),
			`delivery` INT,
			`categories` TINYINT(1),
			`symbol` VARCHAR(4),
			`price_change` TINYINT(1),
			`price_value` TINYINT(1),
			`description` TINYINT(1),
			`images` TINYINT(1),
			`manufacturer` TINYINT(1),
			`parameters` TINYINT(1),
			`update_quantity` TINYINT(1),
			`update_price` TINYINT(1),
			`deactivate` TINYINT(1),
			`authenticate` TINYINT(1),
			`username` VARCHAR(100) NOT NULL,
			`domain` VARCHAR(100) NOT NULL
		)  
		');
		

	}
	

    public function uninstall()
	{
		if (!parent::uninstall())
		return false;
			
		return Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'imin_xmlimport`');
		
	}
	
	private function updateImport()
	{
		$this->_html .= '<div class="conf confirm">'.$this->l('Updated').'</div>';
		
		
		$id_import= Tools::getValue('id_import');
		$code_name = Tools::getValue('code_name');	
		$source= Tools::getValue('source');
		$security_key= Tools::getValue('security_key');
		$unique_field = Tools::getValue('unique_field');
		$language = Tools::getValue('language');
		$available_now = Tools::getValue('available_now');
		$available_later = Tools::getValue('available_later');
		$delivery = Tools::getValue('delivery');
		$categories = Tools::getValue('categories');
		$symbol = Tools::getValue('symbol');
		$price_change = Tools::getValue('price_change');
		$price_value = Tools::getValue('price_value');
		$description = Tools::getValue('description');
		$images = Tools::getValue('images');
		$manufacturer = Tools::getValue('manufacturer');
		$parameters = Tools::getValue('parameters');
		$update_quantity = Tools::getValue('update_quantity');
		$update_price = Tools::getValue('update_price');
		$deactivate = Tools::getValue('deactivate');
		
		
		Db::getInstance()->Execute('
		UPDATE `'._DB_PREFIX_.'imin_xmlimport` 
		SET 
		source="'.pSQL($source).'",
		security_key="'.pSQL($security_key).'",
		unique_field="'.pSQL($unique_field).'",
		language="'.pSQL($language).'",
		available_now="'.pSQL($available_now).'",
		available_later="'.pSQL($available_later).'",
		delivery="'.(int)$delivery.'",
		categories="'.(int)$categories.'",
		symbol="'.pSQL($symbol).'",
		price_change="'.(int)$price_change.'",
		price_value="'.(float)$price_value.'",
		description="'.pSQL($description).'",
		images="'.(int)$images.'",
		manufacturer="'.(int)$manufacturer.'",
		parameters="'.(int)$parameters.'",
		update_quantity="'.(int)$update_quantity.'",
		update_price="'.(int)$update_price.'",
		deactivate="'.(int)$deactivate.'" 
		WHERE id="'.(int)$id_import.'"');
		echo(mysql_error());

		
	}
	
	private function newImport()
	{
	
	$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			
				<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Add new XML').'</legend>		
				<label>'.$this->l('XML Import codename').'</label>
				<div class="margin-form">
					<input type="text" name="iminnewimport" style="width: 400px;"  />
				</div>
				<label>'.$this->l('Your email:').'</label>
				<div class="margin-form">
					<input type="text" name="register_username" style="width: 400px;"  />
				</div>
				<label>'.$this->l('Your domain').'</label>
				<div class="margin-form">
					<input type="text" name="register_domain" style="width: 400px;"  />
				</div>
				<p class="clear">'.$this->l('Codename is in your read me file.').'</p>
				<center><input type="submit" name="'.$this->name.'add_new_import" value="'.$this->l('Add').'" class="button" /></center>			
			</fieldset>
		</form>
		<br/>
		';

						
	}
	
	private function addNewImport()
	{
	$this->codenames = Tools::getValue('iminnewimport');
	$this->username = Tools::getValue('register_username');
	$this->domain = Tools::getValue('register_domain');
	Db::getInstance()->Execute(' INSERT INTO `'._DB_PREFIX_.'imin_xmlimport` (`code_name`,`username`,`domain`,`authenticate`) VALUES ("'.pSQL($this->codenames).'","'.pSQL($this->username).'","'.pSQL($this->domain).'",0)');

	}
	
	
	private function showImports()
	{
		$this->q = Db::getInstance()->Execute(' SELECT * FROM `'._DB_PREFIX_.'imin_xmlimport` ');
		$this->_html .= '
		<fieldset>
				<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Import modules').'</legend>
				<h4>'.$this->l('Options').':</h4>
				';
		
		while($this->import = mysql_fetch_array($this->q))
		{
		
		$this->_html .= '
		
		<form style="float:left; margin: 3px;" action="'.$_SERVER['REQUEST_URI'].'" method="post">
		<input type="hidden" name="importid" value="'.$this->import['id'].'" />
		<input type="submit" name="'.$this->name.'update" value="'.$this->import['code_name'].'" class="button" />
		</form>';
		}

		$this->_html .= '</fieldset> <br/>';
		
	}

	

	public function getContent()
	{
		$this->_html .= '<h2>'.$this->displayName.'</h2>';
		
		
		
		if (Tools::isSubmit($this->name.'update_import'))
		{						
			if (!sizeof($this->_postErrors))
			{
				$this->updateImport();
				
			}
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'.$err.'</div>';
		}

		
		if (Tools::isSubmit($this->name.'add_new_import'))
		{						
			if (!sizeof($this->_postErrors))
			{
				$this->addNewImport();
				
			}
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'.$err.'</div>';
		}
		
		if (Tools::isSubmit($this->name.'update'))
		{						
			if (!sizeof($this->_postErrors))
			{
				$this->updateValues();
				
			}
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'.$err.'</div>';
		}
		
		$this->newImport();	
		$this->showImports();
		
		$this->_help();
		
		return $this->_html;
	}
	
	
	private function updateValues()
	{
		$idimport=$_POST['importid'];
		$this->q = Db::getInstance()->Execute(' SELECT * FROM `'._DB_PREFIX_.'imin_xmlimport` WHERE id = '.(int)$idimport.'' );
		$this->import=mysql_fetch_array($this->q);
		
		$this->_html .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>	
				<legend><img src="../img/admin/cog.gif" alt="" class="middle" />'.$this->l('Settings').'</legend>
				
				<label>'.$this->l('Current XML module:').'</label>
				<div class="margin-form">
				<h3>'.$this->import['code_name'].'</h3>
				</div>
				<input type="hidden" name="code_name" value="'.$this->import['codename'].'" />
				
				<input type="hidden" name="id_import" value="'.$idimport.'" />
				</br>
				<label>'.$this->l('Link to XML').'</label>
				<div class="margin-form">
					<input type="text" name="source" style="width: 300px;" value="'.$this->import['source'].'" />
					<p class="clear">'.$this->l('WARNING!: You must include http:// (example: http://get2shop.com/xml/heureka.xml) ').'</p>
				</div>
				</br>
				<label>'.$this->l('Security Key').'</label>
				<div class="margin-form">
					<input type="text" name="security_key" style="width: 300px;" value="'.$this->import['security_key'].'" />
					<p class="clear">'.$this->l('Your own key').'</p>
				</div>
				</br>
				<label>'.$this->l('ID product codename?').'</label>
				<div class="margin-form">
					<input type="text" name="unique_field" style="width: 200px;" value="'.$this->import['unique_field'].'" />
					<p class="clear">'.$this->l('Unique column in XML.').'</p>
				</div>
				</br>
				<label>'.$this->l('Language: ').'</label>
				<div class="margin-form">
					<input type="text" name="language" style="width: 30px;" value="'.$this->import['language'].'" />
					<p class="clear">'.$this->l('For english: en.').'</p>
				</div>
				</br>
				<label>'.$this->l('Displayed text when in-stock: ').'</label>
				<div class="margin-form">
					<input type="text" name="available_now" style="width: 100px;" value="'.$this->import['available_now'].'" />
				</div>
				</br>
				<label>'.$this->l('Displayed text when allowed to be back-ordered: ').'</label>
				<div class="margin-form">
					<input type="text" name="available_later" style="width: 100px;" value="'.$this->import['available_later'].'" />
				</div>
				</br>
				<label>'.$this->l('If its delivery date longer, then this number. Product its not in stock.: ').'</label>
				<div class="margin-form">
					<input type="text" name="delivery" style="width: 30px;" value="'.$this->import['delivery'].'" />
					<p class="clear">'.$this->l('In days.').'</p>
				</div>
				</br>
				<label>'.$this->l('Import categories?').'</label>
				<div class="margin-form">	
					<input type="radio" name="categories" value="1" '.($this->import['categories'] == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="categories" value="0" '.($this->import['categories'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Symbol to separate categories.').'</label>
				<div class="margin-form">
					<input type="text" name="symbol" style="width: 50px;" value="'.$this->import['symbol'].'" />
					<p class="clear">'.$this->l('Default: "|" ').'</p>
				</div>
				<label>'.$this->l('Change product price?').'</label>
				<div class="margin-form">	
					<input type="radio" name="price_change" value="2" '.($this->import['price_change'] == '2' ? 'checked="checked" ' : '').'/>'.$this->l('Yes - Raise').'
					<input type="radio" name="price_change" value="1" '.($this->import['price_change'] == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes - Decrease').'
					<input type="radio" name="price_change" value="0" '.($this->import['price_change'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<br/>
				<label>'.$this->l('If yes, how much percent?').'</label>
				<div class="margin-form">
					<input type="text" name="price_value" style="width: 50px;" value="'.$this->import['price_value'].'" />
				</div>
				<label>'.$this->l('Import description?').'</label>
				<div class="margin-form">	
					<input type="radio" name="description" value="1" '.($this->import['description']  == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="description" value="0" '.($this->import['description']  == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Import images?').'</label>
				<div class="margin-form">	
					<input type="radio" name="images" value="1" '.($this->import['images']  == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="images" value="0" '.($this->import['images']  == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Import manufacturer?').'</label>
				<div class="margin-form">	
					<input type="radio" name="manufacturer" value="1" '.($this->import['manufacturer']  == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="manufacturer" value="0" '.($this->import['manufacturer'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Import parameters?').'</label>
				<div class="margin-form">	
					<input type="radio" name="parameters" value="1" '.($this->import['parameters'] == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="parameters" value="0" '.($this->import['parameters'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Update stock?').'</label>
				<div class="margin-form">	
					<input type="radio" name="update_quantity" value="1" '.($this->import['update_quantity'] == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="update_quantity" value="0" '.($this->import['update_quantity'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Update prices?').'</label>
				<div class="margin-form">	
					<input type="radio" name="update_price" value="1" '.($this->import['update_price'] == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="update_price" value="0" '.($this->import['update_price'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<label>'.$this->l('Deactivate products when are not in XML?').'</label>
				<div class="margin-form">	
					<input type="radio" name="deactivate" value="1" '.($this->import['deactivate'] == '1' ? 'checked="checked" ' : '').'/>'.$this->l('Yes').'
					<input type="radio" name="deactivate" value="0" '.($this->import['deactivate'] == '0' ? 'checked="checked" ' : '').'/>'.$this->l('No').'
				</div>
				<center><input type="submit" name="'.$this->name.'update_import" value="'.$this->l('Update').'" class="button" /></center>			
			</fieldset>
		</form>
		<br/>';
	
	}
	
	
	


	private function _help(){
	
	$this->_html .= '
		<fieldset class="space">
			<legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Help').'</legend>
			 <h1>'.$this->l('Instructions for use !!!!! - If everything is set, you must follow the instructions').'</h1>
			 <p>'.$this->l('You must open this page and set cron jobs on it').'</p>-------<br /><p> ';
			 
		$this->q = Db::getInstance()->Execute(' SELECT * FROM `'._DB_PREFIX_.'imin_xmlimport` ');	 
		while($this->import = mysql_fetch_array($this->q))
		{
		
		$this->_html .= '
		
		http://'.$_SERVER["HTTP_HOST"].''.__PS_BASE_URI__.'modules/xmlimport/'.$this->import['code_name'].'.php?security='.$this->import['security_key'].'<br />
		
		';
		
		}
			 
			$this->_html .= ' -------</p></strong><br />
			<h2>'.$this->l('We offer web hosting for Prestashop with individual settings').'.</h2><br />
			<h3>'.$this->l('For more informations contact us on').': info@get2shop.com.</h3>
			<h3>'.$this->l('Modules and themes for download here').': <a href="http://iminshop.com<">iminshop.com</a> '.$this->l('and support for your business').': <a href="http://get2shop.com">get2shop.com</a></h3>
				
		</fieldset>';
	
	}
				
}