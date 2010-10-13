<?php

/**
 * Firelog
 *
 * Sends logged messages to FirePHP.
 * 
 * @author Anthony Short
 */
class Firelog
{
	/**
	 * Log Levels
	 *
	 * @var array
	 */
	private static $log_levels = array
	(
		'Errors',
		'Warnings',
		'Information',
		'Debug',
	);
	
	private static $fb_methods = array
	(
		'error',
		'warn',
		'info',
		'log',
	);
	
	/**
	 * Load FirePHP at the start
	 *
	 * @param $css
	 * @return $css string
	 */
	public static function initialize()
	{
		# Load FirePHP
		if(!class_exists('FB'))
			require dirname(__FILE__) . '/libraries/FirePHPCore/fb.php';

		# Enable it
		FB::setEnabled(true);
	}
	
	/**
	 * Outputs the benchamrks from Scaffold to Firebug
	 *
	 * @return void
	 */
	public static function benchmark()
	{
		$system 	= Scaffold_Benchmark::get('system');
		$flags 		= Scaffold_Benchmark::get('system.flags');
		$check		= Scaffold_Benchmark::get('system.check_files');
		
		$table = array();
		$table[] = array('Event', 'Time');
		
		# The benchmark values
		$table[] = array('Total Time',$system['time']);
		$table[] = array('Set Flags', $flags['time']);
		$table[] = array('Parse Files', $check['time']);
		
		FB::table('Benchmark', $table);
	}

	/**
	 * During the output phase, gather all the logs and send them to FireBug
	 *
	 * @author Anthony Short
	 * @param $css
	 * @return void
	 */
	public static function display()
	{
		# Log the benchmarks
		self::benchmark();
		
		# Log about the completed file
		if(Scaffold::$config['Firelog']['file_information'] === true)
		{
			self::_file(Scaffold::$css->file,'File Information');
		}

		# Constants
		if(Scaffold::$config['Firelog']['constants'] === true && class_exists('Constants') && Constants::$constants)
		{
			$table = array();
			$table[] = array('Constants Name', 'Value');
	
			foreach(Constants::$constants as $key => $value)
			{
				$table[] = array($key,$value);
			}

			FB::table('Constants', $table);
		}
		
		# Mixins
		if(Scaffold::$config['Firelog']['mixins'] === true && class_exists('Mixins') && Mixins::$mixins)
		{
			$table = array();
			$table[] = array('Mixin Name', 'Parameters', 'Properties');
			
			foreach(Mixins::$mixins as $key => $value)
			{
				$table[] = array($key,implode(',',$value['params']),$value['properties']);
			}
	
			FB::table('Mixins', $table);
		}

		# Included files
		if(Scaffold::$config['Firelog']['included_files'] === true && class_exists('Import') && Import::$loaded)
		{
			self::_group('Included Files', Import::$loaded, 3);
		}
		
		# Flags
		if(Scaffold::$config['Firelog']['flags'] === true)
		{
			self::_group('Flags', (Scaffold::flags()) ? Scaffold::flags() : 'No flags are set');
		}
		
		# Include Paths
		if(Scaffold::$config['Firelog']['include_paths'] === true)
		{
			self::_group('Include Paths', Scaffold::include_paths());
		}

		# Error Log
		if(Scaffold::$config['Firelog']['error_log'] === true)
		{
			foreach(Scaffold_Log::$log as $type => $value)
			{
				FB::group(self::$log_levels[$type]);
	
				foreach($value as $date => $message)
				{
					self::_log($message,$type);
				}

				FB::groupEnd();
			}
		}
		
		# Custom Functions		
		/*
		if(Scaffold::$config['Firelog']['custom_functions'] === true && class_exists('Extensions') && Extensions::$functions)
		{
			self::_group('Custom Functions', Extensions::$functions);
		}
		
		# Custom Properties
		if(Scaffold::$config['Firelog']['custom_properties'] === true && class_exists('Extensions') && Extensions::$properties)
		{
			self::_group('Custom Properties', Extensions::$properties);
		}
		*/
		
		# Gradients
		if(Scaffold::$config['Firelog']['gradients'] === true && class_exists('Gradient') && Gradient::$gradients)
		{
			$table = array();
			$table[] = array('Direction', 'Size', 'From', 'To', 'Location');
	
			foreach(Gradient::$gradients as $key => $value)
			{
				$table[] = array($value[0],$value[1],$value[2],$value[3], str_replace($_SERVER['DOCUMENT_ROOT'], '', $value[4]));
			}

			FB::table('Gradients', $table);
		}
		
		/*
		# Layout module
		if(Scaffold::$config['Firelog']['layout'] === true && isset(Layout::$columns))
		{
			FB::group('Layout');

			$table = array();
			$table[] = array
			(
				'Column Count', 
				'Column Width', 
				'Total Gutter', 
				'Left Gutter', 
				'Right Gutter', 
				'Total Width', 
				'Baseline', 
				'Unit'
			);

			$table[] = array
			(
				Layout::$column_count,
				Layout::$column_width,
				Layout::$gutter_width,
				Layout::$left_gutter_width,
				Layout::$right_gutter_width,
				Layout::$grid_width,
				Layout::$baseline,
				Layout::$unit
			);
			
			FB::table('Grid Structure', $table);
			
			# Columns
			$table = array();
			$table[] = array
			(
				'Column #',
				'Width'
			);
			
			foreach(Layout::$columns as $key => $width)
			{
				$table[] = array($key,$width);
			}
			
			FB::table('Columns', $table);
			
			# Grid Classes
			
			FB::groupEnd();
		}
		*/
		
		# Validation Errors
		if(Scaffold::$config['Firelog']['validation_errors'] === true && Validate::$errors)
		{
			FB::group('Validation Errors');
			
			foreach(Validate::$errors as $error)
			{
				self::_log("line {$error['line']} near {$error['near']} => {$error['message']}",1);				
			}
			
			FB::groupEnd();
		}
	}

	/**
	 * Logs a string or array to Firebug
	 *
	 * @author Anthony Short
	 * @param $group
	 * @return void
	 */
	private static function _log($message,$level=3)
	{		
		if(is_array($message))
		{
			foreach($message as $key => $value)
			{
				if(is_numeric($key))
				{
					call_user_func(array('FB',self::$fb_methods[$level]), $value);
				}
				else
				{
					self::_log($key,$value,$level);
				}
			}
		}
		else
		{
			call_user_func(array('FB',self::$fb_methods[$level]), $message);
		}
	}
	
	/**
	 * Logs to a group
	 *
	 * @author Anthony Short
	 * @param $group
	 * @return void
	 */
	private static function _group($group,$message,$level=3)
	{
		FB::group($group);
		self::_log($message,$level);
		FB::groupEnd();
	}
	
	/**
	 * Logs info about a file
	 *
	 * @author Anthony Short
	 * @param $file
	 * @return void
	 */
	private static function _file($file,$name = false)
	{
		if($name === false)
			$name = $file;

		# Log about the compiled file
		$contents = file_get_contents($file);
		$gzipped = gzcompress($contents, 9);
		
		$table = array();
		$table[] = array('Name','Value');
		$table[] = array('Compressed Size', Scaffold_Utils::readable_size($contents));
		$table[] = array('Gzipped Size', Scaffold_Utils::readable_size($gzipped));
		FB::table($name,$table);
	}
}