<?php
class PropertyHistory extends PropertyFactory
{
  private $id = false;
  private $data = array();
  private $property = false;
  private $user = false;

  public function __construct( $postid, $data = array(), $arg = array() )
  {
    $this->id = $postid;
    $this->data = $data;
    $this->property = (new Property())->load( $postid );

    return $this;
  }

  public function mods()
  {
    $mods = array();

    foreach ($this->data->modify as $k => $v) {
      if ( !isset($v['f']) ) {
        foreach ($v as $key => $value) {

          if (is_array($value)) {
            $value['type'] = 'meta';
            $mods[$key] = $value;
          } else {
            $mods[$k][] = $value;
          }
        }
      } else {
        $v['type'] = 'post';
        $mods[$k] = $v;
      }
    }

    return $mods;
  }

  public function Date()
  {
    return $this->data->transaction_date;
  }

  public function ID()
  {
    return $this->data->ID;
  }

  public function modText( $key = '' )
  {
    $text = array();

    $texts = array(
			'col1' => array(
				__( 'Azonosító', 'gh' ) => '_listing_idnumber',
				__( 'Ingatlan státusza', 'gh' ) 	=> '_listing_status',
				__( 'Ingatlan állapot', 'gh' ) 	=> '_listing_property_condition',
				__( 'Ingatlan kategória', 'gh' ) 	=> '_listing_property_types',
				__( 'Pontos cím (utca, házszám, stb)', 'gh' ) => '_listing_address',
			  __( 'Irányár', 'gh' ) 	=> '_listing_price',
				__( 'Akciós irányár', 'gh' ) 	=> '_listing_offprice',
				__( 'GPS (lat)', 'gh' ) 	=> '_listing_gps_lat',
				__( 'GPS (lng)', 'gh' ) 	=> '_listing_gps_lng',
			),
			'col2' => array(
		    __( 'Építés éve', 'gh' )  => '_listing_year_built',
				__( 'Szintek száma', 'gh' )  => '_listing_level_numbers',
				__( 'Szobák száma', 'gh' )  => '_listing_room_numbers',
				__( 'Telek alapterület (nm)', 'gh' )  => '_listing_lot_size',
				__( 'Ingatlan alapterület (nm)', 'gh' )  => '_listing_property_size',
				__( 'Fürdőszobák száma', 'gh' )  => '_listing_bathroom_numbers',
				__( 'Archiválás megjegyzés', 'gh' )  => '_listing_archive_text',
				__( 'Archiválta', 'gh' )  => '_listing_archive_who',
			),
			'checkbox' => array(
				__( 'Garázs', 'gh' )  => '_listing_garage',
				__( 'Autóbeálló', 'gh' )  => '_listing_driveways',
				__( 'Kertcsoport, udvar', 'gh' )  => '_listing_garden',
				__( 'Erkély', 'gh' )  => '_listing_balcony',
				__( 'Lift', 'gh' )  => '_listing_lift',
				__( 'Zöldövezet', 'gh' )  => '_listing_green_area',
			),
			'flags' => array(
				__( 'Kiemelt', 'gh' )  => '_listing_flag_highlight',
				__( 'Archivált', 'gh' )  => '_listing_flag_archived',
        __( 'Kizárólagos hirdetés', 'gh' )  => '_listing_flag_exclusive',
        __( 'Premium hirdetés', 'gh' )  => '_listing_premium',
        __( 'Ár jellege', 'gh' )  => '_listing_flag_pricetype',
			),
      'default' => array(
        __( 'Ingatlan főcím (SEO)', 'gh') => 'post_title',
        __( 'Referens', 'gh') => 'post_author',
        __( 'Állapot', 'gh') => 'post_status',
        __( 'Leírás', 'gh') => 'post_content',
        __( 'Rövid leírás', 'gh') => 'post_excerpt',
      ),
      'extra' => array(
        __( 'Profilkép', 'gh') => 'feature_img_id',
        __( 'Törölt képek (ID)', 'gh') => 'deleting_imgs',
        __( 'Feltöltött képek', 'gh') => 'image_uploads',
        __( 'Törölt dokumentumok (ID)', 'gh') => 'deleting_pdf',
        __( 'Feltöltött dokumentumok (pdf)', 'gh') => 'pdf_uploads'
      ),
      'tax' => array(
        __( 'Ingatlan státusza', 'gh') => 'status',
        __( 'Ingatlan kategória', 'gh') => 'property-types',
        __( 'Fűtés', 'gh') => 'property-heating',
        __( 'Ingatlan állapota', 'gh') => 'property-condition',
      )
		);

    foreach ($texts as $tk => $ta ) {
      foreach ($ta as $tkk => $tv ) {
        $text[$tv] = $tkk;
      }
    }

    if (array_key_exists($key, $text)) {
      return $text[$key];
    }
    return $key;
  }

  public function property()
  {
    return $this->property;
  }

  public function formatValue( $key = false, $value = '', $pre_value = false )
  {
    switch ($key) {
      case '_listing_flag_pricetype':
        $value = $this->getPriceTypeText((int)$value);
        return $value;
      break;
      case '_listing_property_size':
      case '_listing_lot_size':
        $value = number_format($value, 0, '', ' '). ' '.__('nm', 'gh');
        return $value;
      break;
      case '_listing_price':
      case '_listing_offprice':
        if (!is_numeric($value)) {
          $value = 0;
        } else {
          if($pre_value){
            if ($pre_value < $value) {
              $diff_val = ($value - $pre_value);
            } else {
                $diff_val = ($pre_value - $value) * -1;
            }
          }
        }
        $value = number_format($value, 0, ' ', '.'). ' '.$this->getValuta() . ( ($diff_val) ? ( ($diff_val > 0) ? '<span class="price-up">+'.number_format($diff_val,0,' ', '.').' '.$this->getValuta().'</span>' : '<span class="price-down">'.number_format($diff_val,0,' ', '.').' '.$this->getValuta().'</span>'  ) : '' );
        return $value;
      break;
      case '_listing_driveways':
      case '_listing_green_area':
      case '_listing_flag_highlight':
      case '_listing_premium':
      case '_listing_garden':
      case '_listing_garage':
      case '_listing_balcony':
      case '_listing_lift':
      case '_listing_flag_exclusive':
        $value = ($value == '0') ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
        return $value;
      break;
      case 'post_author':
        if ($value != '') {
          $refu = new UserHelper(array('id' => $value));
          $value = '<strong>'.$refu->Name() . '</strong> <em>('.$refu->Email().')</em>';
        }
        return $value;
      break;
      case 'post_status':
        $value = $this->StatusText($value);
        return $value;
      break;
      case 'deleting_imgs':
        unset($value['type']);
        return implode($value, ', ');
      break;
      case 'deleting_pdf':
        unset($value['type']);
        return implode($value, ', ');
      break;
      case 'pdf_uploads':
        $set = '';
        foreach ($value as $id ) {
          $src = get_post($id);
          $set .= '<a href="'.$src->guid.'" target="_blank"><i class="fa fa-file-pdf-o"></i> '.$src->post_title.'</a><br>';
        }
        return $set;
      break;
      case 'image_uploads':
        $imgset = '';
        foreach ($value as $id ) {
          $src = wp_get_attachment_image($id, array(90, 90), "", array('class' => 'img-prev'));
          $imgset .= $src;
        }
        return $imgset;
      break;
      case 'status':
      case 'property-condition':
      case 'property-heating':
      case 'property-types':
        if ($value == '' || $value == '0') {
          $value = 'n.a.';
        } else {
          $vt = get_term($value);
          $value = $this->i18n_taxonomy_values($vt->name);
        }
        return $value;
      break;
      default:
        return $value;
      break;
    }
  }

  public function user()
  {
    return new UserHelper( array( 'id' => $this->data->changer_user_id ) );
  }
}
?>
