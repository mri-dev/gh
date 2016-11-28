<?php
class control_properties
{
  public $properties_number = 0;
  private $class = null;
  private $pagination = null;

  public function __construct()
  {
    return $this;
  }

  public function getProperties( $arg = array() )
  {
    $data = false;
    $properties = new Properties( $arg );
    $this->class = $properties;

    $data = $properties->getList();
    $this->properties_number = $properties->CountTotal();

    return $data;
  }

  public function pager( $base = '' )
  {
    return $this->class->pagination( $base );
  }

  public function propertyCount()
  {
    return $this->properties_number;
  }
}
?>
