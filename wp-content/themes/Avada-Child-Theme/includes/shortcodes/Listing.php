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
              'view' => 'standard',
              'limit' => 6
            )
        );

        /* Parse the arguments. */
        $attr           = shortcode_atts( $defaults, $attr );
        $this->params   = $attr;
        $this->type     = $attr['src'];
        $this->template = $attr['view'];

        if (!is_null($attr['src']))
        {
          $output .= '<div class="'.self::SCTAG.'-set-'.$this->type.'">';
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
            case 'get':
              $output .= $this->get();
            break;
            case 'favorite':
              $output .= $this->favorite();
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
    * Kedvencek
    **/
    private function favorite( $arg = array() )
    {
      global $wpdb;

      $o = '<h1>'.__('Kedvenc ingatlanok listája', 'gh').'</h1>';
      $o .= '<div class="subtitle">'.__('Az alábbi listában találja azokat az ingatlanokat, amiket Ön kedvencnek jelölt.').'</div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $get = $_GET;

      $ucid = ucid();

      $fav_ids = array();
      $favs = $wpdb->get_results($wpdb->prepare( "SELECT pid FROM listing_favorites WHERE ucid = %s ORDER BY added DESC;", $ucid ), ARRAY_A);

      foreach ($favs as $f) {
        $fav_ids[] = $f['pid'];
      }

      $arg = array(
        'limit' => $this->params['limit'],
      );

      if (count($fav_ids) != 0)
      {
        $arg['ids'] = $fav_ids;

        $properties = new Properties($arg);
        $list = $properties->getList();

        $o .= '<div class="prop-list style-'.$this->template.'"><div class="prop-wrapper">';
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
        $o .= '</div></div>';
      } else {
        ob_start();
        include(locate_template('templates/parts/nodata-listing-favorite.php'));
        $o .= ob_get_contents();
        ob_end_clean();
      }

      return $o;
    }

    /**
    * Kereső, standard listázás
    **/
    private function get( $arg = array() )
    {
      $o = '<h1>'.__('Keresés eredménye', 'gh').'</h1>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $get = $_GET;

      $arg = array(
        'limit' => $this->params['limit'],
      );

      if (isset($get['hl']) && $get['hl'] == '1') {
        $arg['highlight'] = 1;
      }

      if (isset($get['n']) && !empty($get['n'])) {
        $arg['idnumber'] = $get['n'];
      }

      if (isset($get['rg']) && !empty($get['rg'])) {
        $arg['regio'] = $get['rg'];
      }
      if (isset($get['pa']) && !empty($get['pa'])) {
        $arg['price_from'] = $get['pa'];
        $arg['price'] = 1;
      }
      if (isset($get['pb']) && !empty($get['pb'])) {
        $arg['price_to'] = $get['pb'];
        $arg['price'] = 1;
      }

      if (isset($get['r']) && !empty($get['r'])) {
        $arg['rooms'] = $get['r'];
      }

      if (isset($get['ps']) && !empty($get['ps'])) {
        $arg['alapterulet'] = $get['ps'];
      }

      if (isset($get['ci']) && !empty($get['ci'])) {
        $arg['location'] = explode(",", $get['ci']);
      }
      if (isset($get['cities']) && !empty($get['cities'])) {
        $arg['cities'] = explode(",", $get['cities']);
      }

      if (isset($get['st']) && !empty($get['st'])) {
        $arg['status'] = explode(",", $get['st']);
      }

      if (isset($get['c']) && !empty($get['c'])) {
        $arg['property-types'] = explode(",", $get['c']);
      }

      //print_r($arg);

      $properties = new Properties($arg);
      $list = $properties->getList();


      if ( count($list) != 0 ) {
        $o .= '<div class="prop-list style-'.$this->template.'"><div class="prop-wrapper">';
        foreach ( $list as $e )
        {
          $o .= $t->load_template( array( 'item' => $e ) );
        }
        $o .= '</div></div>';
      } else {
        ob_start();
        include(locate_template('templates/parts/nodata-listing-get.php'));
        $o .= ob_get_contents();
        ob_end_clean();
      }
      return $o;
    }

    /**
    * Kiemelt listázás
    **/
    private function highlight( $arg = array() )
    {
      $o = '<div class="header">
        <div class="morev"><a title="'.__('További kiemelt ingatlanok', 'gh').'" href="/ingatlanok/?kiemelt=1"><i class="fa fa-bars"></i></a></div>
        <h2>'.__('Kiemelt ingatlanok', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $arg = array(
        'limit' => $this->params['limit'],
        'orderby' => 'rand',
        'highlight' => true
      );

      $properties = new Properties($arg);
      $list = $properties->getList();

      $o .= '<div class="prop-list style-'.$this->template.'"><div class="prop-wrapper">';
      foreach ( $list as $e )
      {
        $o .= $t->load_template( array( 'item' => $e ) );
      }
      $o .= '</div></div>';

      return $o;
    }

    /**
    * Most nézik listázás
    **/
    private function watchnow( $arg = array() )
    {
      global $wpdb;

      $o = '<div class="header">
        <h2>'.__('Most nézik', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      // Visited
      $qry = "SELECT pid FROM `".\PropertyFactory::LOG_VIEW_DB."` as t ORDER BY t.visited DESC LIMIT 0, 100;";

      $idsq = $wpdb->get_results($qry, ARRAY_A );
      $ids = array();
      foreach ($idsq as $sid) {
        if(count($ids) >= $this->params['limit']) break;
        if (!in_array($sid['pid'], $ids)) {
          $ids[] = $sid['pid'];
        }
      }

      $arg = array(
        'ids' => $ids,
        'limit' => $this->params['limit'],
        'orderby' => 'post__in'
      );

      print_r($arg);

      $properties = new Properties($arg);
      $list = $properties->getList();

      $o .= '<div class="prop-list im1 style-'.$this->template.'"><div class="prop-wrapper">';
      foreach ( $list as $e )
      {
        $o .= $t->load_template( array( 'item' => $e ) );
      }
      $o .= '</div></div>';

      return $o;
    }

    /**
    * Új ingatlanok listázás
    **/
    private function news( $arg = array() )
    {
      $o = '<div class="header">
        <div class="morev"><a title="'.__('További új ingatlanok mutatása', 'gh').'" href="/ingatlanok/?ob=date&o=desc&title=news"><i class="fa fa-bars"></i></a></div>
        <h2>'.__('Legújabb ingatlanok', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      $arg = array(
        'limit' => $this->params['limit'],
        'orderby' => 'post_date',
        'order' => 'DESC'
      );

      $properties = new Properties($arg);
      $list = $properties->getList();

      $o .= '<div class="prop-list im4 style-'.$this->template.'"><div class="prop-wrapper">';
      foreach ( $list as $e )
      {
        $o .= $t->load_template( array( 'item' => $e ) );
      }
      $o .= '</div></div>';
      return $o;
    }

    /**
    * Megnézett ingatlanok listázás
    **/
    private function viewed( $arg = array() )
    {
      global $wpdb;

      $o = '<div class="header">
        <h2>'.__('Legutóbb megtekintett ingatlanok', 'gh').'</h2>
      </div>';
      $t = new ShortcodeTemplates(__CLASS__.'/'.$this->template);

      // Visited
      $qry = "SELECT pid FROM `".\PropertyFactory::LOG_VIEW_DB."` as t WHERE t.`ip` = '".$_SERVER['REMOTE_ADDR']."' ORDER BY t.visited DESC LIMIT 0, 100;";

      $idsq = $wpdb->get_results($qry, ARRAY_A );
      $ids = array();
      foreach ($idsq as $sid) {
        if(count($ids) >= $this->params['limit']) break;
        if (!in_array($sid['pid'], $ids)) {
          $ids[] = $sid['pid'];
        }
      }

      $arg = array(
        'ids' => $ids,
        'limit' => $this->params['limit'],
        'orderby' => 'post__in'
      );

      $properties = new Properties($arg);
      $list = $properties->getList();

      $o .= '<div class="prop-list im5 style-'.$this->template.'"><div class="prop-wrapper">';
      foreach ( $list as $e )
      {
        $o .= $t->load_template( array( 'item' => $e ) );
      }
      $o .= '</div></div>';

      return $o;
    }

    private function no_src()
    {
      return sprintf(__('(!) Nincs ilyen source: %s', 'gh'), $this->type);
    }
}

new ListingLista();

?>
