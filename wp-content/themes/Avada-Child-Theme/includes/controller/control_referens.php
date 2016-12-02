<?php
class control_referens
{
  public $users = null;
  private $user_ids = array();

  public function __construct()
  {
    global $me;

    $arg = array();
    if (!current_user_can('administrator')) {
      $arg['type'] = 'reference_manager';
      $arg['region'] = $me->RegionID();
    }
  
    $this->users = (new GlobalHungaryUsers($arg))->getUsers();
    return $this;
  }

  public function getUserList()
  {
    $data = $this->users->get_results();

    if( empty($data) ) return false;

    foreach ($data as $d) {
      if (in_array($d->ID, $this->user_ids)) {
        continue;
      }
      $this->user_ids[] = $d->ID;
      $this->user_data[] = new UserHelper(array('id' => $d->ID));
    }

    return $this->user_data;
  }

  public function Count()
  {
    return count($this->user_ids);
  }

  private function get_manager_userlist()
  {
    $user = wp_get_current_user();

    $referens_user_list_ids = get_user_meta( $user->ID, 'gh_manager_referens_ids' , true);

    return explode(",", $referens_user_list_ids);
  }
}
?>
