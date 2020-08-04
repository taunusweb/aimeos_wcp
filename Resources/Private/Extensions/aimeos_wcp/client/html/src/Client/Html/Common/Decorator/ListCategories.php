<?php

namespace Aimeos\Client\Html\Common\Decorator;

class ListCategories
	extends \Aimeos\Client\Html\Common\Decorator\Base
	implements \Aimeos\Client\Html\Common\Decorator\Iface
{
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = array(), &$expire = null )
	{
		$view = parent::addData( $view, $tags, $expire );

		if( $text = $view->param( 'f_search' ) )
		{
			$view->listNodes = \Aimeos\Controller\Frontend::create( $this->getContext(), 'catalog' )->uses( ['media'] )
				->compare( '>', 'catalog:relevance("' . str_replace( '"', ' ', $text ) . '")', 0.65 )
				->sort( '-sort:catalog:relevance("' . str_replace( '"', ' ', $text ) . '")' )->sort( 'catalog.label' )
				->slice( 0, 8 )->search();
		}

		return $view;
	}
}