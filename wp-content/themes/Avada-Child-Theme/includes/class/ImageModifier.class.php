<?php

class ImageModifier
{
  private $watermark_file = 'watermark.png';
  private $watermark = null;
  private $image = null;
  private $image_file = null;
  public $mime;
  public $watermark_options = array(
    'opacity' => 30,
    'quality' => 90,
    'offset'  => -40,
    'ratio_percent' => 60
  );

  public function __construct( $arg = array() )
  {
    $this->watermark_file = get_template_directory() . '-Child-Theme/images/'.$this->watermark_file;

    if (isset($arg['options'])) {
      if(isset($arg['options']['watermark_options'])){
        $this->watermark_options = array_merge($this->watermark_options,$arg['options']['watermark_options']);
      }
    }
  }

  public function loadResourceByID( $attachment_id )
  {
    $this->image_file = get_attached_file( $attachment_id );

    $this->load();
  }

  private function load()
  {
    $this->mime = wp_check_filetype( $this->image_file );
    $this->image = $this->get_image_resource( $this->image_file, $this->mime['type'] );

    if ( $this->image === false ) {
      return false;
    }
  }

  public function watermark()
  {
    $url = $this->watermark_file;

    $watermark_file_info = getimagesize( $url );

    switch ( $watermark_file_info['mime'] ) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$this->watermark = imagecreatefromjpeg( $url );
				break;

			case 'image/gif':
				$this->watermark = imagecreatefromgif( $url );
				break;

			case 'image/png':
				$this->watermark = imagecreatefrompng( $url );
				break;

			default:
				return false;
		}

    // get image dimensions
		$image_width = imagesx( $this->image );
		$image_height = imagesy( $this->image );

    // calculate watermark new dimensions
		list( $w, $h ) = $this->calculate_watermark_dimensions( $image_width, $image_height, imagesx( $this->watermark ), imagesy( $this->watermark ) );

    // calculate image coordinates
		list( $dest_x, $dest_y ) = $this->calculate_image_coordinates( $image_width, $image_height, $w, $h );

		// combine two images together
		$this->imagecopymerge_alpha( $this->image, $this->resize( $this->watermark, $w, $h, $watermark_file_info ), $dest_x, $dest_y, 0, 0, $w, $h, $this->watermark_options['opacity'] );

		imageinterlace( $this->image, true );

    $this->save_image_file( $this->image, $this->mime['type'], $this->image_file, $this->watermark_options['quality'] );

		return $this->image;
  }

  private function save_image_file( $image, $mime_type, $filepath, $quality ) {
		switch ( $mime_type ) {
			case 'image/jpeg':
			case 'image/pjpeg':
				imagejpeg( $image, $filepath, $quality );
				break;

			case 'image/png':
				//imagepng( $image, $filepath, (int) round( 9 - ( 9 * $quality / 100 ), 0 ) );
				break;
		}
	}

  private function imagecopymerge_alpha( $dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct ) {
		// create a cut resource
		$cut = imagecreatetruecolor( $src_w, $src_h );

		// copy relevant section from background to the cut resource
		imagecopy( $cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h );

		// copy relevant section from watermark to the cut resource
		imagecopy( $cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h );

		// insert cut resource to destination image
		imagecopymerge( $dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct );
	}

  private function resize( $image, $width, $height, $info ) {
		$new_image = imagecreatetruecolor( $width, $height );

		// check if this image is PNG, then set if transparent
		if ( $info[2] === 3 ) {
			imagealphablending( $new_image, false );
			imagesavealpha( $new_image, true );
			imagefilledrectangle( $new_image, 0, 0, $width, $height, imagecolorallocatealpha( $new_image, 255, 255, 255, 127 ) );
		}

		imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $width, $height, $info[0], $info[1] );

		return $new_image;
	}

  private function calculate_image_coordinates( $image_width, $image_height, $watermark_width, $watermark_height )
  {
    $position = 'bottom_center';

		switch ( $position ) {
			case 'top_left':
				$dest_x = $dest_y = 0;
				break;

			case 'top_center':
				$dest_x = ( $image_width / 2 ) - ( $watermark_width / 2 );
				$dest_y = 0;
				break;

			case 'top_right':
				$dest_x = $image_width - $watermark_width;
				$dest_y = 0;
				break;

			case 'middle_left':
				$dest_x = 0;
				$dest_y = ( $image_height / 2 ) - ( $watermark_height / 2 );
				break;

			case 'middle_right':
				$dest_x = $image_width - $watermark_width;
				$dest_y = ( $image_height / 2 ) - ( $watermark_height / 2 );
				break;

			case 'bottom_left':
				$dest_x = 0;
				$dest_y = $image_height - $watermark_height;
				break;

			case 'bottom_center':
				$dest_x = ( $image_width / 2 ) - ( $watermark_width / 2 );
				$dest_y = $image_height - $watermark_height;
				break;

			case 'bottom_right':
				$dest_x = $image_width - $watermark_width;
				$dest_y = $image_height - $watermark_height;
				break;

			case 'middle_center':
			default:
				$dest_x = ( $image_width / 2 ) - ( $watermark_width / 2 );
				$dest_y = ( $image_height / 2 ) - ( $watermark_height / 2 );
		}

		$dest_x += 0;
		$dest_y += $this->watermark_options['offset'];

		return array( $dest_x, $dest_y );
	}

  private function calculate_watermark_dimensions( $image_width, $image_height, $watermark_width, $watermark_height )
  {
		$ratio = $image_width * $this->watermark_options['ratio_percent'] / 100 / $watermark_width;

		$width = (int) ( $watermark_width * $ratio );
		$height = (int) ( $watermark_height * $ratio );

		// if watermark scaled height is bigger then image watermark
		if ( $height > $image_height ) {
			$width = (int) ( $image_height * $width / $height );
			$height = $image_height;
		}

		return array( $width, $height );
	}

  private function get_image_resource( $filepath, $mime_type ) {
		switch ( $mime_type ) {
			case 'image/jpeg':
			case 'image/pjpeg':
				$image = imagecreatefromjpeg( $filepath );
				break;

			case 'image/png':
				$image = imagecreatefrompng( $filepath );

				imagefilledrectangle( $image, 0, 0, imagesx( $image ), imagesy( $image ), imagecolorallocatealpha( $image, 255, 255, 255, 127 ) );
				break;

			default:
				$image = false;
		}

		if ( is_resource( $image ) ) {
			imagealphablending( $image, false );
			imagesavealpha( $image, true );
		}

		return $image;
	}

  public function __destruct()
  {
		imagedestroy( $this->image );
		$this->image = null;
  }

}

?>
