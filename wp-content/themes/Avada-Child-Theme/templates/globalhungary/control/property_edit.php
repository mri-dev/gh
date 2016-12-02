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

  $regions = $property->Regions();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <div class="buttons">
      <?php if ( $me->can('property_archive') || (current_user_can('administrator')) ): ?>
        <?php if ( !$property->ArchivingInProgress() ): ?>
            <a href="/control/property_archive/?id=<?=$property->ID()?>" class="btn btn-rounded btn-red"><?=__('Ingatlanhirdetés archiválása', 'gh')?> <i class="fa fa-archive"></i></a>
        <?php endif; ?>
      <?php endif; ?>
    </div>
    <h1><?=__('Ingatlan szerkesztés', 'gh')?></h1>
    <div class="desc"><?=sprintf(__('<strong>%s</strong> ingatlan szerkesztése.', 'gh'), strtoupper($property->Azonosito()))?></div>
  </div>
  <?php if (isset($_GET['saved'])): ?>
    <div class="alert alert-success"><?=__('Változásokat sikeresen mentette.', 'gh')?></div>
  <?php endif; ?>
  <?php if (isset($_GET['archived'])): ?>
    <? if($_GET['archived'] == '100'): ?>
    <div class="alert alert-warning"><?=__('Ön sikeresen elindította az archiválási folyamatot. Hamarosan elbírálásra kerül a kérelme.', 'gh')?></div>
    <? endif; ?>
    <? if($_GET['archived'] == '200'): ?>
    <div class="alert alert-success"><?=__('Ön sikeresen archiválta ezt az ingatlanhirdetést.', 'gh')?></div>
    <? endif; ?>
  <?php endif; ?>
  <?php if ($property->ArchivingInProgress()): $arc_data = $property->ArchivingData(); ?>
    <div class="alert alert-danger">
      <h4><?=__('Archiválási folyamat elindítva ennél az ingatlanhirdetésnél.', 'gh')?></h4>
      <sub>&quot;</sub><em><?=$arc_data->comment?></em><sup>&quot;</sup>
      <div class="">
        <small>@ <?=$arc_data->regDate?></small>
      </div>
    </div>
  <?php endif; ?>
  <? if(!current_user_can('property_edit')): ?>
  <div class="alert alert-danger"><?=__('Ön nem jogosult ingatlan szerkesztésre. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh')?></div>
  <? elseif( $denied_to_edit ): ?>
  <div class="alert alert-danger"><?=sprintf(__('Ön nem jogosult a(z) %s számú ingatlan szerkesztésére. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh'), $property->Azonosito())?></div>
  <? else: ?>
  <form class="wide-form" action="/control/property_save" method="post" enctype="multipart/form-data">
    <input type="hidden" name="property_id" value="<?=$property->ID()?>">
    <input type="hidden" name="post_date" value="<?=$property->CreateAt()?>">
    <h3><?=__('Művelet végrehajtás', 'gh')?></h3>
    <div class="row">

      <div class="col-md-3">
        <label for="post_status"><?=__('Státusz', 'gh')?></label>

        <select class="form-control" name="post_status" id="post_status" <?=(!$me->can('property_edit_status') && !current_user_can('administrator')) ? 'disabled="disabled"' : ''?>>
          <option value="publish" <?=($property->StatusKey() == 'publish')?'selected="selected"':''?>><?=__('Közzétéve (aktív)', 'gh')?></option>
          <option value="draft" <?=($property->StatusKey() == 'draft')?'selected="selected"':''?>><?=__('Vázlat (inaktív)', 'gh')?></option>
        </select>
        <input type="hidden" name="pre[post_status]" value="<?=$property->StatusKey()?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label for=""><?=__('Kizárólagos hirdetés', 'gh')?></label>
        <input type="checkbox" id="_listing_flag_exclusive" name="meta_input[_listing_flag_exclusive]" <?=($property->isExclusive())?'checked="checked"':''?> value="<?=($property->isExclusive())?1:0?>"><label class="fm" for="_listing_flag_exclusive"></label>
        <input type="hidden" name="pre[meta_input][_listing_flag_exclusive]" value="<?=($property->isHighlighted())?1:0?>" class="form-control">
        <input type="hidden" name="metacheckboxes[_listing_flag_exclusive]" value="1">
      </div>
      <?php if ( current_user_can('region_manager') || current_user_can('administrator') ): ?>
      <div class="col-md-3">
        <label for=""><?=__('Kiemelt hirdetés', 'gh')?></label>
        <input type="checkbox" id="_listing_flag_highlight" name="meta_input[_listing_flag_highlight]" <?=($property->isHighlighted())?'checked="checked"':''?> value="<?=($property->isHighlighted())?1:0?>"><label class="fm" for="_listing_flag_highlight"></label>
        <input type="hidden" name="pre[meta_input][_listing_flag_highlight]" value="<?=($property->isHighlighted())?1:0?>" class="form-control">
        <input type="hidden" name="metacheckboxes[_listing_flag_highlight]" value="1">
      </div>
      <?php endif; ?>
    </div>
    <h3><?=__('Alapadatok', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12 reqf">
        <label for="post_title"><?=__('Ingatlan cím (SEO)', 'gh')?></label>
        <input type="text" id="post_title" name="post_title" value="<?=$property->Title()?>" class="form-control">
        <input type="hidden" name="pre[post_title]" value="<?=$property->Title()?>" class="form-control">
        <small class="inputhint"><?=__('Pl.: Újépítésű 120 nm-es 4 szobás családi ház Pécs szívében.', 'gh')?></small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan státusza', 'gh')?></label>
        <? $control->getTaxonomySelects( 'status', $property->StatusID() ); ?>
        <input type="hidden" name="pre[tax][status]" value="<?=$property->StatusID()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan kategória', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-types', $property->CatID() ); ?>
        <input type="hidden" name="pre[tax][property-types]" value="<?=$property->CatID()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan állapota', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-condition', $property->ConditionID() ); ?>
        <input type="hidden" name="pre[tax][property-condition]" value="<?=$property->ConditionID()?>">
      </div>
      <div class="col-md-3 reqf">
        <label for=""><?=__('Fűtés típusa', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-heating', $property->HeatingID() ); ?>
        <input type="hidden" name="pre[tax][property-heating]" value="<?=$property->HeatingID()?>">
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <?
          $parea = end($regions);
        ?>
        <label for=""><?=__('Hirdetés régió', 'gh')?></label>
        <div class="noinp-data"><?=$regions[0]->name?></div>

      </div>
      <div class="col-md-4">
        <label for="tax_locations"><?=__('Város', 'gh')?></label>
        <? wp_dropdown_categories(array(
          'show_option_all' => __('-- válasszon --', 'gh'),
          'taxonomy' => 'locations',
          'hide_empty' => 0,
          'name' => 'tax[locations]',
          'id' => 'tax_locations',
          'parent' => $parea->parent,
          'orderby' => 'name',
          'selected' => $parea->term_id
        )); ?>
        <input type="hidden" name="pre[tax][locations]" value="<?=$parea->term_id?>">
      </div>
      <div class="col-md-6 reqf">
        <label for="_listing_address"><?=__('Pontos cím (utca, házszám, stb)', 'gh')?></label>
        <input type="text" id="_listing_address" name="meta_input[_listing_address]" value="<?=$property->Address()?>" class="form-control">
        <input type="hidden" name="pre[meta_input][_listing_address]" value="<?=$property->Address()?>">
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for="_listing_price"><?=__('Irányár (Ft)', 'gh')?></label>
        <input type="number" min="0" id="_listing_price" name="meta_input[_listing_price]" value="<?=$property->Price()?>" class="form-control" <?=(!$me->can('property_edit_price') && !current_user_can('administrator')) ? 'readonly="readonly"' : ''?>>
        <input type="hidden" name="pre[meta_input][_listing_price]" value="<?=$property->Price()?>">
      </div>
      <div class="col-md-3">
        <label for="_listing_offprice"><?=__('Kedvezményes irányár (Ft)', 'gh')?></label>
        <input type="number" min="0" id="_listing_offprice" name="meta_input[_listing_offprice]" value="<?=$property->OffPrice()?>" class="form-control" <?=(!$me->can('property_edit_price') && !current_user_can('administrator')) ? 'readonly="readonly"' : ''?>>
        <input type="hidden" name="pre[meta_input][_listing_offprice]" value="<?=$property->OffPrice()?>">
      </div>
    </div>
    <h3><?=__('Leírások', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <label for="post_excerpt"><?=__('Rövid ismertető', 'gh')?></label>
        <textarea name="post_excerpt" style="min-height: 100px; font-size: 0.9em;" id="post_excerpt" class="form-control"><?=$property->ShortDesc()?></textarea>
        <input type="hidden" name="pre[post_excerpt]" value="<?=$property->ShortDesc()?>">
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <label for="post_content"><?=__('Ingatlan részletes leírása', 'gh')?></label>
        <?php wp_editor( $property->Description(), 'post_content' ); ?>
        <input type="hidden" name="pre[post_content]" value="<?=$property->Description()?>">
      </div>
    </div>
    <h3><?=__('Paraméterek', 'gh')?></h3>
    <div class="row">
      <? $metai = 0; foreach($params as $title => $meta): $metai++; $value = $property->getMetaValue($meta); if( in_array($meta, array('_listing_archive_text','_listing_archive_who')) ){ continue; }?>
      <div class="col-md-4">
        <label for="<?=$meta?>"><?=$title?></label>
        <input type="text" id="<?=$meta?>" name="meta_input[<?=$meta?>]" value="<?=$value?>" class="form-control">
        <input type="hidden" name="pre[meta_input][<?=$meta?>]" value="<?=$value?>">
      </div>
      <? if($metai%3 === 0): ?></div><div class="row"><? endif; ?>
      <? endforeach; ?>
    </div>
    <h3><?=__('Egyéb opciók', 'gh')?></h3>
    <div class="row">
      <? $metai = 0; foreach($flags as $title => $meta): $metai++; $value = $property->getMetaCheckbox($meta); ?>
      <input type="hidden" name="metacheckboxes[<?=$meta?>]" value="1">
      <div class="col-md-3 boxed-labels">
        <input type="checkbox" id="<?=$meta?>" name="meta_input[<?=$meta?>]" <?=($value == 1)?'checked="checked"':''?> value="<?=$value?>" class="form-control"><label for="<?=$meta?>"><?=$title?></label>
        <input type="hidden" name="pre[meta_input][<?=$meta?>]" value="<?=$value?>">
      </div>
      <? if($metai%4 === 0): ?></div><div class="row"><? endif; ?>
      <? endforeach; ?>
    </div>
    <h3><?=__('Képek', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <label for="property_images"><?=__('Képek tallózása', 'gh')?></label>
        <input type="file" multiple="multiple" name="property_images[]" id="property_images" value="" class="form-control">
      </div>
    </div>
    <?
      $images = $property->Images();
      ob_start();
      include(locate_template('/templates/parts/property_images_editor.php'));
      ob_end_flush();
    ?>
    <?
      $gps = $property->GPS();
    ?>
    <h3><?=__('Ingatlan jelölése térképen', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <?
          ob_start();
          include(locate_template('/templates/parts/map_gps_picker.php'));
          ob_end_flush();
        ?>
      </div>
    </div>
    <?php if(!current_user_can('administrator')): ?>
      <input type="hidden" name="post_author" value="<?=get_current_user_id()?>">
    <?php else: ?>
      <h3><?=__('Ingatlan referense', 'gh')?></h3>
      <div class="row">
        <div class="col-md-12">
          <label for="post_author"></label>
          <?php
            wp_dropdown_users(array(
              'name' => 'post_author',
              'selected' => $property->AuthorID()
            ));
          ?>
        </div>
      </div>
    <?php endif; ?>
    <input type="hidden" name="pre[post_author]" value="<?=$property->AuthorID()?>">
    <div class="submit-property">
      <input type="hidden" name="_nonce" value="<?=wp_create_nonce('property-create')?>">
      <button type="submit" name="createProperty" value="1"><?php echo __('Változások mentése', 'gh'); ?> <i class="fa fa-save"></i></button>
    </div>
  </form>
  <? endif; ?>
</div>
