<?php
class Properties extends PropertyFactory
{
  public function getListParams( $taxonomy, $selected = null )
  {
    wp_dropdown_categories(array(
      'show_option_all' => __('-- válasszon --', 'gh'),
      'taxonomy'        => $taxonomy,
      'name'            => self::PROPERTY_TAXONOMY_META_PREFIX.$taxonomy,
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

		$output .= "\t<option class=\"level-$depth\" value=\"".$category->slug."\"";
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
