<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 * @package MShop
 * @subpackage Service
 */


namespace Aimeos\MShop\Service\Provider\Delivery;


/**
 * XML delivery provider implementation
 *
 * @package MShop
 * @subpackage Service
 */
class XmlWeber
	extends \Aimeos\MShop\Service\Provider\Delivery\Base
	implements \Aimeos\MShop\Service\Provider\Delivery\Iface
{
	private $num = 0;

	private $beConfig = [
		'xml.backupdir' => [
			'code' => 'xml.backupdir',
			'internalcode' => 'xml.backupdir',
			'label' => 'Relative or absolute path of the backup directory (with strftime() placeholders)',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		],
		'xml.exportpath' => [
			'code' => 'xml.exportpath',
			'internalcode' => 'xml.exportpath',
			'label' => 'Relative or absolute path and name of the XML files (with strftime() placeholders)',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => './order_%Y-%m-%d_%T_%%d.xml',
			'required' => true,
		],
		'xml.template' => [
			'code' => 'xml.template',
			'internalcode' => 'xml.template',
			'label' => 'Relative path of the template file name',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => 'service/provider/delivery/xml-body-standard',
			'required' => false,
		],
		'xml.updatedir' => [
			'code' => 'xml.updatedir',
			'internalcode' => 'xml.updatedir',
			'label' => 'Relative or absolute path and name of the order update XML files',
			'type' => 'string',
			'internaltype' => 'string',
			'default' => '',
			'required' => false,
		],
	];


	/**
	 * Checks the backend configuration attributes for validity
	 *
	 * @param array $attributes Attributes added by the shop owner in the administraton interface
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid
	 */
	public function checkConfigBE( array $attributes ) : array
	{
		$errors = parent::checkConfigBE( $attributes );

		return array_merge( $errors, $this->checkConfig( $this->beConfig, $attributes ) );
	}


	/**
	 * Returns the configuration attribute definitions of the provider to generate a list of available fields and
	 * rules for the value of each field in the administration interface.
	 *
	 * @return array List of attribute definitions implementing \Aimeos\MW\Common\Critera\Attribute\Iface
	 */
	public function getConfigBE() : array
	{
		return $this->getConfigItems( $this->beConfig );
	}


	/**
	 * Creates the XML files and updates the delivery status
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $order Order instance
	 * @return \Aimeos\MShop\Order\Item\Iface Updated order item
	 */
	public function process( \Aimeos\MShop\Order\Item\Iface $order ) : \Aimeos\MShop\Order\Item\Iface
	{
		$customerItems = [];
		$baseItem = $this->getOrderBase( $order->getBaseId(), \Aimeos\MShop\Order\Item\Base\Base::PARTS_ALL );

		if( $baseItem->getCustomerId() )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), 'customer' );
			$search = $manager->filter()->slice( 0, 1 );
			$search->setConditions( $search->compare( '==', 'customer.id', $baseItem->getCustomerId() ) );
			$customerItems = $manager->search( $search );
		}

		$this->createFile( $this->createXml( [$order], [$baseItem->getId() => $baseItem], $customerItems ) );

		return $order->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
	}


	/**
	 * Sends the details of all orders to the ERP system for further processing
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orders List of order invoice objects
	 * @return \Aimeos\MShop\Order\Item\Iface[] Updated order items
	 */
	public function processBatch( iterable $orders ) : \Aimeos\Map
	{
		$customerItems = [];
		$baseItems = $this->getOrderBaseItems( $orders );

		$custIds = [];
		foreach( $baseItems as $baseItem ) {
			$custIds[] = $baseItem->getCustomerId();
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'customer' );
		$search = $manager->filter()->slice( 0, count( $orders ) );
		$search->setConditions( $search->compare( '==', 'customer.id', $custIds ) );
		$customerItems = $manager->search( $search );

		$this->createFile( $this->createXml( $orders, $baseItems, $customerItems ) );

		foreach( $orders as $key => $order ) {
			$orders[$key] = $order->setDeliveryStatus( \Aimeos\MShop\Order\Item\Base::STAT_PROGRESS );
		}

		return map( $orders );
	}


	/**
	 * Looks for new update files and updates the orders for which status updates were received.
	 * If batch processing of files isn't supported, this method can be empty.
	 *
	 * @return boolean True if the update was successful, false if async updates are not supported
	 * @throws \Aimeos\MShop\Service\Exception If updating one of the orders failed
	 */
	public function updateAsync() : bool
	{
		$context = $this->getContext();
		$logger = $context->getLogger();
		$location = $this->getConfigValue( 'xml.updatedir' );

		if( $location === '' || !file_exists( $location ) )
		{
			$msg = sprintf( 'File or directory "%1$s" doesn\'t exist', $location );
			throw new \Aimeos\Controller\Jobs\Exception( $msg );
		}

		$logger->log( sprintf( 'Started order status import from "%1$s"', $location ), \Aimeos\MW\Logger\Base::INFO );

		$files = [];

		if( is_dir( $location ) )
		{
			foreach( new \DirectoryIterator( $location ) as $entry )
			{
				if( strncmp( $entry->getFilename(), 'order', 5 ) === 0 && $entry->getExtension() === 'xml' ) {
					$files[] = $entry->getPathname();
				}
			}
		}
		else
		{
			$files[] = $location;
		}

		sort( $files );

		foreach( $files as $filepath ) {
			$this->importFile( $filepath );
		}

		$logger->log( sprintf( 'Finished order status import from "%1$s"', $location ), \Aimeos\MW\Logger\Base::INFO );

		return true;
	}


	/**
	 * Stores the content into the file
	 *
	 * @param string $content XML content
	 */
	protected function createFile( string $content )
	{
		$filepath = $this->getConfigValue( 'xml.exportpath', './order_%Y-%m-%d_%T_%%d.xml' );
		$filepath = sprintf( strftime( $filepath ), $this->num++ );

		if( file_put_contents( $filepath, $content ) === false )
		{
			$msg = sprintf( 'Unable to create order XML file "%1$s"', $filepath );
			throw new \Aimeos\MShop\Service\Exception( $msg );
		}
	}


	/**
	 * Creates the XML file for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items to export
	 * @param \Aimeos\MShop\Order\Item\Base\Iface[] $baseItems Associative list of order base items to export
	 * @param \Aimeos\MShop\Customer\Item\Iface[] $customerItems List of customer items who placed the orders
	 * @return string Generated XML
	 */
	protected function createXml( iterable $orderItems, iterable $baseItems, iterable $customerItems ) : string
	{
		$view = $this->getContext()->getView();
		$template = $this->getConfigValue( 'xml.template', 'service/provider/delivery/xml-body-standard' );

		return $view->assign( [
			'baseItems' => $baseItems,
			'orderItems' => $orderItems,
			'customerItems' => $customerItems
		] )->render( $template );
	}


	/**
	 * Returns the order base items for the given orders
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface[] $orderItems List of order items
	 * @return \Aimeos\Map Associative list of IDs as keys and order base items as values
	 */
	protected function getOrderBaseItems( array $orderItems ) : \Aimeos\Map
	{
		$ids = [];
		$ref = ['order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'];

		foreach( $orderItems as $item ) {
			$ids[$item->getBaseId()] = null;
		}

		$manager = \Aimeos\MShop::create( $this->getContext(), 'order/base' );
		$search = $manager->filter()->slice( 0, count( $ids ) );
		$search->setConditions( $search->compare( '==', 'order.base.id', array_keys( $ids ) ) );

		return $manager->search( $search, $ref );
	}


	/**
	 * Imports all orders from the given XML file name
	 *
	 * @param string $filename Relative or absolute path to the XML file
	 */
	protected function importFile( string $filename )
	{
		$nodes = [];
		$xml = new \XMLReader();
		$logger = $this->getContext()->getLogger();

		if( $xml->open( $filename, LIBXML_COMPACT | LIBXML_PARSEHUGE ) === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'No XML file "%1$s" found', $filename ) );
		}

		$logger->log( sprintf( 'Started order status import from file "%1$s"', $filename ), \Aimeos\MW\Logger\Base::INFO );

		while( $xml->read() === true )
		{
			if( $xml->depth === 1 && $xml->nodeType === \XMLReader::ELEMENT && $xml->name === 'orderitem' )
			{
				if( ( $dom = $xml->expand() ) === false )
				{
					$msg = sprintf( 'Expanding "%1$s" node failed', 'orderitem' );
					throw new \Aimeos\Controller\Jobs\Exception( $msg );
				}

				if( ( $attr = $dom->attributes->getNamedItem( 'ref' ) ) !== null ) {
					$nodes[$attr->nodeValue] = $dom;
				}
			}
		}

		$this->importNodes( $nodes );
		unset( $nodes );

		$logger->log( sprintf( 'Finished order status import from file "%1$s"', $filename ), \Aimeos\MW\Logger\Base::INFO );

		$backup = $this->getConfigValue( 'xml.backupdir' );

		if( !empty( $backup ) && @rename( $filename, strftime( $backup ) ) === false )
		{
			$msg = sprintf( 'Unable to move imported file "%1$s" to "%2$s"', $filename, strftime( $backup ) );
			throw new \Aimeos\Controller\Jobs\Exception( $msg );
		}
	}


	/**
	 * Imports the orders from the given XML nodes
	 *
	 * @param \DomElement[] List of order DOM nodes
	 */
	protected function importNodes( array $nodes )
	{
		$manager = \Aimeos\MShop::create( $this->getContext(), 'order' );
		$search = $manager->filter()->slice( 0, count( $nodes ) );
		$search->setConditions( $search->compare( '==', 'order.id', array_keys( $nodes ) ) );
		$items = $manager->search( $search );

		foreach( $nodes as $node )
		{
			$list = [];

			foreach( $node->childNodes as $childNode ) {
				$list[$childNode->nodeName] = $childNode->nodeValue;
			}

			if( ( $attr = $node->attributes->getNamedItem( 'ref' ) ) !== null && isset( $items[$attr->nodeValue] ) ) {
				$items[$attr->nodeValue]->fromArray( $list );
			}
		}

		$manager->save( $items );
	}
}
