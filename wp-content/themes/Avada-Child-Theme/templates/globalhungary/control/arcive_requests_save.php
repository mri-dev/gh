<?php
  global $me;
  global $wpdb;

  extract($_POST);

  if ( !current_user_can('administrator') && !$me->can('property_archive_mod') ) {
    wp_redirect('/control/home/');
  }

  if ( empty($rid) ) {
    wp_redirect('/control/home/');
  }

  // Allow
  if (isset($allow)) {
    $postid = $wpdb->get_var( $wpdb->prepare("SELECT postID FROM listing_archive_reg WHERE ID = %d;", $rid) );
    update_post_meta($postid, '_listing_flag_archived', 1);
    $wpdb->update(
      'listing_archive_reg',
      array(
        'accept_userid' => $me->ID(),
        'accept_date' => current_time('mysql', 1)
      ),
      array( 'ID' => $rid ),
      array( '%d', '%s' ),
      array( '%d' )
    );
    wp_redirect('/control/arcive_requests/?afterPost=1&back=allow');
  }

  // Disallow
  if (isset($disallow)) {
    $wpdb->delete( 'listing_archive_reg', array( 'ID' => $rid ), array( '%d' ) );
    wp_redirect('/control/arcive_requests/?afterPost=1&back=disallow');
  }

?>
