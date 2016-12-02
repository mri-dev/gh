<?php
  global $me;
  $control  = get_control_controller('property_history');
  $filtered = false;

  $param = array();

  if (isset($_GET['pid']) && !empty($_GET['pid'])) {
    $param['property_id'] = $_GET['pid'];
    $filtered = true;
  }

  if (isset($_GET['azon']) && !empty($_GET['azon'])) {
    $param['azon'] = $_GET['azon'];
    $filtered = true;
  }

  if (isset($_GET['u']) && !empty($_GET['u'])) {
    $param['user_id'] = $_GET['u'];
    $selected_user = new UserHelper(array('id'=> $_GET['u']));
    $filtered = true;
  }

  if (isset($_GET['page']) && !empty($_GET['page'])) {
    $param['page'] = (int)$_GET['page'];
  }

  $list = $control->load( $param );

  $pager = paginate_links( array(
    'base'   => '/control/property_history/%_%',
    'format'  => '?page=%#%',
    'current' => max( 1, get_query_var('page') ),
    'total'   => $list['page']['max']
  ) );

  if ( !current_user_can('administrator') && !current_user_can('region_manager') ) {
    wp_redirect('/control/home');
  }
?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan módosítások', 'gh')?> <span class="badge"><?=$list['count']?></span></h1>
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
      <?php if ($filtered): ?>
        <a href="/control/property_history/">< <?=__('Teljes lista mutatása', 'gh')?></a> <br>
        <?php if ($selected_user): ?>
          <?=sprintf(__('Kiválasztott felhasználó: <strong>%s</strong> által módosítottak', 'gh'), $selected_user->Name())?><br>
        <?php endif; ?>
        <?php if (isset($_GET['pid'])): ?>
          <?=sprintf(__('Kiválasztott ingatlan: <strong>%s számú ingatlan változások</strong>', 'gh'), $_GET['pid'])?><br>
        <?php endif; ?>
        <br>
      <?php endif; ?>

      <form action="/control/property_history/" method="get" class="pull-right">
        <input type="hidden" name="u" value="<?=$_GET['u']?>">
        <div class="inline-input">
          <div>
            <input type="text" name="azon" class="pull-right" id="refid" placeholder="<?=__('Referenciaszám', 'gh')?>" class="form-control" value="<?=$_GET['azon']?>">
          </div>
          <div>
            <button type="submit" class="fusion-button button-flat button-square button-small button-neutral"><?=__('Keresés')?> <i class="fa fa-search"></i></button>
          </div>
        </div>
      </form>

      <div class="clearfix"></div>

      <div class="pagination">
        <?php echo $pager; ?>
      </div>

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
            <?php foreach ($mods as $key => $value): $is_action = (!is_null($value['f'])) ? false : true; ?>
            <div class="mcol <?=($is_action)?'mcolact':''?>">
              <div class="what">
                <?=$c->modText($key)?>
              </div>
              <?php if ($is_action): ?>
                <div class="dact key_<?=$key?>">
                  <?=( empty($value) ) ? 'n.a.' : $c->formatValue($key, $value)?>
                </div>
              <?php else: ?>
                <div class="dfrom key_<?=$key?>">
                  <?=($value['f'] == '') ? 'n.a.' : $c->formatValue($key, $value['f'])?>
                </div>
                <div class="dto key_<?=$key?>">
                  <?=$c->formatValue($key, $value['t'], $value['f'])?>
                </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
      </div>
      <div class="pagination">
        <?php echo $pager; ?>
      </div>
    <?php endif; ?>
    <pre><? //print_r($list); ?></pre>
  </div>
</div>
