<?php
class Helper
{
  static function distanceDate($date = NOW){
			if($date == '0000-00-00 00:00:00'){ return 'sose'; }
			$now 		= strtotime(NOW);
			$date 		= strtotime($date);
			$mode 		= 'past';
			if($date < $now){
				$dif_sec =  $now - $date ;
			}else{
				$mode = 'future';
				$dif_sec =  $date - $now ;
			}

			$ret 		= '';
			///////////////////////////////
			$perc 	= 60;
			$ora 	= $perc * 60;
			$nap 	= $ora * 24;
			$honap 	= $nap * 30;
			$ev 	= $honap * 12;
			///////////////////////////////
				switch($mode){
					case 'past':
						if($dif_sec <= $perc){ // Másodperc
							$ret = $dif_sec.' '. __('másodperce');
						}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
							$ret = floor($dif_sec / $perc).' '.__('perce');
						}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
							$ret = floor($dif_sec / $ora).' '.__('órája');
						}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
							$np = floor($dif_sec / $nap);
							if($np == 1){
								$ret = __('tegnap');
							}else
								$ret = $np.' '.__('napja');
						}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
							$ret = floor($dif_sec / $honap).' '.__('hónapja');
						}else{ // Év
							$ret = floor($dif_sec / $ev).' '.__('éve');
						}
					break;
					case 'future':
						if($dif_sec <= $perc){ // Másodperc
							$ret = $dif_sec.' '. __('másodperc');
						}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
							$ret = floor($dif_sec / $perc).' '.__('perc');
						}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
							$ret = floor($dif_sec / $ora).' '.__('óra');
						}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
							$np = floor($dif_sec / $nap);
							$ret = $np.' '.__('nap');
						}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
							$ret = floor($dif_sec / $honap).' '.__('hónap');
						}else{ // Év
							$ret = floor($dif_sec / $ev).' '.__('év');
						}
					break;
				}


			return $ret;
		}

    static function filesize($bytes, $decimals = 2)
    {
      $sz = 'BKMGTP';
      $factor = floor((strlen($bytes) - 1) / 3);

      $faf = @$sz[$factor];

      switch ($faf) {
        case 'B':
          $fae = 'yte';
        break;

        default:
          $fae = 'B';
        break;
      }

      return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' '.$faf.$fae;
    }
}
?>
