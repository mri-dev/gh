<?
class YoutubeHelper {
	static $video_width = '100%';
	static $video_height = 320;

	static function videoData($yid){
		$url = "http://gdata.youtube.com/feeds/api/videos/" . $yid;
		if($this->video = @simplexml_load_file($url)){}else{ echo 'HIBA: Nincs ilyen video!';}
	}

	static function getVideoId($url){
		$pos = strpos($url,'v=');
		return substr($url,$pos+2,11);
	}
	static function emberCode($str)
	{
		$vw = (is_numeric(self::$video_width)) ? self::$video_width : '100%';
		$vh = (is_numeric(self::$video_height)) ? self::$video_height : 320;

		preg_match_all("#((http://|https://)?(www.)?youtube\.com/watch\?[=a-z0-9&_;]+)#i",$str,$m);
		$vURL = $m[0];

		$vid = array();
		foreach($vURL as $v){
			$str = str_replace(
			$v,
			'<div class="youtube-ember-container"><iframe width="'.$vw.'" height="'.$vh.'" src="//www.youtube.com/embed/'.self::getVideoId($v).'?rel=0" frameborder="0" allowfullscreen></iframe></div>',
			$str);
		}

		return $str;
	}

	static function ember($txt){
		$txt = self::emberCode($txt);
		return $txt;
	}

	static function pushVar($var,$val){
		self::$var = $val;
	}
}
?>
