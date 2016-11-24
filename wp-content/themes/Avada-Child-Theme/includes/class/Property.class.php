<?php
class Property extends PropertyFactory
{
  private $raw_post = false;

  public function __construct( WP_Post $property_post = null )
  {
    $this->raw_post = $property_post;
    return $this;
  }

  public function load( $id )
  {
    $this->raw_post = get_post($id);
    return $this;
  }

  public function ID()
  {
    return $this->raw_post->ID;
  }
  public function Title()
  {
    return $this->raw_post->post_title;
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
  public function AuthorPhone()
  {
    $meta = get_the_author_meta('phone', $this->raw_post->post_author);
    return $meta;
  }
  public function AuthorEmail()
  {
    $meta = get_the_author_meta('email', $this->raw_post->post_author);
    return $meta;
  }
  public function StatusKey()
  {
    if ($this->isArchived()) {
      return 'archived';
    }
    return $this->raw_post->post_status;
  }
  public function URL()
  {
    return get_option('siteurl').'/'.SLUG_INGATLAN.'/'.$this->RegionSlug().'/'.$this->ParentRegionSlug().'/'.sanitize_title($this->Title()).'-'.$this->ID();
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

    return false;
  }

  public function Regions()
  {
    $regions  = array();
    $start    = true;
    $end      = false;
    $terms    = wp_get_post_terms( $this->ID(), 'locations' );
    $term     = $terms[0];
    unset($terms);
    $ctp      = $term->parent;
    $regions[$term->term_id] = $term;

    while ( $ctp )
    {
      $term =  get_term($ctp, 'locations');
      $regions[$term->term_id] = $term;

      if($term->parent != 0) {
        $ctp = $term->parent;
      } else {
        $ctp = false;
        $term = null;
      }
    }

    return array_reverse($regions);
  }

  public function RegionSlug()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        $parent = false;
        if ($term->parent != 0) {
          $parent = get_term($term->parent);
        }
        return ($parent) ? $parent->slug : $term->slug;
      }
    }

    return false;
  }

  public function ParentRegionSlug()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        if ($term->parent != 0) {
          return $term->slug;
        }
      }
    }

    return false;
  }

  public function ParentRegion()
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        if ($term->parent != 0) {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyStatus( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'status' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'status') {
        if ($text) {
          return $this->i18n_taxonomy_values($term->name);
        } else {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyHeating( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-heating' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'property-heating') {
        if ($text) {
          return $this->i18n_taxonomy_values($term->name);
        } else {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyCondition( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-condition' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'property-condition') {
        if ($text) {
          return $this->i18n_taxonomy_values($term->name);
        } else {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function PropertyType( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-types' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'property-types') {
        if ($text) {
          return $this->i18n_taxonomy_values($term->name);
        } else {
          return $term->name;
        }
      }
    }

    return false;
  }

  public function historyChangeCount( $user = false )
  {
    global $wpdb;

    $prep = array();
    $prep[] = $this->ID();

    $q = "SELECT count(ID) FROM listing_change_history WHERE item_id = %d ";

    if ($user && $user->ID()) {
      $q .= " and changer_user_id = %d ";
      $prep[] = $user->ID();
    }

    return $wpdb->get_var($wpdb->prepare($q, $prep));
  }

  public function isNews()
  {
    $h = true;

    return $h;
  }

  public function isArchived()
  {
    if ($this->getMetaValue('_listing_flag_archived') == 1) {
      return true;
    }

    return false;
  }

  public function ArchivingInProgress()
  {
    global $wpdb;

    $arc_reg_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM listing_archive_reg WHERE postID = %d and accept_userid IS NULL;", $this->ID() ) );

    if ($arc_reg_id) {
      return $arc_reg_id;
    }

    return false;
  }

  public function ArchivingData()
  {
    global $wpdb;

    $arcid = $this->ArchivingInProgress();

    if ($arcid) {
      $data = $wpdb->get_row( "SELECT * FROM listing_archive_reg WHERE ID = ".$arcid );
      return $data;
    }

    return false;
  }

  public function isDropOff()
  {
    $h = false;
    $offp = $this->getMetaValue('_listing_offprice');

    if ($offp != 0 && $offp) {
      $h = true;
    }

    return $h;
  }

  public function isHighlighted()
  {
    $h = true;

    $v = $this->getMetaValue('_listing_flag_highlight');

    if (!$v || $v == '' || $v == '0') {
      return false;
    }

    return $h;
  }

  public function Images()
  {
    return get_attached_media( 'image', $this->ID() );
  }

  public function imageNumbers()
  {
    $n = count($this->Images());
    return $n;
  }

  public function StatusID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'status' );

    if (!$terms) {
      return 0;
    }

    return $terms[0]->term_id;
  }

  public function CatID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-types' );

    if (!$terms) {
      return 0;
    }

    return $terms[0]->term_id;
  }

  public function GPS()
  {
    $lat = $this->getMetaValue( '_listing_gps_lat' );
    $lng = $this->getMetaValue( '_listing_gps_lng' );

    if (!$lng || !$lat) {
      return false;
    }

    return array(
      "lat" => (float)$lat,
      "lng" => (float)$lng
    );
  }

  public function HeatingID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-heating' );

    if (!$terms) {
      return 0;
    }

    return $terms[0]->term_id;
  }

  public function ConditionID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-condition' );

    if (!$terms) {
      return 0;
    }

    return $terms[0]->term_id;
  }

  public function ShortDesc()
  {
    return $this->raw_post->post_excerpt;
  }

  public function getMetaValue( $key )
  {
    $value = get_post_meta($this->ID(), $key, true);

    return $value;
  }

  public function getMetaCheckbox( $key )
  {
    $v = $this->getMetaValue( $key );

    if ( !empty($v) && $v == '1') {
      return 1;
    } else {
      return 0;
    }
  }

  public function Description( $front = false )
  {
    $content = apply_filters ("the_content", $this->raw_post->post_content);
    if ($front) {
        $content = YoutubeHelper::ember($content);
    }
    return $content;
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

    if ($this->isDropOff()) {
      $off_price = $this->getMetaValue('_listing_offprice');
      if ($off_price && $off_price != 0) {
        $price = $off_price;
      }
    }

    if ( !$price ) {
      return __('Ár hiányzik (!)', 'gh');
    }

    if ($formated) {
      $price = number_format($price, 0, ' ', '.').' '.$this->getValuta();
    }

    return $price;
  }
  public function OriginalPrice( $formated = false )
  {
    $price = $this->getMetaValue('_listing_price');

    if ( !$price ) {
      return 0;
    }

    if ($formated) {
      $price = number_format($price, 0, ' ', '.').' '.$this->getValuta();
    }

    return $price;
  }
  public function OffPrice( $formated = false )
  {
    $price = $this->getMetaValue('_listing_offprice');

    if ( !$price ) {
      return 0;
    }

    if ($formated) {
      $price = number_format($price, 0, ' ', '.').' '.$this->getValuta();
    }

    return $price;
  }
  public function ProfilImgID()
  {
    return get_post_thumbnail_id( $this->ID() );
  }
  public function ProfilImg()
  {
    $img = wp_get_attachment_image_src( get_post_thumbnail_id( $this->ID() ), "full" );

    if (!$img) {
      return 'https://placeholdit.imgix.net/~text?txtsize=18&txt=GH&w=360&h=200';
    } else {
        return $img[0];
    }
  }
  public function Status( $only_text = true )
  {
    $status = null;

    if (!$only_text) {
      $status .= '<div class="dashboard-label status-label status-'.$this->StatusKey().'" style="background: '.$this->property_status_colors[$this->StatusKey()].';">';
    }

    $status .= $this->StatusText($this->StatusKey());

    if (!$only_text) {
      $status .= '</div>';
    }

    return $status;
  }
}
?>
