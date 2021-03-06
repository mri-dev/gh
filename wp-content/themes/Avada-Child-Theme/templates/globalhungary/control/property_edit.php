<?php
  global $me, $app_languages;
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
    <input type="hidden" name="return" value="<?=base64_decode($_GET['return'])?>">
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
      <div class="col-md-2">
        <label for=""><?=__('Kizárólagos', 'gh')?></label>
        <input type="checkbox" id="_listing_flag_exclusive" name="meta_input[_listing_flag_exclusive]" <?=($property->isExclusive())?'checked="checked"':''?> value="<?=($property->isExclusive())?1:0?>"><label class="fm" for="_listing_flag_exclusive"></label>
        <input type="hidden" name="pre[meta_input][_listing_flag_exclusive]" value="<?=($property->isExclusive())?1:0?>" class="form-control">
        <input type="hidden" name="metacheckboxes[_listing_flag_exclusive]" value="1">
      </div>
      <?php if ( current_user_can('region_manager') || current_user_can('administrator') ): ?>
      <div class="col-md-2">
        <label for=""><?=__('Kiemelt', 'gh')?></label>
        <input type="checkbox" id="_listing_flag_highlight" name="meta_input[_listing_flag_highlight]" <?=($property->isHighlighted())?'checked="checked"':''?> value="<?=($property->isHighlighted())?1:0?>"><label class="fm" for="_listing_flag_highlight"></label>
        <input type="hidden" name="pre[meta_input][_listing_flag_highlight]" value="<?=($property->isHighlighted())?1:0?>" class="form-control">
        <input type="hidden" name="metacheckboxes[_listing_flag_highlight]" value="1">
      </div>
      <?php endif; ?>
      <?php if ( current_user_can('administrator') ): ?>
      <div class="col-md-2">
        <label for=""><?=__('Prémium', 'gh')?> <i title="<?=__('A hirdetés megjelenik a prémium oldalon és a sima oldalon is.','gh')?>" class="fa fa-info-circle"></i></label>
        <input type="checkbox" id="_listing_premium" name="meta_input[_listing_premium]" <?=($property->isPremium())?'checked="checked"':''?> value="<?=($property->isPremium())?1:0?>"><label class="fm" for="_listing_premium"></label>
        <input type="hidden" name="pre[meta_input][_listing_premium]" value="<?=($property->isPremium())?1:0?>" class="form-control">
        <input type="hidden" name="metacheckboxes[_listing_premium]" value="1">
      </div>
      <div class="col-md-2">
        <label for=""><?=__('Csak Prémium', 'gh')?> <i title="<?=__('A prémium hirdetés csak a prémium verziónál jelenik meg kizárólag.','gh')?>" class="fa fa-info-circle"></i></label>
        <input type="checkbox" id="_listing_premium_only" name="meta_input[_listing_premium_only]" <?=($property->isPremiumOnly())?'checked="checked"':''?> value="<?=($property->isPremiumOnly())?1:0?>"><label class="fm" for="_listing_premium_only"></label>
        <input type="hidden" name="pre[meta_input][_listing_premium_only]" value="<?=($property->isPremiumOnly())?1:0?>" class="form-control">
        <input type="hidden" name="metacheckboxes[_listing_premium_only]" value="1">
      </div>
      <?php endif; ?>
    </div>

    <div class="panel-switcher">
      <ul>
        <?php foreach ( $app_languages as $id => $lang ):  if(!$lang['avaiable']) continue;  ?>
          <li class="<?=($lang['code'] == 'hu_HU') ? 'active' : ''?>" data-lang="<?php echo $lang['code']; ?>"> <img src="<?=IMG?>/flags/<?=$lang['code']?>.png" style="height: 16px;" alt="<?php echo $lang['name']; ?>"> <?php echo $lang['name']; ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <!-- lang hu_HU panel -->
    <div class="lang-panel active" data-lang="hu_HU">

    <div class="row">
      <div class="col-md-12">
        <?php $value = ($property->isHUOffline()) ? 1 : 0; ?>
        <input type="hidden" name="metacheckboxes[_listing_flag_hu_off]" value="1">
        <input type="checkbox" id="_listing_flag_hu_off" name="meta_input[_listing_flag_hu_off]" value="<?=$value?>" class="form-control" <?=($value == 1)?'checked="checked"':''?>><label for="_listing_flag_hu_off"><?=__('Hirdetés ne legyen látható a magyar nyelvvű oldalon.', 'gh')?></label>
        <input type="hidden" name="pre[meta_input][_listing_flag_hu_off]" value="<?=$value?>">
      </div>
    </div>

    <h3><?=__('Alapadatok', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12 reqf">
        <label for="post_title"><?=__('Ingatlan cím (SEO)', 'gh')?></label>
        <input type="text" id="post_title" name="post_title" value="<?=$property->Title()?>" maxlength="60" class="form-control">
        <input type="hidden" name="pre[post_title]" value="<?=$property->Title()?>" class="form-control">
        <small class="inputhint"><?=__('Pl.: Újépítésű 120 nm-es 4 szobás családi ház Pécs szívében.', 'gh')?></small>
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for=""><?=__('Ingatlan státusza', 'gh')?></label>
        <?php
          $status_ids = $property->StatusID();
        ?>
        <? $control->getTaxonomySelects( 'status', $status_ids[0] ); ?>
        <input type="hidden" name="pre[tax][status]" value="<?=implode(",",$status_ids)?>">
      </div>
      <div class="col-md-3 reqf">
        <?php
          $cat_ids = (array)$property->CatID();
        ?>
        <label for="kategoria_multiselect_text"><?=__('Ingatlan kategória', 'gh')?></label>
        <div class="tglwatcher-wrapper tgl-def">
          <input type="text" readonly="readonly" id="kategoria_multiselect_text" class="form-control tglwatcher" tglwatcher="kategoria_multiselect" placeholder="<?=__('-- válasszon --', 'gh')?>" value="">
        </div>
        <input type="hidden" id="kategoria_multiselect_ids" name="tax[property-types]" value="<?=implode(",",$cat_ids)?>">
        <div class="multi-selector-holder" tglwatcherkey="kategoria_multiselect" id="kategoria_multiselect">
          <div class="selector-wrapper sel-def">
            <? $kategoria = $control->getSelectors( 'property-types', $cat_ids, array('hide_empty' => false)  ); ?>
            <?php if ($kategoria): ?>
              <?php foreach ($kategoria as $k): ?>
              <div class="selector-row lvl-0">
                <input type="checkbox" <?=(in_array($k->term_id, $cat_ids))?'checked="checked"':''?> tglwatcherkey="kategoria_multiselect" data-parentid="<?=$k->parent?>" data-lvl="0" htxt="<?=$k->name?>" id="kat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="kat_<?=$k->term_id?>"><?=$k->name?> <span class="n">(<?=$k->count?>)</span></label>
              </div>
              <?php if ( !empty($k->children) ): ?>
                <?php foreach ($k->children as $sk): ?>
                <div class="selector-row lvl-1">
                  <input type="checkbox" <?=(in_array($sk->term_id, $cat_ids))?'checked="checked"':''?> tglwatcherkey="kategoria_multiselect" data-parentid="<?=$sk->parent?>" data-lvl="1" htxt="<?=$k->name?> / <?=$sk->name?>" id="kat_<?=$sk->term_id?>" value="<?=$sk->term_id?>"> <label for="kat_<?=$sk->term_id?>"><?=$sk->name?> <span class="n">(<?=$sk->count?>)</span></label>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        <input type="hidden" name="pre[tax][property-types]" value="<?=implode(",",$cat_ids)?>">
      </div>
      <div class="col-md-3 reqf">
        <?php
          $cond_ids = (array)$property->ConditionID();
        ?>
        <label for="allapot_multiselect_text"><?=__('Ingatlan állapot', 'gh')?></label>
        <div class="tglwatcher-wrapper tgl-def">
          <input type="text" readonly="readonly" id="allapot_multiselect_text" class="form-control tglwatcher" tglwatcher="allapot_multiselect" placeholder="<?=__('-- válasszon --', 'gh')?>" value="">
        </div>
        <input type="hidden" id="allapot_multiselect_ids" name="tax[property-condition]" value="<?=implode(",",$cond_ids)?>">
        <div class="multi-selector-holder" tglwatcherkey="allapot_multiselect" id="allapot_multiselect">
          <div class="selector-wrapper sel-def">
            <? $kategoria = $control->getSelectors( 'property-condition', $cond_ids, array('hide_empty' => false) ); ?>
            <?php if ($kategoria): ?>
              <?php foreach ($kategoria as $k): ?>
              <div class="selector-row lvl-0">
                <input type="checkbox" <?=(in_array($k->term_id, $cond_ids))?'checked="checked"':''?>  tglwatcherkey="allapot_multiselect" htxt="<?=$k->name?>" id="kat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="kat_<?=$k->term_id?>"><?=$k->name?> <span class="n">(<?=$k->count?>)</span></label>
              </div>
              <?php if ( !empty($k->children) ): ?>
                <?php foreach ($k->children as $sk): ?>
                <div class="selector-row lvl-1">
                  <input type="checkbox" <?=(in_array($sk->term_id, $cond_ids))?'checked="checked"':''?> tglwatcherkey="allapot_multiselect" data-parentid="<?=$sk->parent?>" data-lvl="1" htxt="<?=$k->name?> / <?=$sk->name?>" id="kat_<?=$sk->term_id?>" value="<?=$sk->term_id?>"> <label for="kat_<?=$sk->term_id?>"><?=$sk->name?> <span class="n">(<?=$sk->count?>)</span></label>
                </div>
                <?php endforeach; ?>
              <?php endif; ?>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
        <input type="hidden" name="pre[tax][property-condition]" value="<?=implode(",",$cond_ids)?>">
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
        <?
        $control->properties->getLocationChilds(
          $regions[0]->term_id,
          array(
            'show_option_all' => __('-- válasszon --', 'gh'),
            'selected' => $parea->term_id,
            'name' => 'tax[locations]',
            'id' => 'tax_locations',
            'hide_empty' => 0
          )
        );
        ?>
        <input type="hidden" name="pre[tax][locations]" value="<?=$parea->term_id?>">
      </div>
      <div class="col-md-6 reqf">
        <label for="_listing_address"><?=__('Pontos cím (utca, házszám, stb)', 'gh')?></label>
        <input type="text" id="_listing_address" name="meta_input[_listing_address]" value="<?=$parea->name?>, <?=$property->Address()?>" class="form-control">
        <input type="hidden" name="pre[meta_input][_listing_address]" value="<?=$property->Address()?>">
        <input type="hidden" id="_listing_address_remove" name="_listing_address_remove" value="<?=$parea->name?>, ">
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for="_listing_flag_pricetype"><?=__('Ár jellege', 'gh')?></label>
        <select class="form-control" name="meta_input[_listing_flag_pricetype]" id="_listing_flag_pricetype">
          <option value="" selected="selected"><?=__('-- válasszon --', 'gh')?></option>
          <option value="" disabled="disabled"></option>
          <?php foreach ($property->price_types as $pt_key => $pt_i): ?>
            <option value="<?=$pt_i?>" <?=($property->getMetaValue('_listing_flag_pricetype') == $pt_i)?'selected="selected"':''?>><?=$property->getPriceTypeText($pt_i)?></option>
          <?php endforeach; ?>
        </select>
        <input type="hidden" name="pre[meta_input][_listing_flag_pricetype]" value="<?=$property->getMetaValue('_listing_flag_pricetype')?>">
      </div>
      <div class="col-md-3 reqf">
        <label for="_listing_price"><?=__('Irányár', 'gh')?></label>
        <input type="text" id="_listing_price" name="meta_input[_listing_price]" value="<?=$property->OriginalPrice()?>" class="form-control pricebind" <?=(!$me->can('property_edit_price') && !current_user_can('administrator')) ? 'readonly="readonly"' : ''?>>
        <input type="hidden" name="pre[meta_input][_listing_price]" value="<?=$property->OriginalPrice()?>">
      </div>
      <div class="col-md-3">
        <label for="_listing_offprice"><?=__('Kedvezményes irányár', 'gh')?></label>
        <input type="text" id="_listing_offprice" name="meta_input[_listing_offprice]" value="<?=$property->OffPrice()?>" class="form-control pricebind" <?=(!$me->can('property_edit_price') && !current_user_can('administrator')) ? 'readonly="readonly"' : ''?>>
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
        <textarea name="pre[post_content]" class="hide"><?=$property->Description()?></textarea>
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
        <div style="color: #53a54f;">
          <small><?=__('A képek automatikusan méretezve lesznek. Méretezés után a képek maximális értékei: 1200 x 1200 pixel.', 'gh')?></small>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <small><input type="checkbox" id="image_watermark" checked="checked" name="image_watermark" value="1"> <label for="image_watermark"><?=__('Képek automatikus vízjelezése', 'gh')?></label></small>
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

      // PDF DOCUMENTUMOK
      $docs = $property->PDFDocuments();
      ob_start();
      include(locate_template('/templates/parts/property_pdf_edit.php'));
      ob_end_flush();
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

    </div>
    <!-- END: lang hu_HU panel -->

    <?php foreach ((array)$app_languages as $id => $lang): if($lang['code'] == 'hu_HU') continue; if(!$lang['avaiable']) continue;  ?>
    <!-- END: lang <?php echo $lang['code']; ?> panel -->
    <div class="lang-panel" data-lang="<?php echo $lang['code']; ?>">
      <?php $lang_param = $property->getLangMetalist($lang['meta_prefix']); ?>
      <?php foreach ($lang_param as $meta => $langp): $value = $langp['value']; $title = $langp['name']; ?>
        <div class="row">
          <div class="col-md-12">
            <?php if ($langp['holdertype'] != 'checkbox'): ?>
              <label for="<?=$meta?>"><?=$title?></label>
            <?php endif; ?>
            <?php switch($langp['holdertype']){
              case 'checkbox':
              ?>
              <input type="hidden" name="metacheckboxes[<?=$meta?>]" value="1">
              <input type="checkbox" id="<?=$meta?>" name="meta_input[<?=$meta?>]" value="<?=$value?>" class="form-control" <?=($value == 1)?'checked="checked"':''?>><label for="<?=$meta?>"><?=$title?></label>
              <input type="hidden" name="pre[meta_input][<?=$meta?>]" value="<?=$value?>">
              <?
              break;
              case 'textfield':
              ?>
              <input type="text" id="<?=$meta?>" name="meta_input[<?=$meta?>]" value="<?=$value?>" class="form-control">
              <input type="hidden" name="pre[meta_input][<?=$meta?>]" value="<?=$value?>">
              <?
              break;
              case 'textarea':
              ?>
              <textarea name="meta_input[<?=$meta?>]" style="height: 80px;" class="form-control" id="<?=$meta?>"><?=$value?></textarea>
              <input type="hidden" name="pre[meta_input][<?=$meta?>]" value="<?=$value?>">
              <?
              break;
              case 'editor':
              ?>
              <br><br>
              <?php wp_editor($value, $meta, array('textarea_name' => "meta_input[".$meta."]")); ?>
              <textarea name="pre[meta_input][<?=$meta?>]" class="hide"><?=$value?></textarea>
              <?
              break;
            } ?>
            <?php if ($langp['hint'] != ''): ?>
              <small class="inputhint"><?php echo $langp['hint']; ?></small>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <!-- END: lang <?php echo $lang['code']; ?> panel -->
    <?php endforeach; ?>

    <input type="hidden" name="pre[post_author]" value="<?=$property->AuthorID()?>">
    <div class="submit-property">
      <input type="hidden" name="_nonce" value="<?=wp_create_nonce('property-create')?>">
      <button type="submit" name="createProperty" value="1"><?php echo __('Változások mentése', 'gh'); ?> <i class="fa fa-save"></i></button>
    </div>
  </form>
  <? endif; ?>
</div>
<script type="text/javascript">
  (function($)
  {
    var autocomplete;
    collect_checkbox('kategoria_multiselect', true);
    collect_checkbox('allapot_multiselect', true);

    google.maps.event.addDomListener(window, 'load', placeAutocomplate);

    function placeAutocomplate() {
      autocomplete = new google.maps.places.Autocomplete(
         (document.getElementById('_listing_address')),
         {
           types: ['address'],
           componentRestrictions: {
             country: 'hu'
           }
         }
      );
    }

    $('.panel-switcher ul li[data-lang]').click(function(){
      var is_active = $(this).hasClass('active');
      var lang =  $(this).data('lang');

      if ( !is_active ) {
        $('.panel-switcher ul li[data-lang].active').removeClass('active');
        $(this).addClass('active');

        $('.lang-panel[data-lang]').removeClass('active');
        $('.lang-panel[data-lang=\''+lang+'\']').addClass('active');
      }

    });

    $('#tax_locations').on('change', function()
    {
      var city = $("#tax_locations option[value='"+$("#tax_locations").val()+"']").text();

      $('#_listing_address').val(city+", ").focus();
      $('#_listing_address_remove').val(city+", ");
    });

    $('#_listing_address, #tax_locations').on('change', function(){
      var qryaddr = 'Magyarország';
      var v = $('#_listing_address').val();
      var c = $('#tax_locations option:selected').text();
      if (typeof c !== 'undefined') {
        qryaddr += ', '+c;
      }
      qryaddr += ', '+v;
      var geo = new google.maps.Geocoder();

      geo.geocode({ address: qryaddr }, function(r,s){
        if (s == 'OK') {
          var center = r[0].geometry.location;
          setGPSMarker(center, r[0].formatted_address);
        }
      });
    });

    $(window).click(function() {
      if (!$(event.target).closest('.toggler-opener').length) {
        $('.toggler-opener').removeClass('opened toggler-opener');
        $('.tglwatcher.toggled').removeClass('toggled');
      }
    });

    $('.tglwatcher').click(function(event){
      event.stopPropagation();
      event.preventDefault();
      var e = $(this);
      var target_id = e.attr('tglwatcher');
      var opened = e.hasClass('toggled');

      if(opened) {
        e.removeClass('toggled');
        $('#'+target_id).removeClass('opened toggler-opener');
      } else {
        e.addClass('toggled');
        $('#'+target_id).addClass('opened toggler-opener');
      }
    });

    $('.multi-selector-holder input[type=checkbox]').change(function()
    {
      var e = $(this);
      var checkin = $(this).is(':checked');
      var tkey = e.attr('tglwatcherkey');
      var selected = collect_checkbox(tkey, false);
      $('#'+tkey+'_ids').val(selected);
    });
    $('.pricebind').bind("keyup", function(event) {
       if(event.which >= 37 && event.which <= 40){
        event.preventDefault();
       }
       var $this = $(this);
       var num = $this.val().replace(/\./gi, "");
       var num2 = num.split(/(?=(?:\d{3})+$)/).join(".");
       $this.val(num2);
    });
    $('.pricebind').each(function(event) {
       if(event.which >= 37 && event.which <= 40){
        event.preventDefault();
       }
       var $this = $(this);
       var num = $this.val().replace(/\./gi, "");
       var num2 = num.split(/(?=(?:\d{3})+$)/).join(".");
       $this.val(num2);
    });

  })(jQuery);

  function collect_checkbox(rkey, loader)
  {
    var arr = [];
    var str = [];
    var seln = 0;

    jQuery('#'+rkey+' input[type=checkbox]').each(function(e,i)
    {
      if(jQuery(this).is(':checked') && !jQuery(this).is(':disabled')){
        seln++;
        arr.push(jQuery(this).val());
        str.push(jQuery(this).attr('htxt'));
      }

      if(loader) {
        var e = jQuery(this);
        var has_child = jQuery(this).hasClass('has-childs');
        var checkin = jQuery(this).is(':checked');
        var lvl = e.data('lvl');
        var parent = e.data('parentid');

        var cnt_child = jQuery('#'+rkey+' .childof'+parent+' input[type=checkbox]:checked').length;

        if(cnt_child == 0) {
          jQuery('#'+rkey+' .zone'+parent+' input[type=checkbox]').prop('disabled', false);
        } else {
          jQuery('#'+rkey+' .childof'+parent).addClass('show');
          jQuery('#'+rkey+' .zone'+parent+' input[type=checkbox]').prop('checked', true).prop('disabled', true);
        }
      }
    });

    if(seln <= 3 ){
      jQuery('#'+rkey+'_text').val(str.join(", "));
    } else {
      jQuery('#'+rkey+'_text').val(seln + " <?=__('kiválasztva', 'gh')?>");
    }

    return arr.join(",");
  }
</script>
