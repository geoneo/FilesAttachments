<?php
App::uses('Component', 'Controller');

/**
 * UtilImage Component
 *
 * @category Component
 * @package  Croogo
 * @author   Ivan Mattoni <iwmattoni@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.php The MIT License
 * @link https://github.com/geoneo
 */
class UtilImageComponent extends Component {

/**
 * error
 *
 * @var boolean
 * @access public
 */
	public $error = false;
/**
 * openImage
 *
 * @param string $filePath
 * @access public
 * @return boolean
 */
	public function openImage($filePath) {
		if (!file_exists($filePath)) {
			return false;
		}

		$this->handle = $this->__getHandle($filePath);
		if (!$this->handle) {
			return false;
		}

		// detect image type
		$this->imageType = exif_imagetype($filePath);
		$this->imageWidth = imagesx($this->handle);
		$this->imageHeight = imagesy($this->handle);
		return true;
	}

/**
 * Resize
 *
 * @access public
 */
	public function resize($options = array()) {
		if ($this->error) {
			return false;
		}
		$options = array_merge(array(
			'quality' => 80,
			'crop' => false,
			'width' => null,
			'height' => null,
		), $options);

		if (empty($options['width']) && empty($options['height'])) {
			$this->__showImage($options['quality']);
			return false;
		}

		$maxWidth = $options['width'];
		$maxHeight = $options['height'];

		if (is_null($maxWidth) || is_null($maxHeight)) {
			$ratio = 1;
		} else {
			$ratio = $maxWidth / $maxHeight;
		}

		if ($options['crop']) {
			$this->__crop($ratio);
		}

		$imageWidthNew = $this->imageWidth;
		$imageHeightNew = $this->imageHeight;

		if (!is_null($maxWidth) && $this->imageWidth >= $maxWidth) {
			$imageWidthNew = $maxWidth;
			$imageHeightNew = floor($this->imageHeight * ($maxWidth/$this->imageWidth));

			if (!is_null($maxHeight) && $imageHeightNew > $maxHeight) {
				$imageWidthNew = floor($imageWidthNew * ($maxHeight/$imageHeightNew));
				$imageHeightNew = $maxHeight;
			}
		} elseif (!is_null($maxHeight) && $this->imageHeight > $maxHeight) {
			$imageHeightNew = $maxHeight;
			$imageWidthNew = floor($this->imageWidth*($maxHeight / $this->imageHeight));

			if (!is_null($maxWidth) && $imageWidthNew > $maxWidth) {
				$imageHeightNew = floor($imageHeightNew * ($maxWidth/$imageWidthNew));
				$imageWidthNew = $maxWidth;
			}
		}

		$resizeHandle = imagecreatetruecolor($imageWidthNew, $imageHeightNew);

		if (image_type_to_extension($this->imageType, false) == 'png') { // Only PNG
			imagealphablending($resizeHandle, false);
			imagesavealpha($resizeHandle, true);
			$transparent = imagecolorallocatealpha($resizeHandle, 255, 255, 255, 127);
			imagefilledrectangle($resizeHandle, 0, 0, $imageWidthNew, $imageHeightNew, $transparent);
		}

		if (imagecopyresampled($resizeHandle, $this->handle, 0, 0, 0, 0, $imageWidthNew, $imageHeightNew, $this->imageWidth, $this->imageHeight)) {
			$this->imageWidth = $imageWidthNew;
			$this->imageHeight = $imageHeightNew;
		}

		imagedestroy($this->handle);
		$this->handle = $resizeHandle;

		$this->__showImage($options['quality']);
	}

/**
 * watermark
 *
 * @param string $watermarkPath
 * @access public
 */
	public function watermark($watermarkPath) {
		if (!file_exists($watermarkPath)) {
			return false;
		}

		$watermarkHandle = $this->getHandle($watermarkPath);
		if (!$watermarkHandle) {
			return false;
		}

		$watermarkWidth = imagesx($watermarkHandle);
		$watermarkWidthNew = $watermarkWidth;
		$watermarkHeight = imagesy($watermarkHandle);
		$watermarkHeightNew = $watermarkHeight;
		$watermarkMaxWidth = floor($this->imageWidth/5);
		$watermarkMaxHeight = floor($this->imageHeight/5);

		// calculate the appropriate watermark dimensions
		if ($this->imageWidth >= $this->imageHeight) {
			$watermarkHeightNew = $watermarkMaxHeight;
			$watermarkWidthNew = floor($watermarkWidth*($watermarkHeightNew/$watermarkHeight));
			if ($watermarkWidthNew > $watermarkMaxWidth) {
				$watermarkHeightNew = floor($watermarkHeightNew*($watermarkMaxWidth/$watermarkWidthNew));
				$watermarkWidthNew = $watermarkMaxWidth;
			}
		} else {
			$watermarkWidthNew = $watermarkMaxWidth;
			$watermarkHeightNew = floor($watermarkHeight*($watermarkWidthNew/$watermarkWidth));
			if ($watermarkHeightNew > $watermarkMaxHeight) {
				$watermarkWidthNew = floor($watermarkWidthNew*($watermarkMaxHeight/$watermarkHeightNew));
				$watermarkHeightNew = $watermarkMaxHeight;
			}
		}
		
		// resize watermark
		$resizedWatermarkHandle = imagecreatetruecolor($watermarkWidthNew, $watermarkHeightNew);
		imagealphablending($resizedWatermarkHandle, false);	// enable alpha channel so that transparent PNGs will stay transparent
		imagesavealpha($resizedWatermarkHandle, true);
		imagecopyresampled($resizedWatermarkHandle, $watermarkHandle, 0, 0, 0, 0, $watermarkWidthNew, $watermarkHeightNew, $watermarkWidth, $watermarkHeight);
		// make background transparent for GIFs
		$black = imagecolorallocate($resizedWatermarkHandle, 0, 0, 0);
		imagecolortransparent($resizedWatermarkHandle, $black);
		
		imagedestroy($watermarkHandle);

		// apply watermark
		$paddingX = ceil($this->imageWidth*0.02);
		$paddingY = ceil($this->imageHeight*0.02);
		$destinationX = $this->imageWidth - $watermarkWidthNew - $paddingX;
		$destinationY = $this->imageHeight - $watermarkHeightNew - $paddingY;
		imagecopy($this->handle, $resizedWatermarkHandle, $destinationX, $destinationY, 0, 0, $watermarkWidthNew, $watermarkHeightNew);
	}

/**
 * Rotate an image
 *
 * @param int $degrees
 * @access public
 */
	public function rotate($degrees) {
		if (empty($degrees)) {
			return false;
		}
		$resizeHandle = imagecreatetruecolor($this->imageWidth, $this->imageHeight);
		imagealphablending($resizeHandle, false);
		imagesavealpha($resizeHandle, true);
		$transparent = imagecolorallocatealpha($resizeHandle, 255, 255, 255, 127);
		$this->handle = imagerotate($this->handle, $degrees, $transparent);
		imagealphablending($this->handle, false);
		imagesavealpha($this->handle, true);
	}






/**
 * Private Functions
 */

/**
 * getHandle
 *
 * @param string $filePath
 * @access private
 * @return mixed
 */
	private function __getHandle($filePath) {
		// create image from stream in string
		if ($imageHandle = imagecreatefromstring(file_get_contents($filePath))) {
			return $imageHandle;
		} else {
			return false;
		}
	}

/**
 * Crop image (mantaining aspect ratio of cropped part).
 *
 * @param int $aspectRatioNew
 * @access private
 * @return crop handle
 */
	private function __crop($aspectRatioNew) {
		if (is_null($aspectRatioNew) || $aspectRatioNew == 0) {
			return false;
		}
		
		$aspectRatio = $this->imageWidth / $this->imageHeight;

		if ($aspectRatio >= 1) {
			if ($aspectRatioNew >= $aspectRatio) {
				$imageWidthNew = $this->imageWidth;
				$imageHeightNew = floor($this->imageWidth / $aspectRatioNew);
			} else {
				$imageHeightNew = $this->imageHeight;
				$imageWidthNew = floor($this->imageHeight * $aspectRatioNew);
			}
		} else {
			if ($aspectRatioNew >= $aspectRatio) {
				$imageWidthNew = $this->imageWidth;
				$imageHeightNew = floor($this->imageWidth / $aspectRatioNew);
			} else {
				$imageHeightNew = $this->imageHeight;
				$imageWidthNew = floor($this->imageHeight * $aspectRatioNew);
			}
		}

		$sourceX = ($this->imageWidth-$imageWidthNew) / 2;
		$sourceY = ($this->imageHeight-$imageHeightNew) / 2;

		$cropHandle = imagecreatetruecolor($imageWidthNew, $imageHeightNew);
		if (image_type_to_extension($this->imageType, false) == 'png') { // Only PNG
			imagealphablending($cropHandle, false);
			imagesavealpha($cropHandle, true);
			$transparent = imagecolorallocatealpha($cropHandle, 255, 255, 255, 127);
			imagefilledrectangle($cropHandle, 0, 0, $imageWidthNew, $imageHeightNew, $transparent);
		}
		if (imagecopyresampled($cropHandle, $this->handle, 0, 0, $sourceX, $sourceY, $imageWidthNew, $imageHeightNew, $imageWidthNew, $imageHeightNew)) {
			$this->imageWidth = $imageWidthNew;
			$this->imageHeight = $imageHeightNew;
		}

		imagedestroy($this->handle);
		$this->handle = $cropHandle;
	}

/**
 * Show image
 *
 * @param int $quality
 * @access private
 */
	private function __showImage($quality = 80) {
		header ("Content-Type: ".image_type_to_mime_type($this->imageType));
		
		switch(image_type_to_extension($this->imageType, false)) {
			case "gif":
				imagegif($this->handle);
				break;
			case "jpg":
			case "jpeg":
				imagejpeg($this->handle, NULL, $quality);
				break;
			case "png":
				imagepng($this->handle, NULL, 9);
				break;
			default:
				die("ERROR: Image type currently unsupported.");
				break;
		}
	}

}
