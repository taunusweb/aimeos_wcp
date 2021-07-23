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
class Appointment
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'appointment.price' => array(
			'code' => 'appointment.price',
			'internalcode' => 'appointment.price',
			'label' => 'Preis Terminvereinbarung',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => 0,
			'required' => true,
		),
	);

	private $feConfig = array(
		'appointment.option' => array(
			'code' => 'appointment.option',
			'internalcode' => 'appointment',
			'label' => 'Terminvereinbarung',
			'type' => 'boolean',
			'internaltype' => 'boolean',
			'default' => '0',
			'required' => false
		),
	);


	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$price = $this->getProvider()->calcPrice( $basket );
		$args = func_get_args(); // additional config parameter

		if( isset( $args[1] ) && is_array( $args[1] ) && isset( $args[1]['appointment.option'] ) && $args[1]['appointment.option'] == 1 ) {
			return $price->setCosts( $price->getCosts() + $this->getConfigValue( 'appointment.price', 0 ) );
		}

		return $price;
	}


	public function checkConfigBE( array $attributes )
	{
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	public function checkConfigFE( array $attributes ) : array
	{
		$result = $this->getProvider()->checkConfigFE( $attributes );
		return array_merge( $result, $this->checkConfig( $this->feConfig, $attributes ) );
	}


	public function getConfigBE()
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	public function getConfigFE( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : array
	{
		$feconfig = $this->feConfig;

		try
		{
			$type = \Aimeos\MShop\Order\Item\Base\Service\Base::TYPE_DELIVERY;
			$service = $this->getBasketService( $basket, $type, $this->getServiceItem()->getCode() );

			if( ( $value = $service->getAttribute( 'appointment.option', 'delivery' ) ) != '' ) {
				$feconfig['appointment.option']['default'] = $value;
			}
		}
		catch( \Aimeos\MShop\Service\Exception $e ) {} // If service isn't available

		return array_merge( $this->getProvider()->getConfigFE( $basket ), $this->getConfigItems( $feconfig ) );
	}
}
