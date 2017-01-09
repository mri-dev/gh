<?php
  global $me;

  $control = get_control_controller('properties');
  $pf = new PropertyFactory();

  $selected_user = false;
  $author = false;
  $filtered = false;
  $archived = false;
  $show_selector = false;
  $location = false;

  if (current_user_can('reference_manager')) {
    $author = $me->ID();
  } else {
    if (isset($_GET['user']) && !empty($_GET['user'])) {
      if ( true ) {
        if ( current_user_can('region_manager') || current_user_can('administrator') ) {
          $author = $_GET['user'];
          $filtered = true;
          $selected_user = new UserHelper(array( 'id' => $_GET['user']) );

          if ($me->can('user_property_connector') || current_user_can('administrator')) {
            $show_selector = true;
          }
        }
      }
    }
  }

  if (current_user_can('region_manager')) {
    $location = $me->RegionID();
  }

  if (isset($_GET['arc'])) {
    $filtered = true;
    if ( current_user_can('administrator') || current_user_can('region_manager') ){
      $archived = true;
    }
  }

  $all_status = array('publish', 'pending', 'draft', 'future');
  $status = $all_status;

  if(isset($_GET['st']) && !empty($_GET['st']))
  {
    $status = array($_GET['st']);
    $filtered = true;
  }

  if (isset($_GET['c']) && !empty($_GET['c'])) {
    $type_ids = explode(",", $_GET['c']);
    $filtered = true;
  }

  $properties = $control->getProperties(array(
    'post_status' => $status,
    'location' => $location,
    'author' => $author,
    'hide_archived' => (($archived) ? false : true),
    'only_archived' => (($archived) ? true : false),
    'page' => (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1,
    'idnumber' => (isset($_GET['id'])) ? $_GET['id'] : false,
    'property-types' => $type_ids,
  ));
  $item_num = $control->propertyCount();
  $pager = $control->pager('/control/properties/');
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <div class="buttons">
      <?php if ( current_user_can('administrator') || current_user_can('region_manager') ): ?>
        <?php if ($_GET['arc'] != '1'): ?>
          <a href="/control/properties/?arc=1" class="btn btn-rounded btn-red"><?=__('Archiváltak', 'gh')?> <i class="fa fa-archive"></i></a>
        <?php endif; ?>
      <?php endif; ?>
      <?php
      // Excel export
      $qrys = $_SERVER['QUERY_STRING'];
      ?>
      <a title="<?=__('Expotálásnál a következő szűrőfeltátelek is érvényesek: Státusz, Referens', 'gh')?>" href="/ingatlan-export/<?=($qrys)?'?'.$qrys:''?>" class="btn btn-rounded btn-green"><?=__('Excel export', 'gh')?> <i class="fa fa-file-excel-o"></i></a>
    </div>
    <h1><?=(isset($_GET['arc'])?__('Archivált', 'gh').' ':'')?><?=sprintf(__('Ingatlanok <span class="region">/ %s</span> <span class="badge">%d</span>', 'gh'), $me->RegionName(), $item_num)?></h1>
    <div class="desc"><?=__('Az alábbi listában az Ön régiójába található ingatlan hirdetéseket találhatja.', 'gh')?></div>
  </div>
  <div class="gh_control_properties_page">
    <?php if( false ): ?>
      <div class="alert alert-danger"><?=__('Önnek nincs joga ezt a funkciót használni. A funkció használata csak Régió Menedzsereknek engedélyezett.', 'gh')?></div>
    <?php else: ?>
    <?php if ($filtered): ?>
      <a href="/control/properties/">< <?=__('Teljes lista mutatása', 'gh')?></a> <br>
      <?php if (isset($_GET['user'])&& !empty($_GET['user'])): ?>
        <?=sprintf(__('Kiválasztott felhasználó: <strong>%s</strong>', 'gh'), $selected_user->Name())?>
      <?php endif; ?>
      <?php if ($archived): ?>
        <?=__('Megjelenített lista: <strong>Archivált ingatlanok</strong>', 'gh')?>
      <?php endif; ?>
    <?php endif; ?>

    <form action="/control/properties/" method="get" class="pull-right">
      <input type="hidden" name="user" value="<?=$_GET['user']?>">
      <div class="inline-input">
        <div class="multiselect">
          <div class="tglwatcher-wrapper">
            <input type="text" readonly="readonly" id="kategoria_multiselect_text" class="form-control tglwatcher" tglwatcher="kategoria_multiselect" placeholder="<?=__('Összes kategória', 'gh')?>" value="">
          </div>
          <input type="hidden" id="kategoria_multiselect_ids" name="c" value="">
          <div class="multi-selector-holder" tglwatcherkey="kategoria_multiselect" id="kategoria_multiselect">
            <div class="selector-wrapper">
              <?
                $selected = (array)explode(",", $_GET['c']);
                $kategoria = $control->getSelectors( 'property-types' );
              ?>
              <?php if ($kategoria): ?>
                <?php foreach ($kategoria as $k): ?>
                <div class="selector-row lvl-0">
                  <input type="checkbox" <?=(in_array($k->term_id, $selected))?'checked="checked"':''?>  tglwatcherkey="kategoria_multiselect" htxt="<?=$k->name?>" id="kat_<?=$k->term_id?>" value="<?=$k->term_id?>"> <label for="kat_<?=$k->term_id?>"><?=$k->name?></label>
                </div>
                <?php if ( !empty($k->children) ): ?>
                  <?php foreach ($k->children as $sk): ?>
                  <div class="selector-row lvl-1">
                    <input type="checkbox" <?=(in_array($sk->term_id, $selected))?'checked="checked"':''?>  tglwatcherkey="kategoria_multiselect" data-parentid="<?=$sk->parent?>" data-lvl="1" htxt="<?=$k->name?> / <?=$sk->name?>" id="kat_<?=$sk->term_id?>" value="<?=$sk->term_id?>"> <label for="kat_<?=$sk->term_id?>"><?=$sk->name?></label>
                  </div>
                  <?php endforeach; ?>
                <?php endif; ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </div>
          </div>
        </div><!--
    --><div>
          <select class="form-control" name="st">
            <option value="" selected="selected"><?=__('Összes állapot', 'gh')?></option>
            <?php foreach ($all_status as $st): ?>
              <option value="<?=$st?>" <?=($_GET['st'] == $st)?'selected="selected"':''?>><?=$pf->StatusText($st)?></option>
            <?php endforeach; ?>
          </select>
        </div><!--
     --><div>
          <input type="text" name="id" class="pull-right" id="refid" placeholder="<?=__('Referenciaszám', 'gh')?>" class="form-control" value="<?=$_GET['id']?>">
        </div>
        <div>
          <button type="submit" class="fusion-button button-flat button-square button-small button-neutral"><?=__('Keresés')?> <i class="fa fa-search"></i></button>
        </div>
      </div>
    </form>

    <div class="clearfix"></div>

    <div class="pagination">
      <?php echo $pager; ?>
    </div>
    <form id="prop-list" action="/control/property_action/" method="post" class="wide-form">
      <div class="data-table">
        <div class="data-head">
          <div class="row">
            <div class="col-md-5">
              <?php if ($show_selector): ?>
                <div class="reconnecter_switcher">
                  <input type="checkbox" id="reconnecter_switch"><label for="reconnecter_switch"></label>
                </div>
              <?php endif; ?>
              <?=__('Ingatlan', 'gh')?></div>
            <div class="col-md-2"><?=__('Felhasználó', 'gh')?></div>
            <div class="col-md-2"><?=__('Állapot', 'gh')?></div>
            <div class="col-md-2"><?=__('Létrehozva', 'gh')?></div>
            <div class="col-md-1"><i class="fa fa-mouse-pointer"></i></div>
          </div>
        </div>
        <div class="data-body">
          <?php foreach( $properties as $p ): ?>
            <div class="row">
              <div class="col-md-5">
                <div class="adv-inf">
                  <?php if ($show_selector): ?>
                  <div class="connecter">
                    <input type="checkbox" id="reconnecter_<?=$p->ID()?>" name="ids[]" value="<?=$p->ID()?>"><label for="reconnecter_<?=$p->ID()?>"></label>
                  </div>
                  <?php endif; ?>
                  <div class="img">
                    <a title="<?=__('Kattintson a kép nagyításához.', 'gh')?>" href="<?=$p->ProfilImg()?>" data-rel="iLightbox[i<?=$p->ID()?>]">
                      <img src="<?=$p->ProfilImg()?>" alt="" />
                    </a>
                  </div>
                  <div class="main-row">
                    <span class="title"><a href="<?=$p->URL()?>" target="_blank"><?=$p->Title()?></a></span>
                  </div>
                  <div class="alt-row">
                    <div class="position">
                        <span class="region"><?=$p->RegionName()?></span> / <span class="address"><?=$p->Address()?></span>
                    </div>
                    <span class="ref-number <?=($p->isExclusive())?'exclusive':''?>" title="<?=($p->isExclusive())?__('Ez a hirdetés kizárólagos hirdetés.','gh'):''?>"><?=$p->Azonosito()?></span>
                    <span class="price"><?=$p->Price(true)?></span>

                  </div>
                </div>
              </div>
              <div class="col-md-2 center"><a title="<?=__('Felhasználó ingatlanjainak listázása', 'gh')?>" href="/control/properties/?user=<?=$p->AuthorID()?>"><?=$p->AuthorName()?></a></div>
              <div class="col-md-2 center"><?=$p->Status(false)?></div>
              <div class="col-md-2 center">
                <?=$p->CreateAt()?>
                <div class="edit">
                  <a href="/control/property_edit/?id=<?=$p->ID()?>&return=<?=base64_encode($_SERVER['REQUEST_URI'])?>"><?=__('szerkeszt', 'gh')?> <i class="fa fa-pencil"></i></a>
                  | <a href="/control/property_history/?pid=<?=$p->ID()?><?=($selected_user)?'&u='.$selected_user->ID():''?>" title="<?=__('Módosítások')?>"><?=$p->historyChangeCount($selected_user)?> <i class="fa fa-history"></i></a>
                </div>
              </div>
              <div class="col-md-1 center">
                <?=$p->Viewed()?>x
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="pagination">
        <?php echo $pager; ?>
      </div>
      <div id="action_selector" class="selector_action_cont hide">
        <h4>(<span id="selected_item_n">0</span>) <?=__('kiválasztott ingatlan műveletvégrehajtás', 'gh')?>:</h4>
        <div class="row">
          <div class="col-md-12">
            <select class="form-control" name="action">
              <option value="" selected="selected"><?=__('-- válasszon --', 'gh')?></option>
              <option value="" disabled="disabled"></option>
              <option value="change_referens"><?=__('Ingatlan referens csere')?></option>
            </select>
          </div>
          <div class="col-md-12">
            <div class="pull-right">
              <button type="submit" class="fusion-button button-flat button-square button-medium button-neutral"><?=__('Tovább', 'gh')?></button>
            </div>
          </div>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>
</div>
<script type="text/javascript">
  (function($){
    collect_checkbox('kategoria_multiselect', true);
    checkCheckbox();

    $('#reconnecter_switch').change(function(){
			var sa 	= $(this).is(':checked');
			var chs = $('.data-body').find('input[type=checkbox]');
			if(sa){
				chs.prop("checked", !chs.prop("checked"));
			}else{
				chs.prop("checked", !chs.prop("checked"));
			}
      checkCheckbox();
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

    $('.data-body input[type=checkbox]').change(function(){
      checkCheckbox();
		});

    function checkCheckbox() {
      var chs = $('.data-body').find('input[type=checkbox]:checked');
      var len = chs.length;

      $('#selected_item_n').text(len);

      if (len == 0) {
        $('#action_selector').removeClass('show');
      } else {
        $('#action_selector').addClass('show');
      }
    }

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

  })(jQuery);
</script>
