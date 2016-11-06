<?php
  global $me;

  $control = get_control_controller('properties');

  $author = false;
  $filtered = false;

  if (current_user_can('reference_manager')) {
    $author = $me->ID();
  } else {
    if (isset($_GET['user'])) {
      if ( true ) {
        if ( current_user_can('region_manager') || current_user_can('administrator') ) {
          $author = $_GET['user'];
          $filtered = true;
          $selected_user = new UserHelper(array( 'id' => $_GET['user']) );
        }
      }
    }
  }

  $properties = $control->getProperties(array(
    'post_status' => array('publish', 'pending', 'draft', 'future'),
    'location' => $me->RegionID(),
    'author' => $author
  ));
  $item_num = $control->propertyCount();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=sprintf(__('Ingatlanok <span class="region">/ %s</span> <span class="badge">%d</span>', 'gh'), $me->RegionName(), $item_num)?></h1>
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
    <?php endif; ?>
    <div class="data-table">
      <div class="data-head">
        <div class="row">
          <div class="col-md-5"><?=__('Ingatlan', 'gh')?></div>
          <div class="col-md-3"><?=__('Referens', 'gh')?></div>
          <div class="col-md-2"><?=__('Állapot', 'gh')?></div>
          <div class="col-md-2"><?=__('Létrehozva', 'gh')?></div>
        </div>
      </div>
      <div class="data-body">
        <?php foreach( $properties as $p ): ?>
          <div class="row">
            <div class="col-md-5">
              <div class="adv-inf">
                <div class="img">
                  <img src="<?=$p->ProfilImg()?>" alt="" />
                </div>
                <div class="main-row">
                  <span class="title"><?=$p->Title()?></span>
                </div>
                <div class="alt-row">
                  <span class="ref-number"><?=$p->Azonosito()?></span>
                  <span class="price"><?=$p->Price(true)?></span>
                  <span class="region"><?=$p->RegionName()?></span> / <span class="address"><?=$p->Address()?></span>
                </div>
              </div>
            </div>
            <div class="col-md-3 center"><a title="<?=__('Felhasználó ingatlanjainak listázása', 'gh')?>" href="/control/properties/?user=<?=$p->AuthorID()?>"><?=$p->AuthorName()?></a></div>
            <div class="col-md-2 center"><?=$p->Status(false)?></div>
            <div class="col-md-2 center">
              <?=$p->CreateAt()?>
              <div class="edit"><a href="/control/property_edit/?id=<?=$p->ID()?>"><?=__('szerkeszt', 'gh')?> <i class="fa fa-pencil"></i></a></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
