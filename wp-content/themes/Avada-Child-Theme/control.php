<?php
  get_header();
  $cp_page = $wp_query->query_vars['cp'];
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="gn_control_content_container">
      <?php get_template_part( 'templates/globalhungary/control/side' ); ?>
      <?php get_template_part( 'templates/globalhungary/control/'.$cp_page );   ?>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
