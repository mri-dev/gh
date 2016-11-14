<?php
  get_header();
  $cp_page = $wp_query->query_vars['cp'];
  $xs = explode("-",$wp_query->query_vars['urlstring']);
  $ingatlan_id = end($xs);

  $properties = new Properties(array(
    'id' => $ingatlan_id,
    'post_status' => array('publish'),
  ));
  $property = $properties->getList();
  $prop = $property[0];

?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_INGATLAN?>-page-view">
      <div class="floating-search-box">
        <? echo do_shortcode('[listing-searcher view="floating"]'); ?>
      </div>
      <div class="data-top">
        <div class="data-top-left">
          <div class="cwrapper">
            <div class="title">
              <h1><?=$prop->PropertyStatus(true)?> <?=$prop->PropertyType(true)?> <span class="addr"><?=$prop->ParentRegion()?>, <?=$prop->Address()?></span></h1>
              <div class="icons"></div>
            </div>
            <div class="images">
              <div class="profil" id="profilimg">
                <a href="<?=$prop->ProfilImg()?>"><img src="<?=$prop->ProfilImg()?>" alt=""></a>
              </div>
              <div class="stack">
                <div class="stack-wrapper">
                  <div class="items image-slide">
                    <? for( $f = 0; $f <= 6; $f++): if($f%2 === 0){ $img = $prop->ProfilImg(); }else{ $img = 'http://globalhungary.mri-dev.com/wp-content/uploads/2016/11/House.jpg'; }  ?>
                    <div class="i">
                      <img src="<?=$img?>" alt="" />
                    </div>
                    <? endfor; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="data-top-right">
          <div class="properties">
            <div class="header">
              <div class="current-price">
                <?=$prop->Price(true)?>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="list">
              <div class="e">
                <div class="h">
                  <div class="ico"><img src="<?=IMG?>/ico/telepules.svg" alt="<?=__('Település', 'gh')?>"></div>
                  <?=__('Település', 'gh')?>
                </div><!--
             --><div class="v"><?=$prop->ParentRegion()?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/cim.svg" alt="<?=__('Cím', 'gh')?>"></div>
                 <?=__('Cím', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->Address()?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Állapot', 'gh')?>"></div>
                 <?=__('Állapot', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->PropertyCondition(true)?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/szoba.svg" alt="<?=__('Szobák száma', 'gh')?>"></div>
                 <?=__('Szobák száma', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->getMetaValue('_listing_room_numbers')?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/telek-alapterulet.svg" alt="<?=__('Telek területe', 'gh')?>"></div>
                 <?=__('Telek területe', 'gh')?>
               </div><!--
            --><div class="v"><?=sprintf(__('%d nm', 'gh'), $prop->getMetaValue('_listing_lot_size'))?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/alapterulet.svg" alt="<?=__('Alapterület', 'gh')?>"></div>
                 <?=__('Alapterület', 'gh')?>
               </div><!--
            --><div class="v"><?=sprintf(__('%d nm', 'gh'), $prop->getMetaValue('_listing_property_size'))?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/szint.svg" alt="<?=__('Szintek száma', 'gh')?>"></div>
                 <?=__('Szintek száma', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->getMetaValue('_listing_level_numbers')?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/home.svg" alt="<?=__('Ingatlan típusa', 'gh')?>"></div>
                 <?=__('Ingatlan típusa', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->PropertyType(true)?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/payment.svg" alt="<?=__('Megbízás típusa', 'gh')?>"></div>
                 <?=__('Megbízás típusa', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->PropertyStatus(true)?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/payment.svg" alt="<?=__('Fűtés', 'gh')?>"></div>
                 <?=__('Fűtés', 'gh')?>
               </div><!--
            --><div class="v"><?=$prop->PropertyHeating(true)?></div>
              </div>
              <div class="cube-properties">
                <div class="cb cb-<?=($prop->getMetaValue('_listing_garage'))?'yes':'no'?>">
                  <div class="t">
                    <?=__('garázs', 'gh')?>
                  </div>
                  <div class="i"></div>
                </div>
                <div class="cb cb-<?=($prop->getMetaValue('_listing_balcony'))?'yes':'no'?>">
                  <div class="t">
                    <?=__('erkély', 'gh')?>
                  </div>
                  <div class="i"></div>
                </div>
                <div class="cb cb-<?=($prop->getMetaValue('_listing_lift'))?'yes':'no'?>">
                  <div class="t">
                    <?=__('lift', 'gh')?>
                  </div>
                  <div class="i"></div>
                </div>
              </div>
            </div>
            <div class="ref-number">
              <div class="row">
                <div class="col-md-6"><?=__('Referenciaszám', 'gh')?></div>
                <div class="col-md-6"><div class="n"><strong><?=$prop->Azonosito()?></strong></div></div>
              </div>
            </div>
          </div>
          <div class="contact">
            <div class="title"><?=__('Keresse kollégánkat', 'gh')?></div>
            <div class="name"><i class="fa fa-user"></i> <?=$prop->AuthorName()?></div>
            <div class="phone"><i class="fa fa-phone"></i> <?=$prop->AuthorPhone()?></div>
            <div class="email"><i class="fa fa-envelope"></i> <a href="mailto:<?=$prop->AuthorEmail()?>"><?=$prop->AuthorEmail()?></a></div>
          </div>
        </div>
      </div>
      <div class="data-main">
        <div class="data-main-left">
          <div class="description-block">
            <div class="head">
              <div class="ico">
                <i class="fa fa-file-text"></i>
              </div>
              <?=__('Leírás', 'gh')?>
            </div>
            <div class="text">
              <?=$prop->Description(true)?>
            </div>
          </div>
        </div>
        <div class="data-main-right">
          <div class="map-block">

          </div>
        </div>
      </div>
      <div class="history-list">
        <? echo do_shortcode('[listing-list view="simple-priced" src="viewed" limit="5"]'); ?>
      </div>
    </div>
	</div>
  <script type="text/javascript">
    (function($){
      $('.image-slide').slick({
        autoplay: true,
        centerMode: true,
        centerPadding: '60px',
        slidesToShow: 5,
      });
      $('.image-slide').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        var cs = $(slick.$slides).get(nextSlide);
        var ci = $(cs).find('img').attr('src');
        $('#profilimg a').attr('href', ci);
        $('#profilimg img').attr('src', ci);
      });
      $('.image-slide .slick-slide').on('click', function() {
        var i = $(this).data('slick-index');
        console.log(i);
      });
    })(jQuery);
  </script>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
