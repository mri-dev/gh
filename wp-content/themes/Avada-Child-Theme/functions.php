<?php

define('IFROOT', get_stylesheet_directory_uri());
define('DEVMODE', true);
define('IMG', IFROOT.'/images');

// Includes
require_once WP_PLUGIN_DIR."/cmb2/init.php";
require_once "includes/include.php";

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css?' . ( (DEVMODE === true) ? time() : '' )  );
    wp_enqueue_style( 'avada-child-stylesheet', IFROOT . '/style.css?' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function custom_theme_enqueue_styles() {
    wp_enqueue_style( 'globalhungary-css', IFROOT . '/assets/css/globalhungary.css?' . ( (DEVMODE === true) ? time() : '' ) );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_styles', 100 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );
/**
* Admin módosítások
**/
// Admin bar menü eltávolítás
function customize_admin_bar()
{
	global $wp_admin_bar;

  // WP logó eltávolítás
  $wp_admin_bar->remove_menu('wp-logo');
  // Site név eltávolítás
  $wp_admin_bar->remove_menu('site-name');

  $user = wp_get_current_user();
  if ( !in_array( 'administrator', (array) $user->roles ) )
  {

  }

}
add_action( 'wp_before_admin_bar_render', 'customize_admin_bar' );

function add_admin_menus()
{
	global $wp_admin_bar;

  $wp_admin_bar->add_menu( array(
    'id' => 'custom_logo',
    'title' => '<img src="'.IMG.'/logo-ico.svg" alt="'.get_option('blogname').'">'.get_option('blogname').' <strong>'.__('Ingatlan', 'gh').'</strong>',
    'href' => get_option('siteurl'),
    'meta' => array(
      'class' => 'company_ico'
    )
  ) );
}
add_action( 'admin_bar_menu', 'add_admin_menus', 10);

// Admin bar stílus
function admin_bar_color() {
?>
  <style>
  #wpadminbar{
    background: #1a1b1d !important;
    border-bottom: 1px solid #333b3b !important;
  }
  .company_ico {
    background: #111111 !important;
    -webkit-box-shadow: inset 0 -1px #333b3b;
    -moz-box-shadow: inset 0 -1px #333b3b;
    box-shadow: inset 0 -1px #333b3b;
  }
  .company_ico a{
    text-transform: uppercase !important;
    font-weight: bold !important;
  }
  .company_ico img {
    height: 100% !important;
    width: 18px !important;
    float: left !important;
    margin-right: 8px !important;
  }
  .company_ico a strong {
    color: #e31f24 !important;
    text-transform: uppercase !important;
    font-weight: bold !important;
  }
  </style>
<?php
}
add_action('wp_head', 'admin_bar_color');
add_action('admin_head', 'admin_bar_color');

/**
* Szerepkörök
**/
$user_roles = false;;
function gh_custom_role()
{
  global $user_roles;
  /**
  * Új szerepkörök
  **/
  $user_roles = new UserRoles();
  $user_roles->addRoles(array(
    array( 'region_manager', __('Régióvezető','gh') ),
    array( 'reference_manager', __('Referens','gh') ),
    array( 'starter', __('Előregisztráló','gh') )
  ));
  // Alap felhasználói körök eltávolítása
  $user_roles->removeRoles(array('subscriber', 'contributor', 'author', 'editor'));

  // Jogkörök
    // Referens
    $user_roles->addAvaiableCaps( 'reference_manager', array(
      'property_create', 'property_archive', 'property_edit', 'property_edit_price',
      'property_edit_autoconfirm_price', 'property_edit_autoconfirm_datas', 'property_archive_autoconfirm',
    ) );
    // Régió Menedzser
    $user_roles->addAvaiableCaps( 'region_manager', array(
      'property_archive', 'property_edit', 'property_edit_price',
      'user_property_connector'
    ) );
    $user_roles->addCap('region_manager', 'read');
    // Admin
    $user_roles->addAvaiableCaps( 'administrator', array(
      'property_create', 'property_delete', 'property_archive', 'property_edit', 'property_edit_price',
      'user_property_connector'
    ) );

  /*
  global $wp_roles;
  $all_roles = $wp_roles->roles;
  */
  //print_r($user_roles->role_caps);
}
add_action('after_setup_theme', 'gh_custom_role');

function admin_init_fc()
{
  $user = wp_get_current_user();
  if ( !in_array( 'administrator', (array) $user->roles ) )
  {
    remove_menu_page( 'index.php' );
  }
}
add_action('admin_init', 'admin_init_fc');

/**
* Felhasználói jogosúltság kezelés aloldal
*/
function role_editor_admin() {
  $user = wp_get_current_user();
  if ( true  )
  {
    add_users_page(__('Szerepkörök jogosultságai', 'gh'), __('Jogosultságok', 'gh'), 'manage_options', 'user-role-editor', 'role_editor_admin_view');
  }
}
add_action('admin_menu', 'role_editor_admin');
function role_editor_admin_view()
{
  global $user_roles;
  ob_start(); include('templates/admin/user-role-editor.php');ob_end_flush();
}

/**
* Egyedi felső menü
**/
function globalhungary_custom_top_menu()
{
  get_template_part( 'templates/globalhungary/top_menu' );
}
