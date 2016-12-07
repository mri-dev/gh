<?php
class IngatlanBazarFeed
{
  public $account_id = 0;

  public function __construct( $arg = array() )
  {
    extract($arg);

    $this->account_id = $account_id;

    return $this;
  }

  public function contactEmail()
  {
    return get_option('admin_email', true);
  }

  public function agents()
  {
    $agents = array();
    $arg = array(
      'role' => -1
    );

    $users = new WP_User_Query($arg);
    $users = $users->get_results();

    //print_r($users);

    if (!empty($users)) {
      foreach ($users as $user) {
        $u = new UserHelper(array('id' => $user->data->ID));

        $agents[] = array(
          'ID' => $u->ID(),
          'name' => $u->Name(),
          'phone' => $u->Phone(),
          'email' => $u->Email(),
        );
      }
    }

    return $agents;
  }
}
?>
