<?php

/**
 * @copyright Aimeos GmbH, 2020
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator to check if carrier transport is necessary.
 *
 * @package MShop
 * @subpackage Service
 */
class Carrier
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	public function isAvailable( \Aimeos\MShop\Order\Item\Base\Iface $basket ) : bool
	{
		$prodIds = [];
		foreach( $basket->getProducts() as $orderProduct ) {
			$prodIds[] = $orderProduct->getProductId();
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'product' );
		$search = $manager->filter()->slice( 0 , 1 );
		$search->setConditions( $search->combine( '&&', [
			$search->compare( '==', 'product.id', $prodIds ),
			$search->compare( '!=', $search->createFunction( 'product:prop', ['shipping', null, 'carrier'] ), null )
		] ) );

		return count( $manager->searchItems( $search ) ) ? $this->getProvider()->isAvailable( $basket ) : false;
	}
}
