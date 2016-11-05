<?php
class Property extends PropertyFactory
{
  private $raw_post = false;

  public function __construct( WP_Post $property_post )
  {
    $this->raw_post = $property_post;
    return $this;
  }

  public function ID()
  {
    return $this->raw_post->ID;
  }

  public function CreateAt()
  {
    return date(get_option('date_format').' '.get_option('time_format'), strtotime($this->raw_post->post_date));
  }
  public function AuthorID()
  {
    return $this->raw_post->post_author;
  }
  public function AuthorName()
  {
    return get_author_name( $this->raw_post->post_author );
  }
  public function StatusKey()
  {
    return $this->raw_post->post_status;
  }
  public function RegionName()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        $parent = false;
        if ($term->parent != 0) {
          $parent = get_term($term->parent);
        }
        return ($parent) ? $parent->name.' > '.$term->name . ( ($parent->name == 'Budapest') ? ' '.__('kerület', 'gh') : '' ) : $term->name;
      }
    }

    return '???';
  }
  public function Address()
  {
    $addr = get_post_meta($this->ID(), '_listing_address', true);

    if (!$addr) {
      return '--';
    }
    return $addr;
  }
  public function Azonosito()
  {
    return get_post_meta($this->ID(), '_listing_idnumber', true);
  }
  public function Price( $formated = false )
  {
    $price = get_post_meta($this->ID(), '_listing_price', true);

    if ( !$price ) {
      return __('Ár hiányzik (!)', 'gh');
    }

    if ($formated) {
      $price = number_format($price, 0, ' ', '.').' '.$this->getValuta();
    }

    return $price;
  }
  public function ProfilImg()
  {
    return 'https://placeholdit.imgix.net/~text?txtsize=12&txt=GH&w=50&h=50';
  }
  public function Status( $only_text = true )
  {
    $status = null;

    if (!$only_text) {
      $status .= '<div class="dashboard-label status-label status-'.$this->StatusKey().'" style="background: '.$this->property_status_colors[$this->StatusKey()].';">';
    }

    switch ($this->StatusKey()) {
      case 'publish':
        $status .= __( 'Közzétéve (aktív)', 'gh');
      break;
      case 'pending':
        $status .= __( 'Függőben', 'gh');
      break;
      case 'draft':
        $status .= __( 'Vázlat', 'gh');
      break;
      case 'future':
        $status .= __( 'Időzített', 'gh');
      break;
      default:
        $status .= $this->StatusKey();
      break;
    }

    if (!$only_text) {
      $status .= '</div>';
    }

    return $status;
  }
}
?>
