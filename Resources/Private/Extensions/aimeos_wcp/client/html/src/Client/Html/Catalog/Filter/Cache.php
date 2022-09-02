<?php


namespace Aimeos\Client\Html\Catalog\Filter;


/**
 * Cache implementation of catalog filter section HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Cache
	extends \Aimeos\Client\Html\Catalog\Filter\Standard
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	private $tags = [];
	private $expire;
	private $view;


	/**
	 * Returns the HTML code for insertion into the body.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string HTML code
	 */
	public function getBody( string $uid = '' ) : string
	{
		$view = $this->getView();
		$context = $this->getContext();
		$confkey = 'client/html/catalog/filter';

		if( ( $html = $this->getCached( 'body', $uid, [], $confkey ) ) === null )
		{
			$tplconf = 'client/html/catalog/filter/template-body';
			$default = 'catalog/filter/body-standard';

			try
			{
				$html = '';
				$this->expire = date( 'Y-m-d H:i:s', time() + 86400 );

				if( !isset( $this->view ) ) {
					$view = $this->view = $this->getObject()->addData( $view, $this->tags, $this->expire );
				}

				foreach( $this->getSubClients() as $subclient ) {
					$html .= $subclient->setView( $view )->getBody( $uid );
				}
				$view->filterBody = $html;

				$html = $view->render( $view->config( $tplconf, $default ) );

                $this->setCached( 'body', $uid, [], $confkey, $html, $this->tags, $this->expire );

				return $html;
			}
			catch( \Aimeos\Client\Html\Exception $e )
			{
				$error = array( $context->translate( 'client', $e->getMessage() ) );
				$view->filterErrorList = array_merge( $view->get( 'filterErrorList', [] ), $error );
			}
			catch( \Aimeos\Controller\Frontend\Exception $e )
			{
				$error = array( $context->translate( 'controller/frontend', $e->getMessage() ) );
				$view->filterErrorList = array_merge( $view->get( 'filterErrorList', [] ), $error );
			}
			catch( \Aimeos\MShop\Exception $e )
			{
				$error = array( $context->translate( 'mshop', $e->getMessage() ) );
				$view->filterErrorList = array_merge( $view->get( 'filterErrorList', [] ), $error );
			}
			catch( \Exception $e )
			{
				$error = array( $context->translate( 'client', 'A non-recoverable error occured' ) );
				$view->filterErrorList = array_merge( $view->get( 'filterErrorList', [] ), $error );
				$this->logException( $e );
			}

			$html = $view->render( $view->config( $tplconf, $default ) );
		}
		else
		{
			$html = $this->modifyBody( $html, $uid );
		}

		return $html;
	}


	/**
	 * Returns the HTML string for insertion into the header.
	 *
	 * @param string $uid Unique identifier for the output if the content is placed more than once on the same page
	 * @return string|null String including HTML tags for the header on error
	 */
	public function getHeader( string $uid = '' ) : ?string
	{
		$view = $this->getView();
		$confkey = 'client/html/catalog/filter';

		if( ( $html = $this->getCached( 'header', $uid, [], $confkey ) ) === null )
		{
			$tplconf = 'client/html/catalog/filter/template-header';
			$default = 'catalog/filter/header-standard';

			try
			{
				$html = ' ';
				$this->expire = date( 'Y-m-d H:i:s', time() + 86400 );

				if( !isset( $this->view ) ) {
					$view = $this->view = $this->getObject()->addData( $view, $this->tags, $this->expire );
				}

				foreach( $this->getSubClients() as $subclient ) {
					$html .= $subclient->setView( $view )->getHeader( $uid );
				}
				$view->filterHeader = $html;

				$html = $view->render( $view->config( $tplconf, $default ) );

                $this->setCached( 'header', $uid, [], $confkey, $html, $this->tags, $this->expire );
			}
			catch( \Exception $e )
			{
				$this->logException( $e );
			}
		}
		else
		{
			$html = $this->modifyHeader( $html, $uid );
		}

		return $html;
	}
}