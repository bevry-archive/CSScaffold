<?php

/**
 * Creates a gradient in the form of a background image.
 *
 * @author Anthony Short
 * @param $param
 * @return string The properties
 */
function Scaffold_background_gradient($params)
{
	if(preg_match_all('/\([^)]*?,[^)]*?\)/',$params, $matches))
	{
		foreach($matches as $key => $original)
		{
			$new = str_replace(',','#COMMA#',$original);
			$params = str_replace($original,$new,$params);
		}
	}

	$params = explode(',',$params);
	
	foreach(array('dir','size','from','to') as $key => $name)
	{
		$$name = trim(str_replace('#COMMA#',',', array_shift($params) ));
	}
	
	$stops = array();
	
	foreach($params as $stop)
	{
		$stop = preg_replace('/color\-stop\(|\)/','',$stop);
		$stop = explode('#COMMA#',$stop);
		$stops[] = array('position' => trim($stop[0]), 'color' => trim($stop[1]));
	}
	
	$from = preg_replace('/from\s*\(|\)/','',$from);
	$to = preg_replace('/to\s*\(|\)/','',$to);
	$size = str_replace('px','',$size);

	return Gradient::create_gradient($dir, $size, $from, $to, $stops);
}