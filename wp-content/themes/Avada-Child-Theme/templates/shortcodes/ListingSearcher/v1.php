<?php
  // Régiók
  $regions = $properties->getRegions();
?>
<div class="searcher-header">
  <ul>
    <li>
      <div class="ico">
        <i class="fa fa-search"></i>
      </div>
    </li><!--
 --><li>
      <input type="radio" checked="checked" id="region_all" name="region" value="0"> <label for="region_all"><?=__('Összes ingatlan', 'gh')?></label>
    </li><!--
 --><? foreach( $regions as $r ): if($r->parent != 0) continue; ?><!--
  --><li>
      <input type="radio" id="region_<?=$r->term_id?>" name="region" value="<?=$rid?>"> <label for="region_<?=$r->term_id?>"><?=$r->name?></label>
    </li><!--
 --><? endforeach; ?>
  </ul>
</div>
<div class="searcher-wrapper">
  <form class="" action="/<?=SLUG_INGATLAN_LIST?>" method="get">
    <div class="form-items">
      <div class="inp inp-city">
        <label for="searcher_city"><?=__('Város', 'gh')?></label>
        <input type="text" id="searcher_city" class="form-control" value="">
        <input type="hidden" name="city_ids" id="searcher_city_ids" value="">
      </div>
      <div class="inp inp-rooms">
        <label for="searcher_rooms"><?=__('Szobák száma', 'gh')?></label>
        <select class="form-control" name="rooms" id="searcher_rooms">
          <option value="0" selected="selected"><?=__('Összes', 'gh')?></option>
          <?php $c = 0; while ( $c <= 10 ): $c++; ?>
          <option value="<?=$c?>"><?=sprintf(_n('%d szoba', '%d szoba', $c, 'gh'), $c)?></option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="inp inp-alapterulet">
        <label for="searcher_property_size"><?=__('Alapterület', 'gh')?></label>
        <input type="number" class="form-control" id="searcher_property_size" name="property_size" min="0" placeholder="<?=__('nm', 'gh')?>" step="10" value="">
      </div>
      <div class="inp inp-kategoria">
        <label for="searcher_kategoria_ids"><?=__('Kategória', 'gh')?></label>
        <input type="text" id="searcher_kategoria_ids" class="form-control" value="">
        <input type="hidden" id="searcher_kategoria_ids" name="name" value="">
      </div>
      <div class="inp inp-azonosito">
        <label for="searcher-idn"><?=__('Referenciaszám', 'gh')?></label>
        <input type="text" class="form-control" id="searcher-idn" name="idn" value="">
      </div>
      <div class="inp inp-status">
        <label for="searcher_status"><?=__('Státusz', 'gh')?></label>
        <input type="text" id="searcher_status" class="form-control" value="">
        <input type="hidden" name="status_ids" id="searcher_status_ids" value="">
      </div>
      <div class="inp inp-price-min">
        <label for="searcher_price_min"><?=__('Minimum ár (Ft)', 'gh')?></label>
        <input type="number" class="form-control" id="searcher_price_min" name="price_min" min="0" placeholder="<?=__('MFt', 'gh')?>" step="100000" value="">
      </div>
      <div class="inp inp-price-max">
        <label for="searcher_price_max"><?=__('Maximum ár (Ft)', 'gh')?></label>
        <input type="number" class="form-control" id="searcher_price_max" name="price_max" min="0" placeholder="<?=__('MFt', 'gh')?>" step="100000" value="">
      </div>
      <div class="inp inp-submit">
        <button type="submit"><i class="fa fa-search"></i> <?=__('Keresés', 'gh')?></button>
      </div>
    </div>
  </form>
</div>
<div class="searcher-footer">
  OPTIONS
</div>
