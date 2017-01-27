<?php
  get_header();
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=PREMIUM_AUTH_PAGE_SLUG?>-view">
      <h1><?=__('Prémium hozzáférés engedélyezése', 'gh')?></h1>
      <h3><?=__('Kérjük adja meg a Mesterkulcsot a prémium tartalom hozzáféréséhez.', 'gh')?></h3>
      <div class="validator-box">
        <form class="" method="post" action="">
          <label for="premium_pas"><?=__('Mesterkulcs megadása', 'gh')?></label>
          <input type="password" id="premium_pas" name="premium_pass">
          <button type="submit" name="accessPremium"><?=__('Premium engedélyezése', 'gh')?> <i class="fa fa-lock"></i></button>
        </form>
      </div>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
