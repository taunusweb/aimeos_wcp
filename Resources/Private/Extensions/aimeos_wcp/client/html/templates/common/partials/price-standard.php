<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 */

$enc = $this->encoder();
$iface = '\Aimeos\MShop\Price\Item\Iface';
$page = $this->get( 'pageView', 'listing' );
$priceItems = $this->get( 'prices', map() );
$prices = [];

foreach( $priceItems as $priceItem )
{
    $qty = $priceItem->getQuantity();
    if( !isset( $prices[$qty] ) || $prices[$qty]->getValue() > $priceItem->getValue() ) {
        $prices[$qty] = $priceItem;
    }
}

ksort( $prices );

$format = array(
    /// Price quantity format with quantity (%1$s)
    'quantity' => $this->translate( 'client', 'from %1$s' ),
    /// Price shipping format with shipping / payment cost value (%1$s) and currency (%2$s)
    'costs' => ( $this->get( 'costsItem', true ) ? $this->translate( 'client', '+ %1$s %2$s/item' ) : $this->translate( 'client', '%1$s %2$s' ) ),
    /// Rebate format with rebate value (%1$s) and currency (%2$s)
    'rebate' => $this->translate( 'client', '%1$s %2$s off' ),
    /// Rebate percent format with rebate percent value (%1$s)
    'rebate%' => $this->translate( 'client', '-%1$s%%' ),
);

/// Tax rate format with tax rate in percent (%1$s)
$withtax = $this->translate( 'client', 'Incl. %1$s%% VAT' );
/// Tax rate format with tax rate in percent (%1$s)
$notax = $this->translate( 'client', '+ %1$s%% VAT' );

$price = ( $p = current( $prices ) ) ? $p->getValue() : 0;
$count = 0;
?>

<?php foreach( $prices as $priceItem ) : ?>
    <?php
    if( !( $priceItem instanceof $iface ) ) {
        throw new \Aimeos\MW\View\Exception( sprintf( 'Object doesn\'t implement "%1$s"', $iface ) );
    }

    if( $priceItem->getValue() > $price ) {
        continue; // Staffelpreise nur wenn sie kleiner sind
    } else {
        $price = $priceItem->getValue();
    }
    $count++;
    $costs = $priceItem->getCosts();
    $rebate = $priceItem->getRebate();
    $key = 'price:' . ( $priceItem->getType() ?: 'default' );

    /// Price format with price value (%1$s) and currency (%2$s)
    $format['value'] = $this->translate( 'client/code', $key );
    $currency = $this->translate( 'currency', $priceItem->getCurrencyId() );
    $taxformat = ( $priceItem->getTaxFlag() ? $withtax : $notax );
    ?>

	<div class="price-item <?= $page ?> <?= ($page == 'detail' ? ' row' : ''); ?> <?= $enc->attr( $priceItem->getType() ); ?>" itemprop="priceSpecification" itemscope="" itemtype="http://schema.org/PriceSpecification">

		<meta itemprop="valueAddedTaxIncluded" content="<?= ( $priceItem->getTaxFlag() ? 'true' : 'false' ); ?>" />
		<meta itemprop="priceCurrency" content="<?= $priceItem->getCurrencyId(); ?>" />
		<meta itemprop="price" content="<?= $priceItem->getValue(); ?>" />

		<span class="quantity<?= ($page == 'detail' ? ' col-4' : ''); ?>" <?= (count($prices) > 1 ? 'style="display:inline-block"' : ''); ?>  itemscope="" itemtype="http://schema.org/QuantitativeValue">
		<?php if( $count > 1 ) : ?>
			<meta itemprop="minValue" content="<?= $priceItem->getQuantity(); ?>" />
            <?= $enc->html( sprintf( $format['quantity'], $priceItem->getQuantity() ), $enc::TRUST ); ?>
        <?php else: ?>
            <?php if($page == 'detail') : ?>
                <?= $enc->html( $this->translate('client', 'Ihr Preis' ) ) ?>
            <?php else: ?>
                <?= $enc->html( $this->translate('client', 'ab' ) ) ?>
            <?php endif; ?>
        <?php endif; ?>
		</span>

		<span class="value text-right text-nowrap <?= ($page == 'detail' ? ' col-3' : ''); ?>">
			<?= $enc->html( sprintf( $format['value'], $this->number( $priceItem->getValue(), $priceItem->getPrecision() ), $currency ), $enc::TRUST ); ?>
		</span>

        <?php if( $priceItem->getValue() > 0 && $rebate > 0 ) : ?>
			<span class="rebate" <?= (count($prices) > 1 ? 'style="display:inline-block"' : ''); ?>>
				<?= $enc->html( sprintf( $format['rebate'], $this->number( $rebate ), $currency ), $enc::TRUST ); ?>
			</span>
			<span class="rebatepercent" <?= (count($prices) > 1 ? 'style="display:inline-block"' : ''); ?>>
				<?= $enc->html( sprintf( $format['rebate%'], $this->number( round( $rebate * 100 / ( $priceItem->getValue() + $rebate ) ), 0 ) ), $enc::TRUST ); ?>
			</span>
        <?php endif; ?>

        <?php if( $costs > 0 ) : ?>
			<span class="costs">
				<?= $enc->html( sprintf( $format['costs'], $this->number( $costs, $priceItem->getPrecision() ), $currency ), $enc::TRUST ); ?>
			</span>
        <?php endif; ?>

	</div>

<?php endforeach; ?>
