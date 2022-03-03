<?php

namespace Aimeos\Client\Html\Catalog\Suggest;


class Weber extends Standard
{
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$text = $view->param( 'f_search' );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )
			->text( $text ); // sort by relevance first

		$domains = $config->get( 'client/html/catalog/suggest/domains', ['text', 'media'] );
		$size = $config->get( 'client/html/catalog/suggest/size', 24 );

		$catItems = \Aimeos\Controller\Frontend::create( $context, 'catalog' )->uses( $domains )
			->compare( '>', 'catalog:relevance("' . str_replace( '"', ' ', $text ) . '")', 0 )
			->sort( '-sort:catalog:relevance("' . str_replace( ['"', ','], ' ', $text ) . '")' )->sort( 'catalog.label' )
			->slice( 0, 4 )->search();

		if( $config->get( 'client/html/catalog/suggest/restrict', true ) == true )
		{
			$level = $config->get( 'client/html/catalog/lists/levels', \Aimeos\MW\Tree\Manager\Base::LEVEL_ONE );
			$catids = $view->param( 'f_catid', $config->get( 'client/html/catalog/lists/catid-default' ) );

			$cntl->category( $catids, 'default', $level )
				->allOf( $view->param( 'f_attrid', [] ) )
				->oneOf( $view->param( 'f_optid', [] ) )
				->oneOf( $view->param( 'f_oneid', [] ) );
		}

		$view->suggestCatalogItems = $catItems;
		$view->suggestItems = $cntl->uses( $domains )->slice( 0, $size - count( $catItems ) )->search();

		return $view;
	}
}
