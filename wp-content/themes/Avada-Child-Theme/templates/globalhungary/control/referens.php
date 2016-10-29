<?php
  $control = get_control_controller('referens');
  // Referensek listája
  $users = $control->users->get_results();
  $users_num = $control->users->get_total();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=sprintf(__('Referensek <span class="badge">%d</span>', 'gh'), $users_num)?></h1>
    <div class="desc"><?=__('Itt találhatók azok a referens felhasználók, akik Ön alá tartoznak.', 'gh')?></div>
  </div>
  <div class="gh_control_referens_page">
    <?php


     if( !empty($users) ) {
      foreach ($users as $u) { ?>
        <?=$u->display_name?>
    <?php }
     } else { ?>
       Nincsennek felhasználók.
    <?php } ?>


  </div>
</div>
