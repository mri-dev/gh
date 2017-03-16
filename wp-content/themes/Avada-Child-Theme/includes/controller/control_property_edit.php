<?php
class control_property_edit
{
  public function __construct()
  {
    return $this;
  }

  public function load( $id = false )
  {
    if ( !$id ) {
      return false;
    }

    $properties = new Properties(array(
      'id' => $id,
      'post_status' => array('publish', 'pending', 'draft', 'future'),
      'admin' => true
    ));
    $property = $properties->getList();

    return $property[0];
  }
}
?>
