<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @copyright Ulrich Diehl, 2020
 */

$enc = $this->encoder();
$params = $this->param();

$manufacturer = $this->get( 'treeCatalogPath', map() )->slice( 1, 1 )->first();
$group = $this->get( 'treeCatalogPath', map() )->slice( 2, 1 )->first();


?>
<?php $this->block()->start( 'catalog/filter/tree' ); ?>
<section>

	<div class="dropdown hersteller">
		<button class="btn btn-secondary dropdown-toggle" type="button"
				id="dd-hersteller" data-toggle="dropdown" aria-expanded="false">
      <span class="hersteller-name">
        <?= $enc->html( $manufacturer ? $manufacturer->getName() : $this->translate( 'client', 'Such nach Hersteller' ) ) ?>
      </span>
		</button>
		<ul class="dropdown-menu" aria-labelledby="dd-hersteller">
            <?php foreach( $this->treeCatalogTree->getChildren() as $hersteller ) : ?>
				<li>
					<a class="dropdown-item" data-id="gruppe-<?= $enc->attr( $hersteller->getId() ) ?>" href="#">
                        <?= $enc->html( $hersteller->getName() ) ?>
					</a>
				</li>
            <?php endforeach; ?>
		</ul>
	</div>

    <?php foreach( $this->treeCatalogTree->getChildren() as $hersteller ) : ?>
		<div class="dropdown dd-gruppe <?= $manufacturer && $manufacturer->getId() == $hersteller->getId() ? '' : 'hidden' ?>"
			 id="gruppe-<?= $enc->attr( $hersteller->getId() ) ?>">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="dd-hersteller-<?= $enc->attr( $hersteller->getId() ) ?>"
					data-toggle="dropdown" aria-expanded="false">
        <span class="gruppe-name">
          <?= $enc->html( $group ? $group->getName() : $this->translate( 'client', 'Modell' ) ) ?>
        </span>
			</button>
			<ul class="dropdown-menu" aria-labelledby="dd-hersteller-<?= $enc->attr( $hersteller->getId() ) ?>">
                <?php foreach( $hersteller->getChildren() as $gruppe ) : ?>
					<li>
						<a class="dropdown-item <?= $enc->attr( $gruppe->getCode() ) ?>"
						   href="<?= $enc->attr( $this->link( 'client/html/catalog/lists/url', ['f_catid' => $gruppe->getId()] + $this->get( 'treeFilterParams', [] ) ) ) ?>">
                            <?= $enc->html( $gruppe->getName() ) ?>
						</a>
					</li>
                <?php endforeach; ?>
			</ul>
		</div>
    <?php endforeach; ?>

</section>
<?php $this->block()->stop(); ?>
<?= $this->block()->get( 'catalog/filter/tree' ); ?>
