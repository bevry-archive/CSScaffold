<?php

/**
 * Creates a cmyka() colour function
 *
 * @author Olivier Gorzalka
 * @param $c Cyan
 * @param $m Magenta
 * @param $y Yellow
 * @param $k Black
 * @param $a Alpha
 * @return string
 */
function Scaffold_cmyka($c,$m,$y,$k,$a)
{
	$c = intval($c) / 100;
	$m = intval($m) / 100;
	$y = intval($y) / 100;
	$k = intval($k) / 100;
	
	$r = intval((1-min(1,$c*(1-$k)+$k))*255+0.5);
	$g = intval((1-min(1, $m * (1 - $k) + $k))*255+0.5);
	$b = intval((1-min(1, $y * (1 - $k) + $k))*255+0.5);
	
	return "rgba($r,$g,$b,$a)";
}