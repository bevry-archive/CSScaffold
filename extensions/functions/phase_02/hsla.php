<?php

/**
 * Works the same way as the hsl() function except it also takes an opacity.
 *
 * @author Olivier Gorzalka
 * @param $h Hue
 * @param $s Saturation
 * @param $l Lightness
 * @param $a Alpha
 * @return string
 */
function Scaffold_hsla($h,$s,$l,$a)
{
	$h = intval($h)/360;
	$s = intval($s)/100;
	$l = intval($l)/100;

	$rgb = array();
	if ($s == 0) {
	  $r = $g = $b = $l * 255;
	} else {
		$var_h = $h * 6;
		$var_i = floor( $var_h );
		$var_1 = $l * ( 1 - $s );
		$var_2 = $l * ( 1 - $s * ( $var_h - $var_i ) );
		$var_3 = $l * ( 1 - $s * (1 - ( $var_h - $var_i ) ) );
		if		 ($var_i == 0) { $var_r = $l	 ; $var_g = $var_3	; $var_b = $var_1 ; }
		else if	 ($var_i == 1) { $var_r = $var_2 ; $var_g = $l		; $var_b = $var_1 ; }
		else if	 ($var_i == 2) { $var_r = $var_1 ; $var_g = $l		; $var_b = $var_3 ; }
		else if	 ($var_i == 3) { $var_r = $var_1 ; $var_g = $var_2	; $var_b = $l	  ; }
		else if	 ($var_i == 4) { $var_r = $var_3 ; $var_g = $var_1	; $var_b = $l	  ; }
		else				   { $var_r = $l	 ; $var_g = $var_1	; $var_b = $var_2 ; }
		$r = ceil($var_r * 255);
		$g = ceil($var_g * 255);
		$b = ceil($var_b * 255);
	}
	return "rgba($r,$g,$b,$a)";
}