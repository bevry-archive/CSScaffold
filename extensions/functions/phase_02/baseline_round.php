<?php

/**
 * Round a number to the nearest baseline multiple.
 * Requires the layout module (@grid rule)
 *
 * @param $num
 * @return string
 */
function Scaffold_baseline_round($num)
{
	if(isset(Layout::$grid_settings['baseline']))
	{
		$baseline = Layout::$grid_settings['baseline'];
		return round($num/$baseline)*$baseline."px";
	}
		
	return false;
}