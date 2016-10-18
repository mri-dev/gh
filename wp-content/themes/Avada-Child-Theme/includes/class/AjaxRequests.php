<?php

class AjaxRequests
{
  public function __construct()
  {
    return $this;
  }

  public function send_travel_request()
  {
    //add_action( 'wp_ajax_'.__FUNCTION__, array( $this, 'sendTravelRequest'));
    //add_action( 'wp_ajax_nopriv_'.__FUNCTION__, array( $this, 'sendTravelRequest'));
  }

  public function sendTravelRequest()
  {
    extract($_POST);
    $return = array(
      'error' => 0,
      'msg'   => ''
    );

    ob_start();
  	  include(locate_template('templates/mails/utazasi-ajanlatkero-ertesites.php'));
      $message = ob_get_contents();
		ob_end_clean();


    echo json_encode($return);
    die();
  }

  public function getMailFormat(){
      return "text/html";
  }

  public function getMailSender($default)
  {
    return get_option('admin_email');
  }

  public function getMailSenderName($default)
  {
    return get_option('blogname', 'Wordpress');
  }

  private function returnJSON($array)
  {
    echo json_encode($array);
    die();
  }

}
?>
