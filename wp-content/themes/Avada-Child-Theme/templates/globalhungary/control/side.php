<div class="gh_control_sidebar">
  <ul>
    <li class="mainhead"><?=__('Kezelőfelület', 'gh')?></li>
    <li class=""><a href="/control/home"><i class="fa fa-gear"></i> <?=__('Gépház', 'gh')?></a></li>
    <!-- <li class=""><a href="/control/account"><i class="fa fa-user"></i> <?=__('Saját fiók', 'gh')?></a></li>-->
    <?php if( current_user_can('region_manager') ): ?>
    <li class=""><a href="/control/referens"><i class="fa fa-users"></i> <?=__('Referensek', 'gh')?></a></li>
    <? endif; ?>
    <li class=""><a href="/control/properties"><i class="fa fa-home"></i> <?=__('Ingatlanok', 'gh')?></a></li>
    <?php if( current_user_can('property_create') ): ?>
    <li class=""><a href="/control/property_create"><i class="fa fa-plus-circle"></i> <?=__('Ingatlan létrehozás', 'gh')?></a></li>
    <?php endif; ?>
    <?php if( current_user_can('stat_property') ): ?>
    <li class=""><a href="/control/property_statistic"><i class="fa fa-pie-chart"></i> <?=__('Ingatlan statisztika', 'gh')?></a></li>
    <?php endif; ?>
    <li class="logout"><a href="<?=wp_logout_url('/')?>"><i class="fa fa-sign-out"></i> <?=__('Kijelentkezés', 'gh')?></a></li>
  </ul>
</div>
