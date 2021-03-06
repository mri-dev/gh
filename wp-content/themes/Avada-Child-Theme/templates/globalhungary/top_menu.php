<?php
  global $notify, $app_languages;
  // Top social icons
  $social = new Avada_Social_Icons();
  $icons_html = $social->render_social_icons(array('position' => 'header'));
?>
<div class="top-float-menubar">
  <div class="menu-holder">
    <div class="contact">
      <div class="phone">
        <i class="fa fa-phone"></i>
        06 1 790 58 24 &nbsp;&nbsp; 06 72 222 404
      </div>
    </div><!--
 --><div class="accounts">
      <?php if(is_user_logged_in()): $logged_user = wp_get_current_user(); ?>
      <a href="<?php echo bloginfo('siteurl');?>/control/home"><div class="ico"><i class="fa fa-user"></i>&nbsp;</div><?php echo $logged_user->display_name; ?></a>
      <?php else: ?>
      <a href="<?php echo bloginfo('siteurl');?>/admin"><div class="ico"><img src="<?=IMG?>/ico-lock.svg" alt="Account" /></div><?php echo __('Belépés', 'gh'); ?></a>
      <?php endif; ?>
    </div><!--
 --><div class="languages">
     <ul>
       <?php foreach ($app_languages as $langid => $lang): if(!$lang['avaiable']) continue; ?>
         <li class="<?=(get_locale() == $lang['code'])?'active':''?>"><a title="<?php echo $lang['name']; ?>" href="<?php echo PROTOCOL; ?>://<?php echo $lang['subdomain'].TARGETDOMAIN; ?>"><img src="<?php echo IMG . '/flags/circles/'.$lang['code'].'.png'; ?>" alt="<?php echo $lang['name']; ?>"></a></li>
       <?php endforeach; ?>
     </ul>
   </div><!--
--><div class="notify-icos"><!--
    --><div class="notify-favorite">
        <a class="trans-on" href="/kedvencek" title="<?=__('Kedvenceknek elmentett ingatlanok', 'gh')?>">
          <img src="<?=IMG?>/ico-heart.svg" alt="Favorite" />
          <div class="fnl" id="notification-favorite"></div>
        </a>
      </div><!--
   --><div class="notify-newhouse" title="<?=__('Nem megtekintett ingatlanok listája', 'gh')?>">
        <? $unwatched = $notify->propertyUnwatched(); ?>
        <a class="trans-on" href="/news">
          <img src="<?=IMG?>/ico-house.svg" alt="New House" />
          <div class="fnl <?=($unwatched > 0)?'has':''?>" id="notification-newhouse"><?=$unwatched?></div>
        </a>
      </div><!--
  --></div><!--
 --><div class="socials">
      <?php echo $icons_html; ?>
    </div>
  </div>
</div>
