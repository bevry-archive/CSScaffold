<?php

/**
 * Utils
 *
 * Holds various utility functions used by CSScaffold
 * 
 * @author Anthony Short
 */
class Scaffold_Utils
{
	/**
	 * Takes a relative path, gets the full server path, removes
	 * the www root path, leaving only the url path to the file/folder
	 *
	 * @author Anthony Short
	 * @param $relative_path
	 */
	public static function url_path($path) 
	{
		return self::reduce_double_slashes(str_replace( SCAFFOLD_DOCROOT, DIRECTORY_SEPARATOR, realpath($path) ));
	}

	/**
	 * Preg quote. But better. Does the - character too. Like it should.
	 *
	 * @author Anthony Short
	 * @param $str
	 * @return string
	 */
	public static function preg_quote($str)
	{
		$str = preg_quote($str);
		
		# PHP 5.3 does this, but any version lower doesn't
		if (version_compare(PHP_VERSION, '5.3.0') < 0)
		{
   			$str = str_replace('-','\-',$str);
		}		
		
		$str = preg_replace('#\s+#','\s*',$str);
		$str = str_replace('#','\#',$str);
		$str = str_replace('/','\/',$str);

		return $str;
	}

	/**
	 * Fixes a path (including Windows paths), finds the full path,
	 * and adds a trailing slash. This way we always know what our paths
	 * will look like.
	 */
	public static function fix_path($path)
	{
		$path = str_replace('\\', '/', $path);
		return realpath($path) . '/';
	}
	
	/**
	 * Checks if a file is an image.
	 *
	 * @author Anthony Short
	 * @param $path string
	 */
	public static function is_image($path)
	{
		if (array_search(pathinfo($path, PATHINFO_EXTENSION), array('gif', 'jpg', 'jpeg', 'png')) !== false)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Checks if a file is css.
	 *
	 * @author Anthony Short
	 * @param $path string
	 */	
	public static function is_css($path)
	{
		return (pathinfo($path, PATHINFO_EXTENSION) == 'css') ? true : false;
	}

	/**
	 * Quick regex matching
	 *
	 * @author Anthony Short
	 * @param $regex
	 * @param $subject
	 * @param $i
	 * @return array
	 */
	public static function match($regex, $subject, $i = "")
	{
		if(preg_match_all($regex, $subject, $match))
		{
			return ($i == "") ? $match : $match[$i];
		}
		else
		{
			return array();
		}
	}
	
	/** 
	 * Removes all quotes from a string
	 *
	 * @author Anthony Short
	 * @param $str string
	 */
	public static function remove_all_quotes($str)
	{
		return str_replace(array('"', "'"), '', $str);
	}
	
	/** 
	 * Removes quotes surrounding a string
	 *
	 * @author Anthony Short
	 * @param $str string
	 */
	public static function unquote($str)
	{
		return trim($str, "'\" ");
	}
	
	/** 
	 * Makes sure the string ends with a /
	 *
	 * @author Anthony Short
	 * @param $str string
	 */
	public static function right_slash($str)
	{
	    return rtrim($str, '/') . '/';
	}
	
	/** 
	 * Makes sure the string starts with a /
	 *
	 * @author Anthony Short
	 * @param $str string
	 */
	public static function left_slash($str)
	{
	    return '/' . ltrim($str, '/');
	}
	
	/** 
	 * Makes sure the string doesn't end with a /
	 *
	 * @author Anthony Short
	 * @param $str string
	 */
	public static function trim_slashes($str)
	{
	    return trim($str, '/');
	}
	
	/** 
	 * Replaces double slashes in urls with singles
	 *
	 * @author Anthony Short
	 * @param $str string
	 */
	public static function reduce_double_slashes($str)
	{
		return preg_replace("#//+#", "/", $str);
	}
	
	/**
	 * Joins any number of paths together
	 *
	 * @param $path
	 */
	public static function join_path()
	{
		$num_args = func_num_args();
		$args = func_get_args();
		$path = $args[0];
		
		if( $num_args > 1 )
		{
			for ($i = 1; $i < $num_args; $i++)
			{
				$path .= DIRECTORY_SEPARATOR.$args[$i];
			}
		}
		
		return self::reduce_double_slashes($path);
	}
	
	/**
	 * Returns the size of a string as human readable
	 *
	 * @author Anthony Short
	 * @param $string
	 * @return string Size of string
	 */
	public static function readable_size($string)
	{
		$units = explode(' ','bytes KB MB GB TB PB');
		$size = strlen($string);
		$mod = 1000;
		
		for ($i = 0; $size > $mod; $i++) 
		{
			$size /= $mod;
		}
		
		return round($size, 2) . ' ' . $units[$i];
	}

}