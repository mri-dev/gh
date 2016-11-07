<?php
class ListingLista
{
    const SCTAG = 'listing-list';
    // Elérhető set-ek
    public $params = array();
    public $template = 'standard';
    public $type;

    public function __construct()
    {
        add_action( 'init', array( &$this, 'register_shortcode' ) );
    }

    public function register_shortcode() {
        add_shortcode( self::SCTAG, array( &$this, 'do_shortcode' ) );
    }

    public function do_shortcode( $attr, $content = null )
    {
        $output = '<div class="'.self::SCTAG.'-holder listing-sc">';

    	  /* Set up the default arguments. */
        $defaults = apply_filters(
            self::SCTAG.'_defaults',
            array(
              'src' => 'list',
              'view' => 'standard'
            )
        );

        /* Parse the arguments. */
        $attr           = shortcode_atts( $defaults, $attr );
        $this->params   = $attr;
        $this->type     = $attr['src'];
        $this->template = $attr['view'];

        if (!is_null($attr['src']))
        {
          $output .= '<div class="'.self::SCTAG.'-set-'.$attr['set'].'">';
          switch ( $this->type )
          {
            case 'highlight':
              $output .= $this->highlight();
            break;
            case 'watchnow':
              $output .= $this->watchnow();
            break;
            case 'news':
              $output .= $this->news();
            break;
            case 'viewed':
              $output .= $this->viewed();
            break;
            default:
              $output .= $this->no_src();
            break;
          }
          $output .= '</div>';
        }

        $output .= '</div>';

        /* Return the output of the tooltip. */
        return apply_filters( self::SCTAG, $output );
    }

    /**
    * Kiemelt listázás
    **/
    private function highlight( $arg = array() )
    {
      $o = '<div class="header">
        <div><a title="'.__('További kiemelt ingatlanok', 'gh').'" href="/ingatlanok/?kiemelt=1"><i class="fa fa-bars"></i></a></div>
        <h2>'.__('Kiemelt ingatlanok', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      return $o;
    }

    /**
    * Most nézik listázás
    **/
    private function watchnow( $arg = array() )
    {
      $o = '<div class="header">
        <h2>'.__('Most nézik', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      return $o;
    }

    /**
    * Új ingatlanok listázás
    **/
    private function news( $arg = array() )
    {
      $o = '<div class="header">
        <div><a title="'.__('További új ingatlanok mutatása', 'gh').'" href="/ingatlanok/?ob=date&o=desc&title=news"><i class="fa fa-bars"></i></a></div>
        <h2>'.__('Legújabb ingatlanok', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      return $o;
    }

    /**
    * Megnézett ingatlanok listázás
    **/
    private function viewed( $arg = array() )
    {
      $o = '<div class="header">
        <h2>'.__('Legutóbb megtekintett ingatlanok', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      return $o;
    }

    private function no_src()
    {
      return sprintf(__('(!) Nincs ilyen source: %s', 'gh'), $this->type);
    }
}

new ListingLista();

?>
