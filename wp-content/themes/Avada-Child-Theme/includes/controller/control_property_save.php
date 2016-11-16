<?php
class control_property_save
{

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
    $images = $post['property_images'];

    if ($post['ID'] != 0) {
      $mode = 'save';
    }

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
            $pre = wp_strip_all_tags($pre);
            $now = wp_strip_all_tags($now);
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

    unset($post['metacheckboxes']);
    unset($post['property_id']);
    unset($post['createProperty']);
    unset($post['_nonce']);
    unset($post['post_author_override']);
    unset($post['tax']);
    unset($post['property_images']);

    extract($post);
    $form_errors = null;

    // Validate
    if ( empty($post_title) ) {
      $form_errors .= __('- A ingatlan cím (SEO) értéke nem lehet üres. Kérjük, hogy pótolja. ','gh') . "<br />";
    }

    if ( empty($taxes['status']) ) {
      $form_errors .= __('- Kérjük, hogy válassza ki az ingatlan státuszát.','gh') . "<br />";
    }
    if ( empty($taxes['property-types']) ) {
      $form_errors .= __('- Kérjük, hogy válassza ki az ingatlan kategóriáját.','gh') . "<br />";
    }
    if ( empty($taxes['property-condition']) ) {
      $form_errors .= __('- Kérjük, hogy válassza ki az ingatlan állapotát.','gh') . "<br />";
    }
    if ( empty($post['meta_input']['_listing_idnumber']) ) {
      $form_errors .= __('- Az ingatlan azonosítója hiányzik. Kérjük, hogy pótolja.','gh') . "<br />";
    }
    if ( empty($post['meta_input']['_listing_address']) ) {
      $form_errors .= __('- Az ingatlan pontos címe hiányzik. Kérjük, hogy pótolja.','gh') . "<br />";
    }
    if ( empty($post['meta_input']['_listing_price']) ) {
      $form_errors .= __('- Az ingatlan irányára hiányzik. Kérjük, hogy pótolja.','gh') . "<br />";
    }

    if ( empty($post_excerpt) ) {
      $form_errors .= __('- A ingatlan rövid ismertető leírása nem lehet üres. Kérjük, hogy pótolja. ','gh') . "<br />";
    }
    if ( empty($post_content) ) {
      $form_errors .= __('- A ingatlan részletes leírása nem lehet üres. Kérjük, hogy pótolja. ','gh') . "<br />";
    }
    if ( $mode == 'create' && empty($post['valid-datas']) ) {
      $form_errors .= __('- El kell fogadnia, hogy a megadott adatok valósak.','gh') . "<br />";
    }

    if ( $form_errors ) {
      $form_errors = "<h3>".__('Hiányzó / Hibás értékek', 'gh').":</h3>".$form_errors;
      throw new Exception( $form_errors );
    }

    $post_id = wp_insert_post( $post );

    if ( $post_id != 0 ) {
      wp_set_object_terms( $post_id, array((int)$taxes['locations']), 'locations' );
      wp_set_object_terms( $post_id, array((int)$taxes['status']), 'status' );
      wp_set_object_terms( $post_id, array((int)$taxes['property-types']), 'property-types' );
      wp_set_object_terms( $post_id, array((int)$taxes['property-condition']), 'property-condition' );
      wp_set_object_terms( $post_id, array((int)$taxes['property-heating']), 'property-heating' );
    }

    // Változások logolása
    if ( $post['ID'] != 0 && !empty($changed) ) {
      $this->logChanges( get_current_user_id(), $post['ID'], $changed );
    }

    //return $post;
    return array( 'id' => $post_id, 'mode' => $mode );
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
