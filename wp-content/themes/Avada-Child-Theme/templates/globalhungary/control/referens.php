<?php
  global $me;

  $control = get_control_controller('referens');
  // Referensek listája
  $users = $control->getUserList();
  $users_num = $control->Count();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=sprintf(__('Referensek <span class="region">/ %s</span> <span class="badge">%d</span>', 'gh'), $me->RegionName(), $users_num)?></h1>
    <div class="desc"><?=__('Itt találhatók azok a referens felhasználók, akik Ön régiója alá tartoznak.', 'gh')?></div>
  </div>
  <div class="gh_control_referens_page">
    <?php if( !current_user_can('region_manager') ): ?>
      <div class="alert alert-danger"><?=__('Önnek nincs joga ezt a funkciót használni. A funkció használata csak Régió Menedzsereknek engedélyezett.', 'gh')?></div>
    <?php else: ?>
    <div class="data-table">
      <div class="data-head">
        <div class="row">
          <div class="col-md-4"><?=__('Név', 'gh')?></div>
          <div class="col-md-3"><?=__('Email', 'gh')?></div>
          <div class="col-md-2"><?=__('Ingatlanok', 'gh')?></div>
          <div class="col-md-3"><?=__('Utoljára belépett', 'gh')?></div>
        </div>
      </div>
      <div class="data-body">
      <?php
      if( !empty($users) ) {
        foreach ($users as $u) {
        ?>
          <div class="row">
            <div class="col-md-4">
              <strong><?=$u->Name()?></strong><br>
              <small>Tel: <?=$u->Phone()?></small>
            </div>
            <div class="col-md-3 center"><?=$u->Email()?></div>
            <div class="col-md-2 center"><a href="/control/properties/?user=<?=$u->ID()?>"><?=$u->PropertiesCount()?> <?=__('db', 'gh')?></a></div>
            <div class="col-md-3 center"><?=$u->LastLogin()?></div>
          </div>
      <?php }
       } else { ?>
        <div class="row">
          <div class="col-md-12">
            <?=__('Nincsennek felhasználók.', 'gh')?>
          </div>
        </div>
      <?php } ?>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
