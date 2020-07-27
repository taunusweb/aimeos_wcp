<?php

$cattarget = $this->config( 'client/html/catalog/lists/url/target' );
$catcntl = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$cataction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$catconfig = $this->config( 'client/html/catalog/lists/url/config', [] );

$target = $this->config( 'client/html/catalog/detail/url/target' );
$cntl = $this->config( 'client/html/catalog/detail/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/detail/url/action', 'detail' );
$config = $this->config( 'client/html/catalog/detail/url/config', [] );
$filter = array_flip( $this->config( 'client/html/catalog/detail/url/filter', ['d_prodid'] ) );

$items = [];
$enc = $this->encoder();

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


foreach( $this->get( 'suggestCatalogItems', [] ) as $id => $catItem )
{
	$media = '';
	$name = strip_tags( $catItem->getName() );
	$mediaItems = $catItem->getRefItems( 'media', 'default', 'default' );

	if( ( $mediaItem = reset( $mediaItems ) ) !== false ) {
		$media = $this->content( $mediaItem->getPreview() );
	}

	$params = ['f_name' => $catItem->getName( 'url' ), 'f_catid' => $catItem->getId()];
	$items[] = array(
		'label' => $name,
		'html' => '
			<li class="aimeos catalog-suggest">
				<a class="suggest-item" href="' . $enc->attr( $this->url( $cattarget, $catcntl, $cataction, $params, [], $catconfig ) ) . '">
					<div class="item-image" style="background-image: url(' . $enc->attr( $media ) . ')"></div>
					<div class="item-name">' . $enc->html( $name ) . '</div>
				</a>
			</li>
		'
	);
}

foreach( $this->get( 'suggestItems', [] ) as $id => $productItem )
{
	$media = $price = '';
	$name = strip_tags( $productItem->getName() );
	$mediaItems = $productItem->getRefItems( 'media', 'default', 'default' );
	$priceItems = $productItem->getRefItems( 'price', 'default', 'default' );

	if( ( $mediaItem = reset( $mediaItems ) ) !== false ) {
		$media = $this->content( $mediaItem->getPreview() );
	}

	if( ( $priceItem = reset( $priceItems ) ) !== false ) {
		$price = sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $this->translate( 'currency', $priceItem->getCurrencyId() ) );
	}

	$params = array_diff_key( ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId(), 'd_pos' => ''], $filter );
	$items[] = array(
		'label' => $name,
		'html' => '
			<li class="aimeos catalog-suggest">
				<a class="suggest-item" href="' . $enc->attr( $this->url( $target, $cntl, $action, $params, [], $config ) ) . '">
					<div class="item-image" style="background-image: url(' . $enc->attr( $media ) . ')"></div>
					<div class="item-name">' . $enc->html( $name ) . '</div>
				</a>
			</li>
		'
	);
}

echo json_encode( $items );
