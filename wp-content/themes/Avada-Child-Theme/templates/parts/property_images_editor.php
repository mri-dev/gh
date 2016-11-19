<h4><?=__('Feltöltött képek', 'gh')?> (<?=$property->imageNumbers()?>)</h4>
<div class="row">
  <div class="col-md-12">
    <div class="image-set">
      <?php foreach ($images as $aid => $img): ?>
      <div class="image"><a href="<?=$img->guid?>"><img src="<?=$img->guid?>" alt=""></a></div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
