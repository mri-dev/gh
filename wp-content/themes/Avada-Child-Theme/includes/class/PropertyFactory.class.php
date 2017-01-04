<?php

class PropertyFactory
{
  const PROPERTY_TAXONOMY_META_PREFIX = '_listing_';
  const LOG_CHANGE_DB = 'listing_change_history';
  const LOG_VIEW_DB = 'listing_views';
  const LOG_WATCHTIME_DB = 'listing_watchtimestamp';
  const PROPERTY_ARCHIVE_DB = 'listing_archive_reg';
  const PRICE_TYPE_FIX_INDEX = 0;
  const NEWSDAY = 30;

  public $property_taxonomies_id = array('property-types', 'property-condition', 'property-heating', 'status', 'locations');
  public $fake_city = array('Kecskemét', 'Kiskunhalas');
  public $property_status_colors = array(
    'publish' => '#c6e8c6',
    'draft'   => '#e2e2e2',
    'pending' => '#ffd383',
    'future' => '#fff8a8',
    'archived' => '#ff9797',
  );

  public $price_types = array(
    'fix' => 0,
    'per_nm' => 1,
    'per_ha' => 2,
    'per_month' => 3,
   );

  public function getValuta()
  {
    return 'Ft';
  }

  public function __construct()
  {
    return $this;
  }

  public function get_controller()
  {
    global $_wp_listings;

    return $_wp_listings;
  }

  public function StatusText( $status = null )
  {
    switch ( $status ) {
      case 'publish':
        return __( 'Közzétéve (aktív)', 'gh');
      break;
      case 'pending':
        return __( 'Függőben', 'gh');
      break;
      case 'draft':
          return __( 'Vázlat', 'gh');
      break;
      case 'archived':
        return __( 'Archivált', 'gh');
      break;
      case 'future':
        return __( 'Időzített', 'gh');
      break;
      default:
        return $status;
      break;
    }
  }

  public function getPriceTypeText( $index = -1 )
  {
    return $this->i18n_pricetype_values($index);
  }

  public function i18n_pricetype_values( $index )
  {
    $texts = array(
      0 => __('Ár', 'gh'),
      1 => sprintf(__('%s / nm', 'gh'), $this->getValuta()),
      2 => sprintf(__('%s / Ha', 'gh'), $this->getValuta()),
      3 => sprintf(__('%s / hó', 'gh'), $this->getValuta()),
    );

    return $texts[$index];
  }

  public static function i18n_taxonomy_values( $key )
  {
    $texts = array(
      'elado' => __('Eladó', 'gh'),
      'kiado' => __('Kiadó', 'gh'),
      'berbeado' => __('Bérbeadó', 'gh'),
      // Ingatlan típusok
      'lakas' => __('Lakás', 'gh'),
      'haz' => __('Ház', 'gh'),
      'nyaralo' => __('Nyaraló', 'gh'),
      'telek' => __('Telek', 'gh'),
      'ipartelep' => __('Ipartelep', 'gh'),
      'kereskedelmi' => __('Kereskedelmi ingatlan', 'gh'),
      'mezogazdasagi' => __('Mezőgazdasági terület', 'gh'),
      'garazs' => __('Garázs', 'gh'),
      'csaladi_haz' => __('Családi ház', 'gh'),
      'panel' => __('Panel', 'gh'),
      'sorhaz' => __('Sorház', 'gh'),
      'tegla' => __('Tégla', 'gh'),
      'uj_epitesu' => __('Új építésű', 'gh'),
      'ikerhaz' => __('Ikerház', 'gh'),
      'hazresz' => __('Házrész', 'gh'),
      'kastely_villa' => __('Kastély, villa', 'gh'),
      'tanya' => __('Tanya', 'gh'),
      // Ingatlan állapotok
      'uj' => __('Új', 'gh'),
      'ujszeru' => __('Újszerű', 'gh'),
      'felkesz' => __('Félkész', 'gh'),
      'azonnal-koltozheto' => __('Azonnal költözhető', 'gh'),
      'hasznalt' => __('Használt', 'gh'),
      'felujitando' => __('Felújítandó', 'gh'),
      'felujitott' => __('Felújított', 'gh'),
      'lakhatatlan' => __('Lakhatatlan', 'gh'),
      'lakhato' => __('Lakható', 'gh'),
      'tehermentes' => __('Tehermentes', 'gh'),
      'jo_allapotu' => __('Jó állapotú', 'gh'),
      // Fűtés típuskulcsok
      'gaz-cirko' => __('Gáz / Cirkó', 'gh'),
      'elektromos' => __('Elektromos', 'gh'),
      'gaz-konvektor' => __('Gáz / Konvektor', 'gh'),
      'gaz-napkollektor' => __('Gáz + Napkollektor', 'gh'),
      'gazkazan' => __('Gázkazán', 'gh'),
      'geotermikus' => __('Geotermikus', 'gh'),
      'hazkozponti' => __('Házközponti', 'gh'),
      'tavfutes' => __('Távfűtés', 'gh'),
      'tavfutes-egyedi-meressel' => __('Távfűtés egyedi mérssel', 'gh'),
      'vegyes' => __('Vegyes', 'gh'),
      'egyeb' => __('Egyéb', 'gh'),
    );

    $t = $texts[$key];

    if (empty($t)) {
      return $key;
    }

    return $t;
  }

  public function getLocationChilds( $parent = 0, $arg = array() )
  {
    $param = array(
      'taxonomy' => 'locations',
      'echo' => true,
      'hierarchical' => 1,
      'child_of' => $parent,
      'orderby' => 'name',
      'order' => 'ASC',
      'walker' => new Location_Childs_Walker
    );
    $param = array_merge($param, $arg);
    wp_dropdown_categories($param);
  }

  public function getZoneGPS( $term_id = false )
  {
    if ( !$term_id ) {
      return false;
    }

    $gps_lat = get_term_meta( $term_id, 'gps_lat', true );
    $gps_lng = get_term_meta( $term_id, 'gps_lng', true );

    if ( !$gps_lat || !$gps_lng ) {
      return false;
    }

    return array(
      'lat' => (float)$gps_lat,
      'lng' => (float)$gps_lng
    );
  }
}

class Location_Childs_Walker extends Walker_CategoryDropdown {
  function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat('&mdash; ', $depth);

		$cat_name = apply_filters('list_cats', $category->name, $category);

    $cat_name = PropertyFactory::i18n_taxonomy_values($cat_name);

    $parent_term = get_term($category->parent);

		$output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
    if($parent_term->name == 'Budapest') {
      $output .= ' '.__('kerület', 'gh');
    }
		if ( $args['show_count'] )
			$output .= '  ('. $category->count .')';
		if ( $args['show_last_update'] ) {
			$format = 'Y-m-d';
			$output .= '  ' . gmdate($format, $category->last_update_timestamp);
		}
		$output .= "</option>\n";
	}
}

?>
