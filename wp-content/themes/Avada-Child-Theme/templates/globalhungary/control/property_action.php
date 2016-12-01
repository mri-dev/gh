<?php
  global $me;

  extract($_POST);

  switch ($action) {
    case 'change_referens':
      $title = __('Referens módosítás ingatlanoknál','gh');

      if ( !current_user_can('administrator') && !$me->can('user_property_connector') ) {
        wp_redirect('/control/home/');
      }
    break;
    default:
      wp_redirect('/control/home/');
    break;
  }
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=$title?></h1>
  </div>
  <a href="javascript:void(0);" onclick="history.go(-1);"><< <?=__('Vissza a kiválasztáshoz', 'gh')?></a>
  <br><br>
  <div class="">
    <div class="alert alert-warning"><?=sprintf(__('%d db ingatlan kiválasztva módosításra.','gh'), count($ids))?></div>
    <form class="wide-form" action="/control/property_action_save" method="post">
      <input type="hidden" name="action" value="<?=$action?>">
      <?php foreach ($ids as $id): ?>
      <input type="hidden" name="ids[]" value="<?=$id?>">
      <?php endforeach; ?>
      <?php
        ob_start();
        include(locate_template('templates/globalhungary/control/property_actions/'.$action.'.php'));
        ob_end_flush();
      ?>
      <br>
      <input type="submit" class="fusion-button button-flat button-square button-medium button-neutral" value="<?=__('Művelet végrehajtása', 'gh')?>">
    </form>
  </div>
</div>
