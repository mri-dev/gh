<?php
  get_header();
  $cp_page = $wp_query->query_vars['cp'];
  $xs = explode("-",$wp_query->query_vars['urlstring']);
  $ingatlan_id = end($xs);

  $properties = new Properties(array(
    'id' => $ingatlan_id,
    'post_status' => array('publish'),
    'lang' => get_locale()
  ));
  $property = $properties->getList();
  $prop = $property[0];
  if (!$prop || $prop->StatusKey() != 'publish') {
    wp_redirect('/');
  }
  $properties->logView();
  $regions = array();
  if ($regions) {
    $regions = $prop->Regions();
  }

  $backuri = $_COOKIE['__lastsearchuri'];
  if (!empty($backuri)) {
    $backuri = json_decode(base64_decode($backuri), true);
  }
?>
	<div id="content" <?php Avada()->layout->add_class( 'content_class' ); ?> <?php Avada()->layout->add_style( 'content_style' ); ?>>
    <div class="<?=SLUG_INGATLAN?>-page-view">
      <div class="floating-search-box">
        <? echo do_shortcode('[listing-searcher view="floating"]'); ?>
      </div>
      <?php if ($backuri): ?>
      <div class="backurl">
      <a href="<?=$backuri?>"><< <?=__('vissza a találati listához', 'gh')?></a>
      </div>
      <?php endif; ?>
      <div class="data-top">
        <div class="data-top-left">
          <div class="cwrapper">
            <div class="title">
              <h1>
                <?php
                $end_reg = end($regions);
                if(in_array($end_reg->name, $properties->fake_city)) { ?>
                  <span class="addr"><?=$end_reg->name?></span>
                <? }else{ ?>
                    <span class="addr"><?php $regtext = ''; foreach ($regions as $r ): $regtext .= $r->name.', '; endforeach; $regtext = rtrim($regtext, ', '); ?><?=$regtext?></span>
                <? } ?> <?=$prop->PropertyStatus(true)?> <?=$prop->Title()?>
              </h1>
              <div class="icons">
                <div class="facebook">
                  <a href="javascript:void(0);" onclick="window.open('https://www.facebook.com/dialog/share?app_id=<?=FB_APP_ID?>&amp;display=popup&amp;href=<?=PROTOCOL.'://'.DOMAIN?><?=$_SERVER['REQUEST_URI']?><?=($_GET['share']=='')?'?share=fb'.((is_user_logged_in())?'.u-'.get_current_user_id():''):''?>&amp;redirect_uri=<?=PROTOCOL.'://'.DOMAIN?>/close.html','','width=800, height=240')"><i class="fa fa-facebook"></i></a>
                </div>
              </div>
            </div>
            <div class="images">
              <?php $profil_attr = $prop->ProfilImgAttr(); ?>
              <div class="profil or-<?=$profil_attr['orientation']?>" id="profilimg">
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
                    <? foreach( $newimgs as $img ):?>
                    <div class="i or-<?=$img->params['orientation']?>">
                      <img src="<?=$img->guid?>" alt="<?=$prop->Title()?>" />
                    </div>
                    <? endforeach; ?>
                  </div>
                </div>
              </div>
              <? endif; ?>
            </div>
            <?php $desc = $prop->Description(true); ?>
            <?php if ($desc): ?>
            <div class="description-block">
              <div class="head">
                <div class="ico">
                  <i class="fa fa-file-text"></i>
                </div>
                <?=__('Leírás', 'gh')?>
              </div>
              <div class="text">
                <?=$desc?>
              </div>
            </div>
            <?php endif; ?>
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
        <div class="data-top-right">
          <div class="properties">
            <?php if ($prop->isDropOff()): ?>
            <div class="header col-grey">
              <div class="old-price">
                <?=$prop->OriginalPrice(true)?>
              </div>
              <div class="clearfix"></div>
            </div>
            <?php endif; ?>
            <div class="header">
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
             --><div class="v"><?=($regio)?$regio:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?><?php if ($prop->iShowAddress()): ?>
                <div class="address"><?php echo $prop->Address(); ?></div>
             <?php endif; ?></div>
              </div>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/szoba.svg" alt="<?=__('Szobák száma', 'gh')?>"></div>
                 <?=__('Szobák száma', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $prop->getMetaValue('_listing_room_numbers'))?$v:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <?php $v = $prop->getMetaValue('_listing_lot_size'); ?>
              <?php if(!empty($v)): ?>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/telek-alapterulet.svg" alt="<?=__('Telek területe', 'gh')?>"></div>
                 <?=__('Telek területe', 'gh')?>
               </div><!--
            --><div class="v"><?=($v)?sprintf(__('%d nm', 'gh'), $v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div><? endif; ?>
              <?php $v = $prop->getMetaValue('_listing_property_size'); ?>
              <?php if(!empty($v)): ?>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/alapterulet.svg" alt="<?=__('Alapterület', 'gh')?>"></div>
                 <?=__('Alapterület', 'gh')?>
               </div><!--
            --><div class="v"><?=($v)?sprintf(__('%d nm', 'gh'), $v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div><? endif; ?>
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
            --><div class="v"><?=($v = $prop->PropertyHeating(true))?$prop->multivalue_list($v):'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <?php if ( $ch = $prop->getCustomHeating($v)): ?>
              <div class="e">
               <div class="h">
                 <div class="ico"><img src="<?=IMG?>/ico/heating.svg" alt="<?=__('Egyéb fűtés', 'gh')?>"></div>
                 <?=__('Egyéb fűtés', 'gh')?>
               </div><!--
            --><div class="v"><?=($v = $ch)?$ch:'<span class="na">'.__('nincs megadva', 'gh').'</span>'?></div>
              </div>
              <?php endif; ?>
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
              <?php if ( true ): ?>
                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Garázs', 'gh')?>"></div>
                   <?=__('Garázs', 'gh')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_garage'))?'<span>'.__('Van', 'gh').'</span>':'<span>'.__('Nincs', 'gh').'</span>'?></div>
                </div>

                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Erkély', 'gh')?>"></div>
                   <?=__('Erkély', 'gh')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_balcony'))?'<span>'.__('Van', 'gh').'</span>':'<span>'.__('Nincs', 'gh').'</span>'?></div>
                </div>

                <div class="e">
                 <div class="h">
                   <div class="ico"><img src="<?=IMG?>/ico/allapot.svg" alt="<?=__('Lift', 'gh')?>"></div>
                   <?=__('Lift', 'gh')?>
                 </div><!--
              --><div class="v"><?=($v = $prop->getMetaValue('_listing_lift'))?'<span>'.__('Van', 'gh').'</span>':'<span>'.__('Nincs', 'gh').'</span>'?></div>
                </div>
              <?php else: ?>
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
              <?php endif; ?>
            </div>
            <div class="ref-number">
              <div class="row">
                <div class="col-md-6"><?=__('Referenciaszám', 'gh')?></div>
                <div class="col-md-6"><div class="n"><strong><?=$prop->Azonosito()?></strong></div></div>
              </div>
            </div>
          </div>
          <div class="contact">
            <?php
            $avatar = $prop->AuthorImage(200);
            $rate = $prop->AuthorRate();

            if ($avatar): ?>
            <div class="avatar"><div class="in"><?php echo $avatar; ?></div></div>
            <?php endif; ?>
            <?php if ($rate > 0): ?>
            <div class="rate">
              <?php for ($i=1; $i <= 5; $i++) { ?>
                <i class="fa fa-star<?=($rate >= $i)?' hl':''?>"></i>
              <? } ?>
            </div>
            <?php endif; ?>
            <div class="name"><i class="fa fa-user"></i> <?=$prop->AuthorName()?></div>
            <div class="phone"><i class="fa fa-phone"></i> <a href="tel:<?=$prop->AuthorPhone()?>"><?=$prop->AuthorPhone()?></a></div>
            <div class="email"><i class="fa fa-envelope"></i> <a href="mailto:<?=$prop->AuthorEmail()?>"><?=$prop->AuthorEmail()?></a></div>
          </div>
          <?php $kiemelt_title = $prop->KiemeltTitle(); ?>
          <?php if ($kiemelt_title): ?>
          <div class="primary-title">
            <?php echo $kiemelt_title; ?>
          </div>
          <?php endif; ?>
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
        responsive: [
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
            }
          }
        ]
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
