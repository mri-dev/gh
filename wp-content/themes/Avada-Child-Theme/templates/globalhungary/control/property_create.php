<?php
  $control = get_control_controller('property_create');
  $params = $control->getPropertyParams();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan létrehozása', 'gh')?></h1>
    <div class="desc"><?=__('Az alábbi űrlap segítségével létrehozhat egy új ingatlan hirdetést.', 'gh')?></div>
  </div>
  <? if(!current_user_can('property_create')): ?>
  <div class="alert alert-danger"><?=__('Ön nem jogosult ingatlan létrehozására. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh')?></div>
  <? else: ?>
  <form class="wide-form" action="" method="post">
    <h3><?=__('Alapadatok', 'gh')?></h3>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan státusza', 'gh')?></label>
        <? $control->getTaxonomySelects( 'status' ); ?>
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan kategória', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-types' ); ?>
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan állapota', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-condition' ); ?>
      </div>
      <div class="col-md-3 reqf">
        <label for="idnumber"><?=__('Azonosító', 'gh')?></label>
        <input type="text" id="idnumber" name="meta[idnumber]" value="<?=$_POST['meta']['idnumber']?>" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <label for=""><?=__('Hirdetés régió', 'gh')?></label>
        <div class="noinp-data">Budapest</div>
      </div>
      <div class="col-md-7 reqf">
        <label for="address"><?=__('Pontos cím (utca, házszám, stb)', 'gh')?></label>
        <input type="text" id="address" name="meta[address]" value="<?=$_POST['meta']['address']?>" class="form-control">
      </div>
      <div class="col-md-3 reqf">
        <label for="price"><?=__('Irányár (Ft)', 'gh')?></label>
        <input type="number" min="0" id="price" name="meta[price]" value="<?=$_POST['meta']['price']?>" class="form-control">
      </div>
    </div>
    <h3><?=__('Leírás', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <label for="infotext"><?=__('Rövid ismertető', 'gh')?></label>
        <textarea name="meta[infotext]" style="min-height: 100px; font-size: 0.9em;" id="infotext" class="form-control"></textarea>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <label for="post_content"><?=__('Ingatlan részletes leírása', 'gh')?></label>
        <?php wp_editor( $_POST['post_content'], 'post_content' ); ?>
      </div>
    </div>
    <h3><?=__('Paraméterek', 'gh')?></h3>
    <div class="row">
      <? $metai = 0; foreach($params as $title => $meta): $metai++; ?>
      <div class="col-md-4">
        <label for="<?=$meta?>"><?=$title?></label>
        <input type="text" id="<?=$meta?>" name="meta[<?=$meta?>]" value="<?=$_POST['meta'][$meta]?>" class="form-control">
      </div>
      <? if($metai%3 === 0): ?></div><div class="row"><? endif; ?>
      <? endforeach; ?>
    </div>

  </form>
  <? endif; ?>
</div>
