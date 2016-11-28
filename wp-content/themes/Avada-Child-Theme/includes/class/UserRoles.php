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

    public function i18n( $key )
    {
      $lng = array(
        'property_create' => __('Ingatlan létrehozás', 'gh'),
        'property_delete' => __('Ingatlan törlése', 'gh'),
        'property_archive' => __('Ingatlan archiválása', 'gh'),
        'property_edit_price' => __('Ingatlan ár módosítás', 'gh'),
        'property_edit' => __('Ingatlan szerkesztése', 'gh'),
        'property_edit_status' => __('Ingatlan státusz módosítás', 'gh'),
        'property_edit_autoconfirm_price' => __('Ingatlan árának automatikus jóváhagyás', 'gh'),
        'property_edit_autoconfirm_datas' => __('Ingatlan adatmódosítás automatikus jóváhagyás', 'gh'),
        'property_archive_autoconfirm' => __('Ingatlan archiválás automatikus jóváhagyás', 'gh'),
        'user_property_connector' => __('Felhasználó <-> Ingatlan összecsatolás', 'gh'),
        'stat_property' => __('Ingatlan statisztika', 'gh')
      );

      $text = $lng[$key];

      if(empty($text)) return $key;

      return $text;
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
        $this->addCap( $roleid, $cap);
      }
    }

    public function addCap($roleid, $cap)
    {
      $role = get_role( $roleid );

      if ($role) {
        $role->add_cap($cap);
      }
    }

    public function removeCap($roleid, $cap)
    {
      $role = get_role( $roleid );
      $role->remove_cap($cap);
    }
}

?>
