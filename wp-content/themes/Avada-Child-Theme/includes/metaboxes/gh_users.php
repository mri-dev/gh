<?php
add_action( 'cmb2_admin_init', 'gh_register_user_profile_metabox' );
/**
 * Hook in and add a metabox to add fields to the user profile pages
 */
function gh_register_user_profile_metabox() {
  global $user_roles;
	$prefix = 'gh_user_';

  if(isset($_GET['user_id'])){
    $current_user = get_userdata($_GET['user_id']);
  } else return;

  //print_r($user_roles->role_caps);
  //print_r($current_user);

  $main_role = $current_user->roles[0];
  //echo $main_role;

	/**
	 * Metabox for the user profile screen
	 */
	$cmb_user = new_cmb2_box( array(
		'id'               => $prefix . 'permissions',
		'title'            => __( 'Jogosultságok', 'gh' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
    'priority'          => 'high',
		'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
    'cmb_styles' => false
	) );

  $cmb_user->add_field( array(
    'name'     => __( 'Global Hungary Jogosultságok', 'gh' ),
    'desc'     => __( 'Itt állíthatja be a felhasználó speciális jogosultságait.', 'gh' ),
    'id'       => $prefix . 'permissions_description',
    'type'     => 'title',
    'on_front' => false,
  ) );

  if($user_roles->role_caps[$main_role]){
    foreach ($user_roles->role_caps[$main_role] as $role_id_key ) {
      $cmb_user->add_field( array(
        'name' => $user_roles->i18n($role_id_key),
    		'id'   => $prefix . 'permission_'.$role_id_key,
    		'type' => 'checkbox',
        'render_row_cb' => 'gh_checbox_render_row_cb',
      ) );
    }
  }

  $cmb_user_zone = new_cmb2_box( array(
		'id'               => $prefix . 'zona',
		'title'            => __( 'Felhasználó régió', 'gh' ), // Doesn't output for user boxes
		'object_types'     => array( 'user' ), // Tells CMB2 to use user_meta vs post_meta
		'show_names'       => true,
    'priority'          => 'high',
		'new_user_section' => 'add-new-user', // where form will show on new user page. 'add-existing-user' is only other valid option.
    'cmb_styles' => false
	) );

  $cmb_user_zone->add_field( array(
    'name'     => __( 'Felhasználó zóna adatok', 'gh' ),
    'desc'     => __( 'Válassza ki, hogy a felhasználó melyik régió zónába tartozik. A felhasználó jellemzően a régióhoz kapcsolódó adatokat láthatja, szerkesztheti.', 'gh' ),
    'id'       => $prefix . 'zona_desc',
    'type'     => 'title',
    'on_front' => false,
  ) );

  $cmb_user_zone->add_field( array(
    'name' => $prefix.'regio',
    'id'   => $prefix . 'regio',
    'type' => 'select',
    'render_row_cb' => 'gh_regio_select_render_row_cb',
  ) );
}

// Kapcsolati adatok kibővítése
function extend_user_contact_methods( $user_contact ) {
	$user_contact['phone']   = __( 'Telefonszám', 'gh' );
	return $user_contact;
}
add_filter( 'user_contactmethods', 'extend_user_contact_methods' );
?>
