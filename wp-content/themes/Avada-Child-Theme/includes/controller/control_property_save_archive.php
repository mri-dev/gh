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

      // Alert
      $emails = $this->get_alert_users( $property_id );

      if (!empty($emails)) {
        $alert_emails = array();

        foreach ($emails as $im) {
          if ( DEVMODE == true ) {
            if (!in_array($im['email'], array('info@mri-dev.com'))) {
              continue;
            }
          }
          $alert_emails[] = $im['email'];
        }

        $prop = (new Property())->load( $property_id );

        $mailer = new MailManager( $alert_emails, __('Értesítő','gh').': '.__('Új archiválási kérelem', 'gh').' - '.$prop->Azonosito());
        $mailer->setContent(array(
          'post'  => $prop,
          'who'   => (new UserHelper(array('id' => get_current_user_id()))),
          'comment' => $why,
          'date'    => current_time( 'mysql' )
        ));
        $mailer->setTemplate( 'admin_property_achive_new_'.LANGKEY );
        $mailer->send();
      }
    }

    return array(
      'id' => $property_id,
      'state' => $state
    );
  }

  private function get_alert_users( $pid = false )
  {
    $e = array();
    $arg = array();
    $pterm = array();
    $arg['role__in'] = array( 'administrator', 'region_manager' );

    if ($pid) {
      $terms = wp_get_post_terms( $pid, 'locations' );
      foreach ($terms as $term) {
        if ($term->parent != 0) {
          $parent = get_term($term->parent);
          $pterm[] = (int)$parent->term_id;
        } else if($term->parent == 0){
          $pterm[] = (int)$term->term_id;
        }
      }
    }

    $uquery = new WP_User_Query($arg);
    $users = $uquery->get_results();

    foreach ($users as $u) {
      if ($u->roles[0] == 'region_manager' && !array_key_exists('property_archive_mod', $u->caps)) {
        continue;
      }

      $user_regio = (int)get_user_meta( $u->data->ID, 'gh_user_regio', true);
      if ($u->roles[0] == 'region_manager' && !empty($pterm) && !in_array($user_regio, $pterm)) {
        continue;
      }

      $e[] = array(
        'ID' => $u->data->ID,
        'email' => $u->data->user_email,
        'name' => $u->data->display_name
      );
    }

    return $e;
  }
}
?>
