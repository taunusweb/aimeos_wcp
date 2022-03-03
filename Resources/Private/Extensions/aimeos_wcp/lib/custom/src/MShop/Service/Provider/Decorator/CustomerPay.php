<?php

/**
 * @copyright Aimeos GmbH, 2020
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator to check if payment is available for customer.
 *
 * @package MShop
 * @subpackage Service
 */
class CustomerPay
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'customerpay.payment' => array(
			'code' => 'customerpay.payment',
			'internalcode' => 'customerpay.payment',
			'label' => 'Required payment',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => true,
		),
	);


	public function checkConfigBE( array $attributes ) : array
	{
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	public function getConfigBE() : array
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		$context = $this->getContext();

		if( $context->getUserId() )
		{
			$item = \Aimeos\MShop::create( $context, 'customer' )->get( $context->getUserId(), ['customer/property'] );
			$option = current($item->getProperties( 'payment' ));

			if( $option && $option === $this->getConfigValue( 'customerpay.payment' ) ) {
				return false;
			}
		}

		return $this->getProvider()->isAvailable( $basket );
	}
}
