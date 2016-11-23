<?php
  global $me;
  $control  = get_control_controller('property_history');

  $param = array();

  if (isset($_GET['pid']) && !empty($_GET['pid'])) {
    $param['property_id'] = $_GET['pid'];
  }

  if (isset($_GET['u']) && !empty($_GET['u'])) {
    $param['user_id'] = $_GET['u'];
  }

  $list = $control->load( $param );

  if ( !current_user_can('administrator') ) {
    wp_redirect('/control/home');
  }
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan módosítások', 'gh')?></h1>
    <div class="desc"><?=__('Az alábbi listában találhatóak az ingatlanoknál történő módosítások.', 'gh')?></div>
  </div>
  <div class="gh_control_property_history_page">
    <?php if ($list['count'] == 0): ?>
    <?php
      ob_start();
      include(locate_template('templates/parts/nodata-property-history.php'));
      ob_end_flush();
    ?>
    <?php else: ?>
      <div class="modify-list">
      <?php foreach ($list['data'] as $c): $mods = $c->mods(); ?>
        <div class="modify-row">
          <div class="head">
            <div class="prof">
              <div class="img">
                <img src="<?=$c->property()->ProfilImg()?>" alt="">
              </div>
              <div class="title">
                <a href="<?=$c->property()->URL()?>"><?=$c->property()->Title()?></a> <a href="/control/property_history/?pid=<?=$c->property()->ID()?>&u=<?=$_GET['u']?>" title="<?=__('Csak ennek az ingatlannak a módosításait listázzuk', 'gh')?>" style="color: black; font-size: 0.8em;" href="#"><i class="fa fa-filter"></i></a>
              </div>
              <div class="meta">
                  <span class="code"><?=$c->property()->Azonosito()?></span>
                  <span class="region"><?=$c->property()->RegionName()?></span>

              </div>
              <div class="modifier">
                <span class="who"><a href="/control/property_history/?u=<?=$c->user()->ID()?>"><?=$c->user()->Name()?></a></span>
                <?=__('módosította, ekkor:', 'gh')?>
                <span class="date"><?=$c->Date()?></span>
                <span class="refid" title="<?=__('Esemény azonosító', 'gh')?>">&nbsp;&nbsp;(#<?=$c->ID()?>)</span>
              </div>
            </div>
            <div class="change-n">
              <div class="n"><?=count($mods)?></div>
              <?=__('változás', 'gh')?>
            </div>
          </div>
          <div class="mods">
            <div class="mcol mcolh">
              <div class="what">
                <?=__('Adatérték', 'gh')?>
              </div>
              <div class="dfrom">
                <?=__('Korábbi érték', 'gh')?>
              </div>
              <div class="dto">
                <?=__('Új érték', 'gh')?>
              </div>
            </div>
            <?php foreach ($mods as $key => $value): ?>
            <div class="mcol">
              <div class="what">
                <?=$c->modText($key)?>
              </div>
              <div class="dfrom key_<?=$key?>">
                <?=($value['f'] == '') ? 'n.a.' : $c->formatValue($key, $value['f'])?>
              </div>
              <div class="dto key_<?=$key?>">
                <?=$c->formatValue($key, $value['t'], $value['f'])?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
    <?php endif; ?>
    <pre><? //print_r($list); ?></pre>
  </div>
</div>
