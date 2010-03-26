<?php
/**
 * This file acts as the front controller for CSScaffold.
 * If you plan on using Scaffold anywhere else, you
 * probably want to do what this file is doing. True story.
 *
 * @package CSScaffold
 */
 
ini_set('display_errors', TRUE);
error_reporting(E_ALL & ~E_STRICT);

/**
 * Set the server variable for document root. A lot of 
 * the utility functions depend on this. Windows servers
 * don't set this, so we'll add it manually if it isn't set.
 */
if(!isset($_SERVER['DOCUMENT_ROOT']))
{
	if (isset($_SERVER['SERVER_SOFTWARE']) && 0 === strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/'))
	{
	    $_SERVER['DOCUMENT_ROOT'] = rtrim(substr(
	        $_SERVER['PATH_TRANSLATED']
	        ,0
	        ,strlen($_SERVER['PATH_TRANSLATED']) - strlen($_SERVER['SCRIPT_NAME'])
	    ), '\\');
	    if ($unsetPathInfo) {
	        unset($_SERVER['PATH_INFO']);
	    }
	}
}

# Include the config file
include 'config.php';

# Load the libraries. Do it manually if you don't like this way.
include 'libraries/Bootstrap.php';

/**
 * Set timezone, just in case it isn't set. PHP 5.2+ 
 * throws a tantrum if you try and use time() without
 * this being set.
 */
if (function_exists('date_default_timezone_set'))
{
	date_default_timezone_set('GMT');
}

# And we're off!
if(isset($_GET['f']))
{
	/**
	 * The files we want to parse. Full absolute URL file paths work best.
	 * eg. request=/themes/stylesheets/master.css,/themes/stylesheets/screen.css
	 */
	$files = explode(',', $_GET['f']);
	
	/**
	 * Various options can be set in the URL. Scaffold
	 * itself doesn't use these, but they are handy hooks
	 * for modules to activate functionality if they are 
	 * present.
	 */
	$options = (isset($_GET['options'])) ? array_flip(explode(',',$_GET['options'])) : array();
	
	/**
	 * Whether to output the CSS, or return the result of Scaffold
	 */
	$display = true;

	/**
	 * Set a base directory
	 */	
	if(isset($_GET['d']))
	{
		foreach($files as $key => $file)
		{
			$files[$key] = Scaffold_Utils::join_path($_GET['d'],$file);
		}
	}

	/**
	 * Parse and join an array of files
	 */
	$result = Scaffold::parse($files,$config,$options,$display);
	
	if($display === false)
		stop($result);
}

/**
 * Prints out the value and exits.
 *
 * @author Anthony Short
 * @param $var
 */
function stop($var = '') 
{
	if( $var == '' ) $var = 'Hammer time! Line ' . __LINE__;
	header('Content-Type: text/plain');
	print_r($var);
	exit;
}