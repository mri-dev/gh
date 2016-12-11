<?php
  global $me;

  $control = get_control_controller('properties');

  $selected_user = false;
  $author = false;
  $filtered = false;
  $archived = false;
  $show_selector = false;

  if (current_user_can('reference_manager')) {
    $author = $me->ID();
  } else {
    if (isset($_GET['user'])) {
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

  if (isset($_GET['arc'])) {
    $filtered = true;
    if ( current_user_can('administrator') || current_user_can('region_manager') ){
      $archived = true;
    }
  }

  $properties = $control->getProperties(array(
    'post_status' => array('publish', 'pending', 'draft', 'future'),
    'location' => $me->RegionID(),
    'author' => $author,
    'hide_archived' => (($archived) ? false : true),
    'only_archived' => (($archived) ? true : false),
    'page' => (isset($_GET['page']) && is_numeric($_GET['page'])) ? $_GET['page'] : 1,
    'idnumber' => (isset($_GET['id'])) ? $_GET['id'] : false
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
      <?php if (isset($_GET['user'])): ?>
        <?=sprintf(__('Kiválasztott felhasználó: <strong>%s</strong>', 'gh'), $selected_user->Name())?>
      <?php endif; ?>
      <?php if ($archived): ?>
        <?=__('Megjelenített lista: <strong>Archivált ingatlanok</strong>', 'gh')?>
      <?php endif; ?>
    <?php endif; ?>

    <form action="/control/properties/" method="get" class="pull-right">
      <input type="hidden" name="user" value="<?=$_GET['user']?>">
      <div class="inline-input">
        <div>
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
            <div class="col-md-2"><?=__('Referens', 'gh')?></div>
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
                    <img src="<?=$p->ProfilImg()?>" alt="" />
                  </div>
                  <div class="main-row">
                    <span class="title"><a href="<?=$p->URL()?>" target="_blank"><?=$p->Title()?></a></span>
                  </div>
                  <div class="alt-row">
                    <span class="ref-number <?=($p->isExclusive())?'exclusive':''?>" title="<?=($p->isExclusive())?__('Ez a hirdetés kizárólagos hirdetés.','gh'):''?>"><?=$p->Azonosito()?></span>
                    <span class="price"><?=$p->Price(true)?></span>
                    <span class="region"><?=$p->RegionName()?></span> / <span class="address"><?=$p->Address()?></span>
                  </div>
                </div>
              </div>
              <div class="col-md-2 center"><a title="<?=__('Felhasználó ingatlanjainak listázása', 'gh')?>" href="/control/properties/?user=<?=$p->AuthorID()?>"><?=$p->AuthorName()?></a></div>
              <div class="col-md-2 center"><?=$p->Status(false)?></div>
              <div class="col-md-2 center">
                <?=$p->CreateAt()?>
                <div class="edit">
                  <a href="/control/property_edit/?id=<?=$p->ID()?>"><?=__('szerkeszt', 'gh')?> <i class="fa fa-pencil"></i></a>
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

  })(jQuery);
</script>
