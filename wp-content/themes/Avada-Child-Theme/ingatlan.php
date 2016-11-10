<?php
  get_header();
  $cp_page = $wp_query->query_vars['cp'];
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_INGATLAN?>-page-view">
      <? print_r($_SERVER['SCRIPT_FILENAME']); ?>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
