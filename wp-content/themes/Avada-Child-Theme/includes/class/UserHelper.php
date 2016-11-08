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
    return get_user_meta( $this->ID(), 'gh_user_regio', true);
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
    return get_current_user_id();
  }

  public function Name()
  {
    return $this->user->display_name;
  }

  public function Phone()
  {
    return get_user_meta($this->ID(), 'phone', true);
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
    $c = count_user_posts($this->ID(), 'listing');
    return $c;
  }
}
?>