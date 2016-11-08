<div class="prop-item">
  <div class="prop-item-wrapper">
    <div class="features">

    </div>
    <div class="image">
      <a title="<?=$item->Title()?>" href="<?=$item->URL()?>"><img src="<?=$item->ProfilImg()?>" alt="<?=$item->Title()?>" /></a>
      <? if( ($excp = $item->ShortDesc()) != "" ): ?>
      <div class="excerpt transf"><?=$excp?></div>
      <? endif; ?>
    </div>
    <div class="line-region">
      <?=$item->RegionName()?>
    </div>
    <div class="line-price">
      <?=$item->Price(true)?>
    </div>
  </div>
</div>
