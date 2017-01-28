<?php
  get_header();
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=PREMIUM_AUTH_PAGE_SLUG?>-view">
      <h1><?=__('Prémium hozzáférés engedélyezése', 'gh')?></h1>
      <h3><?=__('Kérjük adja meg a Mesterkulcsot a prémium tartalom hozzáféréséhez.', 'gh')?></h3>
      <?php if (isset($_GET['ekey'])): ?>
        <?php if ($_GET['ekey'] == 'failauth'): ?>
          <div class="">
            <div class="fusion-alert alert notice alert-dismissable alert-danger alert-shadow fusion-animated" data-animationtype="shake" data-animationduration="0.5" data-animationoffset="100%" style="visibility: visible; animation-duration: 0.5s;"> <button type="button" class="close toggle-alert" data-dismiss="alert" aria-hidden="true">×</button><?=__('Sikertelen azonosítás. A megadott mesterjelszó hibés. Hozzéférés megtagadva!', 'gh')?></div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
      <div class="validator-box">
        <form class="" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
          <label for="premium_pas"><?=__('Mesterkulcs megadása', 'gh')?></label>
          <input type="password" id="premium_pas" name="premium_pass">
          <input type="hidden" name="action" value="premium_validation">
          <button type="submit" name="accessPremium" value="1"><?=__('Premium engedélyezése', 'gh')?> <i class="fa fa-lock"></i></button>
        </form>
      </div>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
