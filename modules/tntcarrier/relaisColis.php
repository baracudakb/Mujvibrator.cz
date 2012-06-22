<?php
require('../../config/config.inc.php');
$relais = Db::getInstance()->getValue('SELECT c.id_carrier 
													FROM `'._DB_PREFIX_.'carrier` as c, `'._DB_PREFIX_.'tnt_carrier_option` as o 
													WHERE c.id_carrier = o.id_carrier 
													AND o.option LIKE "%D%" 
													AND c.external_module_name = "tntcarrier"
													AND c.deleted = "0" AND c.id_carrier = "'.(int)($_GET['id_carrier']).'"');

function phoneForm($mob, $tel)
{
	if ($tel != '06' && $mob != '06' && $tel != '07' && $mob != '07')
	{
		echo 'Afin d\'ameliorer les conditions de livraison, veuillez renseigner votre numero de mobile<br/>';
		echo 'Numero de telephone mobile : <input type="mobile" name="mobileTnt" id="mobileTnt" onblur="postMobile(\''.Configuration::get('TNT_CARRIER_TOKEN').'\')"/>
		<input type="hidden" id="id_cart" value="'.Tools::safeOutput($_GET['idcart']).'"/>';
		return true;
	}
	else
		return false;
}
													
$postcode = '';
if (isset($_GET['idcart']))
{
	$cartId = htmlentities($_GET['idcart']);
	$cart = new Cart($cartId);
	$address = new Address($cart->id_address_delivery);
	$postcode = $address->postcode;
}

$mob = substr($address->phone_mobile, 0, 2);
$tel = substr($address->phone, 0, 2);

if ($relais)
{
phoneForm($mob, $tel);
?>
<input id="tntRCSelectedCode" type="hidden" value="">
<input id="tntRCSelectedNom" type="hidden" value="">
<input id="tntRCSelectedAdresse" type="hidden" value="">
<input id="tntRCSelectedCodePostal" type="hidden" value="">
<input id="tntRCSelectedCommune" type="hidden" value="">
<h3>Choisissez le Relais Colis<sup class="tntRCSup">&reg;</sup>qui vous convient :</h3>
	<div><label>Entrez le code postal : </label><input id="tntRCInputCP" class="tntRCInput" type="text" value="<?php echo $postcode;?>" size="5" maxlength="5"> <button type="button" class="button" onclick="tntRCgetCommunes();">Ok</button></div><br/>
<div id="relaisColisResponse"></div>
<div id="map_canvas" class="exemplePresentation" style="margin-top:10px;width: 100%; height: 482px"></div>
<?php
}
else
{
	if (!phoneForm($mob, $tel))
		echo "none";
}

?>