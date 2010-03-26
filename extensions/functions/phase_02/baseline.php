<?php

/**
 * x number of baselines. Quicker way of doing calc($baseline * n)
 *
 * @param $num
 * @return string
 */
function Scaffold_baseline($num)
{
	if( isset(Layout::$grid_settings['baseline']) )
	{
		return (Layout::$grid_settings['baseline'] * $num) . 'px ';
	}

	return false;
}