<?
global $me;
global $notify;
?>
<div class="gh_control_sidebar">
  <ul>
    <li class="mainhead"><?=__('Kezelőfelület', 'gh')?></li>
    <li class=""><a href="/control/home"><i class="fa fa-gear"></i> <?=__('Gépház', 'gh')?></a></li>
    <?php if ( current_user_can('administrator') ): ?>
    <li class=""><a href="/wp-admin/users.php"><i class="fa fa-users"></i> <?=__('Felhasználók', 'gh')?></a></li>
    <?php endif; ?>
    <?php if (!current_user_can('administrator')): ?>
    <li class=""><a href="/wp-admin/profile.php"><i class="fa fa-user"></i> <?=__('Saját fiók', 'gh')?></a></li>
    <?php endif; ?>
    <?php if( current_user_can('region_manager') || current_user_can('administrator') ): ?>
    <li class=""><a href="/control/referens"><i class="fa fa-users"></i> <?=__('Referensek', 'gh')?></a></li>
    <? endif; ?>
    <?php if ( current_user_can('administrator') || $me->can('property_archive_mod') ): ?>
    <li class=""><a href="/control/archive_requests"><i class="fa fa-archive"></i> <?=__('Archiválás kérelmek', 'gh')?>
    <?php $noti_arc = $notify->propertyArchiveRequests();
    if ( $noti_arc != 0 ): ?>
      <div class="badge"><?=$noti_arc?></div>
    <?php endif; ?></a></li>
    <?php endif; ?>
    <li class=""><a href="/control/properties"><i class="fa fa-home"></i> <?=__('Ingatlanok', 'gh')?></a></li>
    <?php if( $me->can('property_create') || ( current_user_can('administrator') || current_user_can('region_manager') ) ): ?>
    <li class=""><a href="/control/property_create"><i class="fa fa-plus-circle"></i> <?=__('Ingatlan létrehozás', 'gh')?></a></li>
    <?php endif; ?>
    <?php if( $me->can('stat_property') || ( current_user_can('administrator') || current_user_can('region_manager') ) ): ?>
    <li class=""><a href="/control/property_statistic"><i class="fa fa-pie-chart"></i> <?=__('Ingatlan statisztika', 'gh')?></a></li>
    <?php endif; ?>
    <?php if(current_user_can('administrator') || current_user_can('region_manager')): ?>
    <li class=""><a href="/control/property_history"><i class="fa fa-history"></i> <?=__('Ingatlan módosítások', 'gh')?></a></li>
    <?php endif; ?>
    <li class="logout"><a href="<?=wp_logout_url('/')?>"><i class="fa fa-sign-out"></i> <?=__('Kijelentkezés', 'gh')?></a></li>
  </ul>
</div>
