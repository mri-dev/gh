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
          }

          $mods[$key] = $value;
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
    $text = array(
      'post_content' => __('Leírás', 'gh'),
      '_listing_flag_highlight' => __('Kiemelt hirdetés', 'gh'),
      '_listing_driveways' => __('Autóbeálló', 'gh'),
      '_listing_green_area' => __('Zöldövezet', 'gh'),
      'xxx' => __('', 'gh'),
    );

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
      case '_listing_garden':
        $value = ($value == '0') ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>';
        return $value;
      break;
      case 'post_status':
        $value = $this->StatusText($value);
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
