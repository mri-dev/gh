<?php
class control_property_history
{
  public function __construct()
  {
    return $this;
  }

  public function load( $arg = array() )
  {
    $properties = new Properties();
    return $properties->listChangeHistory( $arg );
  }
}
?>
