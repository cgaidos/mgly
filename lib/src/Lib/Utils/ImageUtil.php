<?php
namespace Moowgly\Lib\Utils;

class ImageUtil	
{

	public static function compressAndResize($source, $quality = 80, $max_width = 700, $max_height = 700) {
		$info = getimagesize ( $source );
		list ( $orig_width, $orig_height ) = getimagesize ( $source );
		switch ($info ['mime']) {
			case 'image/jpeg' :
				$image = imagecreatefromjpeg ( $source );
				break;
			case 'image/gif' :
				$image = imagecreatefromgif ( $source );
				break;
			case 'image/png' :
				$image = imagecreatefrompng ( $source );
				break;
			default :
				$image = imagecreatefromjpeg ( $source );
				break;
		}
	
		$destination = uniqid ( $source ) . '.jpeg';
	
		// Gestion de la taille
		$width = $orig_width;
		$height = $orig_height;
	
		// taller
		if ($height > $max_height) {
			$width = ($max_height / $height) * $width;
			$height = $max_height;
		}
	
		// wider
		if ($width > $max_width) {
			$height = ($max_width / $width) * $height;
			$width = $max_width;
		}
	
		$image_p = imagecreatetruecolor ( $width, $height );
	
		imagecopyresampled ( $image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height );
	
		imagejpeg ( $image_p, $destination, $quality );
		return $destination;
	}
	
	public static function compressAndResizeFromS3($source, $fileName, $quality = 80, $max_width = 30, $max_height = 30) {
		$image = ImageCreateFromString ( $source );
	
		// Gestion de la taille
		$orig_width = imageSX ( $image );
		$orig_height = imageSY ( $image );
	
		// Gestion de la taille
		$width = $orig_width;
		$height = $orig_height;
	
		// taller
		if ($height > $max_height) {
			$width = ($max_height / $height) * $width;
			$height = $max_height;
		}
	
		// wider
		if ($width > $max_width) {
			$height = ($max_width / $width) * $height;
			$width = $max_width;
		}
	
		$tmpImage = imagecreatetruecolor ( $width, $height );
	
		imagecopyresampled ( $tmpImage, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height );
	
		ob_start ();
		imagejpeg ( $tmpImage, null, $quality );
	
		$final_image = ob_get_contents ();
	
		ob_end_clean ();
		return $final_image;
	}
	
	public static function createBlankImage() {
		$img = imagecreatetruecolor ( 120, 20 );
		$bg = imagecolorallocate ( $img, 255, 255, 255 );
		imagefilledrectangle ( $img, 0, 0, 120, 20, $bg );
		ob_start ();
		imagejpeg ( $img, null, 100 );
		$final_image = ob_get_contents ();
		ob_end_clean ();
		return $final_image;
	}
}