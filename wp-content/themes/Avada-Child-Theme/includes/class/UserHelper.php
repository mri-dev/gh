<?php
class UserHelper
{
  public $user = null;

  public function __construct( $param = array() )
  {
    $this->user  = (isset($param['id'])) ? get_userdata($param['id']) : wp_get_current_user();
    return $this;
  }

  public function RegionID()
  {
    $rid = get_user_meta( $this->ID(), 'gh_user_regio', true);
    if (!$rid || $rid <= 0) {
      $rid = 0;
    }
    return $rid;
  }

  public function can( $cap )
  {
    if ( $this->user ) {
      if ( array_key_exists($cap, $this->user->caps) ) {
        return true;
      }
    }

    return false;
  }

  public function RegionName()
  {
    $parent     = false;
    $region_id  = $this->RegionID();
    $term       = get_term( $region_id );

    if ($term->parent != 0) {
      $parent = get_term($term->parent);
    }

    return ($parent) ? $parent->name.' > '.$term->name . ( ($parent->name == 'Budapest') ? ' '.__('kerület', 'gh') : '' ) : $term->name;
  }

  public function ID()
  {
    return $this->user->ID;
  }

  public function Name()
  {
    return $this->user->display_name;
  }

  public function Phone()
  {
    $phone = get_user_meta($this->ID(), 'phone', true);
    $_phone = PHONE_PREFIX.' '.substr($phone, 0, 2);
    $last = substr($phone, 2);

    if (strlen($last) > 6) {
      $_phone .= ' '.substr($last, 0, 3).' '. substr($last, 3 );
    } else {
      $_phone .= ' '. substr($last, 0, 3).' '. substr($last, 3 );
    }
    return $_phone;
  }

  public function Email()
  {
    return $this->user->user_email;
  }

  public function LastLogin()
  {
    return get_user_meta($this->ID(), 'last_login', 'n/a');
  }

  public function PropertiesCount()
  {
    global $wpdb;


  	$count = $wpdb->get_var( $wpdb->prepare($q = "SELECT
      COUNT(p.ID)
    FROM $wpdb->posts as p
    LEFT JOIN {$wpdb->prefix}postmeta ON ( {$wpdb->prefix}postmeta.post_id = p.ID and wp_gh_postmeta.meta_key = '_listing_flag_archived')
    WHERE
      post_type = 'listing' and
      post_author = %d and
      post_parent = 0 and
      (wp_gh_postmeta.meta_value IS NULL or wp_gh_postmeta.meta_value = '')", $this->ID()) );

    return apply_filters( 'get_usernumposts', $count, $this->ID() );
  }
}
?>
