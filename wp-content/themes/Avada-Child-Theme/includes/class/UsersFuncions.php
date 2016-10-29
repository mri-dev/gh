<?php
/**
* Felhasználó jogosultságok mentése
**/
add_action('edit_user_profile_update', 'gh_users_data_save' );
function gh_users_data_save( $id )
{
  global $user_roles;
  $userdata = get_userdata($_POST['user_id']);
  $all_caps = $user_roles->role_caps[$userdata->roles[0]];

  foreach ($_POST as $key => $value) {
    if(strpos($key, 'gh_user_permission_') === 0) {
      $cap_id = str_replace('gh_user_permission_', '', $key);
      if(!array_key_exists($cap_id, $userdata->caps)){
        $userdata->add_cap($cap_id);
      }

      $aid = array_search($cap_id, $all_caps);
      unset($all_caps[$aid]);
    }
  }

  if($all_caps){
    foreach ($all_caps as $rcap) {
      $userdata->remove_cap($rcap);
    }
  }
}

/**
* Bejelentkezési idő mentése
**/
function user_login_save_date ( $login )
{
  $user = get_userdatabylogin ( $login );
  update_usermeta ( $user->ID, 'last_login', date ( 'Y-m-d H:i:s' ) );
}
add_action ( 'wp_login', 'user_login_save_date' );
?>
