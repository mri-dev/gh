<?php

class AjaxRequests
{
  public function __construct()
  {
    return $this;
  }

  public function check_property_fav()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'checkPropertyFavorites'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'checkPropertyFavorites'));
  }

  public function property_fav_action()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'propertyFavAction'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'propertyFavAction'));
  }

  public function city_autocomplete($value='')
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'AutocompleteCity'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'AutocompleteCity'));
  }

  public function checkPropertyFavorites()
  {
    global $wpdb;

    extract($_POST);
    $return = array(
      'error' => 0,
      'msg'   => '',
      'in_fav' => array()
    );
    $total = 0;

    $ucid = ucid();

    $ids = $wpdb->get_results("SELECT pid FROM listing_favorites WHERE ucid = '$ucid' GROUP BY pid;", ARRAY_A);

    foreach ($ids as $id) {
      $total++;
      $return['in_fav'][] = (int)$id[pid];
    }

    $return['check_ids'] = $_POST['ids'];
    $return['num'] = $total;


    //ob_start();
  	  //include(locate_template('templates/mails/utazasi-ajanlatkero-ertesites.php'));
      //$message = ob_get_contents();
		//ob_end_clean();


    echo json_encode($return);
    die();
  }

  public function propertyFavAction()
  {
    global $wpdb;

    extract($_POST);

    $return = array(
      'error' => 0,
      'msg'   => '',
      'id'    => (int)$id
    );

    $ucid = ucid();

    //check
    $c = (int)$wpdb->get_var( $wpdb->prepare("SELECT count(ID) FROM listing_favorites WHERE ucid = %s and pid = %d", $ucid, $id) );

    if ( $c == 0 ) {
      $wpdb->insert(
        "listing_favorites",
        array(
          'ucid' => $ucid,
          'pid' => $id
        ),
        array( '%s', '%d' )
      );
      $return['did'] = 'add';
    } else {
      $wpdb->delete( "listing_favorites", array("ucid" => $ucid, "pid" => $id), array('%s', '%d'));
      $return['did'] = 'remove';
    }



    echo json_encode($return);
    die();
  }

  public function AutocompleteCity()
  {
    global $wpdb;

    extract($_GET);

    $return = array();
    $arg    = array(
      'taxonomy' => 'locations'
    );

    if ($region) {
      $arg['parent'] = $region;
    }

    $arg['name__like'] = $search;

    $terms = get_terms( $arg );

    foreach ($terms as $t) {
      if ($t->parent == 0) {
        continue;
      }
      if ($t->parent != 0) {
        $parent = get_term($t->parent);
      }
      $return[] = array(
        'label' => ( ($parent->slug == 'budapest') ? $parent->name.' / '.$t->name.' '.__('kerület') : $t->name  ),
        'value' => (int)$t->term_id,
        'slug' => $t->slug,
        'region' => $t->parent,
        'count' => $t->count
      );
    }

    header('Content-Type: application/json;charset=utf8');
    echo json_encode($return);
    die();
  }

  public function getMailFormat(){
      return "text/html";
  }

  public function getMailSender($default)
  {
    return get_option('admin_email');
  }

  public function getMailSenderName($default)
  {
    return get_option('blogname', 'Wordpress');
  }

  private function returnJSON($array)
  {
    echo json_encode($array);
    die();
  }

}
?>
