<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @copyright Ulrich Diehl, 2020
 */

$enc = $this->encoder();
$params = $this->param();

$listTarget = $this->config( 'client/html/catalog/lists/url/target' );
$listController = $this->config( 'client/html/catalog/lists/url/controller', 'catalog' );
$listAction = $this->config( 'client/html/catalog/lists/url/action', 'list' );
$listConfig = $this->config( 'client/html/catalog/lists/url/config', [] );


?>
<?php $this->block()->start( 'catalog/filter/tree' ); ?>
<section>

  <div class="dropdown">
    <button class="btn btn-secondary dropdown-toggle" type="button" id="dd-hersteller" data-toggle="dropdown" aria-expanded="false">
    <span class="hersteller-name">Hersteller</span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dd-hersteller">
      <?php foreach( $this->treeCatalogTree->getChildren() as $hersteller ) : ?>
        <li><span class="dropdown-item <?= $enc->attr( $hersteller->getCode() ) ?>" data-id="gruppe-<?= $enc->attr( $hersteller->getId() ) ?>"><?= $enc->html( $hersteller->getName() ) ?></span></li>
      <?php endforeach; ?>
    </ul>
  </div>

  <?php foreach( $this->treeCatalogTree->getChildren() as $hersteller ) : ?>
    <div class="dropdown dd-gruppe" id="gruppe-<?= $enc->attr( $hersteller->getId() ) ?>">
      <button class="btn btn-secondary dropdown-toggle" type="button" id="dd-hersteller-<?= $enc->attr( $hersteller->getId() ) ?>" data-toggle="dropdown" aria-expanded="false">
        <span class="gruppe-name"><?= $enc->html( $hersteller->getName() ) ?></span>
      </button>
      <ul class="dropdown-menu" aria-labelledby="dd-hersteller-<?= $enc->attr( $hersteller->getId() ) ?>">
        <?php foreach( $hersteller->getChildren() as $gruppe ) : ?>
          <li>
            <a class="dropdown-item <?= $enc->attr( $gruppe->getCode() ) ?>"
              href="<?= $enc->attr( $this->link( 'client/html/catalog/lists', ['f_catid' => $gruppe->getId()] + $this->get( 'treeFilterParams', [] ) ) ) ?>">
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
