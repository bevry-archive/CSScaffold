<?php

class GradientGD
{
	// $image = new GradientGD(width,height,)
	
	// Constructor. Creates, fills and returns an image
	function __construct($w,$h,$d,$s,$e,$stops=array()) 
	{
		$this->width = $w;
		$this->height = $h;
		$this->direction = $d;
		$this->startcolor = $s;
		$this->endcolor = $e;

		// Attempt to create a blank image in true colors, or a new palette based image if this fails
		if(function_exists('imagecreatetruecolor')) 
		{
			$this->image = imagecreatetruecolor($this->width,$this->height);
		} 
		elseif(function_exists('imagecreate')) 
		{
			$this->image = imagecreate($this->width,$this->height);
		} 
		else 
		{
			return false;
		}
		
		array_unshift($stops, array('position'=>0,'color'=>$s));
		array_push($stops, array('position'=>1,'color'=>$e));
		
		for ($i = 0; $i < count($stops) - 1; $i++)
		{		
			$this->fill(
				$this->image,
				$this->direction,
				
				(string)$stops[$i]['position'],
				(string)$stops[$i + 1]['position'],
				
				$stops[$i]['color'],
				$stops[$i + 1]['color']
			);
		}
		
		return $this->image;
	}
	
	/**
	 * Saves the image to a file
	 *
	 * @param $file
	 * @return void
	 */
	function save($file)
	{
		imagepng($this->image, $file);
	}
	
	// The main function that draws the gradient
	function fill($im,$direction,$start,$end,$from,$to) 
	{
		if($direction == 'horizontal')
		{
			if($start != 0)
				floor($start = $start * $this->width);
				
			$end = floor($end * $this->width);
			
			//$line_numbers = imagesx($im);

			$line_width = imagesy($im);
			list($r1,$g1,$b1) = $this->hex2rgb($from);
			list($r2,$g2,$b2) = $this->hex2rgb($to);
		}

		elseif($direction == 'vertical')
		{
			if($start != 0)
				$start = floor($start * $this->height);

			$end = floor($end * $this->height);
			
			//$line_numbers = imagesy($im);
			$line_width = imagesx($im);
			list($r1,$g1,$b1) = $this->hex2rgb($from);
			list($r2,$g2,$b2) = $this->hex2rgb($to);
		}
		
		//echo $start . '-' . $end . '--' . $from . '-' . $to . "\n";

		$r = $g = $b = '';
		
		for ( $i = 0; $i < ($end - $start); $i++ ) 
		{
			// old values :
			$old_r = $r;
			$old_g = $g;
			$old_b = $b;

			$line = $start + $i;

			// new values :
			//$r = ( $r2 - $r1 != 0 ) ? intval( $r1 + ( $r2 - $r1 ) * ( $i / $line_numbers ) ): $r1;
			$r = ( $r2 - $r1 != 0 ) ? intval( $r1 + ( $r2 - $r1 ) * ( $i / ($end - $start) )): $r1;
			$g = ( $g2 - $g1 != 0 ) ? intval( $g1 + ( $g2 - $g1 ) * ( $i / ($end - $start) )): $g1;
			$b = ( $b2 - $b1 != 0 ) ? intval( $b1 + ( $b2 - $b1 ) * ( $i / ($end - $start) )): $b1;

			// if new values are really new ones, allocate a new color, otherwise reuse previous color.
			// There's a "feature" in imagecolorallocate that makes this function
			// always returns '-1' after 255 colors have been allocated in an image that was created with
			// imagecreate (everything works fine with imagecreatetruecolor)
			if ( "$old_r,$old_g,$old_b" != "$r,$g,$b")
			{
				$fill = imagecolorallocate( $im, $r, $g, $b );
			}

			switch($direction) 
			{
				case 'vertical':
					// ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
					imagefilledrectangle($im, 0, $line, $line_width, $line, $fill);
					break;

				case 'horizontal':
					imagefilledrectangle( $im, $line, 0, $line, $line_width, $fill);
					break;

				default:	
			}		
		}
	}
	
	// #ff00ff -> array(255,0,255) or #f0f -> array(255,0,255)
	function hex2rgb($color) 
	{
		$color = str_replace('#','',$color);
		$s = strlen($color) / 3;
		$rgb[]=hexdec(str_repeat(substr($color,0,$s),2/$s));
		$rgb[]=hexdec(str_repeat(substr($color,$s,$s),2/$s));
		$rgb[]=hexdec(str_repeat(substr($color,2*$s,$s),2/$s));
		return $rgb;
	}
}
