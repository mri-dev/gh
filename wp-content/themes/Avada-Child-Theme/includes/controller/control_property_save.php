<?php
class control_property_save
{
  private $temppostid = 0;
  private $do_watermark = true;

  public function __construct()
  {
    return $this;
  }

  public function createsave( $post )
  {
    $post_id = 0;
    $changed = array();
    $mode    = 'create';

    if ( !wp_verify_nonce($post['_nonce'], 'property-create') ) {
      throw new Exception( __('Nem sikerült létrehozni az ingatlanhirdetést. Ön nem az oldalunkról kezdeményezte a folyamatot!', 'gh') );
    }

    if ( !current_user_can('property_create') ) {
      throw new Exception( __('Önnek nincs jogában ingatlanhirdetést létrehozni. Vegy fel a kapcsolatot az oldal üzemeltetőjével / tulajdonosával.', 'gh') );
    }

    $post['ID'] = $post['property_id'];
    $post['post_type'] = 'listing';
    $post['post_status'] = ($post['ID'] == 0) ? 'draft' : $post['post_status'];
    $post['post_title'] = wp_strip_all_tags($post['post_title']);
    $post['post_excerpt'] = wp_strip_all_tags($post['post_excerpt']);
    $taxes = $post['tax'];
    $extra = $post['extra'];
    $images = $post['property_images'];
    $pre = $post['pre'];
    $this->do_watermark = (isset($post['image_watermark'])) ? true : false;

    if ($post['ID'] != 0) {
      $mode = 'save';
      $this->temppostid = $post['ID'];
    }

    // Check formats
    $post['meta_input']['_listing_price'] = (int)str_replace(".","",$post['meta_input']['_listing_price']);
    $post['meta_input']['_listing_offprice'] = (int)str_replace(".","",$post['meta_input']['_listing_offprice']);

    // Meta checkboxes
    foreach ($post['metacheckboxes'] as $mkey => $mv) {
      if (isset($post['meta_input'][$mkey])) {
        $post['meta_input'][$mkey] = 1;
      } else {
        $post['meta_input'][$mkey] = 0;
      }
    }

    if (isset($post['pre'])) {
      foreach ($post['pre'] as $pkey => $pvals) {

        if (is_array($pvals)) {
          foreach ($pvals as $key => $value) {
            if ($post[$pkey][$key] != $value) {
              $changed[$pkey][$key] = array(
                'f'  => $value,
                't'  => $post[$pkey][$key]
              );
            }
          }
        } else {
          $pre = $pvals;
          $now = $post[$pkey];

          if ($pkey == 'post_content') {
            $pre = wp_strip_all_tags($pre, true);
            $now = wp_strip_all_tags($now, true);
          }

          if ($now != $pre) {
            $changed[$pkey] = array(
              'f'  => $pvals,
              't'  => $post[$pkey]
            );
          }
        }

      }
    }

    unset($post['pre']);
    unset($post['extra']);
    unset($post['metacheckboxes']);
    unset($post['property_id']);
    unset($post['createProperty']);
    unset($post['_nonce']);
    unset($post['post_author_override']);
    unset($post['tax']);
    unset($post['property_images']);
    unset($post['image_watermark']);

    extract($post);
    $form_errors = null;

    // Validate
    if ( empty($post_title) ) {
      $form_errors .= __('- A ingatlan cím (SEO) értéke nem lehet üres. Kérjük, hogy pótolja. ','gh') . "<br />";
    }

    if ( empty($taxes['status']) ) {
      //$form_errors .= __('- Kérjük, hogy válassza ki az ingatlan státuszát.','gh') . "<br />";
    }
    if ( empty($taxes['property-types']) ) {
      //$form_errors .= __('- Kérjük, hogy válassza ki az ingatlan kategóriáját.','gh') . "<br />";
    }
    if ( empty($taxes['property-condition']) ) {
      //$form_errors .= __('- Kérjük, hogy válassza ki az ingatlan állapotát.','gh') . "<br />";
    }
    if ( empty($post['meta_input']['_listing_address']) ) {
      //$form_errors .= __('- Az ingatlan pontos címe hiányzik. Kérjük, hogy pótolja.','gh') . "<br />";
    }
    if ( empty($post['meta_input']['_listing_price']) ) {
      //$form_errors .= __('- Az ingatlan irányára hiányzik. Kérjük, hogy pótolja.','gh') . "<br />";
    }
    if ( is_null($post['meta_input']['_listing_flag_pricetype']) ) {
      //$form_errors .= __('- Az ingatlan ár jellegét kötelező kiválasztani. Kérjük, hogy pótolja.','gh') . "<br />";
    }

    if ( empty($post_excerpt) ) {
      //$form_errors .= __('- A ingatlan rövid ismertető leírása nem lehet üres. Kérjük, hogy pótolja. ','gh') . "<br />";
    }
    if ( empty($post_content) ) {
      //$form_errors .= __('- A ingatlan részletes leírása nem lehet üres. Kérjük, hogy pótolja. ','gh') . "<br />";
    }

    if ( $form_errors ) {
      $form_errors = "<h3>".__('Hiányzó / Hibás értékek', 'gh').":</h3>".$form_errors;
      throw new Exception( $form_errors );
    }

    $post_id = wp_insert_post( $post );

    if ($mode == 'create') {
      $this->temppostid = $post_id;

      // Save azon
      update_post_meta( $post_id, '_listing_idnumber', 'GH'.$post_id );
    }

    if ( $post_id != 0 ) {
      wp_set_object_terms( $post_id, array((int)$taxes['locations']), 'locations' );
      wp_set_object_terms( $post_id, array((int)$taxes['status']), 'status' );
      wp_set_object_terms( $post_id, array((int)$taxes['property-heating']), 'property-heating' );

      $exp_tax_type = explode(",",$taxes['property-types']);
      $exp_tax_type = array_map( 'intval', $exp_tax_type );
      $exp_tax_type = array_unique( $exp_tax_type );
      wp_set_object_terms( $post_id, $exp_tax_type, 'property-types' );

      $exp_tax_cond = explode(",",$taxes['property-condition']);
      $exp_tax_cond = array_map( 'intval', $exp_tax_cond );
      $exp_tax_cond = array_unique( $exp_tax_cond );
      wp_set_object_terms( $post_id, $exp_tax_cond, 'property-condition' );
    }

    /**
    * Images
    **/
    $uploads_dir = wp_upload_dir();
    $property_image_dir = $uploads_dir['basedir'] . '/listing/' . $post_id;

    if ( $_FILES )
    {
      $first_imaged = false;
      add_filter( 'upload_dir', array( $this, 'upload_dir_filter') );
      add_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );

      $files = $_FILES["property_images"];

      foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
          $file = array(
              'name' => $files['name'][$key],
              'type' => $files['type'][$key],
              'tmp_name' => $files['tmp_name'][$key],
              'error' => $files['error'][$key],
              'size' => $files['size'][$key]
          );

          $_FILES = array ("property_images" => $file);

          foreach ($_FILES as $file => $array) {
            $newupload = $this->uploads_handler( $file, $this->temppostid );

            $changed['image_uploads'][] = $newupload;

            // Első kép beállítása
            if ( !$first_imaged && empty($extra['feature_img_id']) ) {
              set_post_thumbnail( $this->temppostid, $newupload);
              $first_imaged = true;
            }

          }
        }
      }
      remove_filter( 'upload_dir', array( $this, 'upload_dir_filter') );
      remove_filter( 'intermediate_image_sizes', '__return_empty_array', 99 );
    }

    if ($mode == 'save') {
      // Profilkép cseréje
      if ( $extra['feature_img_id'] != $pre['extra']['feature_img_id']) {
        set_post_thumbnail( $this->temppostid, $extra['feature_img_id']);
      }
      // Kép(ek) törlése
      if (!empty($extra['deleting_imgs'])) {
        foreach ($extra['deleting_imgs']as $did => $v) {
          wp_delete_attachment( $did );
          $changed['extra']['deleting_imgs'][] = $did;
        }
      }
    }

    // Változások logolása
    if ( $post['ID'] != 0 && !empty($changed) ) {
      $this->logChanges( get_current_user_id(), $post['ID'], $changed );
    }
    //return $post;
    return array( 'id' => $post_id, 'mode' => $mode );
  }

  public function uploads_handler ( $file_handler, $post_id, $set_thu = false )
  {
    // check to make sure its a successful upload
    if ($_FILES[$file_handler]['error'] !== UPLOAD_ERR_OK) __return_false();

    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');

    $attach_id = media_handle_upload( $file_handler, $this->temppostid );

    // Resize
    if ( true && $attach_id )
    {
      $this->resize_attachment($attach_id, 1200, 1200);
    }

    // Watermark
    if ( $this->do_watermark && $attach_id )
    {
      $image = new ImageModifier();
      $image->loadResourceByID($attach_id);
      $image->watermark();
    }

    return $attach_id;
  }

  public function upload_dir_filter( $dir )
  {
    return array(
      'path'   => $dir['basedir'] . '/listing/'.$this->temppostid,
      'url'    => $dir['baseurl'] . '/listing/'.$this->temppostid,
      'subdir' => '/listing/'.$this->temppostid,
     ) + $dir;
  }

  private function resize_attachment( $attachment_id, $width = 1024, $height = 1024 )
  {
    // Get file path
    $file = get_attached_file($attachment_id);

    // Get editor, resize and overwrite file
    $image_editor = wp_get_image_editor($file);
    $image_editor->resize($width, $height);
    $image_editor->set_quality(80);
    $saved = $image_editor->save($file);

    // We need to change the metadata of the attachment to reflect the new size

    // Get attachment meta
    $image_meta = get_post_meta($attachment_id, '_wp_attachment_metadata', true);

    // We need to change width and height in metadata
    $image_meta['height'] = $saved['height'];
    $image_meta['width']  = $saved['width'];

    // Update metadata
    return update_post_meta($attachment_id, '_wp_attachment_metadata', $image_meta);
  }

  private function logChanges( $uid, $pid, $changes_arr = array())
  {
    global $wpdb;

    $wpdb->insert(
      \PropertyFactory::LOG_CHANGE_DB,
      array(
        'changer_user_id' => $uid,
        'item_id' => $pid,
        'group_key' => 'property',
        'mod_data_json' => json_encode($changes_arr, \JSON_UNESCAPED_UNICODE)
      ),
      array(
        '%d', '%d', '%s', '%s'
      )
    );
  }
}
?>
