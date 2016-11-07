<div class="searcher-header">
  <ul>
    <li>
      <div class="ico">
        <i class="fa fa-search"></i>
      </div>
    </li><!--
 --><li>
      <input type="radio" checked="checked" id="region_all" name="region" value="0"> <label for="region_all"><?=__('Összes ingatlan', 'gh')?></label>
    </li><!--
 --><? foreach( array(1 => 'Budapest', 2 => 'Pécs') as $rid => $r ): ?><!--
  --><li>
      <input type="radio" id="region_<?=$rid?>" name="region" value="<?=$rid?>"> <label for="region_<?=$rid?>"><?=$r?></label>
    </li><!--
 --><? endforeach; ?>
  </ul>
</div>
<div class="searcher-wrapper">
  KERESŐ
</div>
<div class="searcher-footer">
  OPTIONS
</div>
