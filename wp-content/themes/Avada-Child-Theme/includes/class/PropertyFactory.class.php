<?php

class PropertyFactory
{
  const PROPERTY_TAXONOMY_META_PREFIX = '_listing_';

  public $property_taxonomies_id = array('property-types', 'property-condition', 'status', 'locations');
  public $property_status_colors = array(
    'publish' => '#c6e8c6',
    'draft'   => '#e2e2e2',
    'pending' => '#ffd383',
    'future' => '#fff8a8'
  );

  public function getValuta()
  {
    return 'Ft';
  }

  public function __construct()
  {
    return $this;
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
