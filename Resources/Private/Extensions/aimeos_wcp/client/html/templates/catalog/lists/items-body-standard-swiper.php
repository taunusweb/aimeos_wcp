<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();

/** client/html/catalog/lists/basket-add
 * Display the "add to basket" button for each product item
 *
 * Enables the button for adding products to the basket from the list view.
 * This works for all type of products, even for selection products with product
 * variants and product bundles. By default, also optional attributes are
 * displayed if they have been associated to a product.
 *
 * '''Note:''' To fetch the necessary product variants, you have to extend the
 * list of domains for "client/html/catalog/lists/domains", e.g.
 *
 *  client/html/catalog/lists/domains = array( 'attribute', 'media', 'price', 'product', 'text' )
 *
 * @param boolean True to display the button, false to hide it
 * @since 2016.01
 * @category Developer
 * @category User
 * @see client/html/catalog/domains
 */
$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );
$listParams = $this->get( 'listParams', [] );

/** client/html/catalog/lists/infinite-scroll
 * Enables infinite scrolling in product catalog list
 *
 * If set to true, products from the next page are loaded via XHR request
 * and added to the product list when the user reaches the list bottom.
 *
 * @param boolean True to use infinite scrolling, false to disable it
 * @since 2019.10
 * @category Developer
 */
$infiniteScroll = $this->config( 'client/html/catalog/lists/infinite-scroll', false );
$infiniteUrl = ( $infiniteScroll && $this->get( 'listPageNext', 0 ) > $this->get( 'listPageCurr', 0 ) ) ? $this->url( $listTarget, $listController, $listAction, array( 'l_page' => $this->get( 'listPageNext' ) ) + $listParams, [], $listConfig ) : '';

$exact = $similar = [];

foreach( $this->get( 'listProductItems', [] ) as $id => $product )
{
    if( $product->get( 'score' ) >= 400 ) {
        $exact[$id] = $product;
    } else {
        $similar[$id] = $product;
    }
}


?>
<?php $this->block()->start( 'catalog/lists/items' ); ?>
<div class="catalog-list-items swipertest" data-infinite-url="<?= $infiniteUrl ?>">

    <?= $this->partial(
        $this->config( 'client/html/common/partials/products', 'common/partials/products-standard' ),
        array(
            'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
            'basket-add' => $this->config( 'client/html/catalog/lists/basket-add', false ),
            'productItems' => $this->get( 'itemsProductItems', [] ),
            'products' => $exact,
            'position' => $this->get( 'itemPosition' ),
        )
    ); ?>
    <hr>

    <?php if( !empty( $similar ) ) : ?>

        <?= $this->partial(
            $this->config( 'client/html/common/partials/products', 'common/partials/products-standard-swiper' ),
            array(
                'require-stock' => (int) $this->config( 'client/html/basket/require-stock', true ),
                'basket-add' => $this->config( 'client/html/catalog/lists/basket-add', false ),
                'productItems' => $this->get( 'itemsProductItems', [] ),
                'products' => $similar,
                'position' => $this->get( 'itemPosition' ),
            )
        ); ?>
    <?php endif ?>

</div>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/lists/items' ); ?>
