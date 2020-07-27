<?php

namespace Aimeos\MShop\Catalog\Manager\Decorator;


/**
 * Full text search config for catalog label
 */
class Weber
	extends \Aimeos\MShop\Catalog\Manager\Decorator\Base
{
	private $attr = [
		'catalog:relevance' => array(
			'code' => 'catalog:relevance()',
			'internalcode' => ':site AND mcat."langid" = $1 AND MATCH( mcat."content" ) AGAINST( $2 IN BOOLEAN MODE )',
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
		'sort:catalog:relevance' => array(
			'code' => 'sort:catalog:relevance()',
			'internalcode' => 'MATCH( mcat."content" ) AGAINST( $2 IN BOOLEAN MODE )',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => \Aimeos\MW\DB\Statement\Base::PARAM_FLOAT,
			'public' => false,
		),
	];


	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		parent::__construct( $context );

		$func = function( $source, array $params ) {

			if( isset( $params[1] ) )
			{
				$str = '';
				$regex = '/(\&|\||\!|\-|\+|\>|\<|\(|\)|\~|\*|\:|\"|\'|\@|\\| )+/';
				$search = trim( mb_strtolower( preg_replace( $regex, ' ', $params[1] ) ), "' \t\n\r\0\x0B" );

				foreach( explode( ' ', $search ) as $part )
				{
					if( strlen( $part ) > 2 ) {
						$str .= $part . '* ';
					}
				}

				$params[1] = '\'' . $str . '"' . $search . '"\'';
			}

			return $params;
		};

		$name = 'catalog:relevance';
		$siteIds = $context->getLocale()->getSitePath();
		$expr = $siteIds ? $this->toExpression( 'mcat."siteid"', $siteIds ) : '1=1';

		$this->searchConfig[$name]['internalcode'] = str_replace( ':site', $expr, $this->searchConfig[$name]['internalcode'] );
		$this->searchConfig['sort:catalog:relevance']['function'] = $func;
		$this->searchConfig['catalog:relevance']['function'] = $func;
	}


	public function getSearchAttributes( $sub = true )
	{
		return parent::getSearchAttributes( $sub ) + $this->createAttributes( $this->attr );
	}
}
