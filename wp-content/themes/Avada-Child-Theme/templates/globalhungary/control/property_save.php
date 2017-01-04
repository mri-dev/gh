<?php
  global $me;
  $control = get_control_controller('property_save');

  if (isset($_POST['createProperty'])) {
    try {

      $f = $control->createsave($_POST);

      if ($f['mode'] == 'save')
      {
        if ( !empty($f['return']) )
        {
          wp_redirect($f['return']);
        }
        else
        {
          wp_redirect('/control/property_edit/?id='.$f['id'].'&saved=1');
        }
      }
      else if($f['mode'] == 'create')
      {
        wp_redirect('/control/properties/?createdAdv=1');
      }

    } catch (Exception $e) {
      $error = $e->getMessage();
    }
  }
?>
<div class="gh_control_content_holder">
  <div class="gh_control_account_page">
    <?php if (isset($error) && $error): ?>
      <div class="alert alert-danger"><?=$error?></div>
      <a href="javascript:void(0);" onclick="history.go(-1);"><?=__('vissza a szerkesztéshez', 'gh')?></a>
    <?php endif; ?>
  </div>
</div>
