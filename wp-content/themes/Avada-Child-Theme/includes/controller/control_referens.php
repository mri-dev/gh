<?php
class control_referens
{
  public $users = null;
  public function __construct()
  {
    $this->users = (new GlobalHungaryUsers(array(
      'type' => 'reference_manager',
      //'reference_manager_id' => get_current_user_id()
    )))->getUsers();
    return $this;
  }

  private function get_manager_userlist()
  {
    $user = wp_get_current_user();

    $referens_user_list_ids = get_user_meta( $user->ID, 'gh_manager_referens_ids' , true);

    return explode(",", $referens_user_list_ids);
  }
}
?>
