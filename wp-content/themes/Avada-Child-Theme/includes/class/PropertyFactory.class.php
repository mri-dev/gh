<?php

class PropertyFactory
{
  const PROPERTY_TAXONOMY_META_PREFIX = '_listing_';
  const LOG_CHANGE_DB = 'listing_change_history';
  const LOG_VIEW_DB = 'listing_views';
  const PRICE_TYPE_FIX_INDEX = 0;

  public $property_taxonomies_id = array('property-types', 'property-condition', 'property-heating', 'status', 'locations');
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

  public function i18n_pricetype_values( $index )
  {
    $texts = array(
      0 => __('Fix ár', 'gh'),
      1 => sprintf(__('%s / nm', 'gh'), $this->getValuta()),
      2 => sprintf(__('%s / Ha', 'gh'), $this->getValuta()),
      3 => sprintf(__('%s / hó', 'gh'), $this->getValuta()),
    );
  }

  public static function i18n_taxonomy_values( $key )
  {
    $texts = array(
      'elado' => __('Eladó', 'gh'),
      'kiado' => __('Kiadó', 'gh'),
      'berbeado' => __('Bérbeadó', 'gh'),

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

      'uj' => __('Új', 'gh'),
      'felkesz' => __('Félkész', 'gh'),
      'azonnal-koltozheto' => __('Azonnal költözhető', 'gh'),
      'hasznalt' => __('Használt', 'gh'),
      'felujitando' => __('Felújítandó', 'gh'),
      'felujitott' => __('Felújított', 'gh'),
      'lakhatatlan' => __('Lakhatatlan', 'gh'),
      'lakhato' => __('Lakható', 'gh'),
      'tehermentes' => __('Tehermentes', 'gh'),

      'gaz-cirko' => __('Gáz / Cirkó', 'gh'),
      'elektromos' => __('Elektromos', 'gh'),
      'gaz-konvektor' => __('Gáz / Konvektor', 'gh'),
      'gaz-napkollektor' => __('Gáz + Napkollektor', 'gh'),
      'gazkazan' => __('Gázkazán', 'gh'),
      'geotermikus' => __('Geotermikus', 'gh'),
      'hazkozponti' => __('Gáz / Cirkó', 'gh'),
      'tavfutes' => __('Távfűtés', 'gh'),
      'tavfutes-egyedi-meressel' => __('Távfűtés egyedi mérssel', 'gh'),
    );

    $t = $texts[$key];

    if (empty($t)) {
      return $key;
    }

    return $t;
  }
}

?>
