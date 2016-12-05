<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <div class="status status-<?=$item->PropertyStatus()?>"><?=$item->PropertyStatus(true)?></div>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isNews()):?><div class="newi"><?=__('új')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'gh')?>" /></div><? endif; ?>
        <? if($imgnum = $item->imageNumbers()):?><div class="photo trans-on"><img src="<?=IMG?>/ico-photo-white.svg" alt="<?=__('Fényképek', 'gh')?>" /> <span class="nm"><?=$imgnum?></span></div><? endif; ?>
      </div>
      <div class="image">
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="prim-line">
      <div class="pos">
        <div class="region"><?=$item->RegionName()?></div>
        <div class="title"><?=$item->Title()?></div>
      </div>
      <div class="fav">
        <i class="fa fa-heart" favchecker data-fav-pid="<?=$item->ID()?>" title="<?=__('Ingatlanhirdetés mentése kedvencek közzé.', 'gh')?>"></i>
      </div>
    </div>
    <div class="sec-line">
      <div class="price"><?=$item->Price(true)?> <span class="type"><?=$item->PriceType()?></span></div>
      <div class="linkto"><a href="<?=$item->URL()?>"><?=__('Érdekel', 'gh')?></a></div>
    </div>
    <div class="important-options">
      <div class="opt">
        <div class="head"><?=__('Telek alapterület', 'gh')?></div>
        <div class="d">
          <?php $o = $item->getMetaValue('_listing_lot_size'); ?>
          <?php if (!empty($o)): ?>
            <div class="ico"><img src="<?=IMG."/ico/telek-alapterulet.svg"?>" alt="<?=__('Telek alapterület', 'gh')?>" /></div>
            <?=sprintf(__('%s nm', 'gh'), $item->getMetaValue('_listing_lot_size'))?>
          <?php else: ?>
            &mdash;&mdash;
          <?php endif; ?>
        </div>
      </div>
      <div class="opt">
        <div class="head"><?=__('Alapterület', 'gh')?></div>
        <div class="d">
          <?php $o = $item->getMetaValue('_listing_property_size'); ?>
          <?php if (!empty($o)): ?>
            <div class="ico"><img src="<?=IMG."/ico/alapterulet.svg"?>" alt="<?=__('Alapterület', 'gh')?>" /></div>
            <?=sprintf(__('%s nm', 'gh'), $item->getMetaValue('_listing_property_size'))?>
          <?php else: ?>
            &mdash;&mdash;
          <?php endif; ?>
        </div>
      </div>
      <div class="opt">
        <div class="head"><?=__('Szobák száma', 'gh')?></div>
        <div class="d">
          <?php $o = $item->getMetaValue('_listing_room_numbers'); ?>
          <?php if (!empty($o)): ?>
            <div class="ico"><img src="<?=IMG."/ico/szoba.svg"?>" alt="<?=__('Szobák száma', 'gh')?>" /></div>
            <?=sprintf(_n('%s szoba', '%s szoba', $item->getMetaValue('_listing_room_numbers'), 'gh'), $item->getMetaValue('_listing_room_numbers'))?>
          <?php else: ?>
            &mdash;&mdash;
          <?php endif; ?>
        </div>
      </div>
      <div class="opt">
        <div class="head"><?=__('Szintek száma', 'gh')?></div>
        <div class="d">
          <?php $o = $item->getMetaValue('_listing_level_numbers'); ?>
          <?php if (!empty($o)): ?>
            <div class="ico"><img src="<?=IMG."/ico/szint.svg"?>" alt="<?=__('Szintek száma', 'gh')?>" /></div>
            <?=sprintf(_n('%s szint', '%s szint', $item->getMetaValue('_listing_level_numbers'), 'gh'), $item->getMetaValue('_listing_level_numbers'))?>
          <?php else: ?>
            &mdash;&mdash;
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
