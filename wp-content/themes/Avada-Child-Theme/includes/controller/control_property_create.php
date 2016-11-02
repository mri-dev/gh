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

  public function getPropertyParams()
  {
    return $this->listing->property_details['col2'];
  }

  public function getTaxonomySelects( $id, $value = null )
  {
    $this->properties->getListParams( $id, $value );
  }
}
?>
