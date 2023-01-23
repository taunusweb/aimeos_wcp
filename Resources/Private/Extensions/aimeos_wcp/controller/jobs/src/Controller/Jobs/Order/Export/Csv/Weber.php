<?php

namespace Aimeos\Controller\Jobs\Order\Export\Csv;

use \Aimeos\MW\Logger\Base as Log;


/**
 * Job controller for Weber order exports.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Weber
	extends \Aimeos\Controller\Jobs\Base
	implements \Aimeos\Controller\Jobs\Iface
{
	private static $num = 0;


	/**
	 * Returns the localized name of the job.
	 *
	 * @return string Name of the job
	 */
	public function getName() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Order export Weber' );
	}


	/**
	 * Returns the localized description of the job.
	 *
	 * @return string Description of the job
	 */
	public function getDescription() : string
	{
		return $this->getContext()->translate( 'controller/jobs', 'Exports Weber orders to CSV file' );
	}


	/**
	 * Executes the job.
	 *
	 * @throws \Aimeos\Controller\Jobs\Exception If an error occurs
	 */
	public function run()
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$logger = $context->getLogger();

		$mq = $context->getMessageQueueManager()->get( 'mq-admin' )->getQueue( 'order-export' );
        $processed = [];

		while( $msg = $mq->get() )
		{
			try
			{
				$body = $msg->getBody();
				$hash = md5( $body );

				if( !isset( $processed[$hash] ) )
				{
					$processed[$hash] = true;

					if( ( $data = json_decode( $body, true ) ) === null ) {
						throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid message: %1$s', $body ) );
					}

					$this->export( $data );
				}
			}
			catch( \Exception $e )
			{
				$str = 'Order export error: ' . $e->getMessage() . "\n" . $e->getTraceAsString();
				$logger->log( $str, Log::ERR, 'order/export/csv' );
			}

			$mq->del( $msg );
		}
	}


	/**
	 * Creates a new job entry for the exported file
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item
	 * @param string $path Absolute path to the exported file
	 */
	protected function addJob( \Aimeos\MShop\Context\Item\Iface $context, string $path )
	{
		$manager = \Aimeos\MAdmin::create( $context, 'job' );
		$item = $manager->create()->setPath( $path )->setLabel( $path );
		$manager->save( $item, false );
	}


	/**
	 * Exports the orders and returns the exported file name
	 *
	 * @param array $msg Message data passed from the frontend
	 */
	protected function export( array $msg )
	{
		$lcontext = $this->getLocaleContext( $msg );

		if( ( $fh = \tmpfile() ) === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( 'Unable to create temporary file' );
		}

		$manager = \Aimeos\MShop::create( $lcontext, 'order' );
		$ref = ['customer', 'order/base', 'order/base/address', 'order/base/coupon', 'order/base/product', 'order/base/service'];

		$search = $this->initCriteria( $manager->filter( false, true ), $msg );
		$search->setSortations( array_merge( $search->getSortations(), [$search->sort( '+', 'order.id' )] ) );

        $view = $lcontext->view();
		$start = 0;

		do
		{
			$search->slice( $start, 100 );
			$items = $manager->search( $search, $ref );
            $view->orders = $items;

            if( fwrite( $fh, $view->render( 'order/export/csv/weber' ) ) === false ) {
				throw new \Aimeos\Controller\Jobs\Exception( 'Unable to write to temporary file' );
			}

			$count = count( $items );
			$start += $count;
		}
		while( $count === $search->getLimit() );

		$path = $this->moveFile( $lcontext, $fh );
		$this->addJob( $lcontext, $path );

		fclose( $fh );
	}


	/**
	 * Returns a new context including the locale from the message data
	 *
	 * @param array $msg Message data including a "sitecode" value
	 * @return \Aimeos\MShop\Context\Item\Iface New context item with updated locale
	 */
	protected function getLocaleContext( array $msg ) : \Aimeos\MShop\Context\Item\Iface
	{
		$lcontext = clone $this->getContext();
		$manager = \Aimeos\MShop::create( $lcontext, 'locale' );

		$sitecode = ( isset( $msg['sitecode'] ) ? $msg['sitecode'] : 'default' );
		$localeItem = $manager->bootstrap( $sitecode, '', '', false, \Aimeos\MShop\Locale\Manager\Base::SITE_PATH );

		return $lcontext->setLocale( $localeItem );
	}


	/**
	 * Initializes the search criteria
	 *
	 * @param \Aimeos\MW\Criteria\Iface $criteria New criteria object
	 * @param array $msg Message data
	 * @return \Aimeos\MW\Criteria\Iface Initialized criteria object
	 */
	protected function initCriteria( \Aimeos\MW\Criteria\Iface $criteria, array $msg ) : \Aimeos\MW\Criteria\Iface
	{
		return $criteria->add( $criteria->parse( $msg['filter'] ?? [] ) )->order( $msg['sort'] ?? [] );
	}


	/**
	 * Moves the exported file to the final storage
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context item
	 * @param resource $fh File handle of temporary file
	 * @return string Relative path of the file in the storage
	 */
	protected function moveFile( \Aimeos\MShop\Context\Item\Iface $context, $fh ) : string
	{
		$filename = 'order_export-' . date( 'Ymd_His' ) . '-' . self::$num++;
		$context->getFileSystemManager()->get( 'fs-admin' )->writes( $filename, $fh );

		return $filename;
	}
}