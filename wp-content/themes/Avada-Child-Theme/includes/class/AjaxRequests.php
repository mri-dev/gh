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

  public function city_autocomplete()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'AutocompleteCity'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'AutocompleteCity'));
  }

  public function set_regio_gps()
  {
    add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'setRegioGPS'));
    add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'setRegioGPS'));
  }

  public function setRegioGPS()
  {

    extract($_POST);

    $return = array(
      'error' => 0,
      'msg'   => ''
    );

    $lat = (float)$lat;
    $lng = (float)$lng;

    $lat_meta_id = add_term_meta( $term, 'gps_lat', $lat, true);
    $lng_meta_id = add_term_meta( $term, 'gps_lng', $lng, true);

    $return['data']['lng'] = $lng_meta_id;
    $return['data']['lat'] = $lat_meta_id;

    echo json_encode($return);
    die();
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

    $ids = $wpdb->get_results("SELECT
      f.pid
    FROM listing_favorites as f
    LEFT JOIN $wpdb->posts as p ON p.ID = f.pid
    WHERE f.ucid = '$ucid' and p.post_status = 'publish'
    GROUP BY pid;", ARRAY_A);

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

    $pf = new PropertyFactory();

    $return = array();
    $arg    = array(
      'taxonomy' => 'locations',
      'hierarchical' => 1,
      'hide_empty' => 1,
      'orderby' => 'name',
      'order' => 'ASC'
    );

    if ($region) {
      $arg['child_of'] = $region;
    }

    //$arg['name__like'] = $search;

    $terms = get_terms($arg);

    foreach ($terms as $t) {
      if ($t->parent == 0) {
        continue;
      }
      if ($t->parent != 0) {
        $parent = get_term($t->parent);
      }

      $name = ( ($parent->slug == 'budapest') ? $parent->name.' / '.$t->name.' '.__('kerület') : $t->name  );

      if (!empty($search) && stristr($name, $search) === FALSE) {
        continue;
      }

      $return[] = array(
        'label' => $name,
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
