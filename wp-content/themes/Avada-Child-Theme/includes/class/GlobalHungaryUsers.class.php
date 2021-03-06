<?php
class GlobalHungaryUsers
{
  public $users = null;
  public $param = null;

  public function __construct( $param = array() )
  {
    $this->param = $param;
    $arg = array();

    if (isset($param['type'])) {
      $arg['role'] = $param['type'];
    } else {
      $arg['role'] = -1;
    }

    if (isset($param['region']) && $param['region'] != 0) {
      $arg['meta_key'] = 'gh_user_regio';
      $arg['meta_value'] = $param['region'];
    }

    $this->users = new WP_User_Query( $arg );
    return $this;
  }

  public function getUsers()
  {
    return $this->users;
  }

  public function userIDS()
  {
    $ids = array();

    if (!$this->users) {
      return array();
    }

    foreach ($this->users->get_results() as $u) {
      if (!in_array($u->data->ID, $ids)) {
        $ids[] = $u->data->ID;
      }
    }

    return $ids;
  }
}
?>
