<?php
class control_dashboard
{
  public function __construct()
  {
    return $this;
  }

  public function HistoryList( $arg = array() )
  {
    $properties = new Properties();
    return $properties->listChangeHistory( $arg );
  }

  public function LiveWatchedProperties( $arg = array() )
  {
    global $wpdb;

    // Visited
    $qry = "SELECT
      t.pid,
      t.visited
    FROM `".\PropertyFactory::LOG_VIEW_DB."` as t
    LEFT JOIN $wpdb->posts as p ON p.ID = t.pid
    WHERE 1=1 ";

    if ( !empty($arg['only_me']) && is_array($arg['only_me']) ) {
      $qry .= " and p.post_author IN (".implode(",",$arg['only_me']).")";
    }

    $qry .= "ORDER BY t.visited DESC LIMIT 0, 100;";

    $idsq = $wpdb->get_results($qry, ARRAY_A );

    $ids = array();
    $ids_time = array();

    foreach ($idsq as $sid) {
      if(count($ids) >= $arg['limit']) break;
      if (!in_array($sid['pid'], $ids)) {
        $ids[] = $sid['pid'];
        $ids_time[$sid['pid']] = $sid['visited'];
      }
    }

    $arg = array(
      'ids' => $ids,
      'limit' => $arg['limit'],
      'orderby' => 'post__in'
    );

    //print_r($arg);

    $properties = new Properties($arg);
    $list = $properties->getList();

    return array(
      'data' => $list,
      'times' => $ids_time
    );
  }

  public function ClickNumbers( $arg = array() )
  {
    global $wpdb;
    $clicks = 0;

    $pq = array();

    $qry = "SELECT t.ID
    FROM ".\PropertyFactory::LOG_VIEW_DB." as t
    WHERE 1=1 ";

    if (isset($arg['day']) && !empty($arg['day'])) {
      $qry .= " and datediff(now(), t.visited) < %d ";
      $pq[] = (int)$arg['day'];
    }

    if (isset($arg['month']) && !empty($arg['month'])) {
      $qry .= " and t.visited LIKE %s";
      $pq[] = $arg['month'].'%';
    }

    if (isset($arg['unique'])) {
      $qry .= " GROUP BY t.ip ";
    }

    //echo $qry;

    $data = $wpdb->get_results( $wpdb->prepare( $qry, $pq ) );

    $clicks = count($data);

    return $clicks;
  }

}
?>
