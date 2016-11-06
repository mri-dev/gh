<?php
  global $me;
  $control  = get_control_controller('property_create');
  $params = $control->getPropertyParams('col2');
  $flags  = $control->getPropertyParams('checkbox');

  $editor   = get_control_controller('property_edit');
  $property = $editor->load($_GET['id']);

  $denied_to_edit = true;

  if ( $property->AuthorID() == $me->ID() ) {
    $denied_to_edit = false;
  } else {
    if (current_user_can('administrator') || current_user_can('region_manager') ) {
      $denied_to_edit = false;
    }
  }

?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan szerkesztés', 'gh')?></h1>
    <div class="desc"><?=sprintf(__('<strong>%s</strong> ingatlan szerkesztése.', 'gh'), strtoupper($property->Azonosito()))?></div>
  </div>
  <? if(!current_user_can('property_edit')): ?>
  <div class="alert alert-danger"><?=__('Ön nem jogosult ingatlan szerkesztésre. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh')?></div>
  <? elseif( $denied_to_edit ): ?>
  <div class="alert alert-danger"><?=sprintf(__('Ön nem jogosult a(z) %s számú ingatlan szerkesztésére. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh'), $property->Azonosito())?></div>
  <? else: ?>
  <form class="wide-form" action="/control/property_save" method="post">
    <input type="hidden" name="property_id" value="0">
    <h3><?=__('Alapadatok', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12 reqf">
        <label for="post_title"><?=__('Ingatlan cím (SEO)', 'gh')?></label>
        <input type="text" id="post_title" name="post_title" value="<?=$property->Title()?>" class="form-control">
        <input type="hidden" name="post_title_pre" value="<?=$property->Title()?>" class="form-control">
        <small class="inputhint"><?=__('Pl.: Újépítésű 120 nm-es 4 szobás családi ház Pécs szívében.', 'gh')?></small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan státusza', 'gh')?></label>
        <? $control->getTaxonomySelects( 'status', $property->StatusID() ); ?>
        <input type="hidden" name="tax_status_pre" value="<?=$property->StatusID()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan kategória', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-types', $property->CatID() ); ?>
        <input type="hidden" name="tax_status_pre" value="<?=$property->CatID()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan állapota', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-condition', $property->ConditionID() ); ?>
        <input type="hidden" name="tax_status_pre" value="<?=$property->ConditionID()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for="_listing_idnumber"><?=__('Azonosító', 'gh')?></label>
        <input type="text" id="_listing_idnumber" name="meta_input[_listing_idnumber]" value="<?=$property->Azonosito()?>" class="form-control">
        <input type="hidden" name="meta_listing_idnumber_pre" value="<?=$property->Azonosito()?>">
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <label for=""><?=__('Hirdetés régió', 'gh')?></label>
        <div class="noinp-data"><?=$me->RegionName()?></div>
        <input type="hidden" name="tax[locations]" value="<?=$me->RegionID()?>">
      </div>
      <div class="col-md-7 reqf">
        <label for="_listing_address"><?=__('Pontos cím (utca, házszám, stb)', 'gh')?></label>
        <input type="text" id="_listing_address" name="meta_input[_listing_address]" value="<?=$property->Address()?>" class="form-control">
        <input type="hidden" name="meta_listing_address_pre" value="<?=$property->Address()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for="_listing_price"><?=__('Irányár (Ft)', 'gh')?></label>
        <input type="number" min="0" id="_listing_price" name="meta_input[_listing_price]" value="<?=$property->Price()?>" class="form-control">
        <input type="hidden" name="meta_listing_price_pre" value="<?=$property->Price()?>">
      </div>
    </div>
    <h3><?=__('Leírások', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <label for="post_excerpt"><?=__('Rövid ismertető', 'gh')?></label>
        <textarea name="post_excerpt" style="min-height: 100px; font-size: 0.9em;" id="post_excerpt" class="form-control"><?=$property->ShortDesc()?></textarea>
        <input type="hidden" name="post_excerpts_pre" value="<?=$property->ShortDesc()?>">
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <label for="post_content"><?=__('Ingatlan részletes leírása', 'gh')?></label>
        <?php wp_editor( $property->Description(), 'post_content' ); ?>
        <input type="hidden" name="post_content_pre" value="<?=$property->Description()?>">
      </div>
    </div>
    <h3><?=__('Paraméterek', 'gh')?></h3>
    <div class="row">
      <? $metai = 0; foreach($params as $title => $meta): $metai++; $value = $property->getMetaValue($meta); ?>
      <div class="col-md-4">
        <label for="<?=$meta?>"><?=$title?></label>
        <input type="text" id="<?=$meta?>" name="meta_input[<?=$meta?>]" value="<?=$value?>" class="form-control">
        <input type="hidden" name="meta<?=$meta?>_pre" value="<?=$value?>">
      </div>
      <? if($metai%3 === 0): ?></div><div class="row"><? endif; ?>
      <? endforeach; ?>
    </div>
    <h3><?=__('Egyéb opciók', 'gh')?></h3>
    <div class="row">
      <? $metai = 0; foreach($flags as $title => $meta): $metai++; $value = $property->getMetaCheckbox($meta);?>
      <div class="col-md-3 boxed-labels">
        <input type="checkbox" id="<?=$meta?>" name="meta_input[<?=$meta?>]" <?=($value == 1)?'checked="checked"':''?> value="<?=$value?>" class="form-control"><label for="<?=$meta?>"><?=$title?></label>
        <input type="hidden" name="meta<?=$meta?>_pre" value="<?=$value?>">
      </div>
      <? if($metai%4 === 0): ?></div><div class="row"><? endif; ?>
      <? endforeach; ?>
    </div>
    <h3><?=__('Képek', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <label for="property_images"><?=__('Képek tallózása', 'gh')?></label>
        <input type="file" name="property_images[]" id="property_images" value="" class="form-control">
      </div>
    </div>
    <?php if(current_user_can('reference_manager')): ?>
      <input type="hidden" name="property_author" value="<?=get_current_user_id()?>">
    <?php else: ?>
      <h3><?=__('Ingatlan referense', 'gh')?></h3>
      <div class="row">
        <div class="col-md-12">
          <label for="property_author"></label>
          <?php wp_dropdown_users(array('name' => 'post_author')); ?>
        </div>
      </div>
    <?php endif; ?>
    <div class="submit-property">
      <div class="allowvalidate">
        <input type="checkbox" name="valid-datas" id="valid-datas" value="1"> <label for="valid-datas"><?php echo __('Kijelentem, hogy a fent közzétett adatok valósak.', 'gh'); ?></label>
      </div>
      <input type="hidden" name="_nonce" value="<?=wp_create_nonce('property-create')?>">
      <button type="submit" name="createProperty" value="1"><?php echo __('Ingatlanhirdetés rögzítése', 'gh'); ?> <i class="fa fa-file-text-o"></i></button>
    </div>
  </form>
  <? endif; ?>
</div>
