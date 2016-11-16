<?php
/**
* Felhasználó jogosultságok mentése
**/
add_action('edit_user_profile_update', 'gh_users_data_save' );
function gh_users_data_save( $id )
{
  global $user_roles;

  if ( !current_user_can( 'edit_user', $id ) ) return false;

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

  /*echo '<pre>';
    print_r($_POST);
  echo '</pre>';
  exit;*/

  // Régió
  update_user_meta( $_POST['user_id'], 'gh_user_regio', wp_kses_post( $_POST['gh_user_regio'] ), get_user_meta($_POST['user_id'], 'gh_user_regio', true) );
}

/**
* Felh. lista az összes felhasználóval
**/
function overwrite_author_list_all_users( $output )
{
  global $post;
  $users = get_users();
	$current_user =  wp_get_current_user();

  $output = '<select id="post_author_override" name="post_author_override" class="">';

  foreach($users as $user)
  {
		if($post->post_author == $user->ID){
      $select =  'selected';
    }else{
			$select = '';
		}
    $output .= '<option value="'.$user->ID.'"'.$select.'>'.$user->display_name.' ('.$user->user_email.')</option>';
  }
  $output .= '</select>';

  return $output;
}
//add_filter('wp_dropdown_users', 'overwrite_author_list_all_users');

/**
* Összes felhasználó listázása a legördülő select-ben
**/
function all_users_to_dropdown( $query_args, $r )
{
  $query_args['who'] = '';
  return $query_args;
}
add_filter( 'wp_dropdown_users_args', 'all_users_to_dropdown', 10, 2 );

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
