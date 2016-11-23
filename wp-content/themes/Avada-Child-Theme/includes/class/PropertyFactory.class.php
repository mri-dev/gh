<?php

class PropertyFactory
{
  const PROPERTY_TAXONOMY_META_PREFIX = '_listing_';
  const LOG_CHANGE_DB = 'listing_change_history';
  const LOG_VIEW_DB = 'listing_views';

  public $property_taxonomies_id = array('property-types', 'property-condition', 'property-heating', 'status', 'locations');
  public $property_status_colors = array(
    'publish' => '#c6e8c6',
    'draft'   => '#e2e2e2',
    'pending' => '#ffd383',
    'future' => '#fff8a8',
    'archived' => '#ff9797',
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

  public static function i18n_taxonomy_values( $key )
  {
    $texts = array(
      'elado' => __('Eladó', 'gh'),
      'kiado' => __('Kiadó', 'gh'),
      'berbeado' => __('Bérbeadó', 'gh'),
      'apartman' => __('Lakás', 'gh'),
      'house' => __('Ház', 'gh'),
      'villa' => __('Nyaraló', 'gh'),
      'lot' => __('Telek', 'gh'),
      'industrial_plant' => __('Ipartelep', 'gh'),
      'commercial_building' => __('Kereskedelmi ingatlan', 'gh'),
      'new' => __('Új', 'gh'),
      'reconditioned' => __('Felújított', 'gh'),
      'semi-finished' => __('Félkész', 'gh'),
      'used' => __('Használt', 'gh'),
      'gaz-cirko' => __('Gáz / Cirkó', 'gh'),
    );

    $t = $texts[$key];

    if (empty($t)) {
      return $key;
    }

    return $t;
  }
}

?>
