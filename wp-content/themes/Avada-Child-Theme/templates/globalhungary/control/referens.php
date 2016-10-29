<?php
  $control = get_control_controller('referens');
  // Referensek listája
  $users = $control->getUserList();
  $users_num = $control->users->get_total();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=sprintf(__('Referensek <span class="badge">%d</span>', 'gh'), $users_num)?></h1>
    <div class="desc"><?=__('Itt találhatók azok a referens felhasználók, akik Ön alá tartoznak.', 'gh')?></div>
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
              <strong><?=$u->display_name?></strong><br>
              <small>Tel: <?=$u->phone?></small>
            </div>
            <div class="col-md-3 center"><?=$u->user_email?></div>
            <div class="col-md-2 center"><a href="/control/properties/?user=<?=$u->ID?>"><?=$u->properties?> <?=__('db', 'gh')?></a></div>
            <div class="col-md-3 center"><?=get_user_meta($u->ID, 'last_login', 'n/a')?></div>
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
