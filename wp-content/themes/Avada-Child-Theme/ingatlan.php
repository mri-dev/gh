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
  if (!$prop || $prop->StatusKey() != 'publish') {
    wp_redirect('/');
  }
  $properties->logView();
  $regions = $prop->Regions();

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
              <h1><?=$prop->Title()?></h1>
              <div class="subtitle">
                <?php
                $end_reg = end($regions);
                if(in_array($end_reg->name, $properties->fake_city)) { ?>
                  <span class="addr"><i class="fa fa-map-marker"></i> <?=$end_reg->name?></span>
                <? }else{ ?>
                    <span class="addr"><i class="fa fa-map-marker"></i> <?php $regtext = ''; foreach ($regions as $r ): $regtext .= $r->name.' / '; endforeach; $regtext = rtrim($regtext, ' / '); ?><?=$regtext?></span>
                <? } ?>

                <strong><?=$prop->PropertyStatus(true)?> <?=$prop->multivalue_list($prop->PropertyType(true), true, '/'.SLUG_INGATLAN_LIST.'/?c=#value#')?></strong>
              </div>
              <div class="icons">
                <div class="facebook">
                  <a href="javascript:void(0);" onclick="window.open('https://www.facebook.com/dialog/share?app_id=<?=FB_APP_ID?>&amp;display=popup&amp;href=<?=PROTOCOL.'://'.DOMAIN?><?=$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>&amp;redirect_uri=<?=PROTOCOL.'://'.DOMAIN?>/close.html','','width=800, height=240')"><i class="fa fa-facebook"></i></a>
                </div>
                <div class="gplus">
                  <a href="https://plus.google.com/share?url=<?=get_option('siteurl', '').$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>" onclick="javascript:window.open('https://plus.google.com/share?url=<?=PROTOCOL.'://'.DOMAIN?><?=$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>', '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"><i class="fa fa-google-plus"></i></a>
                </div>
              </div>
            </div>
            <div class="images">
              <div class="profil" id="profilimg">
                <a data-rel="iLightbox[p<?=$prop->ID()?>]" class="fusion-lightbox" data-title="<?=$prop->Title()?>" href="<?=$prop->ProfilImg()?>"><img src="<?=$prop->ProfilImg()?>" alt=""></a>
              </div>
              <?
                $pimgid = $prop->ProfilImgID();
                $images = $prop->Images();
                $imn    = $prop->imageNumbers();
                $newimgs = array();
                $newimgs[$pimgid] = $images[$pimgid];
                unset($images[$pimgid]);
                foreach ($images as $iid => $iv) {
                  $newimgs[$iid] = $iv;
                }
              ?>
              <? foreach( $newimgs as $img ): if($img->ID == $pimgid){ continue; } ?>
                <a href="<?=$img->guid?>" data-rel="iLightbox[p<?=$prop->ID()?>]" style="display: none;" class="fusion-lightbox" data-title="<?=$prop->Title()?>"><img src="<?=$img->guid?>" alt="<?=$prop->Title()?>" /></a>
              <? endforeach; ?>
              <? if(  $imn > 1 ): ?>
              <div class="stack">
                <div class="stack-wrapper">
                  <div class="items image-slide">
                    <? foreach( $newimgs as $img ): ?>
                    <div class="i">
                      <img src="<?=$img->guid?>" alt="<?=$prop->Title()?>" />
                    </div>
                    <? endforeach; ?>
                  </div>
                </div>
              </div>
              <? endif; ?>
            </div>
          </div>
        </div>
        <div class="data-top-right">
          <div class="properties">
            <div class="header">
              <?php if ($prop->isDropOff()): ?>
                <div class="old-price">
                  <?=$prop->OriginalPrice()?> <?=$prop->PriceType()?>
                </div>
              <?php endif; ?>
              <div class="current-price">
                <?=$prop->Price(true)?> <span class="type"><?=$prop->PriceType()?></span>
              </div>
              <div class="clearfix"></div>
            </div>
            <div class="list">
              <?php
                $regio = $prop->RegionName();
              ?>
              <div class="e">
                <div class="h">
                  <div class="ico"><img src="<?=IMG?>/ico/telepules.svg" alt="<?=__('Település', 'gh')?>"></div>
                  <?=__('Település', 'gh')?>
                </div><!--
             --><div class="v"><?=($regio)?$regio:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/szoba.svg" alt="<?=__('Szobák száma', 'gh')?>"></div>
                 <?=__('Szobák száma', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->getMetaValue('_listing_room_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/telek-alapterulet.svg" alt="<?=__('Telek területe', 'gh')?>"></div>
                 <?=__('Telek területe', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->getMetaValue('_listing_lot_size'))?sprintf(__('%d nm', 'gh'), $v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/alapterulet.svg" alt="<?=__('Alapterület', 'gh')?>"></div>
                 <?=__('Alapterület', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->getMetaValue('_listing_property_size'))?sprintf(__('%d nm', 'gh'), $v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/szint.svg" alt="<?=__('Szintek száma', 'gh')?>"></div>
                 <?=__('Szintek száma', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->getMetaValue('_listing_level_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/heating.svg" alt="<?=__('Fűtés', 'gh')?>"></div>
                 <?=__('Fűtés', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->PropertyHeating(true))?$v:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/payment.svg" alt="<?=__('Megbízás típusa', 'gh')?>"></div>
                 <?=__('Megbízás típusa', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->PropertyStatus(true))?$v:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/home.svg" alt="<?=__('Ingatlan típusa', 'gh')?>"></div>
                 <?=__('Ingatlan típusa', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->PropertyType(true))?$prop->multivalue_list($v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Állapot', 'gh')?>"></div>
                 <?=__('Állapot', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->PropertyCondition(true))?$prop->multivalue_list($v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
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
          <?php
            $docs = $prop->PDFDocuments();
          ?>
          <?php if ($docs): ?>
            <div class="description-block">
              <div class="head">
                <div class="ico">
                  <i class="fa fa-file-pdf-o"></i>
                </div>
                <?=__('Dokumentumok', 'gh')?> (<?=count($docs)?>)
              </div>
              <div class="text doc-list">
                <?php
                foreach ($docs as $doc):
                  $size = filesize( get_attached_file( $doc->ID ) );
                  $file = str_replace(array('application/'), '', $doc->post_mime_type);
                ?>
                  <div class="doc">
                    <a href="<?=$doc->guid?>" target="_blank"><i class="fa fa-file-pdf-o"></i> <strong><?=$doc->post_title?></strong> <span class="doc-info">(<?=$file?> &mdash; <?=\Helper::filesize($size, 0)?>)</span></a>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
        <div class="data-main-right">
          <div class="map-block">
            <?
              $gps = $prop->GPS();
              $gps_term_id = $regio->term_id;
              ob_start();
              include(locate_template('/templates/parts/map_place_poi.php'));
              ob_end_flush();
            ?>
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
        <?=(count($images)>5)?'centerMode: true,':''?>
        autoplay: false,
        centerPadding: '60px',
        slidesToShow: 5,
      });

      $('.image-slide .slick-slide').on('click', function(e) {
        //e.stopPropagation();
        var index = $(this).data("slick-index");
        if ($('.image-slide').slick('slickCurrentSlide') !== index) {
          $('.image-slide').slick('slickGoTo', index);
        }

        console.log(index);
      });

      $('.image-slide').on('beforeChange', function(event, slick, currentSlide, nextSlide){
        var cs = $(slick.$slides).get(nextSlide);
        var ci = $(cs).find('img').attr('src');
        //$('#profilimg a').attr('href', ci);
        $('#profilimg img').attr('src', ci);
        console.log(ci);
      });

    })(jQuery);
  </script>
	<?php do_action( 'fusion_after_content' ); ?>
<?php get_footer();

// Omit closing PHP tag to avoid "Headers already sent" issues.
