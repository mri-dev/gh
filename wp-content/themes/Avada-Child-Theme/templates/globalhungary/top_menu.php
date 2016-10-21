<?php
  // Top social icons
  $social = new Avada_Social_Icons();
  $icons_html = $social->render_social_icons(array('position' => 'header'));
?>
<div class="top-float-menubar">
  <div class="menu-holder">
    <div class="contact">
      <div class="phone">
        <i class="fa fa-phone"></i>
        06 72 222 404
      </div>
    </div><!--
 --><div class="accounts">
      <a href="#"><div class="ico"><img src="<?=IMG?>/ico-lock.svg" alt="Account" /></div><?php echo __('Belépés / Regisztráció', 'gh'); ?></a>
    </div><!--
 --><div class="notify-icos"><!--
    --><div class="notify-favorite">
        <a class="trans-on" href="#">
          <img src="<?=IMG?>/ico-heart.svg" alt="Favorite" />
          <div class="fnl" id="notification-favorite">14</div>
        </a>
      </div><!--
   --><div class="notify-newhouse">
        <a class="trans-on" href="#">
          <img src="<?=IMG?>/ico-house.svg" alt="New House" />
          <div class="fnl" id="notification-newhouse">194</div>
        </a>
      </div><!--
  --></div><!--
 --><div class="socials">
      <?php echo $icons_html; ?>
    </div>
  </div>
</div>
