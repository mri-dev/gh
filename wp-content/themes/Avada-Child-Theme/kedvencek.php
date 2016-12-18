<?php
  get_header();
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_FAVORITE?>-page-view ingatlan-page-view  ingatlan-list">
      <div class="search-side trans-on">
      <? echo do_shortcode('[listing-searcher view="v2"]'); ?>
    </div><div class="listing-content trans-on" style="min-height:1000px;">
        <div class="listing-wrapper">
          <div class="show-on-mobile">
            <button type="button" id="search-tgl" class="fusion-button button-small button-square" name="button"><?=__('Kereső', 'gh')?> <i class='fa fa-search'></i></button>
          </div>
          <? echo do_shortcode('[listing-list view="standard" src="favorite" limit="999"]'); ?>

          <script type="text/javascript">
            (function($){
              $('#search-tgl').click(function(){
                var _ = $(this);
                var opened = _.hasClass('toggled');

                if (opened) {
                  _.removeClass('toggled');
                  $('.<?=SLUG_INGATLAN?>-page-view').removeClass('searcher-mode');
                }else{
                  _.addClass('toggled');
                  $('.<?=SLUG_INGATLAN?>-page-view').addClass('searcher-mode');
                }
              });
            })(jQuery);
          </script>
        </div>
      </div>
    </div>
	</div>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
