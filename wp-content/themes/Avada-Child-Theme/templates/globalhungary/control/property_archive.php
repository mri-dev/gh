<?php
  global $me;
  $editor   = get_control_controller('property_edit');
  $property = $editor->load($_GET['id']);

  if ( $property->isArchived() ) {
    wp_redirect('/control/property_edit/?id='.$_GET['id']);
  }
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan archiválása', 'gh')?></h1>
  </div>
  <? if(!$me->can('property_archive') && !current_user_can('administrator')): ?>
  <div class="alert alert-danger"><?=__('Ön nem jogosult ingatlan archiválásra. Vegye fel a kapcsolatot felettesével vagy az oldal üzemeltetőjével', 'gh')?></div>
  <? else: ?>
  <form class="wide-form" action="/control/property_save_archive" method="post">
    <input type="hidden" name="post_date" value="<?=$property->CreateAt()?>">
    <input type="hidden" name="property_id" value="<?=$property->ID()?>">
    <h4><?=sprintf(__('Biztos, hogy archiválja a(z) <strong>%s</strong> (%s) ingatlant?'), $property->Title(), $property->Azonosito())?></h4>
    <div class="row">
      <div class="col-md-12 reqf">
        <label for="why"><?=__('Kérjük, hogy indokolja meg miért archiválja az ingatlanhirdetést', 'gh')?></label>
        <textarea name="why" style="height: 110px;" class="form-control"></textarea>
        <a href="/control/property_edit/?id=<?=$_GET['id']?>" class="btn pull-left"><i class="fa fa-arrow-circle-left"></i> <?=__('mégse', 'gh')?></a>
        <button type="submit" class="btn btn-red pull-right" name="doArchive" value=""><?=__('Archiválás indítása', 'gh')?> <i class="fa fa-archive"></i></button>
        <div class="clearfix">

        </div>
        <?php if ( current_user_can('administrator') || $me->can('property_archive_autoconfirm') ): ?>
          <div class="pull-right info-msg-green">
            <input type="hidden" name="access" value="1">
            <?=__('Ön archiválási kérvényt nyújt be az űrlap elküldésével. A kérését azonnal végrehajtódik és nem vonható vissza!', 'gh')?>
          </div>
        <?php else: ?>
          <div class="pull-right info-msg-red">
            <input type="hidden" name="access" value="0">
            <?=__('Ön archiválási kérvényt nyújt be az űrlap elküldésével. A kérését régióvezetőnk feldolgozza, és ha jogszerűnek találjuk kérését, jóváhagyjuk archiválási szándékát.', 'gh')?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </form>
  <? endif; ?>
</div>
