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
            <div class="text">Ez a szöveg</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="bgh">
          <div class="vis vis-red">
            <i class="fa fa-home"></i>
          </div>
          <div class="inf">
            <div class="num"><?=number_format(10000, 0, ".",".")?></div>
            <div class="text">Ez a szöveg</div>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="bgh">
          <div class="vis vis-red">
            <i class="fa fa-home"></i>
          </div>
          <div class="inf">
            <div class="num"><?=number_format(10000, 0, ".",".")?></div>
            <div class="text">Ez a szöveg</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
