<?php
class GHImporter extends PropertyFactory {
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

  public function image_connect()
  {
    global $wpdb;

    $re = array();
    $parg = array();

    $q = "SELECT
      k.*
    FROM keptar as k
    WHERE 1=1
    ";

    $qq = $wpdb->get_results($wpdb->prepare($q, $parg), ARRAY_A);

    $image_prepare = array();

    foreach ($qq as $d)
    {
      $img_ar = array();
      //if($d['ingid'] != '2890') continue;

      // Images
      if ( !array_key_exists($d['ingid'], $image_prepare) )
      {
        $post_qry = new WP_Query(array(
          'post_type' => 'listing',
          'post_status' => -1,
          'meta_query' => array(
            array(
        			'key' => '_listing_idnumber',
        			'value' => 'GH'.$d['ingid'],
        		)
          )
        ));

        $post = $post_qry->posts[0];
        $uploads_dir = wp_upload_dir();
        $image_dir = $uploads_dir['basedir'] . '/listing/original/';

        if ($post) {
          $idir = $image_dir;
          $image_prepare[$d['ingid']]['imgdir'] = $idir;
          $image_prepare[$d['ingid']]['dir_exists'] = (file_exists($idir)) ? true : false;
        }

        $image_prepare[$d['ingid']]['post'] = $post;
      }

      if ($post) {
        // Image check
        $d['img_source'] = $idir . 'or_'.$d['kepid'].'.jpg';
        $d['img_type'] = wp_check_filetype( $d['img_source'], null );
        $d['img_exists'] = (file_exists($d['img_source'])) ? true : false;
      }

      $img_ar['src'][] = $d;
      $image_prepare[$d['ingid']]['src'][] = $d;
    }

    $re['images'] = $image_prepare;


    foreach ( $re['images'] as $rid => $r )
    {
      if (!$r['post']) {
        continue;
      }
      if ( !$r['dir_exists']) {
        continue;
      }

      foreach ($r['src'] as $i)
      {
        if (!$i['img_exists']) {
          continue;
        }

        // The ID of the post this attachment is for.
        $parent_post_id = $r['post']->ID;

        // Check the type of file. We'll use this as the 'post_mime_type'.
        $filetype = $i['img_type'];

        $filename = 'or_'.$i['kepid'].'.'.$filetype['ext'];

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $attachment = array(
        	'guid'           => $wp_upload_dir['url'] . '/listing/original/'.$filename,
        	'post_mime_type' => $filetype['type'],
        	'post_title'     => preg_replace( '/\.[^.]+$/', '', $filename),
        	'post_content'   => '',
        	'post_status'    => 'inherit'
        );

        $connected = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT ID FROM $wpdb->posts
                WHERE
                post_parent = %d and
                guid = %s
                AND post_type = 'attachment'",
                (int) $parent_post_id,
                $attachment['guid']
            )
        );

        if ($connected ) {
          continue;
        }

        // Insert the attachment.
        $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

        // Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.
        require_once( ABSPATH . 'wp-admin/includes/image.php' );

        // Generate the metadata for the attachment, and update the database record.
        $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
        wp_update_attachment_metadata( $attach_id, $attach_data );

        if ($i['sorrend'] == 1) {
          set_post_thumbnail( $parent_post_id, $attach_id );
        }
      }
    }

    return $re;
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

  public function user_connecter()
  {
    global $wpdb;

    $c = array();

    $q = "SELECT
      i.id,
      i.azon,
      i.kapcsolat
    FROM ingatlan_trans as i
    WHERE 1=1 LIMIT 0, 1000";

    $qdata = $wpdb->get_results( $q, ARRAY_A );

    foreach ($qdata as $d) {
      $c[] = array(
        'id' => $d['id'],
        'newid' => $this->check_ghid_usage($d['azon']),
        'kapcsolat' => $d['kapcsolat'],
        'author' => $this->find_post_author($d['kapcsolat'])
      );
    }

    if ($c)
    foreach ($c as $ci) {
      if ( $ci['author'] > 0 && $ci['newid'] ) {
        wp_update_post( array(
          'ID' => $ci['newid'],
          'post_author' => $ci['author']
        ) );
      }
    }

    return $c;
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

  public function ingatlanok()
  {
    global $wpdb;
    $data   = array();
    $src    = array();
    $import = array();

    $q = "SELECT
      i.id,
      i.alkatid,
      i.kiemelt,
      i.telepules,
      i.allapot,
      i.cim,
      i.szobakszama,
      i.alapterulet,
      i.kapcsolat,
      i.vismajor,
      i.vismajor2,
      i.azon,
      i.title,
      i.kulcsszavak,
      i.iranyar,
      i.megjegyzes,
      i.hirdetes
    FROM ingatlan_trans as i
    WHERE 1=1 LIMIT 0, 1000";

    $qdata = $wpdb->get_results( $q, ARRAY_A );
    $cats = $this->cat_connects();

    foreach ($qdata as $d) {
      $this->zonak_rewrite($d);
      $src[] = $d;
      $imp = array();
      $metas = array();
      $taxs = array();

      $content = '';
      if (wp_strip_all_tags($d['cim']) != '') {
        $content .= $d['cim'] . '<br><br>';
      }
      $content .= $d['hirdetes'];

      // Import prepare
      $imp['ID'] = $this->check_ghid_usage($d['azon']);
      $imp['post_type']  = 'listing';
      $imp['post_author'] = $this->find_post_author($d['kapcsolat']);
      $imp['post_title']  = ($d['title'] != '') ? $d['title'] : 'Eladó ingatlan';
      $imp['post_status'] = ($d['allapot'] == '1') ? 'publish' : 'draft';
      $imp['post_excerpt'] = wp_strip_all_tags($d['megjegyzes']);
      $imp['post_content'] = $content;
      $imp['keywords'] = $d['kulcsszavak'];
      $metas['_listing_room_numbers'] = ($d['szobakszama'] == '' || $d['szobakszama'] == '0') ? '' : (int)$d['szobakszama'];
      $metas['_listing_idnumber'] = $d['azon'];

      $price = (int)$d['iranyar'];

      if ($d['kiemelt'] == '1') {
        $metas['_listing_flag_highlight'] = 1;
      }

      $pricetype = 0;
      if ($d['vismajor'] == '' || $d['vismajor'] == 'Ft') {
        $pricetype = (int)$this->price_types['fix'];
      } else if($d['vismajor'] == 'e/hó'){
        $pricetype = (int)$this->price_types['per_month'];
      } else if($d['vismajor'] == 'e/m2'){
        $pricetype = (int)$this->price_types['per_nm'];
      }

      if($d['vismajor2'] == 'Ha') {
        $metas['_listing_lot_size'] = (int)$d['alapterulet'] * 10000;
      } else {
        $metas['_listing_property_size'] = (int)$d['alapterulet'];
      }

      $metas['_listing_flag_pricetype'] = $pricetype;

      $zonak = array(
        'telepules' => sanitize_title($d['telepules'])
      );
      $this->zonak_rewrite($zonak);
      $zonak = $this->db_zones[$zonak['telepules']];

      // Taxs
      $taxs['property-types']  = $cats[$d['alkatid']];
      $taxs['status']     = 45; // Eladó
      $taxs['property-condition'] = 54; // Lakható

      if ( $zonak->term_id ) {
        $taxs['locations']  = $zonak->term_id;
      }

      $metas['_listing_price'] = $price;

      $imp['meta_input'] = $metas;
      $imp['tax_input'] = $taxs;
      $import[] = $imp;
    }

    // Import
    $data['import'] = $import;
    // Kategóriák
    $data['cat_connects'] = $cats;
    // Src
    $data['src'] = $src;

    return $data;
  }

  public function do_ingatlan_import( $prepare = array() )
  {
    $ids = array();

    return false;

    foreach ( $prepare as $p ) {
      $keywords = $p['keywords'];
      $taxs = $p['tax_input'];

      unset($p['tax_input']);
      unset($p['keywords']);

      $postid = wp_insert_post( $p );

      if ($postid) {
        foreach ( $taxs as $tk => $t ) {
          wp_set_post_terms( $postid, $t, $tk );
        }

        if ( $keywords != '' ) {
          wp_set_post_tags( $postid, $keywords, true );
        }
        $ids[] = $postid;
      }
    }

    return $ids;
  }

  private function find_post_author( $kapcs = '' )
  {
    global $wpdb;

    $author = 1;

    $phone = $this->extract_numbers_from_str($kapcs);

    if ($phone == '') {
      return $author;
    }

    $id = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}usermeta WHERE meta_key = 'phone' and meta_value = %s;", $phone));

    if ($id) {
      return (int) $id;
    }

    return $author;
  }

  private function extract_numbers_from_str( $text )
  {
    $text = str_replace(array( '-', '+' ), '', filter_var($text, FILTER_SANITIZE_NUMBER_INT));
    $text = str_replace(
      array('0630', '0620', '0670','3630', '3620', '3670'),
      array('30', '20', '70', '30', '20', '70'),
      $text
    );

    return $text;
  }

  private function check_ghid_usage( $idn )
  {
    global $wpdb;

    $q = "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_listing_idnumber' and meta_value = %s;";

    $postid = $wpdb->get_var($wpdb->prepare($q, $idn));

    return $postid;
  }

  private function cat_connects()
  {
    global $wpdb;
    $ct = array(
      9   => 115,
      10  => 116,
      15  => 49,
      21  => 115,
      31  => 49,
      34  => 11,
      42  => 51,
      48  => 120,
      49  => 51,
      54  => 52,
      55  => 120,
      59  => 49,
      60  => 47,
      62  => 47,
      65  => 117,
      67  => 47,
      82  => 47,
      83  => 48,
      84  => 49,
    );
    $cats = array();

    /* * /
    $q = "SELECT
      i.alkatid,
      ac.fokatid as fokatid,
      fc.nev as fokat_nev,
      ac.nev as alkat_nev,
      count(i.id) as db,
      GROUP_CONCAT(i.id) as ids
    FROM ingatlan_trans as i
    LEFT OUTER JOIN alkat as ac ON ac.id = i.alkatid
    LEFT OUTER JOIN fokat as fc ON ac.fokatid = fc.id
    GROUP BY i.alkatid
    ";

    $datas = $wpdb->get_results( $q, ARRAY_A );

    foreach ($datas as $d) {
      $cats[] = $d;
    }
    /* */

    return $ct;
  }

  private function zonak_rewrite( &$d )
  {
    switch ($d['telepules']) {
      case 'Budapest XII.':
        $d['telepules'] = 'XII.';
        $d['parent'] = $this->db_zones['xii']->term_id;
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
