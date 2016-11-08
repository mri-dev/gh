<?php
class Properties extends PropertyFactory
{
  public $arg = array();
  private $datalist = array();

  public function __construct( $arg = array() )
  {
    $this->arg = array_replace( $this->arg, $arg );

    return $this;
  }

  public function getList()
  {
    $data     = array();
    $post_arg = array(
      'post_type' => 'listing'
    );

    if (isset($this->arg['id'])) {
      $post_arg['post__in'] = array((int)$this->arg['id']);
    }

    if (isset($this->arg['author'])) {
      $post_arg['author'] = $this->arg['author'];
    }

    if (isset($this->arg['post_status'])) {
      $post_arg['post_status'] = $this->arg['post_status'];
    }

    if (isset($this->arg['location']) && !empty($this->arg['location'])) {
      $post_arg['tax_query'][] = array(
        'taxonomy'  => 'locations',
        'field'     => 'term_id',
        'terms'      => $this->arg['location']
      );
    }

    if (isset($this->arg['limit'])) {
      $post_arg['posts_per_page'] = $this->arg['limit'];
    } else {
      $post_arg['posts_per_page'] = 30;
    }



    $posts = get_posts($post_arg);

    foreach($posts as $post) {
      $this->datalist[] = new Property($post);
    }
    return $this->datalist;
  }

  public function CountTotal()
  {
    $n = 0;
    foreach (wp_count_posts( 'listing' ) as $key => $value) {
      if (array_key_exists($key, $this->property_status_colors)) {
        $n += $value;
      }
    }
    return $n;
  }

  public function Count()
  {
    return count($this->datalist);
  }

  public function getListParams( $taxonomy, $selected = null )
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
      'class'           => 'form-control',
      'walker'          => new Properties_Select_Walker
    ));

  }
}

class Properties_Select_Walker extends Walker_CategoryDropdown {
  function start_el(&$output, $category, $depth, $args) {
		$pad = str_repeat(' ', $depth * 3);

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
