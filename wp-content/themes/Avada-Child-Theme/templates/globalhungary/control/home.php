<?php
  global $me;

  $dashboard = get_control_controller('dashboard');

  if (current_user_can('administrator') || current_user_can('region_manager')) {
    // History
    $param = array();
    $param['limit'] = 10;
    $history = $dashboard->HistoryList($param);
  }

  // Last view
  $param = array();
  $param['limit'] = 10;

  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {

    } else {
      $param['only_me'] = array($me->ID());
    }
  }

  $watch = $dashboard->LiveWatchedProperties($param);

  // Clicks
  $param = array();
  $param['month'] = date('Y-m');
  $click_30day_total = $dashboard->ClickNumbers($param);

  $param = array();
  $param['unique'] = true;
  $param['month'] = date('Y-m');
  $click_30day_unique = $dashboard->ClickNumbers($param);


?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Gépház', 'gh')?></h1>
    <div class="desc"><?=sprintf(__('Üdvözöljük a %s ingatlanközvetítő adminisztrációs felületén.', 'gh'), get_option('blogname', '--' ))?></div>
  </div>
  <div class="gh_control_dashboard dashboard-view">
    <div class="row stick-bgh">
      <div class="col-md-4">
        <div class="bgh">
          <div class="vis vis-red">
            <i class="fa fa-home"></i>
          </div>
          <div class="inf">
            <div class="num"><?=number_format(1000000, 0, ".",".")?></div>
            <div class="text"><?=__('Aktív ingatlanhirdetés', 'gh')?></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="bgh">
          <div class="vis vis-lorange">
            <i class="fa fa-mouse-pointer"></i>
          </div>
          <div class="inf">
            <div class="num"><?=number_format($click_30day_total, 0, ".",".")?></div>
            <div class="text"><?=__('Hirdetés oldalbetöltés a hónapban', 'gh')?></div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="bgh">
          <div class="vis vis-lgreen">
            <i class="fa fa-paper-plane"></i>
          </div>
          <div class="inf">
            <div class="num"><?=number_format($click_30day_unique, 0, ".",".")?></div>
            <div class="text"><?=__('Egyedi látogatás a hónapban', 'gh')?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <?php if(current_user_can('administrator') || current_user_can('region_manager')): ?>
      <div class="col-md-6">
        <div class="bgh list-bgh">
          <div class="head">
            <i class="fa fa-history"></i> <?=__('Utoljára módosított ingatlanok', 'gh')?>
          </div>
          <div class="c">
            <div class="data-table">
              <div class="data-head">
                <div class="row">
                  <div class="col-md-4">
                    <?=__('Ingatlan', 'gh')?>
                  </div>
                  <div class="col-md-3">
                    <?=__('Módosította', 'gh')?>
                  </div>
                  <div class="col-md-2">
                    <?=__('Módosítások', 'gh')?>
                  </div>
                  <div class="col-md-3">
                    <?=__('Időpont', 'gh')?>
                  </div>
                </div>
              </div>
              <div class="data-body">
                <?php foreach ($history['data'] as $c): $mods = $c->mods(); ?>
                <div class="row">
                  <div class="col-md-4">
                    <a href="/control/property_history/?u=&azon=<?=$c->property()->Azonosito()?>">[<?=$c->property()->Azonosito()?>] <strong><?=$c->property()->Title()?></strong></a>
                  </div>
                  <div class="col-md-3 center">
                    <a href="/control/property_history/?u=<?=$c->user()->ID()?>"><?=$c->user()->Name()?></a>
                  </div>
                  <div class="col-md-2 center">
                    <?=count($mods)?> <?=__('elem', 'gh')?>
                  </div>
                  <div class="col-md-3 center">
                    <?=$c->Date()?>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <div class="foot">
            <div class="pull-left">
              <strong><?=sprintf(__('%d db elem megjelenítve', 'gh'), $history['page']['limit'])?></strong> (<?php echo sprintf(__('%d db összesen'), $history['count']); ?>)
            </div>
            <a href="/control/property_history" class="pull-right"><?=__('Összes módosítás', 'gh')?> <i class="fa fa-arrow-circle-right"></i></a>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
      <?php endif; ?>
      <div class="col-md-6">
        <div class="bgh list-bgh">
          <div class="head">
            <i class="fa fa-eye"></i> <?=__('Utoljára megtekintett ingatlanok', 'gh')?>
          </div>
          <div class="c">
            <div class="data-table">
              <div class="data-head">
                <div class="row">
                  <div class="col-md-4">
                    <?=__('Ingatlan', 'gh')?>
                  </div>
                  <div class="col-md-3">
                    <?=__('Régió', 'gh')?>
                  </div>
                  <div class="col-md-2">
                    <?=__('Referens', 'gh')?>
                  </div>
                  <div class="col-md-3">
                    <?=__('Idő', 'gh')?>
                  </div>
                </div>
              </div>
              <div class="data-body">
                <?php foreach ($watch['data'] as $c):?>
                <div class="row">
                  <div class="col-md-4">
                    <a href="<?=$c->URL()?>">[<?=$c->Azonosito()?>] <strong><?=$c->Title()?></strong></a>
                  </div>
                  <div class="col-md-3 center">
                    <?=$c->RegionName()?>
                  </div>
                  <div class="col-md-2 center">
                    <a href="/control/properties/?user=<?=$c->AuthorID()?>"><?=$c->AuthorName()?></a>
                  </div>
                  <div class="col-md-3 center">
                    <span title="<?=$watch['times'][$c->ID()]?>"><?=Helper::distanceDate($watch['times'][$c->ID()])?></span>
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="bgh list-bgh">
          <div class="head">
            <i class="fa fa-pie-chart"></i> <?=__('Ingatlan nézettség az elmúlt 90 napban', 'gh')?>
          </div>
          <div class="c">
            asd
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
