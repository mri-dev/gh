<?php
class ListingSearcher extends PropertyFactory
{
    const SCTAG = 'listing-searcher';
    public $excluded_options = array(
      'flag_archived',
      'flag_pricetype',
    );
    public $primary_options = array(
      'flag_highlight',
      'garage',
      'lift',
      'driveways',
      'balcony'
    );

    public function __construct()
    {
      add_action( 'init', array( &$this, 'register_shortcode' ) );
    }

    public function register_shortcode() {
      add_shortcode( self::SCTAG, array( &$this, 'do_shortcode' ) );
    }

    public function do_shortcode( $attr, $content = null )
    {
      /* Set up the default arguments. */
      $defaults = apply_filters(
          self::SCTAG.'_defaults',
          array(
            'view' => 'v1'
          )
      );
        $attr = shortcode_atts( $defaults, $attr );

        $get = $_GET;

        $output = '<div class="'.self::SCTAG.'-holder style-'.$attr['view'].' transf">';

        $properties = new Properties();

        $t = new ShortcodeTemplates(__CLASS__.'/'.$attr['view']);

        // Options
        $options = $this->getOptions();
        if ($get['opt'] != '') {
          $xoptsel = explode(",",$get['opt']);
        }

        $output .= $t->load_template( array(
          'properties' => $properties,
          'form' => $get,
          'options' => $options,
          'primary_options' => $this->primary_options,
          'sel_options' => $xoptsel
        ));

        $output .= '</div>';

        /* Return the output of the tooltip. */
        return apply_filters( self::SCTAG, $output );
    }

    private function getOptions()
    {
      $options = array();
      $temp_opt = array();

      $lc = $this->get_controller();

      $opt = array_merge($lc->property_details['flags'], $lc->property_details['checkbox']);

      if ( is_array($opt) && !empty($opt) )
      foreach ($opt as $text => $id) {
        $id = str_replace('_listing_','',$id);
        if (!in_array($id, $this->excluded_options)) {
          $temp_opt[$id] = $text;
        }
      }

      foreach ($temp_opt as $key => $value)
      {
        if (in_array($key, $this->primary_options)) {
          $options[$key] = $value;
          unset($temp_opt[$key]);
        }
      }

      $options = array_merge($options, $temp_opt);

      return $options;
    }
}

new ListingSearcher();

?>
