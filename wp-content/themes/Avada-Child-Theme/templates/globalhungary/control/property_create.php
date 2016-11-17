<?php
  global $me;
  $control = get_control_controller('property_create');
  $params = $control->getPropertyParams('col2');
  $flags  = $control->getPropertyParams('checkbox');
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan létrehozása', 'gh')?></h1>
    <div class="desc"><?=__('Az alábbi űrlap segítségével létrehozhat egy új ingatlan hirdetést az Ön régiójában.', 'gh')?></div>
  </div>
  <? if(!current_user_can('property_create')): ?>
  <div class="alert alert-danger"><?=__('Ön nem jogosult ingatlan létrehozására. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh')?></div>
  <? else: ?>
  <form class="wide-form" action="/control/property_save" method="post">
    <input type="hidden" name="property_id" value="0">
    <h3><?=__('Alapadatok', 'gh')?></h3>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for="_listing_idnumber"><?=__('Azonosító', 'gh')?></label>
        <input type="text" id="_listing_idnumber" name="meta_input[_listing_idnumber]" value="<?=$_POST['meta']['_listing_idnumber']?>" class="form-control">
      </div>
      <div class="col-md-9 reqf">
        <label for="post_title"><?=__('Ingatlan cím (SEO)', 'gh')?></label>
        <input type="text" id="post_title" name="post_title" value="<?=$_POST['post_title']?>" class="form-control">
        <small class="inputhint"><?=__('Pl.: Újépítésű 120 nm-es 4 szobás családi ház Pécs szívében.', 'gh')?></small>
      </div>
    </div>
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
        <label for=""><?=__('Fűtés típusa', 'gh')?></label>
        <? $control->getTaxonomySelects( 'property-heating' ); ?>
      </div>
    </div>
    <div class="row">
      <div class="col-md-2">
        <label for=""><?=__('Hirdetés régió', 'gh')?></label>
        <div class="noinp-data"><?=(!$me->RegionID()) ? __('Összes', 'gh'):$me->RegionName()?></div>
      </div>
      <div class="col-md-4">
        <label for="tax_locations"><?=__('Város', 'gh')?></label>
        <? wp_dropdown_categories(array(
          'show_option_all' => __('-- válasszon --', 'gh'),
          'taxonomy' => 'locations',
          'hide_empty' => 0,
          'name' => 'tax[locations]',
          'id' => 'tax_locations',
          'parent' => $me->RegionID(),
          'hierarchical' => 1,
          'orderby' => 'name'
        )); ?>
        <input type="hidden" name="pre[tax][locations]" value="<?=$parea->term_id?>">
        <small class="inputhint"><?=__('Nem találja a várost?', 'gh')?> <a href="#"><?=__('Város hozzáadása', 'gh')?></a></small>
      </div>
      <div class="col-md-6 reqf">
        <label for="_listing_address"><?=__('Pontos cím (utca, házszám, stb)', 'gh')?></label>
        <input type="text" id="_listing_address" name="meta_input[_listing_address]" value="<?=$_POST['meta']['_listing_address']?>" class="form-control">
      </div>
    </div>
    <div class="row">
      <div class="col-md-3 reqf">
        <label for="_listing_price"><?=__('Irányár (Ft)', 'gh')?></label>
        <input type="number" min="0" id="_listing_price" name="meta_input[_listing_price]" value="<?=$_POST['meta']['_listing_price']?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label for="_listing_offprice"><?=__('Akciós irányár (Ft)', 'gh')?></label>
        <input type="number" min="0" id="_listing_offprice" name="meta_input[_listing_offprice]" value="<?=$_POST['meta']['_listing_offprice']?>" class="form-control">
      </div>
    </div>
    <h3><?=__('Leírások', 'gh')?></h3>
    <div class="row">
      <div class="col-md-12">
        <label for="post_excerpt"><?=__('Rövid ismertető', 'gh')?></label>
        <textarea name="post_excerpt" style="min-height: 100px; font-size: 0.9em;" id="post_excerpt" class="form-control"></textarea>
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
        <input type="text" id="<?=$meta?>" name="meta_input[<?=$meta?>]" value="<?=$_POST['meta'][$meta]?>" class="form-control">
      </div>
      <? if($metai%3 === 0): ?></div><div class="row"><? endif; ?>
      <? endforeach; ?>
    </div>
    <h3><?=__('Egyéb opciók', 'gh')?></h3>
    <div class="row">
      <? $metai = 0; foreach($flags as $title => $meta): $metai++; ?>
      <div class="col-md-3 boxed-labels">
        <input type="checkbox" id="<?=$meta?>" name="meta_input[<?=$meta?>]" value="1" class="form-control"><label for="<?=$meta?>"><?=$title?></label>
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
    <h3><?=__('Ingatlan jelölése térképen', 'gh')?></h3>
    <small class="inputhint"><?=__('Kattintson a térképen az ingatlan pozíciójának kiválasztásához. Ha módosítani kívánja, fogja meg a markert és helyezze át.', 'gh')?></small>
    <br><br>
    <div class="row">
      <div class="col-md-12">
        <?
          ob_start();
          include(locate_template('/templates/parts/map_gps_picker.php'));
          ob_end_flush();
        ?>
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
<script>
  (function($){
    $('#_listing_address, #tax_locations').on('change', function(){
      var qryaddr = 'Magyarország<?=(!$me->RegionID()) ?'':', '.$me->RegionName().' megye'?>';
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
  })(jQuery);
</script>
