<?php

class ThemeHouse_Objects_Extend_XenForo_Model_AddOn extends XFCP_ThemeHouse_Objects_Extend_XenForo_Model_AddOn
{
	/**
	 * @param array $addOn Add-on info
	 *
	 * @return DOMDocument
	 */
	public function getAddOnXml(array $addOn)
	{
		/* @var $document DOMDocument */
		$document = parent::getAddOnXml($addOn);

		$rootNode = $document->getElementsByTagName('addon')->item(0);
		$addOnId = $rootNode->attributes->getNamedItem('addon_id')->textContent;

		$dataNode = $document->createElement('classes');
		$this->getModelFromCache('ThemeHouse_Objects_Model_Class')->appendClassesAddOnXml($dataNode, $addOnId);
		if ($dataNode->hasChildNodes()) {
		    $refNode = null;
		    foreach ($rootNode->childNodes as $child) {
		        if ($child instanceof DOMElement && $child->tagName > 'classes') {
		            $refNode = $child;
		            break;
		        }
		    }
		    if ($refNode) {
		        $rootNode->insertBefore($dataNode, $refNode);
		    } else {
		        $rootNode->appendChild($dataNode);
		    }
		}

		return $document;
	}

	/**
	 * @param SimpleXMLElement $xml Root node that contains all of the "data" nodes below
	 * @param string $addOnId Add-on to import for
	 */
	public function importAddOnExtraDataFromXml(SimpleXMLElement $xml, $addOnId)
	{
		parent::importAddOnExtraDataFromXml($xml, $addOnId);

		$this->getModelFromCache('ThemeHouse_Objects_Model_Class')->importClassesAddOnXml($xml->classes, $addOnId);
	}
}