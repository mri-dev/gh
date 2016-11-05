<?php
class control_properties
{
  public $properties_number = 0;
  public function __construct()
  {
    return $this;
  }

  public function getProperties( $arg = array() )
  {
    $data = false;
    $properties = new Properties( $arg );
    $data = $properties->getList();
    $this->properties_number = $properties->Count();
    return $data;
  }

  public function propertyCount()
  {
    return $this->properties_number;
  }
}
?>
