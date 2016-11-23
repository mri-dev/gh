<?php
class control_property_save_archive
{
  private $temppostid = 0;

  public function __construct()
  {
    return $this;
  }

  public function archive( $post )
  {
    global $wpdb;

    extract( $post );
    $state = 200;

    if ( $access == '0' ) {
      $state = 100;
    }

    if ( !$property_id ) {
      throw new Exception(__('Hiányzik az ingatlanhirdetés ID-ja. Nem archiválható az ingatlan.', 'gh'));
    }

    if ( empty($why) ) {
      throw new Exception(__('Kérjük, hogy indokolja meg az archiválási szándékát.', 'gh'));
    }

    $post = get_post( $property_id );

    if ( !$post || ($post && $post->post_type != 'listing') ) {
      throw new Exception(__('Hibás ingatlanhirdetés ID. Archiválás sikertelen.', 'gh'));
    }

    wp_update_post ( array(
      'ID' => $property_id,
      'post_status' => 'draft',
      'post_date' => $post_date
    ) );

    if ( $state == 200 )
    {
      update_post_meta ( $property_id, '_listing_flag_archived', 1, 0);
      update_post_meta ( $property_id, '_listing_archive_text', $why);
      update_post_meta ( $property_id, '_listing_archive_who', get_current_user_id());
    } else {
      $wpdb->insert(
        'listing_archive_reg',
        array(
          'userID' => (int)get_current_user_id(),
          'postID' => (int)$property_id,
          'comment' => $why,
        ),
        array( '%d', '%d', '%s' )
      );
    }

    return array(
      'id' => $property_id,
      'state' => $state
    );
  }
}
?>
