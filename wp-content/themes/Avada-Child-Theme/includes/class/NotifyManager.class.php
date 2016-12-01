<?php
class NotifyManager extends PropertyFactory
{
  private $notifications = array();
  private $total_notify = 0;
  public function __construct()
  {
    global $wpdb;

    // Archive requests
    $this->notifications['property']['archive_request'] = $wpdb->get_var( "SELECT count(ID) FROM ".self::PROPERTY_ARCHIVE_DB." WHERE accept_date IS NULL;" );
    $this->total_notify += $this->notifications['property']['archive_request'];

    return $this;
  }

  public function propertyArchiveRequests()
  {
    return $this->notifications['property']['archive_request'];
  }
}
$notify = new NotifyManager();
?>
