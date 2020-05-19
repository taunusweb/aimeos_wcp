<?php

/**
 * @copyright Aimeos GmbH, 2020
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Decorator;


/**
 * Decorator for service providers adding bulk packet costs.
 *
 * @package MShop
 * @subpackage Service
 */
class Bulky
	extends \Aimeos\MShop\Service\Provider\Decorator\Base
	implements \Aimeos\MShop\Service\Provider\Decorator\Iface
{
	private $beConfig = array(
		'bulky.amount' => array(
			'code' => 'bulky.amount',
			'internalcode' => 'bulky.amount',
			'label' => 'Bulk: Decimal amount value',
			'type' => 'number',
			'internaltype' => 'float',
			'default' => 0,
			'required' => true,
		),
	);


	public function checkConfigBE( array $attributes )
	{
		$error = $this->getProvider()->checkConfigBE( $attributes );
		$error += $this->checkConfig( $this->beConfig, $attributes );

		return $error;
	}


	public function getConfigBE()
	{
		return array_merge( $this->getProvider()->getConfigBE(), $this->getConfigItems( $this->beConfig ) );
	}


	public function calcPrice( \Aimeos\MShop\Order\Item\Base\Iface $basket )
	{
		$config = $this->getServiceItem()->getConfig();

		if( !isset( $config['bulky.amount'] ) ) {
			throw new \Aimeos\MShop\Service\Exception( sprintf( 'Missing configuration "%1$s"', 'bulky.amount' ) );
		}

		$prodIds = [];
		foreach( $basket->getProducts() as $orderProduct ) {
			$prodIds[] = $orderProduct->getProductId();
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'product' );
		$search = $manager->createSearch()->setSlice( 0 , 10000 );
		$search->setConditions( $search->combine( '&&', [
			$search->compare( '==', 'product.id', $prodIds ),
			$search->compare( '!=', $search->createFunction( 'product:prop', ['shipping', 'bulky'] ), null )
		] ) );
		$products = $manager->searchItems( $search );

		$qty = 0;
		foreach( $basket->getProducts() as $orderProduct )
		{
			if( isset( $products[$orderProduct->getProductId()] ) ) {
				$qty += $orderProduct->getQuantity();
			}
		}

		$price = $this->getProvider()->calcPrice( $basket );
		return $price->setCosts( $price->getCosts() + $config['bulky.amount'] * $qty );
	}
}
