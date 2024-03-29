<?php

/**
 * @copyright Aimeos GmbH (aimeos.com), 2020
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager\Text;


/**
 * Solr sub-manager for texts
 *
 * @package MShop
 * @subpackage Index
 */
class Weber extends \Aimeos\MShop\Index\Manager\Text\Solr
{
	private $searchConfig = array(
		'index.text:url' => array(
			'code' => 'index.text:url()',
			'internalcode' => 'index.text.url_$1',
			'label' => 'Product URL by language, parameter(<language ID>)',
			'type' => 'string',
			'internaltype' => 'string',
			'public' => false,
		),
		'index.text:name' => array(
			'code' => 'index.text:name()',
			'internalcode' => 'index.text.name_$1',
			'label' => 'Product name by language, parameter(<language ID>)',
			'type' => 'string',
			'internaltype' => 'string',
			'public' => false,
		),
		'sort:index.text:name' => array(
			'code' => 'sort:index.text:name()',
			'internalcode' => 'index.text.name_$1',
			'label' => 'Sort product name by language, parameter(<language ID>)',
			'type' => 'string',
			'internaltype' => 'string',
			'public' => false,
		),
		'index.text:relevance' => array(
			'code' => 'index.text:relevance()',
			'internalcode' => ['bool' => ['should' => []]],
			'label' => 'Product texts, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => 'null',
			'public' => false,
		),
		'sort:index.text:relevance' => array(
			'code' => 'sort:index.text:relevance()',
			'internalcode' => 'score',
			'label' => 'Product text sorting, parameter(<language ID>,<search term>)',
			'type' => 'float',
			'internaltype' => 'null',
			'public' => false,
		),
	);


	/**
	 * Initializes the object
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 */
	public function __construct( \Aimeos\MShop\Context\Item\Iface $context )
	{
		\Aimeos\MShop\Index\Manager\Solr::__construct( $context );


		$this->searchConfig['index.text:relevance']['function'] = function( &$source, array $params ) {

			$text = addcslashes( $params[1], '+&|!(){}[]^~*?:/' );
			$text = str_replace( ['.', '-', ',', '"', '\\'], '', $text );
			$text = \Aimeos\Map::explode( ' ', $text )->filter()->prefix( '+' )->join( ' ' );

			$source['bool']['should'][] = 'product.code:(' . $text . ')^1000';
			$source['bool']['should'][] = 'product.code:(' . $text . '*)^250';

			$source['bool']['should'][] = 'index.text.name_' . $params[0] . ':(' . $text . ')^450';
			$source['bool']['should'][] = 'index.text.name_' . $params[0] . ':(' . $text . '*)^100';
			$source['bool']['should'][] = 'index.text.content_' . $params[0] . ':(' . $text . ')^150';
			$source['bool']['should'][] = 'index.text.content_' . $params[0] . ':(' . $text . '*)^15';
			$source['bool']['should'][] = 'index.text.content_:(' . $text . ')^800';
			$source['bool']['should'][] = 'index.text.content_:(' . $text . '*)^200';

			return $params;
		};
	}


	/**
	 * Returns a list of objects describing the available criteria for searching
	 *
	 * @param boolean $withsub True to return attributes of sub-managers too
	 * @return \Aimeos\MW\Criteria\Attribute\Iface[] List attribute criteria objects
	 */
	public function getSearchAttributes( bool $withsub = true ) : array
	{
		$list = [];

		foreach( $this->searchConfig as $key => $fields ) {
			$list[$key] = new \Aimeos\MW\Criteria\Attribute\Standard( $fields );
		}

		foreach( $this->getSubManagers() as $subManager ) {
			$list += $subManager->getSearchAttributes( $withsub );
		}

		return $list;
	}
}
