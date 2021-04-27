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

		if( !empty( $services = $basket->getService( 'delivery' ) ) && ( $service = current( $services ) )
			&& $service->getAttribute( 'appointment.option', 'delivery' ) == 1
		) {
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
		return array_merge( $this->getProvider()->getConfigFE( $basket ), $this->getConfigItems( $this->feConfig ) );
	}
}
