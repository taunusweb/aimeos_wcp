<?php

namespace Aimeos\Client\Html\Catalog\Suggest;


class Weber extends Standard
{
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], string &$expire = null ) : \Aimeos\MW\View\Iface
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$text = $view->param( 'f_search', '' );

		$domains = $config->get( 'client/html/catalog/suggest/domains', ['text', 'media'] );
		$size = $config->get( 'client/html/catalog/suggest/size', 25 );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'catalog' );

		foreach( explode( ' ', $text ) as $str )
		{
			$origlen = strlen( $str );
			$str = preg_filter( '/[A-Za-z0-9]/', '$0', $str );

			if( ( $len = strlen( $str ) ) > 0 && $origlen < 4 ) {
				$cntl->compare( '~=', 'catalog.label', $len === 1 ? ' ' . $str : $str );
			}
		}

		$catItems = $cntl->uses( $domains )
			->compare( '>', 'catalog:relevance("' . str_replace( ['"', ','], ' ', $text ) . '")', 0 )
			->sort( '-sort:catalog:relevance("' . str_replace( ['"', ','], ' ', $text ) . '")' )
			->slice( 0, $size )
			->search();

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )
			->uses( $domains )->text( $text )->slice( 0, $size ); // sort by relevance first

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
		$view->suggestItems = $cntl->search();

		return $view;
	}
}
