<?php
class Properties extends PropertyFactory
{
  public $arg = array();
  private $datalist = array();
  private $exclue_megye_str = array( 'Budapest', 'Balaton' );
  private $count = 0;
  private $query = null;

  public function __construct( $arg = array() )
  {
    $this->arg = array_replace( $this->arg, $arg );

    return $this;
  }

  public function getRegions()
  {
    $terms = get_terms(array(
      'taxonomy' => 'locations'
    ));

    $t = array();

    foreach ($terms as $term) {
      $origin_name = $term->name;
      if ( !in_array($term->name, $this->exclue_megye_str)) {
        $term->name = sprintf(__('%s megye', 'gh'), $term->name);
      }
      if($origin_name == 'Pest') {
        $term->name .= ' / Budapest';
      }
      $t[] = $term;
    }

    return $t;
  }

  public function getSelectors( $id, $sel_values = array(), $arg = array() )
  {
    if (!$sel_values) {
      $sel_values = array();
    }
    $param = array(
      'taxonomy' => $id,
      'echo' => false
    );
    $param = array_merge($param, $arg);

    $terms = get_terms($param);

    $t = array();

    foreach ($terms as $term) {
      $term->selected = (in_array($term->term_id, $sel_values)) ? true : false;
      $term->name = $this->i18n_taxonomy_values($term->name);
      $t[] = $term;
    }

    $sorted_terms = array();

    $this->sort_hiearchical_order_term($t, $sorted_terms);
    unset($t);
    unset($terms);

    return $sorted_terms;
  }

  private function sort_hiearchical_order_term( Array &$cats, Array &$into, $parentId = 0 )
  {
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parentId) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }

    foreach ($into as $topCat) {
        $topCat->children = array();
        $this->sort_hiearchical_order_term($cats, $topCat->children, $topCat->term_id);
    }
  }

  public function listChangeHistory( $arg = array() )
  {
    global $wpdb;

    $data = array(
      'page' => array(
        'current' => 1,
        'limit'   => 25,
        'max'     => 1
      ),
      'count' => 0,
      'data' => array()
    );

    if (isset($arg['page']) && !empty($arg['page']))
    {
      $data['page']['current'] = (int)$arg['page'];
    }

    if (isset($arg['limit']) && !empty($arg['limit']))
    {
      $data['page']['limit'] = (int)$arg['limit'];
    }

    $params = array();

    // query
    $q = "SELECT SQL_CALC_FOUND_ROWS
      h.ID,
      h.changer_user_id,
      h.item_id,
      h.mod_data_json,
      h.transaction_date
    FROM listing_change_history as h
    LEFT JOIN $wpdb->posts as p ON p.ID = h.item_id ";

    $q .= " WHERE 1=1 ";
    $q .= " and h.group_key ='property'";

    if (isset($arg['property_id']) && !empty($arg['property_id'])) {
      $q .= " and h.item_id = %d ";
      $params[] = $arg['property_id'];
    }

    if (isset($arg['authors']) && is_array($arg['authors']) && !empty($arg['authors'])) {
      $q .= " and p.post_author IN(".implode(",",$arg['authors']).")";
    }

    if (isset($arg['user_id'])) {
      $q .= " and h.changer_user_id IN(%d)";
      $params[] = (int)$arg['user_id'];
    }

    if (isset($arg['azon']) && !empty($arg['azon'])) {
      $q .= " and (SELECT pm1.meta_value FROM {$wpdb->prefix}postmeta as pm1 WHERE pm1.post_id = h.item_id and pm1.meta_key = '_listing_idnumber') = %s ";
      $params[] = $arg['azon'];
    }

    $q .= " ORDER BY h.transaction_date DESC";

    // Limit
    $page_min = ($data['page']['current'] * $data['page']['limit']) - $data['page']['limit'];
    $q .= " LIMIT ".$page_min.", ".$data['page']['limit'];

    $qry = $wpdb->get_results($wpdb->prepare( $q, $params ));
    $count = $wpdb->get_var( "SELECT FOUND_ROWS();" );

    $data['page']['max'] = floor( $count / $data['page']['limit'] );

    if($qry)
    foreach ($qry as $qr)
    {
      $qr->modify = json_decode($qr->mod_data_json, true);
      unset($qr->mod_data_json);

      $data['data'][] = new PropertyHistory( $qr->item_id, $qr );
    }

    $data['count'] = $count;

    return $data;
  }

  public function getList()
  {
    global $wpdb;

    $data     = array();
    $post_arg = array(
      'post_type' => 'listing',
      'no_found_rows' => false
    );
    $meta_qry = array();

    // Archive
    if (isset($this->arg['list_archive']) && $this->arg['list_archive'] !== false)
    {
      $archive_dataset = array();
      $archive_ids = array();

      $aq = "SELECT
        a.*
      FROM ".self::PROPERTY_ARCHIVE_DB." as a
      WHERE 1=1 ";

      if ($this->arg['list_archive'] === 'only_accepted') {
        $aq .= " and a.accept_userid IS NOT NULL ";
      }

      if ($this->arg['list_archive'] === 'only_not_accepted') {
        $aq .= " and a.accept_userid IS NULL ";
      }

      $aq .= " ORDER BY a.accept_userid ASC, a.regDate ASC ";

      $qdata = $wpdb->get_results( $wpdb->prepare($aq, $prep), ARRAY_A );

      if ($qdata) {
        foreach ( $qdata as $qda ) {
          $archive_dataset[$qda['postID']] = $qda;
          $archive_ids[] = $qda['postID'];
        }
      } else {
        $archive_ids[] = -1;
      }

      $post_arg['post__in'] = $archive_ids;
      $post_arg['post_status'] = -1;
    }

    if (isset($this->arg['options']) && is_array($this->arg['options']) && !empty($this->arg['options']))
    {
      $option_meta = array();
      $option_meta['relation'] = 'AND';
      foreach ($this->arg['options'] as $opt) {
        $option_meta[] = array(
          'key' => '_listing_'.$opt,
          'compare' => '=',
          'value' => '1'
        );
      }

      $meta_qry[] = $option_meta;
    }

    if (isset($this->arg['highlight'])) {
      $meta_qry[] = array(
        'key' => '_listing_flag_highlight',
        'value' => '1'
      );
    }

    if (isset($this->arg['orderby'])) {
      $post_arg['orderby'] = $this->arg['orderby'];
    } else {
      $post_arg['orderby'] = 'meta_value_num';
      $post_arg['meta_key'] = '_listing_price';
    }

    if (isset($this->arg['after_date'])) {
      $post_arg['date_query'] = array(
        'after' => $this->arg['after_date']
      );
    }

    if (isset($this->arg['order'])) {
      $post_arg['order'] = $this->arg['order'];
    } else {
      $post_arg['order'] = 'ASC';
    }

    if (isset($this->arg['id'])) {
      $post_arg['post__in'] = array((int)$this->arg['id']);
    }
    if (isset($this->arg['ids']) && is_array($this->arg['ids'])) {
      $post_arg['post__in'] = $this->arg['ids'];
    }
    if (isset($this->arg['exc_ids']) && is_array($this->arg['exc_ids'])) {
      $post_arg['post__not_in'] = $this->arg['exc_ids'];
    }

    if (isset($this->arg['author'])) {
      $post_arg['author'] = $this->arg['author'];
    }

    if (isset($this->arg['authors']) && is_array($this->arg['authors']) && !empty($this->arg['authors'])) {
      $post_arg['author__in'] = $this->arg['authors'];
    }

    if (isset($this->arg['post_status'])) {
      $post_arg['post_status'] = $this->arg['post_status'];
    }

    if (isset($this->arg['hide_archived']) && $this->arg['hide_archived']) {
      $meta_qry[] = array(
          'relation' => 'OR',
          array(
            'key' => '_listing_flag_archived',
            'compare' => 'NOT EXISTS'
          ),
          array(
            'key' => '_listing_flag_archived',
            'value' => ''
          )
      );
    }

    if (isset($this->arg['only_archived']) && $this->arg['only_archived']) {
      $meta_qry[] = array(
        'key' => '_listing_flag_archived',
        'value' => '1'
      );
    }

    if (isset($this->arg['idnumber']) && !empty($this->arg['idnumber'])) {
      $idnum = $this->arg['idnumber'];
      $idnum = strtoupper($idnum);
      if (strpos($idnum,'GH') !== 0) {
        $idnum = 'GH'.$idnum;
      }
      $meta_qry[] = array(
        'key' => '_listing_idnumber',
        'value' => $idnum
      );
    }

    if (isset($this->arg['location']) && !empty($this->arg['location'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'locations',
        'field'     => 'term_id',
        'terms'     => $this->arg['location'],
        'compare'   => 'IN'
      );
    } else {
      if (isset($this->arg['cities']) && !empty($this->arg['cities'])) {
        $post_arg['tax_query'][] = array(
          'taxonomy'  => 'locations',
          'field'     => 'name',
          'terms'     => $this->arg['cities'],
          'compare'   => 'LIKE'
        );
      } else {
        if(isset($this->arg['regio']) && !empty($this->arg['regio'])) {
          $post_arg['tax_query'][] = array(
            'taxonomy'  => 'locations',
            'field'     => 'term_id',
            'terms'     => array($this->arg['regio']),
            'compare'   => 'IN'
          );
        }
      }
    }

    // Ingatlan státusz
    if (isset($this->arg['status']) && !empty($this->arg['status'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'status',
        'field'     => 'term_id',
        'terms'     => $this->arg['status'],
        'compare'   => 'IN'
      );
    }

    // Ingatlan típus
    if (isset($this->arg['property-types']) && !empty($this->arg['property-types'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'property-types',
        'field'     => 'term_id',
        'terms'     => $this->arg['property-types'],
        'compare'   => 'IN'
      );
    }

    // Ingatlan állapot
    if (isset($this->arg['property-condition']) && !empty($this->arg['property-condition'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'property-condition',
        'field'     => 'term_id',
        'terms'     => $this->arg['property-condition'],
        'compare'   => 'IN'
      );
    }

    if (isset($this->arg['limit'])) {
      $post_arg['posts_per_page'] = $this->arg['limit'];
    } else {
      $post_arg['posts_per_page'] = 30;
    }

    // Rooms
    if (isset($this->arg['rooms'])) {
      $meta_qry[] = array(
        'key' => '_listing_room_numbers',
        'value' => (int) $this->arg['rooms'],
        'type' => 'numeric',
        'compare' => '>='
      );
    }

    // Alapterület
    if (isset($this->arg['alapterulet'])) {
      $meta_qry[] = array(
        'key' => '_listing_lot_size',
        'value' => (int) $this->arg['alapterulet'],
        'type' => 'numeric',
        'compare' => '>='
      );
    }

    // Price
    if (isset($this->arg['price'])) {
      $price_meta_qry = array();
      $all_price = false;

      if (isset($this->arg['price_from']) && isset($this->arg['price_to'])) {
        $all_price = true;
      }

      if ( $all_price ) {
        $price_meta_qry['relation'] = 'AND';
      }

      if (isset($this->arg['price_from'])) {
        $this->arg['price_from'] = str_replace(".","", $this->arg['price_from']);
        $price_meta_qry[] = array(
          'key' => '_listing_price',
          'value' => (int) $this->arg['price_from'],
          'type' => 'numeric',
          'compare' => '>='
        );
      }

      if (isset($this->arg['price_to'])) {
        $this->arg['price_to'] = str_replace(".","", $this->arg['price_to']);
        $price_meta_qry[] = array(
          'key' => '_listing_price',
          'value' => (int) $this->arg['price_to'],
          'type' => 'numeric',
          'compare' => '<='
        );
      }

      $meta_qry[] = $price_meta_qry;
    }

    if (!empty($meta_qry)) {
      $post_arg['meta_query'] = $meta_qry;
    }

    $post_arg['paged'] = (int)$this->arg['page'];

    $posts = new WP_Query($post_arg);

    $this->query = $posts;
    $this->count = $posts->found_posts;

    //print_r($posts);

    foreach($posts->posts as $post) {
      $this->datalist[] = new Property($post);
    }
    return $this->datalist;
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function CountTotal()
  {
    return $this->count;
  }

  public function pagination( $base = '' )
  {
    return paginate_links( array(
    	'base'   => $base.'%_%',
    	'format'  => '?page=%#%',
    	'current' => max( 1, get_query_var('page') ),
    	'total'   => $this->query->max_num_pages
    ) );
  }

  public function Count()
  {
    return count($this->datalist);
  }

  public function getListParams( $taxonomy, $selected = null, $render_select = true )
  {
    wp_dropdown_categories(array(
      'show_option_all' => __('-- válasszon --', 'gh'),
      'taxonomy'        => $taxonomy,
      'name'            => 'tax['.$taxonomy.']',
      'id'              => self::PROPERTY_TAXONOMY_META_PREFIX.str_replace("-","_", $taxonomy),
      'orderby'         => 'name',
      'selected'        => $selected,
      'show_count'      => false,
      'hide_empty'      => false,
      'hierarchical'    => 1,
      'class'           => 'form-control',
      'walker'          => new Properties_Select_Walker
    ));
  }

  public function logView()
  {
    global $wpdb;
    if ($this->arg['id']) {

      if( $this->detect_robot() ) return false;

      $ucid = ucid();

      $wpdb->insert(
        self::LOG_VIEW_DB,
        array(
          'ip' => $_SERVER['REMOTE_ADDR'],
          'pid' => $this->arg['id'],
          'ref' => $_SERVER['HTTP_REFERER'],
          'qrystr' => $_SERVER['QUERY_STRING'],
          'ucid' => $ucid
        ),
        array(
          '%s', '%d', '%s', '%s', '%s'
        )
      );
    }
  }

  private function detect_robot()
  {
    if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|facebookexternalhit|Facebot|Googlebot/i', $_SERVER['HTTP_USER_AGENT'])) {
      return true;
    } else {
      return false;
    }
  }
}

class Properties_Select_Walker extends Walker_CategoryDropdown {
  function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat('&mdash; ', $depth);

		$cat_name = apply_filters('list_cats', $category->name, $category);

    $cat_name = PropertyFactory::i18n_taxonomy_values($cat_name);

		$output .= "\t<option class=\"level-$depth\" value=\"".$category->term_id."\"";
		if ( $category->term_id == $args['selected'] )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
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
