<?php
  // Régiók
  $regions = $properties->getRegions();
?>

<form class="" role="searcher" id="searcher-form"  action="/<?=SLUG_INGATLAN_LIST?>/" method="get">
<div class="searcher-header">
  <ul>
    <li>
      <div class="ico">
        <i class="fa fa-search"></i>
      </div>
    </li><!--
 --><li>
      <div class="head-title">
        <?=__('Ingatlan keresés', 'gh')?>
      </div>
    </li>
  </ul>
</div><!--
--><div class="searcher-wrapper">
    <div class="vlabel" eopener=".listing-searcher-holder.style-floating"><?=__('További ingatlanok kersése', 'gh')?></div>
    <div class="form-items">
      <div class="inp inp-city">
        <label for="searcher_city"><?=__('Város', 'gh')?></label>
        <input type="text" id="searcher_city" class="form-control" name="cities" value="<?=$form['cities']?>" placeholder="<?=__('Összes', 'gh')?>">
        <div id="searcher_city_autocomplete" class="selector-wrapper"></div>
        <input type="hidden" name="ci" id="searcher_city_ids" value="<?=$form['ci']?>">
      </div>
      <div class="inp inp-kategoria">
        <label for="kategoria_multiselect_text"><?=__('Kategória', 'gh')?></label>
        <div class="tglwatcher-wrapper">
          <input type="text" readonly="readonly" id="kategoria_multiselect_text" class="form-control tglwatcher" tglwatcher="kategoria_multiselect" placeholder="<?=__('Összes', 'gh')?>" value="">
        </div>
        <input type="hidden" id="kategoria_multiselect_ids" name="c" value="<?=$form['c']?>">
        <div class="multi-selector-holder" tglwatcherkey="kategoria_multiselect" id="kategoria_multiselect">
          <div class="selector-wrapper">
            <?
              $selected = explode(",", $form['c']);
              $kategoria = $properties->getSelectors( 'property-types' ); ?>
            <?php if ($kategoria): ?>
              <?php foreach ($kategoria as $k): ?>
              <div class="selector-row">
                <input type="checkbox" <?=(in_array($k->term_id, $selected))?'checked="checked"':''?> tglwatcherkey="kategoria_multiselect" htxt="<?=$k->name?>" id="kat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="kat_<?=$k->term_id?>"><?=$k->name?> <span class="n">(<?=$k->count?>)</span></label>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="inp inp-status">
        <label for="status_multiselect_text"><?=__('Státusz', 'gh')?></label>
        <div class="tglwatcher-wrapper">
          <input type="text" readonly="readonly" id="status_multiselect_text" class="form-control tglwatcher" tglwatcher="status_multiselect" placeholder="<?=__('Összes', 'gh')?>" value="">
        </div>
        <input type="hidden" id="status_multiselect_ids" name="st" value="<?=$form['st']?>">
        <div class="multi-selector-holder" tglwatcherkey="status_multiselect" id="status_multiselect">
          <div class="selector-wrapper">
            <?
              $selected = explode(",", $form['st']);
              $status = $properties->getSelectors( 'status' );
            ?>
            <?php if ($status): ?>
              <?php foreach ($status as $k): ?>
              <div class="selector-row">
                <input type="checkbox" <?=(in_array($k->term_id, $selected))?'checked="checked"':''?> tglwatcherkey="status_multiselect" htxt="<?=$k->name?>" id="stat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="stat_<?=$k->term_id?>"><?=$k->name?> <span class="n">(<?=$k->count?>)</span></label>
              </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="inp inp-azonosito">
        <label for="searcher-idn"><?=__('Referenciaszám', 'gh')?></label>
        <input type="text" class="form-control" id="searcher-idn" name="n" value="<?=$form['n']?>">
      </div>
      <div class="inp inp-rooms">
        <label for="searcher_rooms"><?=__('Szobák száma', 'gh')?></label>
        <div class="select-wrapper">
          <select class="form-control" name="r" id="searcher_rooms">
            <option value="0" selected="selected"><?=__('Összes', 'gh')?></option>
            <?php $c = 0; while ( $c < 10 ): $c++; ?>
            <option value="<?=$c?>" <?=($c == $form['r'])?'selected="selected"':''?>><?=sprintf(_n('%d+ szoba', '%d+ szoba', $c, 'gh'), $c)?></option>
            <?php endwhile; ?>
          </select>
        </div>
      </div>
      <div class="inp inp-alapterulet">
        <label for="searcher_property_size"><?=__('Min. alapterület', 'gh')?></label>
        <input type="number" class="form-control" id="searcher_property_size" name="ps" min="0" placeholder="<?=__('nm', 'gh')?>" step="10" value="<?=$form['ps']?>">
      </div>
      <div class="inp inp-price-min">
        <label for="searcher_price_min"><?=__('Minimum ár (Ft)', 'gh')?></label>
        <input type="text" class="form-control pricebind" id="searcher_price_min" name="pa" placeholder="<?=__('MFt', 'gh')?>" value="<?=$form['pa']?>">
      </div>
      <div class="inp inp-price-max">
        <label for="searcher_price_max"><?=__('Maximum ár (Ft)', 'gh')?></label>
        <input type="text" class="form-control pricebind" id="searcher_price_max" name="pb" placeholder="<?=__('MFt', 'gh')?>" value="<?=$form['pb']?>">
      </div>
    </div>
</div>
<div class="submit">
  <button type="submit"><i class="fa fa-search"></i> <?=__('Keresés', 'gh')?></button>
</div>
<div class="searcher-footer">
  OPTIONS
</div>
</form>
<script type="text/javascript">
  (function($){
    collect_checkbox('kategoria_multiselect', true);
    collect_checkbox('status_multiselect', true);

    $(window).click(function() {
      if (!$(event.target).closest('.toggler-opener').length) {
        $('.toggler-opener').removeClass('opened toggler-opener');
        $('.tglwatcher.toggled').removeClass('toggled');
      }
    });

    $('.pricebind').bind("keyup change", function() {

    });

    $('*[eopener]').click(function(){
      var _ = $(this);
      var t = _.attr('eopener');

      if ( _.hasClass('eopened') ) {
        $(t).removeClass('opened');
        _.removeClass('eopened');
      } else {
        $(t).addClass('opened');
        _.addClass('eopened');
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
    /* Autocompleter */
    var src_current_region = 0;
    $("#searcher-form input[name='rg']").change(function(){
      var sl = $(this).val();
      src_current_region = sl;
    });
    $('#searcher_city').autocomplete({
        serviceUrl: '/wp-admin/admin-ajax.php?action=city_autocomplete',
        appendTo: '#searcher_city_autocomplete',
        paramName: 'search',
        params : { "region": get_current_regio() },
        type: 'GET',
        dataType: 'json',
        transformResult: function(response) {
            return {
                suggestions: $.map(response, function(dataItem) {
                    //return { value: dataItem.label.toLowerCase().capitalizeFirstLetter(), data: dataItem.value };
                    return { value: dataItem.label, data: dataItem.value };
                })
            };
        },
        onSelect: function(suggestion) {
          $('#searcher_city_ids').val(suggestion.data);
        },
        onSearchComplete: function(query, suggestions){
        },
        onSearchStart: function(query){
          $(this).autocomplete().options.params.region = get_current_regio();
        },
        onSearchError: function(query, jqXHR, textStatus, errorThrown){
            console.log('Autocomplete error: '+textStatus);
        }
    });

     function get_current_regio() {
       return $("#searcher-form input[name=rg]:checked").val();
     }

    String.prototype.capitalizeFirstLetter = function() {
      return this;
      //return this.charAt(0).toUpperCase() + this.slice(1);
    }
    /* E:Autocompleter */
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
