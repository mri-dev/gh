<?php
class GHImporter {
  private $db_zones = array();
  public function __construct()
  {
    $zonak = get_terms(array(
      'taxonomy' => 'locations',
      'hide_empty' => false
    ));

    foreach ($zonak as $z ) {
      $this->db_zones[$z->slug] = $z;
    }
  }

  public function zonak()
  {
    $pre = array();
    require_once get_stylesheet_directory().'/import_files/zonak.php';

    if ($zonak) {
      foreach ($zonak as $d) {
        $d['parent'] = 0;
        $this->zonak_rewrite($d);

        if( !$d ) continue;

        $slug = sanitize_title($d['telepules']);
        $has_rel = $this->db_zones[$slug];
        $parent = ($has_rel) ? $has_rel->parent : $d['parent'];

        $pre[] = array(
          'name' => $d['telepules'],
          'slug' => $slug,
          'count' => $d['db'],
          'parent' => $parent,
          'has_rel' => $has_rel
        );
      }
    }
    return $pre;
  }

  public function insert_zonak( $prepare = array() )
  {
    if (!$prepare) {
      return false;
    }
    $inserts = array();

    foreach ( $prepare as $d ) {
      if($d[has_rel]) continue;

      $arg = array(
        'slug' => $d['slug'],
        'parent' => $d['parent']
      );

      $insert = array(
        $d['name'],
        'locations',
        $arg
      );
      wp_insert_term($d['name'], 'locations', $arg);
      $inserts[] = $insert;
    }

    return $inserts;
  }

  private function zonak_rewrite( &$d )
  {
    switch ($d['telepules']) {
      case 'Budapest XII.':
        $d['telepules'] = 'XII.';
        $d['parent'] = -1;
      break;
      case 'Budapest II. kerület':
      case 'Budapest - II. kerület - Budaliget':
      case 'Budapest - Budaliget - II/a kerület':
      case 'Budapest - II. kerület - Máriaremete':
      case 'Budapest -II. kerület - Hidegkút':
      case 'Budapest - II. kerület - Hűvösvölgy':
      case 'Budapest, II. kerület':
      case 'Budapest II. ker.':
      case 'Budapest - II/a kerület - Budaliget':
        $d['telepules'] = 'II.';
        $d['parent'] = $this->db_zones['ii']->term_id;
      break;
      case 'Budapest III. kerület Ürömhegy':
      case 'Budapest III. Csillaghegy':
        $d['telepules'] = 'III.';
        $d['parent'] = $this->db_zones['iii']->term_id;
      break;
      case 'Budapest, XI ker.':
        $d['telepules'] = 'XI.';
        $d['parent'] = $this->db_zones['xi']->term_id;
      break;
      case 'Budapest, V.':
        $d['telepules'] = 'V.';
        $d['parent'] = $this->db_zones['v']->term_id;
      break;
      case 'Budapest VII. kerület':
      case 'Budapest, VII. kerület':
      case 'Budapest - Svábhegy - XII. kerület':
        $d['telepules'] = 'VII.';
        $d['parent'] = $this->db_zones['vii']->term_id;
      break;
      case 'Budapest, XIII.':
        $d['telepules'] = 'XIII.';
        $d['parent'] = $this->db_zones['xiii']->term_id;
      break;
      case 'Pécs-Hird':
      case 'PÉcs (Hird)':
        $d['telepules'] = 'Hird';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Tenkes hegy':
        $d['telepules'] = 'Tenkes';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Pécs-nyugat közeli ':
      case 'Pécs mellett':
      case 'Pécs-Kővágószőllős között':
      case 'Pécs Vasas':
        $d['telepules'] = 'Vasas';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Pécs-Nagyárpád':
        $d['telepules'] = 'Nagyárpád';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Pécs-Cserkút':
        $d['telepules'] = 'Cserkút';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Pécs-Somogy':
        $d['telepules'] = 'Somogy';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Pécs mellett, Cserkúton':
        $d['telepules'] = 'Cserkút';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Rédics':
      case 'Brodarica (CRO)':
      case 'Kaposszekcső':
      case 'Bátaszék, Tolna megye':
        $d = false;
      break;
      case 'Üröm':
      case 'Isaszeg':
        $d['parent'] = $this->db_zones['pest']->term_id;
      break;
      case 'Balatonfenyves':
      case 'Balatonszárszó':
        $d['parent'] = $this->db_zones['balaton']->term_id;
      break;
      case 'Pogány (reptér közelében)':
        $d['telepules'] = 'Pogány';
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
      case 'Kozármisleny':
      case 'Pogány':
      case 'Nagykozár':
      case 'Pellérd':
      case 'Egerág':
      case 'Keszü':
      case 'Orfű':
      case 'Harkány': case 'Harkány, ':
      case 'Mohács':
      case 'Bogád':
      case 'Hásságy':
      case 'Sikonda':
      case 'Pécsudvard':
      case 'Babarc':
      case 'Áta':
      case 'Szentlőrinc':
      case 'Sásd':
      case 'Szászvár':
      case 'Gemenc':
      case 'Görcsöny':
      case 'Szemely':
      case 'Berkesd':
      case 'Pécsbánya':
      case 'Szalánta':
      case 'Aranyosgadány':
      case 'Csatahely':
      case 'Keszü Újtelep':
      case 'Hosszúhetény':
      case 'Cserkút':
      case 'Diosviszló':
      case 'Boldogasszonyfa':
      case 'Siklós':
      case 'Magyarbóly':
        $d['parent'] = $this->db_zones['baranya']->term_id;
      break;
    }
  }
}
?>
