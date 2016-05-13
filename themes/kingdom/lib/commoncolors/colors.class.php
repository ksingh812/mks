<?php
/**
 * Get Most Common Colors
 * http://www.aa-team.com
 * =======================
 *
 * @package		kingdom
 * @author		AA-Team
 */
class wooColorsGetMostCommonColors
{
	const VERSION	= 0.1;

	/**
	 * Baseconfigurationstorage 
	 *
	 * @var array
	 */
	public $config = array();
	
	protected $image = array();
	
	// store the public kingdom class
	private $kingdom; 
	
	public $error;

	/**
	 * @param string $imageUrl
	 */
	public function __construct( $imageUrl )
	{
		global $kingdom;
		$this->kingdom = $kingdom;
		
		// setup the config
		$this->config['valid_image_extensions'] = array('jpg', 'gif', 'png', 'bmp', 'jpeg');
		$this->config['return_colors_nr'] = 6;
		$this->config['reduce_brightness'] = true;
		$this->config['reduce_gradients'] = true;
		$this->config['delta'] = 16;
		$this->config['check_on'] = array(
			'width' => 200,
			'height' => 200
		);
		$this->config['exclude_colors'] = array('ffffff');
		$this->config['named_color'] = array();
		
		// set the new image source
		$this->image['url'] = $imageUrl;
		
		// try to convert the image URL to PATH 
		$this->getImagePath();
		
		if( !$this->is_valid_image() ) { 
			throw new Exception('Invalid IMAGE');
		}
		
		// overwrite from DB
		$this->updateDBSettings();
	}
	
	private function updateDBSettings ()
	{
		$config = $this->kingdom->getAllSettings('array', 'color_config');
		if( trim($config['colors_name']) != ""){
			$color_name_str = $config['colors_name'];
			
			// trim by row
			$_ = explode("\n", $color_name_str);
			$colors = array();
			if(count($_) > 0){
				foreach ($_ as $key => $value){
					$value = str_replace(" ", "", $value);
					$__ = explode("=>", $value);
					if(count($__) > 0){
						$colors[trim($__[0])] = explode(",", trim($__[1]));
					}
				}
			}
			
			$this->config['named_color'] = array_merge( $this->config['named_color'], $colors );
		}
		
		if( trim($config["return_colors_nr"]) != ""){
			$this->config['return_colors_nr'] = $config['return_colors_nr'] + 1; 
		}
		
		if( in_array( (boolean) $config["reduce_brightness"], array(true, false))){
			$this->config['reduce_brightness'] = (boolean) $config["reduce_brightness"]; 
		}
		
		if( in_array( (boolean) $config["reduce_gradients"], array(true, false))){
			$this->config['reduce_gradients'] = (boolean) $config["reduce_gradients"]; 
		}
		
		if( trim($config["delta"]) != ""){
			$this->config['delta'] = (int) $config['delta']; 
		}
		
		if( trim($config["check_on"]) != ""){
			$_ = explode("X", $config['check_on'] ); 
			$this->config['check_on'] = array(
				'width' => $_[0],
				'height' => $_[1]
			);
		}
	}
	
	public function setup ( $custom_option=array() )
	{
		// overwrite default value with custom value
		if(count($custom_option) > 0){
			$this->config = array_merge( $this->config, $custom_option );
		}
	}
	
	private function is_valid_image ()
	{
		// check if image exist as path 
		if( !is_file($this->image['path']) ) return false;
		
		// check image extension 
		$ext = strtolower(end(explode('.', $this->image['url'])));
		if( !in_array($ext, $this->config['valid_image_extensions']) ) return false;
		
		return true;
	}
	
	private function getImagePath ()
	{
		$_file_url = explode('wp-content', $this->image['url']);
		$this->image['path'] = ABSPATH . "wp-content" . end($_file_url);
	}
	
	public function getColors()
	{
		$img = $this->image['path'];
		if (is_readable( $img ))
		{
			if ( $this->config['delta'] > 2 )
			{
				$half_delta = $this->config['delta'] / 2 - 1;
			}
			else
			{
				$half_delta = 0;
			}
			// WE HAVE TO RESIZE THE IMAGE, BECAUSE WE ONLY NEED THE MOST SIGNIFICANT COLORS.
			$size = GetImageSize($img);
			$scale = 1;
			
			if ($size[0]>0)
			$scale = min($this->config['check_on']['width']/$size[0], $this->config['check_on']['height']/$size[1]);
			if ($scale < 1)
			{
				$width = floor($scale*$size[0]);
				$height = floor($scale*$size[1]);
			}
			else
			{
				$width = $size[0];
				$height = $size[1];
			}
			$image_resized = imagecreatetruecolor($width, $height);
			if ($size[2] == 1)
			$image_orig = imagecreatefromgif($img);
			if ($size[2] == 2)
			$image_orig = imagecreatefromjpeg($img);
			if ($size[2] == 3)
			$image_orig = imagecreatefrompng($img);
			// WE NEED NEAREST NEIGHBOR RESIZING, BECAUSE IT DOESN'T ALTER THE COLORS
			imagecopyresampled($image_resized, $image_orig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]);
			$im = $image_resized;
			$imgWidth = imagesx($im);
			$imgHeight = imagesy($im);
			$total_pixel_count = 0;
			for ($y=0; $y < $imgHeight; $y++)
			{
				for ($x=0; $x < $imgWidth; $x++)
				{
					$total_pixel_count++;
					$index = imagecolorat($im,$x,$y);
					$colors = imagecolorsforindex($im,$index);
					// ROUND THE COLORS, TO REDUCE THE NUMBER OF DUPLICATE COLORS
					if ( $this->config['delta'] > 1 )
					{
						$colors['red'] = intval((($colors['red'])+$half_delta)/$this->config['delta'])*$this->config['delta'];
						$colors['green'] = intval((($colors['green'])+$half_delta)/$this->config['delta'])*$this->config['delta'];
						$colors['blue'] = intval((($colors['blue'])+$half_delta)/$this->config['delta'])*$this->config['delta'];
						if ($colors['red'] >= 256)
						{
							$colors['red'] = 255;
						}
						if ($colors['green'] >= 256)
						{
							$colors['green'] = 255;
						}
						if ($colors['blue'] >= 256)
						{
							$colors['blue'] = 255;
						}

					}

					$hex = substr("0".dechex($colors['red']),-2).substr("0".dechex($colors['green']),-2).substr("0".dechex($colors['blue']),-2);

					if ( ! isset( $hexarray[$hex] ) )
					{
						$hexarray[$hex] = 1;
					}
					else
					{
						$hexarray[$hex]++;
					}
				}
			}

			// Reduce gradient colors
			if ( $this->config['reduce_gradients'] )
			{
				// if you want to *eliminate* gradient variations use:
				// ksort( &$hexarray );
				arsort( $hexarray, SORT_NUMERIC );

				$gradients = array();
				foreach ($hexarray as $hex => $num)
				{
					if ( ! isset($gradients[$hex]) )
					{
						$new_hex = $this->_find_adjacent( $hex, $gradients, $this->config['delta'] );
						$gradients[$hex] = $new_hex;
					}
					else
					{
						$new_hex = $gradients[$hex];
					}

					if ($hex != $new_hex)
					{
						$hexarray[$hex] = 0;
						$hexarray[$new_hex] += $num;
					}
				}
			}

			// Reduce brightness variations
			if ( $this->config['reduce_brightness'] )
			{
				// if you want to *eliminate* brightness variations use:
				// ksort( &$hexarray );
				arsort( $hexarray, SORT_NUMERIC );

				$brightness = array();
				foreach ($hexarray as $hex => $num)
				{
					if ( ! isset($brightness[$hex]) )
					{
						$new_hex = $this->_normalize( $hex, $brightness, $this->config['delta'] );
						$brightness[$hex] = $new_hex;
					}
					else
					{
						$new_hex = $brightness[$hex];
					}

					if ($hex != $new_hex)
					{
						$hexarray[$hex] = 0;
						$hexarray[$new_hex] += $num;
					}
				}
			}

			arsort( $hexarray, SORT_NUMERIC );

			// convert counts to percentages
			foreach ($hexarray as $key => $value)
			{
				$hexarray[$key] = (float)$value / $total_pixel_count;
			}
			
			return $this->excludeHexColors( $hexarray );
		}
		else
		{
			$this->error = "Image ".$img." does not exist or is unreadable";
			return false;
		}
	}

	private function excludeHexColors ( $arr=array() )
	{
		if( count($arr) > 0 ) {
			if( count($this->config['exclude_colors']) > 0 ){
				$retArr = array();
				foreach ($arr as $key => $value){
					if( !in_array($key, $this->config['exclude_colors']) ){
						$retArr[$key] = $value;
					}
				}
			}
		}
		return $retArr;
	}
	
	private function _normalize( $hex, $hexarray )
	{
		$lowest = 255;
		$highest = 0;
		$colors['red'] = hexdec( substr( $hex, 0, 2 ) );
		$colors['green']  = hexdec( substr( $hex, 2, 2 ) );
		$colors['blue'] = hexdec( substr( $hex, 4, 2 ) );

		if ($colors['red'] < $lowest)
		{
			$lowest = $colors['red'];
		}
		if ($colors['green'] < $lowest )
		{
			$lowest = $colors['green'];
		}
		if ($colors['blue'] < $lowest )
		{
			$lowest = $colors['blue'];
		}

		if ($colors['red'] > $highest)
		{
			$highest = $colors['red'];
		}
		if ($colors['green'] > $highest )
		{
			$highest = $colors['green'];
		}
		if ($colors['blue'] > $highest )
		{
			$highest = $colors['blue'];
		}

		// Do not normalize white, black, or shades of grey unless low delta
		if ( $lowest == $highest )
		{
			if ($this->config['delta'] <= 32)
			{
				if ( $lowest == 0 || $highest >= (255 - $this->config['delta']) )
				{
					return $hex;
				}
			}
			else
			{
				return $hex;
			}
		}

		for (; $highest < 256; $lowest += $this->config['delta'], $highest += $this->config['delta'])
		{
			$new_hex = substr("0".dechex($colors['red'] - $lowest),-2).substr("0".dechex($colors['green'] - $lowest),-2).substr("0".dechex($colors['blue'] - $lowest),-2);

			if ( isset( $hexarray[$new_hex] ) )
			{
				// same color, different brightness - use it instead
				return $new_hex;
			}
		}

		return $hex;
	}

	private function _find_adjacent( $hex, $gradients )
	{
		$red = hexdec( substr( $hex, 0, 2 ) );
		$green  = hexdec( substr( $hex, 2, 2 ) );
		$blue = hexdec( substr( $hex, 4, 2 ) );

		if ($red > $this->config['delta'])
		{
			$new_hex = substr("0".dechex($red - $this->config['delta']),-2).substr("0".dechex($green),-2).substr("0".dechex($blue),-2);
			if ( isset($gradients[$new_hex]) )
			{
				return $gradients[$new_hex];
			}
		}
		if ($green > $this->config['delta'])
		{
			$new_hex = substr("0".dechex($red),-2).substr("0".dechex($green - $this->config['delta']),-2).substr("0".dechex($blue),-2);
			if ( isset($gradients[$new_hex]) )
			{
				return $gradients[$new_hex];
			}
		}
		if ($blue > $this->config['delta'])
		{
			$new_hex = substr("0".dechex($red),-2).substr("0".dechex($green),-2).substr("0".dechex($blue - $this->config['delta']),-2);
			if ( isset($gradients[$new_hex]) )
			{
				return $gradients[$new_hex];
			}
		}

		if ($red < (255 - $this->config['delta']))
		{
			$new_hex = substr("0".dechex($red + $this->config['delta']),-2).substr("0".dechex($green),-2).substr("0".dechex($blue),-2);
			if ( isset($gradients[$new_hex]) )
			{
				return $gradients[$new_hex];
			}
		}
		if ($green < (255 - $this->config['delta']))
		{
			$new_hex = substr("0".dechex($red),-2).substr("0".dechex($green + $this->config['delta']),-2).substr("0".dechex($blue),-2);
			if ( isset($gradients[$new_hex]) )
			{
				return $gradients[$new_hex];
			}
		}
		if ($blue < (255 - $this->config['delta']))
		{
			$new_hex = substr("0".dechex($red),-2).substr("0".dechex($green),-2).substr("0".dechex($blue + $this->config['delta']),-2);
			if ( isset($gradients[$new_hex]) )
			{
				return $gradients[$new_hex];
			}
		}

		return $hex;
	}
	
	public function convertHexToColorNames ( $value='' )
	{
		if(trim($value) != ""){
			$distances = array();
			$val = $this->html2rgb($value);
			
			foreach ($this->config['named_color'] as $name => $c) {
				$distances[$name] = $this->distancel1($c, $val);
			}
			$mincolor = "";
			$minval = pow(2, 30);
			foreach ($distances as $k => $v) {
				if ($v < $minval) {
					$minval = $v;
					$mincolor = $k;
				}
			}
			
			return $mincolor;
		}
		
		return 'invalid color';
	}
	
	private function html2rgb ($color)
	{
		if ($color[0] == '#')
			$color = substr($color, 1);

		if (strlen($color) == 6)
			list($r, $g, $b) = array(
				$color[0].$color[1],
				$color[2].$color[3],
				$color[4].$color[5]
			);
		elseif (strlen($color) == 3)
			list($r, $g, $b) = array($color[0].$color[0],
				$color[1].$color[1], $color[2].$color[2]);
		else
			return false;

		$r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

		return array($r, $g, $b);
	}
	
	private function distancel2(array $color1, array $color2) {
		return sqrt(pow($color1[0] - $color2[0], 2) +
			pow($color1[1] - $color2[1], 2) +
			pow($color1[2] - $color2[2], 2));
	}
	
	private function distancel1(array $color1, array $color2) {
		return abs($color1[0] - $color2[0]) + 
			abs($color1[1] - $color2[1]) +
			abs($color1[2] - $color2[2]);
	}
}