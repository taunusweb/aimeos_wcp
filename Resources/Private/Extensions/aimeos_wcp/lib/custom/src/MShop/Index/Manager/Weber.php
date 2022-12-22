<?php

/**
 * @copyright Aimeos GmbH (aimeos.com), 2018-2021
 * @package MShop
 * @subpackage Index
 */


namespace Aimeos\MShop\Index\Manager;


/**
 * Weber index manager
 *
 * @package MShop
 * @subpackage Index
 */
class Weber extends \Aimeos\MShop\Index\Manager\Solr
{
	/**
	 * Returns the fields for the attached text data
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Product item with referenced text items
	 * @return array Associative list of key/value pairs for the document
	 */
	protected function getTextData( \Aimeos\MShop\Product\Item\Iface $item ) : array
	{
		$data = $texts = [];

		foreach( $item->getRefItems( 'text', 'url', 'default' ) as $text ) {
			$texts[$text->getLanguageId()]['url'] = \Aimeos\MW\Str::slug( $text->getContent() );
		}

		foreach( $item->getRefItems( 'text', 'name', 'default' ) as $text ) {
			$texts[$text->getLanguageId()]['name'] = $text->getContent();
		}

		$types = $this->getContext()->getConfig()->get( 'mshop/index/manager/text/types' );
		$products = ( $item->getType() === 'select' ? $item->getRefItems( 'product', null, 'default' ) : [] );
		$products[] = $item;

		foreach( $products as $product )
		{
			foreach( $this->getLanguageIds() as $langId )
			{
				$texts[$langId]['content'][] = $product->getCode();

				foreach( $product->getCatalogItems() as $catItem ) {
					$texts[$langId]['content'][] = $catItem->getName();
				}

				foreach( $product->getSupplierItems() as $supItem ) {
					$texts[$langId]['content'][] = $supItem->getName();
				}

				foreach( $product->getRefItems( 'attribute', null, ['default', 'variant'] ) as $attrItem ) {
					$texts[$langId]['content'][] = $attrItem->getName();
				}
			}

			foreach( $product->getRefItems( 'text', $types ) as $text ) {
				$texts[$text->getLanguageId()]['content'][] = $text->getContent();
			}
		}

		foreach( $texts as $langId => $map )
		{
			if( !isset( $map['url'] ) ) {
				$map['url'] = \Aimeos\MW\Str::slug( $item->getLabel() );
			}

			if( !isset( $map['name'] ) ) {
				$map['content'][] = $map['name'] = $item->getLabel();
			}

			$data['index.text.url'] = $map['url'];
			$data['index.text.name_' . $langId] = $map['name'];
			$data['index.text.sort_' . $langId] = $map['name'];
			$data['index.text.content_' . $langId] = $map['content'];
		}

		return $data;
	}
}
