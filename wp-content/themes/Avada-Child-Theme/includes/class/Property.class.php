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
    $phone = get_the_author_meta('phone', $this->raw_post->post_author);

    $_phone = PHONE_PREFIX.' '.substr($phone, 0, 2);
    $last = substr($phone, 2);

    if (strlen($last) > 6) {
      $_phone .= ' '.substr($last, 0, 3).' '. substr($last, 3 );
    } else {
      $_phone .= ' '. substr($last, 0, 3).' '. substr($last, 3 );
    }
    return $_phone;
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
    $regionslug = $this->ParentRegionSlug();
    $megye = $this->RegionSlug();

    if(in_array($this->ParentRegion(), $this->fake_city)) {
      $megye = 'magyarorszag';
    }

    if (empty($regionslug)) {
      $regionslug = '-';
    }

    return get_option('siteurl').'/'.SLUG_INGATLAN.'/'.$megye.'/'.$regionslug.'/'.sanitize_title($this->Title()).'-'.$this->ID();
  }
  public function RegionName( $html_text = true )
  {
    $terms = wp_get_post_terms( $this->ID(), 'locations' );

    foreach ($terms as $term) {
      if($term->taxonomy == 'locations') {
        $parent = false;
        if ($term->parent != 0) {
          $parent = get_term($term->parent);
        }

        if(in_array($term->name, $this->fake_city)) {
          $parent = false;
        }

        if ($html_text) {
          return ($parent) ? $parent->name.' <span class="sep">/</span> '.$term->name . ( ($parent->name == 'Budapest') ? ' '.__('kerület', 'gh') : '' ) : $term->name;
        } else {
          return ($parent) ? $parent->name.', '.$term->name . ( ($parent->name == 'Budapest') ? ' '.__('kerület', 'gh') : '' ) : $term->name;
        }

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

    if($term->parent != 0){
      $pt = get_term($term->parent);
      if($pt->name == 'Budapest') {
        $term->name .= ' '.__('kerület', 'gh');
      }
    }

    while ( $ctp )
    {
      $term =  get_term($ctp, 'locations');

      if($term->parent != 0){
        $pt = get_term($term->parent);
        if($pt->name == 'Budapest') {
          $term->name .= ' '.__('kerület', 'gh');
        }
      }

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

    return $terms;
  }

  public function multivalue_list( $term_list = array(), $linked = false, $base = '' )
  {
    $text = '';

    foreach ($term_list as $term) {
      if (!$linked) {
        $text .= $this->i18n_taxonomy_values($term->name).', ';
      }else{
        $link = str_replace('#value#', $term->term_id, $base);
        $text .= '<a target="_blank" href="'.$link.'">'.$this->i18n_taxonomy_values($term->name).'</a>, ';
      }
    }

    $text = rtrim($text, ', ');

    return $text;
  }

  public function PropertyType( $text = false )
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-types' );
    return $terms;
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

    // Diff
    $diff = 86400 * self::NEWSDAY;

    $time = ((int)strtotime($this->raw_post->post_date)) + $diff;

    if ( time() > $time ) {
      $h = false;
    }

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

  public function isExclusive()
  {
    $h = true;

    $v = $this->getMetaValue('_listing_flag_exclusive');

    if (!$v || $v == '' || $v == '0') {
      return false;
    }

    return $h;
  }

  public function Images()
  {
    return get_attached_media( 'image', $this->ID() );
  }

  public function PDFDocuments()
  {
    return get_attached_media( 'application/pdf', $this->ID() );
  }


  public function imageNumbers()
  {
    $n = count($this->Images());
    return $n;
  }

  public function StatusID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'status' );
    $ids = array();

    if (!$terms) {
      return 0;
    }

    foreach ($terms as $t) {
      $ids[] = $t->term_id;
    }

    return $ids;
  }

  public function CatID()
  {
    $terms = wp_get_post_terms( $this->ID(), 'property-types' );
    $ids = array();

    if (!$terms) {
      return 0;
    }

    foreach ($terms as $t) {
      $ids[] = $t->term_id;
    }

    return $ids;
  }

  public function GPS()
  {
    $lat = $this->getMetaValue( '_listing_gps_lat' );
    $lng = $this->getMetaValue( '_listing_gps_lng' );

    if (!$lng || !$lat)
    {
      // Mentett GPS vizsgálat GEO alapján
      $parent_zone_term = end($this->Regions());
      $zone_gps = $this->getZoneGPS($parent_zone_term->term_id);

      if ($zone_gps) {
        $lng = (float) $zone_gps['lng'];
        $lat = (float) $zone_gps['lat'];
      }
    }

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
    $ids = array();

    if (!$terms) {
      return 0;
    }

    foreach ($terms as $t) {
      $ids[] = $t->term_id;
    }

    return $ids;
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

  public function RawDescription()
  {
    return sanitize_text_field($this->raw_post->post_content);
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
      $price = number_format($price, 0, ' ', '.');
    }

    return $price;
  }
  public function PriceType()
  {
    $price_index = (int)$this->getMetaValue('_listing_flag_pricetype');
    if ($price_index === 0) {
      return $this->getValuta();
    }
    return $this->getPriceTypeText($price_index);
  }
  public function PriceTypeID()
  {
    return (int)$this->getMetaValue('_listing_flag_pricetype');
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

  public function ProfilImgAttr()
  {
    $imgmeta = wp_get_attachment_metadata($this->ProfilImgID());
    if (is_array($imgmeta))
    {
      $width = $imgmeta['width'];
      $height = $imgmeta['height'];

      if ($width === $height) {
        $imgmeta['orientation'] = 'square';
      } else if($width < $height ){
        $imgmeta['orientation'] = 'portrait';
      } else {
        $imgmeta['orientation'] = 'landscape';
      }
    } else {
      $imgmeta = array();

      $prof_img = $this->ProfilImg();

      $size = getimagesize($prof_img);

      if (!$size) {
        return false;
      }

      $width = $size[0];
      $height = $size[1];

      if ($width === $height) {
        $imgmeta['orientation'] = 'square';
      } else if($width < $height ){
        $imgmeta['orientation'] = 'portrait';
      } else {
        $imgmeta['orientation'] = 'landscape';
      }
    }
    return $imgmeta;
  }

  public function Viewed()
  {
    global $wpdb;
    $click = 0;

    $click = $wpdb->get_var("SELECT count(ID) FROM ".self::LOG_VIEW_DB." WHERE pid = ".$this->ID()." GROUP BY pid");

    if (!$click) {
      return 0;
    }


    return $click;
  }

  public function ProfilImg()
  {
    global $wpdb;
    $img_id = (int)get_post_thumbnail_id( $this->ID() );

    if (!$img_id) {
      return IMG.'/default_image.jpg';
    } else {
      return $wpdb->get_var($wpdb->prepare("SELECT guid FROM $wpdb->posts WHERE post_type='attachment' and post_parent = %d and ID = %d", $this->ID(), $img_id));
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
