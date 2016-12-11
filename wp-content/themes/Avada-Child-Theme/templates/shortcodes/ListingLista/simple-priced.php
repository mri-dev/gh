<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <div class="status status-<?=$item->PropertyStatus()?>"><?=$item->PropertyStatus(true)?></div>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"><img src="<?=IMG?>/discount-label.svg" alt="<?=__('Leárazott', 'gh')?>" /></div><? endif; ?>
        <? if($imgnum = $item->imageNumbers()):?><div class="photo trans-on"><img src="<?=IMG?>/ico-photo-white.svg" alt="<?=__('Fényképek', 'gh')?>" /> <span class="nm"><?=$imgnum?></span></div><? endif; ?>
      </div>
      <?
        // Image attr
        $prof_img_attr = $item->ProfilImgAttr();
      ?>
      <div class="image or-<?=$prof_img_attr['orientation']?>">
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="sec-line">
      <div class="price"><?=$item->Price(true)?> <span class="type"><?=$item->PriceType()?></span></div>
    </div>
  </div>
</div>
