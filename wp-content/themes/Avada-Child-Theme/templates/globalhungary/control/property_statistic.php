<?php
  global $me;

  $dashboard = get_control_controller('dashboard');

  $dt_selected = false;
  $dt_from = date('Y / m').' / 01';
  $dt_to = date('Y / m / d');

  if (isset($_GET['from']) && !empty($_GET['from'])) {
    $dt_from = str_replace("-"," / ", $_GET['from']);
  }

  if (isset($_GET['to']) && !empty($_GET['to'])) {
    $dt_to = str_replace("-"," / ", $_GET['to']);
  }

  $dt_from_s = str_replace(" / ", "-", $dt_from);
  $dt_to_s = str_replace(" / ", "-", $dt_to);

  if ( current_user_can('region_manager') )
  {
    $ul = new GlobalHungaryUsers(array(
      'region' => $me->RegionID()
    ));
    $region_users = $ul->getUsers();
    $region_user_ids = $ul->userIDS();
  }

  // Last view
  $param = array();
  $param['limit'] = 10;
  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {
      $param['only_me'] = $region_user_ids;
    } else {
      $param['only_me'] = array($me->ID());
    }
  }

  $watch = $dashboard->LiveWatchedProperties($param);

  // Popular view
  $param = array();
  $param['limit'] = 10;
  $param['datetime'] = array(
    'from' => $dt_from_s,
    'to' => $dt_to_s
  );

  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {
      $param['authors'] = $region_user_ids;
    } else {
      $param['authors'] = array($me->ID());
    }
  }

  $popular = $dashboard->PopularProperties($param);

  // Properties count
  $param = array();
  $param['limit'] = 1;
  $param['post_status'] = 'publish';
  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {
      $param['authors'] = $region_user_ids;
    } else {
      $param['authors'] = array($me->ID());
    }
  }
  $prop_count = $dashboard->PropertyCount($param);

  // Clicks
  $param = array();
  $param['datetime'] = array(
    'from' => $dt_from_s,
    'to' => $dt_to_s
  );
  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {
      $param['authors'] = $region_user_ids;
    } else {
      $param['authors'] = array($me->ID());
    }
  }
  $click_30day_total = $dashboard->ClickNumbers($param);

  $param = array();
  $param['unique'] = true;
  $param['datetime'] = array(
    'from' => $dt_from_s,
    'to' => $dt_to_s
  );
  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {
      $param['authors'] = $region_user_ids;
    } else {
      $param['authors'] = array($me->ID());
    }
  }
  $click_30day_unique = $dashboard->ClickNumbers($param);

  // Click stat
  $param = array();
  $param['datetime'] = array(
    'from' => $dt_from_s,
    'to' => $dt_to_s
  );
  if (!current_user_can('administrator')) {
    if (current_user_can('region_manager')) {
      $param['authors'] = $region_user_ids;
    } else {
      $param['authors'] = array($me->ID());
    }
  }
  $stat_click_all = $dashboard->ClickNumberStat($param);

  $selected_date = $dt_from . ' &mdash; '.$dt_to;

?>
<div class="gh_control_content_holder">
  <div class="heading">
    <h1><?=__('Ingatlan statisztika', 'gh')?></h1>
    <div class="pull-to-title">
      <form class="" action="" method="get">
        <div class="inline-input">
          <div class="">
            <input type="date" name="from" value="<?=$dt_from_s?>" class="form-control">
          </div>
          <div class="">
            &mdash;
          </div>
          <div class="">
            <input type="date" name="to" value="<?=$dt_to_s?>" class="form-control">
          </div>
          <div class="">
            <button type="submit" class="fusion-button button-flat button-square button-small button-neutral"><?=__('Végrehajt', 'gh')?> <i class="fa fa-filter"></i></button>
          </div>
          <?php if (isset($_GET['from'])): ?>
            <div class="">
              <a title="<?=__('Kiválasztott időpont eltávolítása.','gh')?>" href="/control/property_statistic/" class="fusion-button button-flat button-square button-small button-red"><i class="fa fa-times"></i></a>
            </div>
          <?php endif; ?>
        </div>
      </form>
    </div>
    <div class="clearfix"></div>
    <div class="desc"></div>
  </div>
  <div class="gh_control_dashboard dashboard-view">
    <?php if (current_user_can('region_manager')): ?>
      <div class="stat-restricter">
        <?=sprintf(__('<strong>%s</strong> régió adatai alapján', 'gh'), $me->RegionName())?>
      </div>
    <?php elseif(current_user_can('reference_manager')): ?>
      <div class="stat-restricter">
        <?=sprintf(__('<strong>%s</strong> adatai alapján', 'gh'), $me->Name())?>
      </div>
    <?php endif; ?>

    <div class="row">
      <div class="col-md-12">
        <div class="bgh list-bgh">
          <div class="head">
            <i class="fa fa-pie-chart"></i> <?=sprintf(__('Ingatlan nézettség <span class="dt-pick">%s</span>', 'gh'), $selected_date)?>
          </div>
          <div class="c">
            <div id="clickStats"></div>
          </div>
        </div>
      </div>
    </div>

    <div class="row stick-bgh">
      <div class="col-md-4">
        <div class="bgh">
          <div class="vis vis-red">
            <i class="fa fa-home"></i>
          </div>
          <div class="inf">
            <div class="num"><?=number_format($prop_count, 0, ".",".")?></div>
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
            <div class="text"><?=__('Hirdetés oldalbetöltés', 'gh')?></div>
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
            <div class="text"><?=__('Egyedi látogatás', 'gh')?></div>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="bgh list-bgh">
          <div class="head">
            <i class="fa fa-line-chart"></i> <?=__('10 legnézettebb ingatlan', 'gh')?>
          </div>
          <div class="c">
            <div class="data-table">
              <div class="data-head">
                <div class="row">
                  <div class="col-md-10">
                    <?=__('Ingatlan', 'gh')?>
                  </div>
                  <div class="col-md-2">
                    <?=__('Oldalbetöltés', 'gh')?>
                  </div>
                </div>
              </div>
              <div class="data-body">
                <?php foreach ($popular['data'] as $c):?>
                <div class="row">
                  <div class="col-md-1">
                    <div class="prop-img">
                      <a href="<?=$c->ProfilImg()?>" data-title="<?=$c->Title()?>" data-rel="iLightbox[pop_p<?=$c->ID()?>]" class="fusion-lightbox"><img src="<?=$c->ProfilImg()?>" alt=""></a>
                    </div>
                  </div>
                  <div class="col-md-9">
                    <div class="name">
                      <a href="<?=$c->URL()?>"><strong><?=$c->Title()?></strong></a>
                    </div>
                    <div>
                       <strong>[<?=$c->Azonosito()?>]</strong> <?=$c->RegionName()?>
                    </div>
                  </div>
                  <div class="col-md-2 center">
                    <?=$popular['counts'][$c->ID()]['ct']?> x
                  </div>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
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
                  <div class="col-md-1">
                    <div class="prop-img">
                      <a href="<?=$c->ProfilImg()?>" data-title="<?=$c->Title()?>" data-rel="iLightbox[watch_p<?=$c->ID()?>]" class="fusion-lightbox"><img src="<?=$c->ProfilImg()?>" alt=""></a>
                    </div>
                  </div>
                  <div class="col-md-5">
                    <div class="name">
                      <a href="<?=$c->URL()?>"><strong><?=$c->Title()?></strong></a>
                    </div>
                    <div>
                       <strong>[<?=$c->Azonosito()?>]</strong> <?=$c->RegionName()?>
                    </div>
                  </div>
                  <div class="col-md-3 center">
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
  </div>
</div>
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(clickStats);

  function clickStats()
  {
    var data = new google.visualization.DataTable();
    data.addColumn('string', '<?=__('Nap', 'gh')?>');
    data.addColumn('number', '<?=__('Ingatlan oldalbetöltés', 'gh')?>');
    data.addColumn({type: 'number', role: 'annotation'});

    data.addRows([
    <?php foreach ($stat_click_all as $s): $s->day = substr($s->day, 2, 2).'/'.substr($s->day, 5, 2).'/'.substr($s->day, 8, 2); ?>
        ['<?=$s->day?>', <?=$s->ct?>,  <?=$s->ct?>],
    <?php endforeach; ?>
    ]);

    var options = {
      'width': '100%',
      'height': 400,
      'fontSize': 12,
      'legend': { position: 'bottom' },
      'titleTextStyle' : {
        'fontSize': 10
      },
      'colors': ['#ffac42'],
      'chartArea': {'width': '100%', 'height': '80%'},
      'series': {
        0: { lineWidth: 3 },
      },
      hAxis : {
          textStyle : {
              fontSize: 8
          }
      }
    };

    var chart = new google.visualization.LineChart(document.getElementById('clickStats'));
    chart.draw(data, options);
  }
</script>
