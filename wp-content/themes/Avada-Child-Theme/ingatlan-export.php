<?php
  global $me;

  /** Include PHPExcel */
  require_once "includes/class/PHPExcel/PHPExcel.php";

  $pfa = new PropertyFactory();

  $blogname = get_option('blogname');
  $location = false;

  $all_status = array('publish', 'pending', 'draft', 'future');
  $status = $all_status;

  $filters = array();

  $arg = array();
  $arg['limit'] = -1;

  if(isset($_GET['st']) && !empty($_GET['st']))
  {
    $status = array($_GET['st']);
    $filters['Státusz'] = $pfa->StatusText($_GET['st']);
  }

  if (isset($_GET['c']) && !empty($_GET['c'])) {
    //$arg['property-types'] = explode(",", $_GET['c']);
    //$filtered = true;
  }

  if (current_user_can('reference_manager')) {
    $author = $me->ID();
  } else {
    if (isset($_GET['user']) && !empty($_GET['user'])) {
      if ( true ) {
        if ( current_user_can('region_manager') || current_user_can('administrator') ) {
          $author = $_GET['user'];
          $filtered = true;
          $selected_user = new UserHelper(array( 'id' => $_GET['user']) );

          if ($me->can('user_property_connector') || current_user_can('administrator')) {
            $show_selector = true;
          }
        }
      }
    }
  }

  if (current_user_can('region_manager')) {
    $location = $me->RegionID();
  }

  if ($author) {
    $arg['author'] = $author;
    if ($selected_user) {
      $filters['Referens'] = $selected_user->Name();
    }
  }
  $arg['location'] = $location;
  $arg['post_status'] = $status;
  $arg['hide_archived'] = (($archived) ? false : true);
  $arg['only_archived'] = (($archived) ? true : false);

    //print_r($arg); exit;

  $properties = new Properties( $arg );
  $property_list = $properties->getList();


  //print_r($filters); exit;

  // Create new PHPExcel object
  $excel = new PHPExcel();

  // Set document properties
  $excel->getProperties()->setCreator($blogname)
  							 ->setLastModifiedBy($blogname)
  							 ->setTitle($blogname." Ingatlan lista")
  							 ->setSubject("Lista exportálása");

  // Header
  $excel->setActiveSheetIndex(0)
        ->setCellValue('A1', 'Ingatlan azonosító')
        ->setCellValue('B1', 'Ingatlan elnevezése')
        ->setCellValue('C1', 'Régió')
        ->setCellValue('D1', 'Cím')
        ->setCellValue('E1', 'Ár')
        ->setCellValue('F1', 'Akciós ár')
        ->setCellValue('G1', 'Státusz')
        ->setCellValue('H1', 'Létrehozva')
        ->setCellValue('I1', 'Referens')
        ->setCellValue('J1', 'Referens email')
        ->setCellValue('K1', 'Referens telefon')
        ->setCellValue('L1', 'GPS');

  if (!empty($filters))
  {
    $excel->setActiveSheetIndex(0)
          ->setCellValue('M1', 'Alkalmazott szűrőfeltételek');
    $excel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
    $excel->getActiveSheet()->getStyle('M1')->getFont()
      ->setBold(true)
      ->setColor( new PHPExcel_Style_Color( 'FFFFFFFF' ) );
    $excel->getActiveSheet()->getStyle('M1')->getFill()
      ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
      ->setStartColor( new PHPExcel_Style_Color( 'FFFF0000' ) )
      ->setEndColor( new PHPExcel_Style_Color( 'FFFF0000' ) );

    $fi = 1;
    foreach ($filters as $key => $value)
    {
      $fi++;
      $excel->setActiveSheetIndex(0)
            ->setCellValue('M'.$fi, $key.': '.$value);
    }
  }

  // Lista
  $row = 1;
  foreach ( $property_list as $p )
  {
    $coords = '0,0';

    $gps = $p->GPS();

    if($gps) {
      $coords = $gps['lat'].','.$gps['lng'];
    }

    $row++;
    $excel->setActiveSheetIndex(0)
          ->setCellValue('A'.$row, $p->Azonosito() )
          ->setCellValue('B'.$row, $p->Title() )
          ->setCellValue('C'.$row, $p->RegionName(false) )
          ->setCellValue('D'.$row, $p->Address() )
          ->setCellValue('E'.$row, $p->OriginalPrice() )
          ->setCellValue('F'.$row, $p->OffPrice() )
          ->setCellValue('G'.$row, $p->Status() )
          ->setCellValue('H'.$row, $p->CreateAt() )
          ->setCellValue('I'.$row, $p->AuthorName() )
          ->setCellValue('J'.$row, $p->AuthorEmail() )
          ->setCellValue('K'.$row, $p->AuthorPhone() )
          ->setCellValue('L'.$row, $coords );

    $status_color = strtoupper(str_replace('#','',$properties->property_status_colors[$p->StatusKey()]));

    $excel->getActiveSheet()->getStyle('G'.$row)->getFill()
      ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
      ->setStartColor( new PHPExcel_Style_Color( 'FF'.$status_color ) )
      ->setEndColor( new PHPExcel_Style_Color( 'FF'.$status_color ) );

    $excel->getActiveSheet()->getStyle('G'.$row)->getAlignment('D'.$row)
      ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
  }

  // Rename worksheet
  $excel->getActiveSheet()->setTitle('Ingatlan lista');
  $excel->getActiveSheet()->getStyle('A1:L1')->getFont()
    ->setBold(true);
  $excel->getActiveSheet()->getStyle('A1:L1')->getFill()
    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
    ->setStartColor( new PHPExcel_Style_Color( 'FFEEEEEE' ) )
    ->setEndColor( new PHPExcel_Style_Color( 'FFEEEEEE' ) );
  $excel->getActiveSheet()->getStyle('I')->getFont()
    ->setBold(true);

  $excel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
  $excel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);


  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
  $excel->setActiveSheetIndex(0);

  //exit;

  // Redirect output to a client’s web browser (Excel5)
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="'.$blogname.' - Ingatlan lista export ('.date('Y-m-d H.i').').xls"');
  header('Cache-Control: max-age=0');

  // If you're serving to IE 9, then the following may be needed
  header('Cache-Control: max-age=1');

  // If you're serving to IE over SSL, then the following may be needed
  header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
  header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
  header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
  header ('Pragma: public'); // HTTP/1.0

  $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
  $objWriter->save('php://output');

  exit;

?>
