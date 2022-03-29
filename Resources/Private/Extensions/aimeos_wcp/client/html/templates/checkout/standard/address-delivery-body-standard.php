<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 */

$enc = $this->encoder();


$target = $this->config( 'client/html/checkout/standard/url/target' );
$controller = $this->config( 'client/html/checkout/standard/url/controller', 'checkout' );
$action = $this->config( 'client/html/checkout/standard/url/action', 'index' );
$config = $this->config( 'client/html/checkout/standard/url/config', [] );


?>
<?php $this->block()->start( 'checkout/standard/address/delivery' ) ?>
<div class="checkout-standard-address-delivery col-xs-12 col-xl">

	<h2><?= $enc->html( $this->translate( 'client', 'Delivery address' ), $enc::TRUST ) ?></h2>

	<div class="item-address item-like">
		<div class="header">
			<input id="ca_deliveryoption-like" type="radio" value="like"
				name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
				<?= $this->get( 'addressDeliveryOption', 'like' ) == 'like' ? 'checked="checked"' : '' ?>>
			<label for="ca_deliveryoption-like" class="values value-like">
				<?= $enc->html( $this->translate( 'client', 'like billing address' ), $enc::TRUST ) ?>
			</label>
		</div>
	</div>


	<?php foreach( $this->get( 'addressDeliveryValues', [] ) as $id => $addr ) : ?>

		<div class="item-address">
			<div class="header">
				<a class="modify minibutton delete"
					href="<?= $enc->attr( $this->url( $target, $controller, $action, ['step' => 'address', 'ca_delivery_delete' => $id], [], $config ) ) ?>">
				</a>
				<input id="ca_deliveryoption-<?= $id ?>" type="radio" value="<?= $enc->attr( $id ) ?>"
					name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
					<?= $this->get( 'addressDeliveryOption' ) == $id ? 'checked="checked"' : '' ?>>
				<label for="ca_deliveryoption-<?= $id ?>" class="values">
					<?= nl2br( $enc->html( $this->value( 'addressDeliveryStrings', $id, '' ) ) ) ?>
				</label>
			</div>

			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $addr,
						'error' => $this->get( 'addressDeliveryOption' ) == $id ? $this->get( 'addressDeliveryError', [] ) : [],
						'salutations' => $this->get( 'addressDeliverySalutations', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'languageid' => $this->get( 'contextLanguage' ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressDeliveryCss', [] ),
						'type' => 'delivery',
						'id' => $id,
					)
				) ?>
			</div>
		</div>

	<?php endforeach ?>


	<?php if( !$this->config( 'client/html/checkout/standard/address/delivery/disable-new', false ) ) : ?>

		<div class="item-address item-new" data-option="<?= $enc->attr( $this->get( 'addressDeliveryOption' ) ) ?>">
			<div class="header">
				<input id="ca_deliveryoption-null" type="radio" value="null"
					name="<?= $enc->attr( $this->formparam( ['ca_deliveryoption'] ) ) ?>"
					<?= $this->get( 'addressDeliveryOption' ) == 'null' ? 'checked="checked"' : '' ?>>
				<label for="ca_deliveryoption-null" class="values value-new">
					<?= $enc->html( $this->translate( 'client', 'new address' ), $enc::TRUST ) ?>
				</label>
			</div>

			<div class="form-list">
				<?= $this->partial(
					$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
					array(
						'address' => $this->get( 'addressDeliveryValuesNew', [] ),
						'error' => $this->get( 'addressDeliveryOption' ) == 'null' ? $this->get( 'addressDeliveryError', [] ) : [],
						'salutations' => $this->get( 'addressDeliverySalutations', [] ),
						'countries' => $this->get( 'addressCountries', [] ),
						'languages' => $this->get( 'addressLanguages', [] ),
						'languageid' => $this->get( 'contextLanguage' ),
						'states' => $this->get( 'addressStates', [] ),
						'css' => $this->get( 'addressDeliveryCss', [] ),
						'type' => 'delivery'
					)
				) ?>

				<div class="row form-item form-group store <?= join( ' ', $this->value( 'addressDeliveryCss', 'nostore', [] ) ) ?>">
					<label class="col-md-5" for="address-delivery-store">
						<?= $enc->html( $this->translate( 'client', 'Don\'t store address' ), $enc::TRUST ) ?>
					</label>
					<div class="col-md-7">
						<input class="custom-control custom-checkbox" type="checkbox" value="1" name="<?= $enc->attr( $this->formparam( ['ca_delivery', 'nostore'] ) ) ?>">
					</div>
				</div>
			</div>
		</div>

	<?php endif ?>

	<?php if( !empty( $this->get( 'addressDeliveryItems', [] ) ) ) : ?>
		<div class="item-address" onclick="$('.checkout-standard-address-delivery .address-list').toggle()">
			<div class="header">
				<span>&#9660;</span><?= $enc->html( $this->translate( 'client', 'Gespeicherte Adressen' ) ) ?>
			</div>
		</div>
	<?php endif ?>

	<div class="address-list" style="display: none">
		<?php foreach( $this->get( 'addressDeliveryItems', [] ) as $id => $addr ) : ?>
			<div class="item-address">

				<div class="header">
					<a class="modify minibutton" href="<?= $enc->attr( $this->url( $target, $controller, $action, array( 'step' => 'address', 'ca_delivery_delete' => $id ), [], $config ) ); ?>">X</a>
						<input id="ca_deliveryoption-<?= $id; ?>" type="radio" name="<?= $enc->attr( $this->formparam( array( 'ca_deliveryoption' ) ) ); ?>" value="<?= $enc->attr( $addr->getAddressId() ); ?>" <?= ( $deliveryOption == $id ? 'checked="checked"' : '' ); ?> />
							<label for="ca_deliveryoption-<?= $id; ?>" class="values">
<?php
	echo preg_replace( "/\n+/m", "<br/>", trim( $enc->html( sprintf(
		/// Address format with company (%1$s), salutation (%2$s), title (%3$s), first name (%4$s), last name (%5$s),
		/// address part one (%6$s, e.g street), address part two (%7$s, e.g house number), address part three (%8$s, e.g additional information),
		/// postal/zip code (%9$s), city (%10$s), state (%11$s), country (%12$s), language (%13$s),
		/// e-mail (%14$s), phone (%15$s), facsimile/telefax (%16$s), web site (%17$s), vatid (%18$s)
		$this->translate( 'client', '%1$s, %5$s
%9$s %10$s, %12$s'
		),
		$addr->getCompany(),
		( !in_array( $addr->getSalutation(), array( 'company' ) ) ? $this->translate( 'mshop/code', $addr->getSalutation() ) : '' ),
		$addr->getTitle(),
		$addr->getFirstName(),
		$addr->getLastName(),
		$addr->getAddress1(),
		$addr->getAddress2(),
		$addr->getAddress3(),
		$addr->getPostal(),
		$addr->getCity(),
		$addr->getState(),
		$this->translate( 'country', $addr->getCountryId() ),
		$this->translate( 'language', $addr->getLanguageId() ),
		$addr->getEmail(),
		$addr->getTelephone(),
		$addr->getTelefax(),
		$addr->getWebsite(),
		$addr->getVatID()
	) ) ) );
?>
							</label>
					</div>

<?php
        $deliveryCss = $deliveryCssAll;
        if( $deliveryOption == $id )
        {
                foreach( $this->get( 'deliveryError', [] ) as $name => $msg ) {
                        $deliveryCss[$name][] = 'error';
                }
        }

        $addrValues = $addr->toArray();
        if( !isset( $addrValues['order.base.address.languageid'] ) || $addrValues['order.base.address.languageid'] == '' ) {
                $addrValues['order.base.address.languageid'] = $this->get( 'deliveryLanguage', 'en' );
        }
?>
					<ul class="form-list">
						<?= $this->partial(
							$this->config( 'client/html/checkout/standard/partials/address', 'checkout/standard/address-partial-standard' ),
							array(
								'address' => $addrValues,
								'salutations' => $deliverySalutations,
								'languages' => $deliveryLanguages,
								'countries' => $deliveryCountries,
								'states' => $deliveryStates,
								'type' => 'delivery',
								'css' => $deliveryCss,
								'id' => $id,
							)
						); ?>
					</ul>

			</div>

		<?php endforeach; ?>
	</div>


</div>
<?php $this->block()->stop() ?>
<?= $this->block()->get( 'checkout/standard/address/delivery' ) ?>
