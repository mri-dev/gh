<?php
class FeedManager
{
  private $output = '';
  private $feed = false;
  public function __construct()
  {
    header('Content-Type: text/xml; charset='.get_option('blog_charset'), true);
    $this->output .= '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';

    return $this;
  }

  public function setController( $feedClass, $arg = array() )
  {
    $this->feed = new $feedClass($arg);
    return $this;
  }

  public function load( $feedname )
  {
    $feed = $this->feed;

    ob_start();
    include(locate_template('/rss-'.$feedname.'.php'));
    $this->output .= ob_get_clean();

    return $this;
  }

  public function render()
  {
    echo $this->output;
  }
}
?>
