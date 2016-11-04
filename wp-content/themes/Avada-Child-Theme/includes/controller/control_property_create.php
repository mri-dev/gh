<?php
class control_property_create
{
  public $listing = null;
  private $properties = null;

  public function __construct()
  {
    $this->listing = new WP_Listings();
    $this->properties = new Properties();

    return $this;
  }

  public function getPropertyParams( $group = 'col1' )
  {
    return $this->listing->property_details[$group];
  }

  public function getTaxonomySelects( $id, $value = null )
  {
    $this->properties->getListParams( $id, $value );
  }
}
?>
