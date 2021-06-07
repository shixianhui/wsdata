<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Video conversion and the interception of images
 * 
 * @author zeping xiang 2010-09-26
 *
 */
class Movietransformclass {
	public function __construct() {
	}
	
    public function createMovie($ffmpeg, $sourcePath, $targetPath, $width, $heith) {
		exec("{$ffmpeg} -y -i {$sourcePath} -ab 56 -ar 22050 -b 500 -r 29.97 -vcodec libx264 -s {$width}x{$heith} {$targetPath}", $output, $ret);
       
		return $ret?false:true;
	}
	
	public function createThumbnail($ffmpeg, $sourcePath, $targetPath, $width, $heith, $second = '1') {
		exec("{$ffmpeg} -i {$sourcePath} -y -f image2 -ss {$second} -s {$width}x{$heith} {$targetPath}", $output, $ret);
		
		return $ret?false:true;
	}
}
// END Movietransformclass class

/* End of file Movietransformclass.php */
/* Location: ./system/libraries/Movietransformclass.php */