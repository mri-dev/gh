<? if( ($imgn = $property->imageNumbers()) != 0 ): ?>
<h4><?=__('Feltöltött képek', 'gh')?> (<?=$imgn?>)</h4>
<div class="row">
  <div class="col-md-12">
    <div class="image-set">
      <?php foreach ($images as $aid => $img): ?>
      <div class="image">
        <div class="iwrapper">
          <a href="<?=$img->guid?>"><img src="<?=$img->guid?>" alt=""></a>
          <? if($property->ProfilImgID() != $aid): ?>
          <div class="delete_selector">
            <input type="checkbox" id="delete_img_<?=$aid?>" name="extra[deleting_imgs][<?=$aid?>]" value="1"><label for="delete_img_<?=$aid?>"><?=__('töröl', 'gh')?></label>
          </div>
          <? endif; ?>
          <div class="profil_selector">
            <input type="radio" id="profil_img_<?=$aid?>" <?=($property->ProfilImgID() == $aid)?'checked="checked"':''?> name="extra[feature_img_id]" value="<?=$aid?>"><label for="profil_img_<?=$aid?>"><?=__('főkép', 'gh')?></label>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
      <input type="hidden" name="pre[extra][feature_img_id]" value="<?=$property->ProfilImgID()?>">
    </div>
  </div>
</div>
<? endif; ?>
