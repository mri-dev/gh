<?php
  get_header();
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_INGATLAN?>-page-view">
      <div class="search-side">
        <? echo do_shortcode('[listing-searcher view="v2"]'); ?>
      </div><div class="listing-content">
        <div class="listing-wrapper">
          <? echo do_shortcode('[listing-list view="standard" src="get" limit="30"]'); ?>
        </div>
      </div>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
