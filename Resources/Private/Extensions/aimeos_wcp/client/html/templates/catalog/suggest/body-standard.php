<?php

$items = [];
$enc = $this->encoder();

/// Price format with price value (%1$s) and currency (%2$s)
$priceFormat = $this->translate( 'client', '%1$s %2$s' );


foreach( $this->get( 'suggestCatalogItems', [] ) as $id => $catItem )
{
	$name = strip_tags( $catItem->getName() );
	$media = $catItem->getRefItems( 'media', 'default', 'default' )->getPreview()->first() ?: '../../typo3conf/ext/aimeos_wcp/Resources/Public/wasserzeichen.png';

	$items[] = array(
		'label' => $name,
		'html' => '
			<div class="aimeos catalog-suggest suggest-catalog">
				<a class="suggest-item" href="' . $enc->attr( $this->link( 'client/html/catalog/lists/url', ['f_name' => $catItem->getName( 'url' ), 'f_catid' => $catItem->getId()] ) ) . '">
					<div class="item-image" style="background-image: url(' . $enc->attr( $this->content( $media ) ) . ')"></div>
					<div class="item-name">' . $enc->html( $name ) . '</div>
				</a>
			</div>
		'
	);
}

foreach( $this->get( 'suggestItems', [] ) as $id => $productItem )
{
	$price = '';
	$name = strip_tags( $productItem->getName() );
	$media = $productItem->getRefItems( 'media', 'default', 'default' )->getPreview()->first() ?: '../../typo3conf/ext/aimeos_wcp/Resources/Public/wasserzeichen.png';

	if( $priceItem = $productItem->getRefItems( 'price', 'default', 'default' )->first() ) {
		$price = sprintf( $priceFormat, $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $this->translate( 'currency', $priceItem->getCurrencyId() ) );
	}

	$items[] = array(
		'label' => $name,
		'html' => '
			<div class="aimeos catalog-suggest suggest-product ' . $enc->attr( $productItem->score >= 500 ? 'exact' : 'similar' ) . '">
				<a class="suggest-item" href="' . $enc->attr( $this->link( 'client/html/catalog/detail/url', ['d_name' => $productItem->getName( 'url' ), 'd_prodid' => $productItem->getId(), 'd_pos' => ''] ) ) . '">
					<div class="item-image" style="background-image: url(' . $enc->attr( $this->content( $media ) ) . ')"></div>
					<div class="item-name">' . $enc->html( $name ) . '</div>
				</a>
			</div>
		'
	);
}

echo json_encode( $items );
