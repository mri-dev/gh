<?php
  global $me;
  $control = get_control_controller('property_save_archive');

  if (isset($_POST['doArchive'])) {
    try {
      $f = $control->archive($_POST);
      wp_redirect('/control/property_edit/?id='.$f['id'].'&archived='.$f['state']);
    } catch (Exception $e) {
      $error = $e->getMessage();
    }
  }
?>
<div class="gh_control_content_holder">
  <div class="gh_control_account_page">
    <?php if (isset($error) && $error): ?>
      <div class="alert alert-danger"><?=$error?></div>
      <a href="javascript:void(0);" onclick="history.go(-1);"><?=__('vissza az archiváláshoz', 'gh')?></a>
    <?php endif; ?>
  </div>
</div>
