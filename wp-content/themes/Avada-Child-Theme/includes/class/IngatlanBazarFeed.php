<?php
class IngatlanBazarFeed
{
  public $account_id = 0;

  public function __construct( $arg = array() )
  {
    extract($arg);

    $this->account_id = $account_id;

    return $this;
  }

  public function contactEmail()
  {
    return get_option('admin_email', true);
  }

  public function agents()
  {
    $agents = array();
    $arg = array(
      'role' => -1
    );

    $users = new WP_User_Query($arg);
    $users = $users->get_results();

    //print_r($users);

    if (!empty($users)) {
      foreach ($users as $user) {
        $u = new UserHelper(array('id' => $user->data->ID));

        $agents[] = array(
          'ID' => $u->ID(),
          'name' => $u->Name(),
          'phone' => $u->Phone(),
          'email' => $u->Email(),
        );
      }
    }

    return $agents;
  }

  public function properties()
  {
    $arg = array();
    $arg['limit'] = -1;
    $arg['post_status']  = 'publish';

    $prop = new Properties($arg);
    $list = $prop->getList();

    return $list;
  }

  public function halfrooms( Property $property = null )
  {
    $v = false;

    $v = (int)$property->getMetaValue('_listing_halfrooms');

    return $v;
  }

  public function rooms( Property $property = null )
  {
    $v = false;

    $v = (int)$property->getMetaValue('_listing_room_numbers');

    return $v;
  }

  public function floorspace( Property $property = null )
  {
    $v = false;

    $v = (int)$property->getMetaValue('_listing_property_size');

    return $v;
  }

  public function propertyspace( Property $property = null  )
  {
    $v = false;

    $v = (int)$property->getMetaValue('_listing_lot_size');

    return $v;
  }

  public function conditionConverter( $obj = false )
  {
    $newid = false;

    $connect = array(
      // Újszerű
      1 => array(53,550),
      // Átlagos
      3 => array(),
      // Jó
      4 => array(),
      // Felújított
      5 => array(55),
      // Felújításra szorul
      6 => array(56,133),
      // Új építésű
      7 => array(),
      // Kiváló
      8 => array(122),
    );

    $newid = $obj[0];

    if ($newid) {
      $newid = $this->arraySearch($connect, $newid);
    }

    return $newid;
  }

  public function heatingConverter( $obj = false )
  {
    $newid = false;

    $connect = array(
      // Gáz
      1 => array(69,125,127),
      // Egyedi
      2 => array(),
      // Cirkó
      3 => array(),
      // Központi fűtés
      4 => array(129),
      // Távfűtés
      5 => array(130),
      // Központi, egyedi mérés
      6 => array(),
      // Távfűtés, egyedi mérés
      7 => array(131),
      // Elektromos fűtés
      8 => array(132),
      // Gáz + alternatív
      9 => array(126),
      // Nincs fűtés
      10 => array(),
      // Megújuló energia
      11 => array(128),
    );

    $newid = $obj;

    if ($newid) {
      $newid = $this->arraySearch($connect, $newid);
    }

    return $newid;
  }

  public function typeConverter( $obj = false )
  {
    $newid = false;

    $connect = array(
      // Hétvégi ház
      2 => array(),
      // Családi ház
      10 => array(120),
      // Ikerház
      11 => array(546),
      // Házrész
      12 => array(547),
      // Sorház
      13 => array(119),
      // Villa
      14 => array(548),
      // Kastély
      15 => array(),
      // Tégla lakás
      3 => array(115),
      // Panel lakás
      4 => array(116),
      // Új építésű lakás
      16 => array(545),
      // Alagútzsalu
      17 => array(),
      // Egyéb lakás
      18 => array(47, 48),
      // Nyaraló
      19 => array(52),
      // Garázs
      5 => array(117),
      // Egyéb telek
      6 => array(),
      // Belterületi építési telek
      31 => array(49),
      // Nyaralóövezeti telek
      32 => array(),
      // Ipari vagy kereskedelmi telek
      33 => array(50),
      // Mezőgazdasági telek
      34 => array(),
      // Iroda
      20 => array(),
      // Üzlethelyiség
      21 => array(),
      // Vendéglőhely
      22 => array(),
      // Telephely
      23 => array(),
      // Egyéb kereskedelmi-, vagy ipari Ingatlan
      24 => array(51),
      // Mezőgazdasági ingatlan
      9 => array(118),
      // Műhely
      25 => array(),
      // Raktár
      26 => array(),
      // Zártkerti ingatlan
      27 => array(),
      // Tanya
      28 => array(549),
      // Ipari ingatlan
      29 => array(),
      // Szálláshely
      30 => array(),
    );

    if (count($obj) == 1) {
      $newid = $obj[0]->term_id;
    } else {
      if($obj)
      foreach ($obj as $o) {
        if ($o->parent != 0) {
          $newid = $o->term_id;
          break;
        }
      }
    }

    if ($newid) {
      $newid = $this->arraySearch($connect, $newid);
    }

    return $newid;
  }

  public function periodConverter( $pricetypeid = false )
  {
    $connect = array(
      // Egyszeri
      1 => array(0, 1, 2),
      // Havonta
      2 => array(3)
    );

    $got = $this->arraySearch($connect, $pricetypeid);

    if ($got) {
      return $got;
    }

    return 1;
  }

  public function statusConverter( $ids = array() )
  {
    if (empty($ids)) {
      return 1;
    }

    $connect = array(
      // Eladó
      1 => array(45),
      // Kiadó
      2 => array(44),
    );

    $got = $this->arraySearch($connect, $ids[0]);

    if ($got) {
      return $got;
    }

    return 1;
  }

  private function arraySearch($arr, $id)
  {
    if(!$arr) return false;

    foreach ($arr as $key => $ids) {
      if (in_array($id, $ids)) {
        return $key;
      }
    }

    return false;
  }
}
?>
