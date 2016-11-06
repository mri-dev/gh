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

    if ( !wp_verify_nonce($post['_nonce'], 'property-create') ) {
      throw new Exception( __('Nem sikerült létrehozni az ingatlanhirdetést. Ön nem az oldalunkról kezdeményezte a folyamatot!', 'gh') );
    }

    if ( !current_user_can('property_create') ) {
      throw new Exception( __('Önnek nincs jogában ingatlanhirdetést létrehozni. Vegy fel a kapcsolatot az oldal üzemeltetőjével / tulajdonosával.', 'gh') );
    }
    $post['ID'] = $post['property_id'];
    $post['post_author'] = $post['post_author_override'];
    $post['post_type'] = 'listing';
    $post['post_status'] = 'draft';
    $post['post_title'] = wp_strip_all_tags($post['post_title']);
    $post['post_excerpt'] = wp_strip_all_tags($post['post_excerpt']);
    $taxes = $post['tax'];
    $images = $post['property_images'];

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
    if ( empty($post['valid-datas']) ) {
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
    }

    //return $post;
    return $post_id;
  }
}
?>
