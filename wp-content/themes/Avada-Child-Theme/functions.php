<?php
define('PROTOCOL', 'http');
define('DOMAIN', $_SERVER['HTTP_HOST']);
define('IFROOT', str_replace(get_option('siteurl'), '//'.DOMAIN, get_stylesheet_directory_uri()));
define('DEVMODE', true);
define('IMG', IFROOT.'/images');
define('SLUG_INGATLAN', 'ingatlan');
define('SLUG_INGATLAN_LIST', 'ingatlanok');
define('SLUG_FAVORITE', 'kedvencek');
define('SLUG_NEWS', 'news');
define('GOOGLE_API_KEY', 'AIzaSyA0Mu8_XYUGo9iXhoenj7HTPBIfS2jDU2E');
define('PHONE_PREFIX', '06');
define('LANGKEY','hu');
define('FB_APP_ID', '1380917375274546');
// PREMIUM
define('FIND_PREMIUM_DOMAIN_PREFIX', 'premium.');
define('PREMIUM_AUTH_PAGE_SLUG', 'validatePremium');
define('PREMIUM_MASTER_PW', 'globalpremium2017');

// Includes
require_once WP_PLUGIN_DIR."/cmb2/init.php";
require_once "includes/include.php";

$me = new UserHelper();

function theme_enqueue_styles() {
    wp_enqueue_style( 'avada-parent-stylesheet', get_template_directory_uri() . '/style.css?' . ( (DEVMODE === true) ? time() : '' )  );
    wp_enqueue_style( 'avada-child-stylesheet', IFROOT . '/style.css?' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_style( 'slick', IFROOT . '/assets/vendor/slick/slick.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_style( 'slick-theme', IFROOT . '/assets/css/slick-theme.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_script( 'slick', IFROOT . '/assets/vendor/slick/slick.min.js?t=' . ( (DEVMODE === true) ? time() : '' ) , array('jquery'));
    wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?sensor=false&language=hu&region=hu&libraries=places&key='.GOOGLE_API_KEY);
    wp_enqueue_script( 'google-charts', '//www.gstatic.com/charts/loader.js');
    wp_enqueue_script( 'mocjax', IFROOT . '/assets/vendor/autocomplete/scripts/jquery.mockjax.js');
    wp_enqueue_script( 'autocomplete', IFROOT . '/assets/vendor/autocomplete/dist/jquery.autocomplete.min.js');
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );


function add_opengraph_doctype( $output ) {
	return $output . ' xmlns:og="http://opengraphprotocol.org/schema/" xmlns:fb="http://www.facebook.com/2008/fbml"';
}
add_filter('language_attributes', 'add_opengraph_doctype');

function app_locale( $locale )
{
  /*
    $lang = explode('/', $_SERVER['REQUEST_URI']);
    if(array_pop($lang) === 'en'){
      $locale = 'en_US';
    }else{
      $locale = 'gr_GR';
    }*/
    $locale = 'en_US';
    return $locale;
}
add_filter('locale','app_locale', 10);

function facebook_og_meta_header()
{
  global $wp_query;

  if ( !in_array($wp_query->query['custom_page'], array(SLUG_INGATLAN, SLUG_INGATLAN_LIST)) ) {
    return;
  }

  $title = get_option('blogname');
  $image = '//'.DOMAIN.'/wp-content/uploads/global-hungary-logo-wtext-h75.png';
  $desc  = get_option('blogdescription');
  $url   = get_option('site_url');

  if($wp_query->query_vars['custom_page'] == 'ingatlan' ) {
    $xs = explode("-",$wp_query->query_vars['urlstring']);
    $ingatlan_id = end($xs);
    $properties = new Properties(array(
      'id' => $ingatlan_id,
      'post_status' => array('publish'),
    ));
    $property = $properties->getList();
    $property = $property[0];

    if ($property) {
      $title = $property->Title() . ' ['.$property->Azonosito().']' . ' - ' . $property->PropertyStatus(true) . ' '. $property->multivalue_list($property->PropertyType(true)) . ' - '. $property->RegionName( false );
      $image = $property->ProfilImg();
      $desc = $property->ShortDesc();
      $url = $url.$property->URL();
    }
  }

  echo '<meta property="fb:app_id" content="'.FB_APP_ID.'"/>'."\n";
  echo '<meta property="og:title" content="' . $title . '"/>'."\n";
  echo '<meta property="og:type" content="article"/>'."\n";
  echo '<meta property="og:url" content="' . $url . '/"/>'."\n";
  echo '<meta property="og:description" content="' . $desc . '/"/>'."\n";
  echo '<meta property="og:site_name" content="'.get_option('blogname').'"/>'."\n";
  echo '<meta property="og:image" content="' . $image . '"/>'."\n";

}
add_action( 'wp_head', 'facebook_og_meta_header', 5);

function custom_theme_enqueue_styles() {
    wp_enqueue_style( 'globalhungary-css', IFROOT . '/assets/css/globalhungary.css?t=' . ( (DEVMODE === true) ? time() : '' ) );
    wp_enqueue_script( 'globalhungary', IFROOT . '/assets/js/master.js?t=' . ( (DEVMODE === true) ? time() : '' ), array('jquery'), '', 999 );
}
add_action( 'wp_enqueue_scripts', 'custom_theme_enqueue_styles', 100 );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/langs';
	load_child_theme_textdomain( 'gh', $lang );

  $ucid = ucid();

  $ucid = $_COOKIE['uid'];
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function ucid()
{
  $ucid = $_COOKIE['ucid'];

  if (!isset($ucid)) {
    $ucid = mt_rand();
    setcookie( 'ucid', $ucid, time() + 60*60*24*365*2, "/");
  }

  return $ucid;
}
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

  $wp_admin_bar->add_menu( array(
    'id' => 'gh_dashboard',
    'title' => '<span class="ab-icon"></span> '.__('Gépház', 'gh'),
    'href' => get_option('siteurl') . '/control/home',
    'meta' => array(
      'class' => ''
    )
  ) );
}
add_action( 'admin_bar_menu', 'add_admin_menus', 10);

// Admin menü
add_filter( 'admin_footer_text', '__return_empty_string', 11 );
add_filter( 'update_footer', '__return_empty_string', 11 );

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
  #wpadminbar #wp-admin-bar-gh_dashboard .ab-icon:before {
    content: "\f111";
    top: 3px;
  }
  #collapse-menu:hover, #wpadminbar .ab-item, #wpadminbar a.ab-item, #wpadminbar>#wp-toolbar span.ab-label, #wpadminbar>#wp-toolbar span.noticon{
	  color: #b5aeae;
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
    'property_create', 'property_archive', 'property_edit', 'property_edit_price', 'property_edit_status',
    'property_edit_autoconfirm_price', 'property_edit_autoconfirm_datas', 'property_archive_autoconfirm',
    'stat_property'
  ) );
  $user_roles->addCap('reference_manager', 'read');
  // Régió Menedzser
  $user_roles->addAvaiableCaps( 'region_manager', array(
    'property_create', 'property_archive', 'property_archive_mod', 'property_edit', 'property_edit_price', 'property_edit_status',
    'user_property_connector',
    'stat_property'
  ) );
  $user_roles->addCap('region_manager', 'read');

  // Admin
  $user_roles->addAvaiableCaps( 'administrator', array(
    'property_create', 'property_delete', 'property_archive', 'property_edit', 'property_edit_price', 'property_edit_status',
    'user_property_connector',
    'property_edit_autoconfirm_price', 'property_edit_autoconfirm_datas', 'property_archive_autoconfirm',
    'stat_property'
  ) );

  /* * /
  print_r(wp_get_current_user());
  /* */

  //echo get_locale();
}
add_action('after_setup_theme', 'gh_custom_role');

// Helper tab lecsúszó eltávolítás
function gh_remove_help_tabs() {
    $screen = get_current_screen();
    $screen->remove_help_tabs();
}
add_action('admin_head', 'gh_remove_help_tabs');

// Admin dashboard widget eltávolítások
function remove_dashboard_widget() {
 	remove_meta_box( 'themefusion_news', 'dashboard', 'side');
  remove_meta_box( 'dashboard_primary', 'dashboard', 'side');
  remove_meta_box( 'dashboard_activity', 'dashboard', 'core');
}
add_action('wp_dashboard_setup', 'remove_dashboard_widget' );

function admin_init_fc()
{
  $user = wp_get_current_user();
  if ( !in_array( 'administrator', (array) $user->roles ) )
  {
    remove_menu_page( 'index.php' );
  }
}
add_action('admin_init', 'admin_init_fc');

function gh_init()
{
  add_filter('option_siteurl', 'replace_siteurl');
  add_filter('option_home', 'replace_siteurl');
  remove_filter('template_redirect','redirect_canonical');

  if( strpos(DOMAIN,FIND_PREMIUM_DOMAIN_PREFIX) !== false) {
      define('IS_PREMIUM', true);
      define('SHOW_PREMIUM_ONLY', true);
  }

  if (defined('IS_PREMIUM')) {
    if( !isset($_COOKIE['access_to_premium_'.ucid()]) )
    {
      if(
        strpos($_SERVER['REQUEST_URI'], '/'.PREMIUM_AUTH_PAGE_SLUG) === false &&
        strpos($_SERVER['REQUEST_URI'], '/wp-admin') === false
      )
      {
        wp_redirect('/'.PREMIUM_AUTH_PAGE_SLUG); exit;
      }
    }

    // Allowed
  }

  date_default_timezone_set('Europe/Budapest');
  add_rewrite_rule('^control/([^/]+)', 'index.php?cp=$matches[1]', 'top');
  add_rewrite_rule('^'.PREMIUM_AUTH_PAGE_SLUG, 'index.php?custom_page='.PREMIUM_AUTH_PAGE_SLUG, 'top');
  add_rewrite_rule('^'.SLUG_INGATLAN_LIST.'/?', 'index.php?custom_page='.SLUG_INGATLAN_LIST.'&urlstring=$matches[1]', 'top');
  add_rewrite_rule('^'.SLUG_FAVORITE.'/?', 'index.php?custom_page='.SLUG_FAVORITE.'&urlstring=$matches[1]', 'top');
  add_rewrite_rule('^'.SLUG_NEWS.'/?', 'index.php?custom_page='.SLUG_NEWS.'&urlstring=$matches[1]', 'top');
  add_rewrite_rule('^'.SLUG_INGATLAN.'/([^/]+)/([^/]+)/([^/]+)', 'index.php?custom_page='.SLUG_INGATLAN.'&regionslug=$matches[1]&cityslug=$matches[2]&urlstring=$matches[3]', 'top');
  add_rewrite_rule('^ingatlan-export/?', 'index.php?custom_page=ingatlan-export&urlstring=$matches[1]', 'top');
}
add_action('init', 'gh_init');

function replace_siteurl($val) {
  return '//'.$_SERVER['HTTP_HOST'];
}

function admin_post_premium_validation_handler()
{
  $err = false;

  if (isset($_POST['accessPremium']))
  {
    if(!empty($_POST['premium_pass']))
    {
      if($_POST['premium_pass'] == PREMIUM_MASTER_PW)
      {
        $pkey = 'access_to_premium_'.ucid();
        setcookie($pkey, time(), time()+3600*24, '/');
        wp_redirect('//'.DOMAIN); exit;
      } else $err = true;
    } else $err = true;
  } else $err = true;


  if($err) {
    wp_redirect('/'.PREMIUM_AUTH_PAGE_SLUG.'/?ekey=failauth');
    exit;
  }
}
add_action( 'admin_post_nopriv_premium_validation', 'admin_post_premium_validation_handler' );
add_action( 'admin_post_premium_validation', 'admin_post_premium_validation_handler' );

function gh_get_fnc()
{
  global $wpdb;
  if (isset($_GET['setwatched']) && $_GET['setwatched'] == '1')
  {
    $ucid = ucid();

    $wpdb->insert(
      \PropertyFactory::LOG_WATCHTIME_DB,
      array(
        'ucid'  => $ucid,
        'ip'    => $_SERVER['REMOTE_ADDR'],
        'wtime' => current_time('mysql')
      ),
      array(
        '%s', '%s', '%s'
      )
    );

    wp_redirect('/news/?settedWatchedAll=1');
    exit;
  }
}
add_action('init', 'gh_get_fnc');

function old_importer()
{
  //$importer = new GHImporter();
  //$imp_zona_pre = $importer->zonak();
  //$imp_zone_ins = $importer->insert_zonak( $imp_zona_pre );
  if ($_GET['imp'] == '1') {
    //$ing = $importer->ingatlanok();
    //$ing = $importer->do_ingatlan_import($ing['import']);
    //$ing = $importer->user_connecter();
    //$ing = $importer->image_connect();
    /* * /
    echo '<pre>';
    print_r($ing);
    exit;
    /* */
  }

  if ($_GET['img'] == '1')
  {
    return;
    $attach_id = $_GET['kep'];
    $image = new ImageModifier();
    $image->loadResourceByID($attach_id);
    $image->watermark();
  }
}
add_action('init', 'old_importer', 9999);

function tester()
{
  if ($_GET['testmail'] == '1') {
    $to = array( 'pocokxtrame@gmail.com' );
    $sub = 'Teszt email';
    $mail = (new MailManager( $to, $sub ))
      ->setContent('Ez itt egy tartalom')
      ->setTemplate('test');
    $mail->send();
    /* */
    echo '<pre>';
    print_r($mail);
    exit;
    /* */
  }
}
add_action('init', 'tester', 9999);

function get_control_controller( $controller_class )
{ global $wp_query;

  // Template controller
  if ( file_exists(dirname(__FILE__).'/includes/controller/control_'.$controller_class.'.php') ) {
    include dirname(__FILE__).'/includes/controller/control_'.$controller_class.'.php';
    $controller_class = 'control_'.$controller_class;
    return new $controller_class;
  }

  return false;
}

function gh_custom_template($template) {
  global $post, $wp_query;

  if ( isset($wp_query->query_vars['cp'])) {
    add_filter( 'body_class','gh_control_panel_class_body' );
    if ( !is_user_logged_in() ) {
      wp_redirect('/admin');
    }
    return get_stylesheet_directory() . '/control.php';
  } else if(isset($wp_query->query_vars['custom_page'])) {

    if (SLUG_INGATLAN == $wp_query->query_vars['custom_page']) {
      add_filter( 'body_class','gh_ingatlan_class_body' );
      add_filter( 'document_title_parts', 'ingatlan_custom_title' );
    }

    // Premium template
    if (PREMIUM_AUTH_PAGE_SLUG == $wp_query->query_vars['custom_page']) {
      add_filter( 'body_class','gh_premium_class_body' );
      add_filter( 'document_title_parts', 'premiumvalidator_custom_title' );
    }

    return get_stylesheet_directory() . '/'.$wp_query->query_vars['custom_page'].'.php';
  } else {
    return $template;
  }
}
add_filter( 'template_include', 'gh_custom_template' );


function gh_premium_class_body( $classes ) {
  $classes[] = 'gh_premium';
  return $classes;
}
function gh_control_panel_class_body( $classes ) {
  $classes[] = 'gh_control_panel';
  return $classes;
}
function gh_ingatlan_class_body( $classes ) {
  $classes[] = 'gh_ingatlan_page';
  return $classes;
}
function premiumvalidator_custom_title( $title )
{
  $title['title'] = __('Prémium hozzáférés azonosítás', 'gh');
  return $title;
}

function ingatlan_custom_title($title)
{ global $wp_query;

  if($wp_query->query_vars['custom_page'] == 'ingatlan' ) {
    $xs = explode("-",$wp_query->query_vars['urlstring']);
    $ingatlan_id = end($xs);
    $properties = new Properties(array(
      'id' => $ingatlan_id,
      'post_status' => array('publish'),
    ));
    $property = $properties->getList();
    $property = $property[0];

    if ($property) {
      $title['title'] = $property->Title() . ' ['.$property->Azonosito().']' . ' - ' . $property->PropertyStatus(true) . ' '. $property->multivalue_list($property->PropertyType(true)) . ' - '. $property->RegionName( false );
    }
  }

  if($wp_query->query_vars['custom_page'] == 'ingatlanok' ) {
    $title['title'] = __('Ingatlankereső', 'gh');
  }

  if($wp_query->query_vars['custom_page'] == 'news' ) {
    $title['title'] = __('Nem megtekintett ingatlanok', 'gh');
  }

  return $title;
}

function gh_query_vars($aVars) {
  $aVars[] = "cp";
  $aVars[] = "custom_page";
  $aVars[] = "urlstring";
  $aVars[] = "cityslug";
  $aVars[] = "regionslug";
  return $aVars;
}
add_filter('query_vars', 'gh_query_vars');

function gh_login_redirect( $redirect_to, $request, $user ) {
	//is there a user to check?
	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
		//check for admins
		if ( in_array( 'administrator', $user->roles ) ) {
			// redirect them to the default place
			// return $redirect_to;
      return '/control/home';
		} else {
			return '/control/home';
		}
	} else {
		return $redirect_to;
	}
}
add_filter( 'login_redirect', 'gh_login_redirect', 10, 3 );

/**
* Egyedi felső menü
**/
function globalhungary_custom_top_menu()
{
  get_template_part( 'templates/globalhungary/top_menu' );
}

/**
* AJAX REQUESTS
*/
function ajax_requests()
{
  $ajax = new AjaxRequests();
  $ajax->check_property_fav();
  $ajax->property_fav_action();
  $ajax->city_autocomplete();
  $ajax->set_regio_gps();
}
add_action( 'init', 'ajax_requests' );

// AJAX URL
function get_ajax_url( $function )
{
  return admin_url('admin-ajax.php?action='.$function);
}

function after_logo_content()
{
  echo '<div class="badge">'.__('Alapítva 1999','gh').'</div>';
  if (defined('IS_PREMIUM') && IS_PREMIUM === true) {
    echo '<div class="premium-badge"><i class="fa fa-star"></i> '.__('prémium','gh').' <i class="fa fa-star"></i></div>';
  }
}
add_filter('avada_logo_append', 'after_logo_content');

/**
* Setup Feeds
**/
function setup_feeds(){
  add_feed('ingatlanbazar', 'feed_ingatlanbazar');
}
add_action('init', 'setup_feeds');

function feed_ingatlanbazar()
{
  add_filter('pre_option_rss_use_excerpt', '__return_zero');

  $feed = new FeedManager();
  $feed->setController('IngatlanBazarFeed', array( 'account_id' => 508239 ));
  $feed->load('ingatlanbazar');
  $feed->render();
}

function custom_rss_content_type( $content_type, $type ) {
	if ( 'ingatlanbazar' === $type ) {
		return feed_content_type( 'rss2' );
	}
	return $content_type;
}
add_filter( 'feed_content_type', 'custom_rss_content_type', 10, 2 );

/* GOOGLE ANALYTICS */
if( defined('DEVMODE') && DEVMODE === false ) {
	function ga_tracking_code () {
		?>
		<script>
		  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

		  ga('create', 'UA-24431206-1', 'auto');
		  ga('send', 'pageview');

		</script>
		<?
	}
	add_action('wp_footer', 'ga_tracking_code');
}

function premium_site_switcher()
{
  $premium = false;
  $premium = (defined('IS_PREMIUM') && IS_PREMIUM) ? true : false;

  if ($premium) {
    return '//'.str_replace(FIND_PREMIUM_DOMAIN_PREFIX,'',DOMAIN) . $_SERVER['REQUEST_URI'];
  } else {
    return '//'.FIND_PREMIUM_DOMAIN_PREFIX.DOMAIN . $_SERVER['REQUEST_URI'];
  }
}
