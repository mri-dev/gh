<?php

class UserRoles
{
    public $role_caps = array();

    public function __construct()
    {

    }

    public function addRoles( $role_set = array() )
    {
      foreach ($role_set as $role ) {
        add_role($role[0], $role[1]);
      }
    }

    public function removeRoles( $role_set = array() )
    {
      foreach ($role_set as $key ) {
        remove_role($key);
      }
    }

    public function addAvaiableCaps( $roleid, $capidset = array())
    {
      $role = get_role( $roleid );

      foreach ( $capidset as $cap ) {
        $this->role_caps[$roleid][] = $cap;
      }
    }

    public function addCap($roleid, $cap)
    {
      $role = get_role( $roleid );
      $role->add_cap($cap);
    }
}

?>
