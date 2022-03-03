<?php

namespace Aimeos\Controller\Frontend\Basket;


/**
 * Weber implementation of the basket frontend controller.
 *
 * @package Controller
 * @subpackage Frontend
 */
class Weber extends Standard
{
	/**
	 * Adds an address of the customer to the basket
	 *
	 * @param string $type Address type code like 'payment' or 'delivery'
	 * @param array $values Associative list of key/value pairs with address details
	 * @return \Aimeos\Controller\Frontend\Basket\Iface Basket frontend object for fluent interface
	 */
	public function addAddress( string $type, array $values = [], int $position = null ) : \Aimeos\Controller\Frontend\Basket\Iface
	{
		foreach( $values as $key => $value )
		{
			if( is_string( $value ) ) {
				$values[$key] = strip_tags( $value ); // prevent XSS
			}
		}

		$context = $this->getContext();
		$address = \Aimeos\MShop::create( $context, 'order/base/address' )->create()->fromArray( $values );
		$address->set( 'nostore', ( $values['nostore'] ?? false ) ? true : false );

		$this->get()->addAddress( $address, $type, $position );
		return $this->save();
	}


	/**
	 * Adds the delivery/payment service including the given configuration
	 *
	 * @param \Aimeos\MShop\Service\Item\Iface $service Service item selected by the customer
	 * @param array $config Associative list of key/value pairs with the options selected by the customer
	 * @param integer|null $position Position of the address in the list to overwrite
	 * @return \Aimeos\Controller\Frontend\Basket\Iface Basket frontend object for fluent interface
	 * @throws \Aimeos\Controller\Frontend\Basket\Exception If given service attributes are invalid
	 */
	public function addService( \Aimeos\MShop\Service\Item\Iface $service, array $config = [], int $position = null ) : \Aimeos\Controller\Frontend\Basket\Iface
	{
		$context = $this->getContext();
		$manager = \Aimeos\MShop::create( $context, 'service' );

		$provider = $manager->getProvider( $service, $service->getType() );
		$errors = $provider->checkConfigFE( $config );
		$unknown = array_diff_key( $config, $errors );

		if( count( $unknown ) > 0 )
		{
			$msg = $context->getI18n()->dt( 'controller/frontend', 'Unknown service attributes' );
			throw new \Aimeos\Controller\Frontend\Basket\Exception( $msg, -1, null, $unknown );
		}

		if( count( array_filter( $errors ) ) > 0 )
		{
			$msg = $context->getI18n()->dt( 'controller/frontend', 'Invalid service attributes' );
			throw new \Aimeos\Controller\Frontend\Basket\Exception( $msg, -1, null, array_filter( $errors ) );
		}

		// remove service rebate of original price
		$price = $provider->calcPrice( $this->get(), $config )->setRebate( '0.00' );

		$orderBaseServiceManager = \Aimeos\MShop::create( $context, 'order/base/service' );

		$orderServiceItem = $orderBaseServiceManager->create()->copyFrom( $service )->setPrice( $price );
		$orderServiceItem = $provider->setConfigFE( $orderServiceItem, $config );

		$this->baskets[$this->type] = $this->get()->addService( $orderServiceItem, $service->getType(), $position );
		return $this->save();
	}
}