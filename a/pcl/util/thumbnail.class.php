<?php
	
/*
* @credits: The PHP  Anthology 2nd Ed.
*/
class ThumbnailException extends Exception
{
	public function __construct($message = null, $code = 0)
	{
		parent::__construct($message, $code);
		error_log( 'Error in '.$this->getFile(). ' Line: '.$this->getLine(). ' Error: '.$this->getMessage() );
	}
}
class ThumbnailFileException extends ThumbnailException {}
class ThumbnailNotSupportedException extends ThumbnailException {}


/*
* Usage Examples:
*
* require_once('thumbnail.class.php');
* 
* 
* 1. Output directly to the browser (screen) : 
* 
* $tn = new Thumbnail(200,200);
* $tn->loadFile('sample_images/terrier.jpg');
* header('Content-Type: '.$tn->getMime());
* $tn->buildThumb();
*
* 
* 2. Save to file and display in HTML page :
* 
* $tn = new Thumbnail(200, 200);
* $image = file_get_contents('sample_images/terrier.jpg');
* $tn->loadData($image, 'image/jpeg');
* $tn->buildThumb('sample_images/nice_doggie.jpg');
* <img src="sample_images/nice_doggie.jpg" width="<?php echo $tn->getThumbWidth();?>" height="<?php echo $tn->getThumbHeight();?>" alt="Resized Image" />
*/

class Thumbnail
{
	private $maxWidth; //int max width of thumbnail
	private $maxHeight; //int max height of thumbnail
	private $scale; //boolean, 
	private $inflate; //boolean, if original is smaller than thumbnail, true inflates it to size of thumbnail, false returns original
	private $types; //supported image (mime-) types
	private $imgLoaders; //array of built in PHP functions for loading images into memory (e.g imagecreatefromjpeg, etc)
	private $imgCreators; //array of built in PHP functions for outputting images to the screen (e.g imagejpeg, etc)
	private $source; //string the original image source
	private $sourceWidth; //int width of the original image
	private $sourceHeight; //int height of the original image
	private $sourceMime; //int the original image mime-type
	private $thumb; //the thumb PHP image resource
	private $thumbWidth; //the final (computed) thumb width
	private $thumbHeight; //the final (computed) thumb height
	
	public function __construct($maxWidth, $maxHeight, $scale = true, $inflate = true)
	{
		$this->maxWidth    = $maxWidth;
		$this->maxHeight   = $maxHeight;
		$this->scale       = $scale;
		$this->inflate     = $inflate;
		$this->types       = array( 'image/jpeg', 'image/png', 'image/gif');
		$this->imgLoaders  = array( 'image/jpeg'=>'imagecreatefromjpeg', 'image/png'=>'imagecreatefrompng','image/gif'=>'imagecreatefromgif' );
		$this->imgCreators = array( 'image/jpeg'=>'imagejpeg', 'image/png'=>'imagepng', 'image/gif'=>'imagegif' );
	}
	
	/*
	* specify a local file to load:
	*/
	public function loadFile ($image)
	{
		if (!$dims = @getimagesize($image))
		{
			throw new ThumbnailFileException('Could not find image: '. $image);
		}
		if ( in_array($dims['mime'],$this->types) )
		{
			$loader = $this->imgLoaders[$dims['mime']];
			$this->source = $loader($image);
			$this->sourceWidth = $dims[0];
			$this->sourceHeight = $dims[1];
			$this->sourceMime = $dims['mime'];
			$this->initThumb();
			return true;
		}
		else
		{
			throw new ThumbnailNotSupportedException('Image MIME type '.$dims['mime'].' not supported');
		}
	}
	
	/*
	* loadDatamethod performs the same function as loadFile,
	* except that we load an image from a string rather than a file. 
	* The string might come from a database, for example 
	* or from an image bit-string got using PHP's file_get_contents() function.
	*/
	public function loadData ($image, $mime)
	{
		if ( in_array($mime,$this->types) ) 
		{
			if($this->source = @imagecreatefromstring($image))
			{
				$this->sourceWidth  = imagesx($this->source);
				$this->sourceHeight = imagesy($this->source);
				$this->sourceMime   = $mime;
				$this->initThumb();
				return true;
			}
			else
			{
				throw new ThumbnailFileException('Could not load image from string');
			}
		}
		else
		{
			throw new ThumbnailNotSupportedException('Image MIME type '.$mime.' not supported');
		}
	}
	
	/*
	* buildThumb method is used to render the finished thumbnail
	* If you pass this method a filename, the thumbnail will be stored as a file that uses the name you’ve specified. 
	* Otherwise, the image is output directly to the browser, in which case, you’ll need to ensure that you’ve sent the correct HTTP mime-type header first
	*/
	public function buildThumb($file = null)
	{
		$creator = $this->imgCreators[$this->sourceMime];
		if (isset($file))
		{
			return $creator($this->thumb, $file);
		} 
		else
		{
			return $creator($this->thumb);
		}
	}
	
	/*
	* The getMime method returns the MIME type, which can be used to generate a Content-Type header for the thumbnail
	*/
	public function getMime()
	{
		return $this->sourceMime;
	}
	
	/*
	* The getThumbWidth and getThumbHeightmethods are used to return the width and height of the thumbnail in pixels; 
	* you could use that information to create an HTML img tag.
	*/
	public function getThumbWidth()
	{
		return $this->thumbWidth;
	}
	
	public function getThumbHeight()
	{
		return $this->thumbHeight;
	}
	
	/*
	* initThumb handles the scaling and inflating functions of our class
	*/
	private function initThumb ()
	{
		if ( $this->scale )
		{
			if ( $this->sourceWidth > $this->sourceHeight )
			{
				$this->thumbWidth = $this->maxWidth;
				$this->thumbHeight = floor( $this->sourceHeight * ($this->maxWidth/$this->sourceWidth) );
			}
			else if ( $this->sourceWidth < $this->sourceHeight )
			{
				$this->thumbHeight = $this->maxHeight;
				$this->thumbWidth = floor( $this->sourceWidth * ($this->maxHeight/$this->sourceHeight) );
			}
			else
			{
				$this->thumbWidth = $this->maxWidth;
				$this->thumbHeight = $this->maxHeight;
			}
		}
		
		else
		{
			$this->thumbWidth = $this->maxWidth;
			$this->thumbHeight = $this->maxHeight;
		}
		
		$this->thumb = imagecreatetruecolor( $this->thumbWidth, $this->thumbHeight );
		
		if ( ($this->sourceWidth <= $this->maxWidth) && ($this->sourceHeight <= $this->maxHeight) && ($this->inflate == false) )
		{
			$this->thumb = $this->source;
		}
		else
		{
			imagecopyresampled( $this->thumb, $this->source, 0, 0, 0, 0, $this->thumbWidth, $this->thumbHeight, $this->sourceWidth, $this->sourceHeight );
		}
	}
}