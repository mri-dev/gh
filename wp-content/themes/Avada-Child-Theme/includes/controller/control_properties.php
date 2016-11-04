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
    $this->properties_number = $properties->Count();
    return $properties->getList();
  }

  public function propertyCount()
  {
    return $this->properties_number;
  }
}
?>
