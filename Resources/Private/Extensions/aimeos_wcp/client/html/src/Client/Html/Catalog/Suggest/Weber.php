<?php

namespace Aimeos\Client\Html\Catalog\Suggest;


class Weber
	extends Aimeos\Client\Html\Catalog\Suggest\Standard
	implements \Aimeos\Client\Html\Common\Client\Factory\Iface
{
	public function addData( \Aimeos\MW\View\Iface $view, array &$tags = [], &$expire = null )
	{
		$context = $this->getContext();
		$config = $context->getConfig();
		$text = str_replace( '"', ' ', $view->param( 'f_search' ) );

		$cntl = \Aimeos\Controller\Frontend::create( $context, 'product' )
			->text( $text ); // sort by relevance first

		$domains = $config->get( 'client/html/catalog/suggest/domains', ['text', 'media'] );
		$size = $config->get( 'client/html/catalog/suggest/size', 24 );
		$lang = $context->getLocale()->getLanguageId();

		$catItems = \Aimeos\Controller\Frontend::create( $context, 'catalog' )->uses( $domains )
			->compare( '!=', 'catalog:relevance("' . $lang . '","' . $text . '")', null )
			->sort( 'sort:catalog:relevance("' . $lang . '","' . $text . '")' )
			->slice( 0, $size )->search();

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

		return parent::addData( $view, $tags, $expire );
	}
}
