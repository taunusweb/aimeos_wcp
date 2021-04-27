<?php

/**
 * @copyright Aimeos GmbH, 2021
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for neutral delivery
 *
 * @package MShop
 * @subpackage Service
 */
class Neutral
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $feConfig = array(
		'neutral.option' => array(
			'code' => 'neutral.option',
			'internalcode' => 'neutral',
			'label' => 'Neutralversand',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '0',
			'required' => false
		),
	);


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the frontend.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Iface $basket Basket object
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : array
	{
		return array_merge( $this->getProvider()->getConfigFE( $basket ), $this->getConfigItems( $feconfig ) );
	}


	/**
	 * Checks the frontend configuration attributes for validity.
	 *
	 * @param array $attributes Attributes entered by the customer during the checkout process
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function checkConfigFE( array $attributes ) : array
	{
		$result = $this->getProvider()->checkConfigFE( $attributes );
		return array_merge( $result, $this->checkConfig( $this->feConfig, $attributes ) );
	}
}