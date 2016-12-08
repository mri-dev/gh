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

  public function PropertyCount( $arg = array() )
  {
    $param = array();
    $param['limit'] = $arg['limit'];
    $param['post_status'] = $arg['post_status'];
    $param['authors'] = $arg['authors'];
    $properties = new Properties($param);
    $properties->getList();

    $q = $properties->getQuery();

    return $q->found_posts;
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

  public function PopularProperties($arg = array())
  {
    global $wpdb;

    $qry = "SELECT t.pid, COUNT( t.ID ) AS ct
    FROM ".\PropertyFactory::LOG_VIEW_DB." as t
    LEFT JOIN $wpdb->posts as p ON p.ID = t.pid
    WHERE 1=1 ";

    if (isset($arg['authors']) && is_array($arg['authors']) && !empty($arg['authors'])) {
      $qry .= " and p.post_author IN (".implode(",",$arg['authors']).") ";
    }

    if (isset($arg['month']) && !empty($arg['month'])) {
      $qry .= " and t.visited LIKE '{$arg['month']}%'";
    }

    if (isset($arg['datetime']) && is_array($arg['datetime'])) {
      $qry .= " and (t.visited >= '{$arg['datetime']['from']}' and t.visited <= '{$arg['datetime']['to']}') ";
    }

    $qry .= " GROUP BY t.pid ";
    $qry .= " ORDER BY ct DESC ";

    //echo $qry;

    $idsq = $wpdb->get_results($qry, ARRAY_A );

    $ids_num = array();
    $ids = array();

    foreach ($idsq as $sid) {
      if(count($ids) >= $arg['limit']) break;
      if (!in_array($sid['pid'], $ids)) {
        $ids_num[$sid['pid']] = array(
          'ct' => $sid['ct']
        );
        $ids[] = $sid['pid'];
      }
    }

    if (empty($ids)) {
      return array(
        'data' => array(),
        'counts' => array()
      );
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
      'counts' => $ids_num
    );

  }

  public function ClickNumberStat( $arg = array() )
  {
    global $wpdb;
    $pq = array();

    $qry = "SELECT SUBSTR( t.visited, 1, 10 ) as day, count(t.ID) as ct
    FROM ".\PropertyFactory::LOG_VIEW_DB." as t
    LEFT JOIN $wpdb->posts as p ON p.ID = t.pid
    WHERE 1=1 ";

    if (isset($arg['authors']) && is_array($arg['authors']) && !empty($arg['authors'])) {
      $qry .= " and p.post_author IN (".implode(",",$arg['authors']).") ";
    }

    if (isset($arg['day']) && !empty($arg['day'])) {
      $qry .= " and datediff(now(), t.visited) < %d ";
      $pq[] = (int)$arg['day'];
    }

    if (isset($arg['datetime']) && is_array($arg['datetime'])) {
      $qry .= " and (t.visited >= %s and t.visited <= %s) ";
      $pq[] = $arg['datetime']['from'];
      $pq[] = $arg['datetime']['to'];
    }

    if (isset($arg['month']) && !empty($arg['month'])) {
      $qry .= " and t.visited LIKE %s";
      $pq[] = $arg['month'].'%';
    }

    $qry .= " GROUP BY SUBSTR( t.visited, 1, 10 ) ";
    $qry .= " ORDER BY SUBSTR( t.visited, 1, 10 ) ASC ";

    //echo $qry;

    $data = $wpdb->get_results( $wpdb->prepare( $qry, $pq ) );

    return $data;

  }

  public function ClickNumbers( $arg = array() )
  {
    global $wpdb;
    $clicks = 0;

    $pq = array();

    $qry = "SELECT t.ID
    FROM ".\PropertyFactory::LOG_VIEW_DB." as t
    LEFT JOIN $wpdb->posts as p ON p.ID = t.pid
    WHERE 1=1 ";

    if (isset($arg['authors']) && is_array($arg['authors']) && !empty($arg['authors'])) {
      $qry .= " and p.post_author IN (".implode(",",$arg['authors']).") ";
    }

    if (isset($arg['datetime']) && is_array($arg['datetime'])) {
      $qry .= " and (t.visited >= '{$arg['datetime']['from']}' and t.visited <= '{$arg['datetime']['to']}') ";
    }

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
