<?php
class ListingSearcher
{
    const SCTAG = 'listing-searcher';

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

        $output .= $t->load_template( array( 'properties' => $properties, 'form' => $get ));

        $output .= '</div>';

        /* Return the output of the tooltip. */
        return apply_filters( self::SCTAG, $output );
    }

}

new ListingSearcher();

?>
