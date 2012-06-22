<?php
class TntWebService
{
	private $_login;
	private $_password;
	private $_account;
	private $authheader;
	private $authvars;
	private $header;
	private $file;

	public	function __construct($id_shop = null)
	{
		if (_PS_VERSION_ >= 1.5)
		{
			$this->_login = Configuration::get('TNT_CARRIER_LOGIN', null, null, $id_shop);
			$this->_password = Configuration::get('TNT_CARRIER_PASSWORD', null, null, $id_shop);
			$this->_account = Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT', null, null, $id_shop);
		}
		else
		{
			$this->_login = Configuration::get('TNT_CARRIER_LOGIN');
			$this->_password = Configuration::get('TNT_CARRIER_PASSWORD');
			$this->_account = Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT');
		}

		$this->_authheader = $this->genAuth();
		$this->_authvars = new SoapVar($this->_authheader, XSD_ANYXML);
		$this->_header = new SoapHeader("http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd", "Security", $this->_authvars);
		$this->_file = "http://www.tnt.fr/service/?wsdl";
	}

	public function getSoapClient()
	{
		$soapclient = new SoapClient($this->_file, array('trace'=> 1));
		return $soapclient;
	}

	public function genAuth()
	{
		 return sprintf('
				<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">
				<wsse:UsernameToken>
				<wsse:Username>%s</wsse:Username>
				<wsse:Password>%s</wsse:Password>
				</wsse:UsernameToken>
				</wsse:Security>', htmlspecialchars($this->_login), htmlspecialchars($this->_password));
	}

	public function getCity($postal)
	{
		$soapclient = $this->getSoapClient();
		$soapclient->__setSOAPHeaders(array($this->_header));

		$cities = $soapclient->citiesGuide(array('zipCode' => $postal));
		return ($cities);
	}

	public function verifCity($postal, $city)
	{
		$soapclient = $this->getSoapClient();
		$soapclient->__setSOAPHeaders(array($this->_header));
		$cities = $soapclient->citiesGuide(array('zipCode' => $postal));

		if (!isset($cities->City))
			return false;
		if (is_array($cities->City))
		{
			foreach ($cities->City as $v)
			{
				if (Tools::strtoupper($v->name) == Tools::strtoupper($city))
					return true;
			}
		}
		else
		{
			if (Tools::strtoupper($city) == Tools::strtoupper($cities->City->name))
				return true;
		}
		return false;
	}

	public function getFaisability($dest, $postcode, $city,	$date_exp)
	{
		$service = array();
		try
		{
			foreach ($dest as $key => $val)
			{
				$service[] = $this->faisabilite($date_exp, Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE'), Configuration::get('TNT_CARRIER_SHIPPING_CITY'),
										 $postcode, $city, $val);
			}
		}
		catch (SoapFault $e)
		{
			if (strrpos($e->faultstring, "shippingDate") != false)
				$service = $this->getFaisability($dest, $postcode, $city, date("Y-m-d", strtotime($date_exp.' + 1 day')));
			else if (strrpos($e->faultstring, "(zip code / city)") === 0)
				return $e->faultstring;
			else
				return null;
		}
		return $service;
	}

	public function faisabilite($dateExpedition, $codePostalDepart, $communeDepart, $codePostalArrivee, $communeArrivee, $typeDestinataire)
	{
		$soapclient = $this->getSoapClient();
		$soapclient->__setSOAPHeaders(array($this->_header));

		$sender = array("zipCode" => $codePostalDepart, "city" => $communeDepart);
		$receiver = array("zipCode" => $codePostalArrivee, "city" => $communeArrivee, "type" => $typeDestinataire);
		$parameters = array("accountNumber" => $this->_account, "shippingDate" => $dateExpedition, "sender" => $sender, "receiver" => $receiver);
		$services = $soapclient->feasibility(array('parameters' => $parameters));
		return ($services);
	}

	public function getPackage($info)
	{
		$soapclient = $this->getSoapClient();
		$soapclient->__setSOAPHeaders(array($this->_header));
		$tntcarrier = new TntCarrier();

		$sender = array(
			'type' => "ENTERPRISE",//(Configuration::get('TNT_CARRIER_SHIPPING_COLLECT') ? "ENTERPRISE" : "DEPOT"), //ENTREPRISE OR DEPOT
			'typeId' => "",//(Configuration::get('TNT_CARRIER_SHIPPING_COLLECT') ? "" : Configuration::get('TNT_CARRIER_SHIPPING_PEX')) , // code PEX if DEPOT is ON
			'name' => Configuration::get('TNT_CARRIER_SHIPPING_COMPANY'), // raison social
			'address1' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS1'),
			'address2' => Configuration::get('TNT_CARRIER_SHIPPING_ADDRESS2'),
			'zipCode' => Configuration::get('TNT_CARRIER_SHIPPING_ZIPCODE'),
			'city' => $tntcarrier->putCityInNormeTnt(Configuration::get('TNT_CARRIER_SHIPPING_CITY')),
			'contactLastName' => Configuration::get('TNT_CARRIER_SHIPPING_LASTNAME'),
			'contactFirstName' => Configuration::get('TNT_CARRIER_SHIPPING_FIRSTNAME'),
			'emailAddress' => Configuration::get('TNT_CARRIER_SHIPPING_EMAIL'),
			'phoneNumber' => Configuration::get('TNT_CARRIER_SHIPPING_PHONE'),
			'faxNumber' => '' //may be later
		);

		if ($info[4] == null)
			$receiver = array(
				'type' => ($info[0]['company'] != '' && (strlen($info[3]['option']) == 1 || substr($info[3]['option'], 1, 1) == 'S') ? "ENTERPRISE" : 'INDIVIDUAL'), // ENTREPRISE DEPOT DROPOFFPOINT INDIVIDUAL
				'typeId' => '', // IF DEPOT => code PEX else if DROPOFFPOINT => XETT
				'name' => ($info[0]['company'] != '' ? $info[0]['company'] : ''),
				'address1' => (strlen($info[0]['address1']) > 32 ? substr($info[0]['address1'], 0, 32) : $info[0]['address1']),
				'address2' => (strlen($info[0]['address2']) > 32 ? substr($info[0]['address2'], 0, 32) : $info[0]['address2']),
				'zipCode' => $info[0]['postcode'],
				'city' => $tntcarrier->putCityInNormeTnt((strlen($info[0]['city']) > 27 ? substr($info[0]['city'], 0, 27) : $info[0]['city'])),
				'instructions' => '',
				'contactLastName' => (strlen($info[0]['lastname']) > 32 ? substr($info[0]['lastname'], 0, 32) : $info[0]['lastname']),
				'contactFirstName' => (strlen($info[0]['firstname']) > 32 ? substr($info[0]['firstname'], 0, 32) : $info[0]['firstname']),
				'emailAddress' => $info[0]['email'],
				'phoneNumber' => (isset($info[0]['phone_mobile']) && $info[0]['phone_mobile'] != '' ? $info[0]['phone_mobile'] : $info[0]['phone']),
				'accessCode' => '',
				'floorNumber' => '',
				'buildingId' => '',
				'sendNotification' => ''
			);
		else
			$receiver = array(
				'type' => 'DROPOFFPOINT', // ENTREPRISE DEPOT DROPOFFPOINT INDIVIDUAL
				'typeId' => $info[4]['code'], // IF DEPOT => code PEX else if DROPOFFPOINT => XETT
				//'name' => $info[4]['name'],
				/*'address1' => $info[4]['address'],
				'address2' => '',*/
				'zipCode' => $info[4]['zipcode'],
				'city' => $tntcarrier->putCityInNormeTnt((strlen($info[4]['city']) > 27 ? substr($info[4]['city'], 0, 27) : $info[4]['city'])),
				'instructions' => '',
				'contactLastName' => (strlen($info[0]['lastname']) > 32 ? substr($info[0]['lastname'], 0, 32) : $info[0]['lastname']),
				'contactFirstName' => (strlen($info[0]['firstname']) > 32 ? substr($info[0]['firstname'], 0, 32) : $info[0]['firstname']),
				'emailAddress' => $info[0]['email'],
				'phoneNumber' => (isset($info[0]['phone_mobile']) && $info[0]['phone_mobile'] != '' ? $info[0]['phone_mobile'] : $info[0]['phone']),
				'accessCode' => '',
				'floorNumber' => '',
				'buildingId' => '',
				'sendNotification' => ''
			);

		foreach ($info[1]['weight'] as $k => $v)
		{
			$parcelRequest[$k] = array(
				'sequenceNumber' => $k + 1, // package number, there's only one at this moment
				'customerReference' => $info[0]['id_customer'], // customer ref
				'weight' => $v,
				'insuranceAmount' => '',
				'priorityGuarantee' => '',
				'comment' => ''
			);
		}

		$parcelsRequest = array('parcelRequest' => $parcelRequest);

		$pickUpRequest = array(
			'media' => "EMAIL",
			'faxNumber' => "",
			'emailAddress' => Configuration::get('TNT_CARRIER_SHIPPING_EMAIL'),
			'notifySuccess' => "1",
			'service' => "",
			'lastName' => "",
			'firstName' => "",
			'phoneNumber' => Configuration::get('TNT_CARRIER_SHIPPING_PHONE'),
			'closingTime' => Configuration::get('TNT_CARRIER_SHIPPING_CLOSING'),
			'instructions' => ""
		);

		if (Configuration::get('TNT_CARRIER_SHIPPING_COLLECT') == 1)
		{
			$paremeters = array(
			'pickUpRequest' => $pickUpRequest,
			'shippingDate' => $info[2]['delivery_date'],
			'accountNumber' => Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'),
			'sender' => $sender,
			'receiver' => $receiver,
			'serviceCode' => $info[3]['option'],
			'quantity' => count($info[1]['weight']), //number of package; count($parcelsRequest)
			'parcelsRequest' => $parcelsRequest,
			'saturdayDelivery' => ($info[5]['saturday'] ? '1' : '0'),
			//'paybackInfo' => $paybackInfo,
			'labelFormat' => (!Configuration::get('TNT_CARRIER_PRINT_STICKER') ? "STDA4" : Configuration::get('TNT_CARRIER_PRINT_STICKER'))
			);
		}
		else
		{
			$paremeters = array(
			'shippingDate' => $info[2]['delivery_date'],
			'accountNumber' => Configuration::get('TNT_CARRIER_NUMBER_ACCOUNT'),
			'sender' => $sender,
			'receiver' => $receiver,
			'serviceCode' => $info[3]['option'],
			'quantity' => count($info[1]['weight']), //number of package; count($parcelsRequest)
			'parcelsRequest' => $parcelsRequest,
			'saturdayDelivery' => ($info[5]['saturday'] ? '1' : '0'),
			//'paybackInfo' => $paybackInfo,
			'labelFormat' => (!Configuration::get('TNT_CARRIER_PRINT_STICKER') ? "STDA4" : Configuration::get('TNT_CARRIER_PRINT_STICKER'))
			);
		}
		$package = $soapclient->expeditionCreation(array('parameters' => $paremeters));
		return $package;
	}

	public function followPackage($transport)
	{
		$soapclient = $this->getSoapClient();
		$soapclient->__setSOAPHeaders(array($this->_header));

		$reponse = $soapclient->trackingByConsignment(array('parcelNumber' => $transport));

		if (isset($reponse->Parcel) && $reponse->Parcel)
		{
			$colis = $reponse->Parcel;
			$expediteur = $colis->sender;
			$destinataire = $colis->receiver;
			$evenements = $colis->events;

			$requestDate = new DateTime($evenements->requestDate);
			$processDate = new DateTime($evenements->processDate);
			$arrivalDate = new DateTime($evenements->arrivalDate);
			$deliveryDepartureDate = new DateTime($evenements->deliveryDepartureDate);
			$deliveryDate = new DateTime($evenements->deliveryDate);
		}

		$packageParam = array(
			'number' => (isset($colis->consignmentNumber) ? $colis->consignmentNumber : ''),
			'status' => (isset($colis->shortStatus) ? $colis->shortStatus : ''),
			'account_number' => (isset($colis->accountNumber) ? $colis->accountNumber : ''),
			'service' => (isset($colis->service) ? $colis->service : ''),
			'reference' => (isset($colis->reference) ? $colis->reference : ''),
			'weight' => (isset($colis->weight) ? $colis->weight : ''),
			'expediteur_name' => (isset($expediteur->name) ? $expediteur->name : ''),
			'expediteur_addr1' => (isset($expediteur->address1) ? $expediteur->address1 : ''),
			'expediteur_addr2' => (isset($expediteur->address2) ? $expediteur->address2 : ''),
			'expediteur_zipcode' => (isset($expediteur->zipCode) ? $expediteur->zipCode : ''),
			'expediteur_city' => (isset($expediteur->city) ? $expediteur->city : ''),
			'destinataire_name' => (isset($destinataire->name) ? $destinataire->name : ''),
			'destinataire_addr1' => (isset($destinataire->address1) ? $destinataire->address1 : ''),
			'destinataire_addr2' => (isset($destinataire->address2) ? $destinataire->address2 : ''),
			'destinataire_zipcode' => (isset($destinataire->zipCode) ? $destinataire->zipCode : ''),
			'destinataire_city' => (isset($destinataire->city) ? $destinataire->city : ''),
			'request' => (isset($evenements->requestDate) ? $evenements->requestDate : ''),
			'requestDate' => (isset($requestDate) && isset($evenements->requestDate) ? $requestDate : ''),
			'process' => (isset($evenements->processDate) ? $evenements->processDate : ''),
			'process_date' => (isset($processDate) && isset($evenements->processDate) ? $processDate : ''),
			'process_center' => (isset($evenements->processCenter) ? $evenements->processCenter : ''),
			'arrival' => (isset($evenements->arrivalDepartureDate) ? $evenements->arrivalDepartureDate : ''),
			'arrival_date' => (isset($arrivalDate) ? $arrivalDate : ''),
			'arrival_center' => (isset($evenements->arrivalCenter) ? $evenements->arrivalCenter : ''),
			'delivery_departure' => (isset($evenements->deliveryDepartureDate) ? $evenements->deliveryDepartureDate : ''),
			'delivery_departure_date' => (isset($deliveryDepartureDate) ? $deliveryDepartureDate : ''),
			'delivery_departure_center' => (isset($evenements->deliveryDepartureCenter) ? $evenements->deliveryDepartureCenter : ''),
			'delivery' => (isset($evenements->deliveryDate) ? $evenements->deliveryDate : ''),
			'delivery_date' => (isset($deliveryDate) ? $deliveryDate : ''),
			'long_status' => (isset($colis->longStatus) ? $colis->longStatus : ''),
			'linkPicture' => (isset($colis->primaryPODUrl) ? $colis->primaryPODUrl : '')
			);
		return $packageParam;
	}
}
?>