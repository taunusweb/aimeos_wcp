<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();
$params = $this->get( 'listParams', [] );
$catPath = $this->get( 'listCatPath', [] );

$target = $this->config( 'client/html/catalog/tree/url/target' );
$cntl = $this->config( 'client/html/catalog/tree/url/controller', 'catalog' );
$action = $this->config( 'client/html/catalog/tree/url/action', 'tree' );
$config = $this->config( 'client/html/catalog/tree/url/config', [] );

$optTarget = $this->config( 'client/jsonapi/url/target' );
$optCntl = $this->config( 'client/jsonapi/url/controller', 'jsonapi' );
$optAction = $this->config( 'client/jsonapi/url/action', 'options' );
$optConfig = $this->config( 'client/jsonapi/url/config', [] );


/** client/html/catalog/lists/pagination/enable
 * Enables or disables pagination in list views
 *
 * Pagination is automatically hidden if there are not enough products in the
 * category or search result. But sometimes you don't want to show the pagination
 * at all, e.g. if you implement infinite scrolling by loading more results
 * dynamically using AJAX.
 *
 * @param boolean True for enabling, false for disabling pagination
 * @since 2019.04
 * @category User
 * @category Developer
 */
$pagination = '';

if( $this->get( 'listProductTotal', 0 ) > 1 && $this->config( 'client/html/catalog/lists/pagination/enable', true ) == true )
{
    /** client/html/catalog/lists/partials/pagination
     * Relative path to the pagination partial template file for catalog lists
     *
     * Partials are templates which are reused in other templates and generate
     * reoccuring blocks filled with data from the assigned values. The pagination
     * partial creates an HTML block containing a page browser and sorting links
     * if necessary.
     *
     * @param string Relative path to the template file
     * @since 2017.01
     * @category Developer
     */
    $pagination = $this->partial(
        $this->config( 'client/html/catalog/lists/partials/pagination', 'catalog/lists/pagination-standard' ),
        array(
            'params' => $params,
            'size' => $this->get( 'listPageSize', 48 ),
            'total' => $this->get( 'listProductTotal', 0 ),
            'current' => $this->get( 'listPageCurr', 0 ),
            'prev' => $this->get( 'listPagePrev', 0 ),
            'next' => $this->get( 'listPageNext', 0 ),
            'last' => $this->get( 'listPageLast', 0 ),
        )
    );
}

?>
<section class="aimeos catalog-list <?= $enc->attr( $this->get( 'listCatPath', map() )->getConfigValue( 'css-class', '' )->join( ' ' ) ) ?>"
         data-jsonurl="<?= $enc->attr( $this->url( $optTarget, $optCntl, $optAction, [], [], $optConfig ) ); ?>">

    <?php if( isset( $this->listErrorList ) ) : ?>
        <ul class="error-list">
            <?php foreach( (array) $this->listErrorList as $errmsg ) : ?>
                <li class="error-item"><?= $enc->html( $errmsg ); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>


    <?php if( $this->get( 'listNodes', [] ) !== [] ) : ?>
        <h1 class="mb-2"><strong>Modelle</strong> zu Ihrer Suche (<?= $enc->html( $this->get( 'listNodesTotal', 0 ) ) ?> Modelle)</h1>
        <div class="bg-extra-light p-2 mb-3">
            <div class="catalog-filter-wcp d-flex flex-wrap  align-content-center mb-3 slick-responsive">
                <?php foreach( $this->get( 'listNodes', [] ) as $item ) : ?>
                    <?php if( $item->getStatus() > 0 ) : ?>
                        <?php $id = $item->getId(); $config = $item->getConfig(); ?>
                        <?php $params['f_name'] = $item->getName( 'url' ); $params['f_catid'] = $id; unset( $params['f_search'] ); ?>
                        <?php $class = ' catcode-' . $item->getCode() . ( isset( $config['css-class'] ) ? ' ' . $config['css-class'] : '' ); ?>
                        <div class="<?= $level; ?> <?= $item->getLevel(); ?>  cat-item p-2 catid-<?= $enc->attr( $id . $class ); ?>" data-id="<?= $id; ?>" >
                            <a class="cat-item" href="<?= $enc->attr( $this->url( ( $item->getTarget() ?: $target ), $controller, $action, $params, [], $config ) ); ?>"><!--
                                --><div class="media-list"><!--

                                    <?php foreach( $item->getRefItems( 'media', 'default', 'default' ) as $mediaItem ) : ?>
                                        <?= '-->' . $this->partial(
                                        $this->config( 'client/html/common/partials/media', 'common/partials/media-standard' ),
                                        array( 'item' => $mediaItem, 'boxAttributes' => array( 'class' => 'media-item' ) )
                                    ) . '<!--'; ?>
                                    <?php endforeach; ?>

                                --></div><!--
                                --><span class="cat-name"><?= $enc->html( $item->getName(), $enc::TRUST ); ?></span><!--
                            --></a>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif ?>


    <?= $this->block()->get( 'catalog/lists/promo' ); ?>





    <?php if( ( $searchText = $this->param( 'f_search', null ) ) != null ) : ?>
        <div class="list-search">
			<h2 class="mb-2"><strong>Artikel</strong> zu Ihrer Suche</h2>
            <?php if( ( $total = $this->get( 'listProductTotal', 0 ) ) > 0 ) : ?>
                <?= $enc->html( sprintf(
                    $this->translate(
                        'client',
                        'Search result for <span class="searchstring">"%1$s"</span> (%2$d article)',
                        'Search result for <span class="searchstring">"%1$s"</span> (%2$d articles)',
                        $total
                    ),
                    $searchText,
                    $total
                ), $enc::TRUST ); ?>
            <?php else : ?>
                <?= $enc->html( sprintf(
                    $this->translate(
                        'client',
                        'No articles found for <span class="searchstring">"%1$s"</span>. Please try again with a different keyword.'
                    ),
                    $searchText
                ), $enc::TRUST ); ?>
            <?php endif; ?>

        </div>
    <?php endif; ?>

    <?= $pagination; ?>

    <?= $this->block()->get( 'catalog/lists/items' ); ?>


    <?= $pagination; ?>

</section>
