<?php
  $control = get_control_controller('properties');
  $item_num = 0;
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=sprintf(__('Ingatlanok <span class="badge">%d</span>', 'gh'), $item_num)?></h1>
    <div class="desc"><?=__('Az alábbi listában az Ön régiójába található ingatlan hirdetéseket találhatja.', 'gh')?></div>
  </div>
  <div class="gh_control_properties_page">
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
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
