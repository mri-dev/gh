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
    }

    if (isset($param['reference_manager_id'])) {
      $arg['include'] = $this->get_manager_userlist($param['reference_manager_id']);
    }

    $this->users = new WP_User_Query( $arg );

    return $this;
  }

  public function get_manager_userlist( $user_id = false )
  {
    if (!$user_id) {
      $user = wp_get_current_user();
      $user_id = $user->ID;
    }

    $referens_user_list_ids = get_user_meta( $user_id, 'gh_manager_referens_ids' , true);

    return explode(",", $referens_user_list_ids);
  }

  public function getUsers()
  {
    return $this->users;
  }
}
?>
