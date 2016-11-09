<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="top-wp">
      <div class="features">
        <div class="status status-<?=$item->PropertyStatus()?>"><?=$item->PropertyStatus(true)?></div>
        <? if($item->isHighlighted()):?><div class="highlight"><?=__('Kiemelt', 'gh')?></div><? endif; ?>
        <? if($item->isDropOff()):?><div class="dropoff"></div><? endif; ?>
      </div>
      <div class="image">
        <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
        <? if( ($excp = $item->ShortDesc()) != "" ): ?>
        <div class="excerpt transf"><?=$excp?></div>
        <? endif; ?>
      </div>
    </div>
    <div class="sec-line">
      <div class="price"><?=$item->Price(true)?></div>
    </div>
  </div>
</div>
