<?php
  global $wpdb;
  global $me;

  $actions = array( 'change_referens' );

  extract($_POST);

  if ( !in_array($action, $actions) ) {
    exit_re();
  }

  if ( count($ids) == 0 ) {
    exit_re();
  }


  switch ($action)
  {
    // Referens csere
    case 'change_referens':
      if ( !$post_author || empty($post_author) ) {
        exit_re();
      }

      foreach ( $ids as $id ) {
        $post = get_post( $id );
        wp_update_post(array(
          'ID' => $id,
          'post_author' => $post_author
        ));

        $wpdb->insert(
          \PropertyFactory::LOG_CHANGE_DB,
          array(
            'changer_user_id' => $me->ID(),
            'item_id' => $id,
            'group_key' => 'property',
            'mod_data_json' => json_encode(array( 'post_author' => array( 'f' => $post->post_author, 't' => $post_author)), \JSON_UNESCAPED_UNICODE)
          ),
          array(
            '%d', '%d', '%s', '%s'
          )
        );
      }
      wp_redirect('/control/properties/?user='.$post_author);
    break;
    default:
      exit_re();
    break;
  }

  function exit_re()
  {
    wp_redirect('/control/home');
  }
?>
