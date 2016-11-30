<?php
  global $me;

  if ( !current_user_can('administrator') && !$me->can('property_archive_mod') ) {
    wp_redirect('/control/home/');
  }
  $arg = array();

  $arg['list_archive'] = true;
  $arg['orderby'] = 'post__in';

  $properties = new Properties( $arg );
  $list = $properties->getList();
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlanok archiválási kérelmek', 'gh')?> <span class="badge"><?=$properties->Count()?></span></h1>
  </div>
  <div class="gh_control_property_archive_page">

    <?php if (isset($_GET['afterPost'])): ?>
      <?php if ($_GET['back'] == 'disallow'): ?>
        <div class="alert alert-danger"><?=__('Ön elutasított egy archiválási kérelmet.', 'gh')?></div>
      <?php endif; ?>
      <?php if ($_GET['back'] == 'allow'): ?>
        <div class="alert alert-success"><?=__('Ön elfogadott egy archiválási kérelmet. Az ingatlan archiválva lett.', 'gh')?></div>
      <?php endif; ?>

    <?php endif; ?>

    <div class="modify-list">
    <?php foreach ($list as $c): $arcdata = $c->ArchivingData(); ?>
      <div class="modify-row">
        <div class="head">
          <div class="prof">
            <div class="img">
              <img src="<?=$c->ProfilImg()?>" alt="">
            </div>
            <div class="title">
              <a href="<?=$c->URL()?>"><?=$c->Title()?></a>
            </div>
            <div class="meta">
              <span class="code"><?=$c->Azonosito()?></span>
              <span class="region"><?=$c->RegionName()?></span>
            </div>
            <div class="modifier">
              <span class="who"><a title="<?=__('Felhasználó ingatlan hirdetései')?>" href="/control/properties/?user=<?=$c->AuthorID()?>"><?=$c->AuthorName()?></a></span>
              <?=__('által létrehozott ingatlan', 'gh')?>
            </div>
          </div>
          <div class="change-n">
            <form class="" action="/control/arcive_requests_save/" method="post">
              <input type="hidden" name="rid" value="<?=$arcdata->ID?>">
              <button type="submit" class="fusion-button button-square button-small button-flat button-green" name="allow" value="1"><?=__('Elfogad', 'gh')?> <i class="fa fa-check"></i></button><br><br>
              <button type="submit" class="fusion-button button-square button-small button-flat button-red" name="disallow" value="1"><?=__('Elutasít', 'gh')?> <i class="fa fa-times"></i></button>
            </form>
          </div>
        </div>
        <div class="mods">
          <div class="archive-comment">
            <div class="text">
              <sub>&quot;</sub> <?=$arcdata->comment?> <sup>&quot;</sup>
            </div>
            <div class="who">
              <?php $arc_who = new UserHelper(array('id' => $arcdata->userID)); ?>
              &mdash; <strong><?=$arc_who->Name()?></strong> <?=__('kérelmezte ekkor:', 'gh')?> <?=$arcdata->regDate?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    </div>
  </div>
</div>
