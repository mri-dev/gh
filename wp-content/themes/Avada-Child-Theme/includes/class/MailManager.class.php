<?php
class MailManager
{
  private $tos = array();
  private $template = false;
  private $subject = false;
  private $content = false;

  public function __construct( $tos = array(), $subject )
  {
    $this->subject = $subject;
    $this->tos = $tos;

    return $this;
  }

  public function setContent( $content = false )
  {
    $this->content = $content;
    return $this;
  }

  public function setTemplate( $template )
  {
    if (is_array($this->content)) {
      extract($this->content);
    } else {
      $content = (string)$this->content;
    }

    ob_start();
    include(locate_template('/templates/mails/'.$template.'.php'));
    $this->template = ob_get_contents();
    ob_end_clean();

    return $this;
  }

  public function send()
  {
    add_filter( 'wp_mail_content_type', array($this, 'html_mode') );

    $content = $this->template;

    ob_start();
    include(locate_template('/templates/mail.php'));
    $this->content = ob_get_contents();
    ob_end_clean();

    $headers = 'From: '.get_option('blogname', false).' <'.get_option('admin_email', false).'>' . "\r\n";

    $to = $this->tos;
    $subject = $this->subject;
    $body = $this->content;

    $wpmailre = wp_mail( $to, $subject, $body, $headers );

    remove_filter( 'wp_mail_content_type', array($this, 'html_mode') );

    return $wpmailre;
  }

  public function html_mode(){
      return "text/html";
  }

}
?>
