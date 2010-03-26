<?php

/**
 * Custom_Properties
 *
 * Allows you to create new properties by dumping a function into
 * the properties folder.
 * 
 * @author Anthony Short
 */
class Extensions
{
	/**
	 * The list of created properties
	 * @var array
	 */
	public static $extensions = array();
	
	/**
	 * Post Process
	 *
	 * @param $css
	 * @return string
	 */
	public static function post_process()
	{
		self::load_extensions('extensions/properties','find_property');
		self::load_extensions('extensions/functions','find_functions',true);
	}

	/**
	 * Loads each of the property functions and parses them.
	 *
	 * @param $name The location of the extension files
	 * @param $function The CSS function to call to look for instances of it in the CSS
	 * @param $split_params Explode the params before sending them off to the user function
	 * @return $css string
	 */
	public static function load_extensions($location,$function,$split_params = false)
	{
		$files = Scaffold::list_files($location,true);
				
		foreach ($files as $path)
		{
			if(is_dir($path))
				continue;

			/**
			 * If the functions or properties ARE unique, they will
			 * be parsed as such. If not, properties or functions that
			 * are found to be exactly the same will be merged.
			 */
			$unique = false;
			
			/**
			 * The name of the property that can be used in Scaffold CSS
			 */
			$extension_name = pathinfo($path, PATHINFO_FILENAME);
			
			/**
			 * Include the function we'll use as a callback
			 */
			if(!isset(self::$extensions[$extension_name]))
			{
				include_once $path;
			}
			else
			{
				$unique = self::$extensions[$extension_name]['unique'];
			}
			
			/**
			 * The name of the function we'll call for this property
			 */
			$callback = 'Scaffold_'.str_replace('-','_',$extension_name);
			
			/**
			 * Save this extension
			 */
			self::$extensions[$extension_name] = array
			(
				'unique' => $unique,
				'path' => $path,
				'callback' => $callback,
				'function' => $function,
				'split_params' => $split_params
			);

			/**
			 * Find an replace them
			 */
			if($found = Scaffold::$css->$function($extension_name))
			{
				// Make the list unique or not
				$originals = ($unique === false) ? array_unique($found[0]) : $found[0];
	
				// Loop through each found instance
				foreach($originals as $key => $value)
				{
					// Explode the params to send them as function params or as a single param
					if($split_params === true)
					{
						$result = call_user_func_array($callback,explode(',',$found[2][$key]));
					}
					else
					{
						$result = call_user_func($callback,$found[2][$key]);
					}
	
					// Run the user callback										
					if($result === false)
					{
						Scaffold::error('Invalid Extension Syntax - <strong>' . $originals[$key] . '</strong>');
					}
					
					// Just replace the first match if they are unique
					elseif($unique === true)
					{
						$pos = strpos(Scaffold::$css->string,$originals[$key]);

						if($pos !== false)
						{
						    Scaffold::$css->string = substr_replace(Scaffold::$css->string,$result,$pos,strlen($originals[$key]));
						}
					}
					else
					{
						Scaffold::$css->string = str_replace($originals[$key],$result,Scaffold::$css->string);
					}
				}
			}
		}
	}
}