<?php

/**
 * Grid class
 *
 * @author Anthony Short
 */
class Layout
{

	/**
	 * All of the grid settings
	 *
	 * @var Array
	 */
	public static $grid_settings;

	/**
	 * Parse the @grid rule and calculate the grid.
	 *
	 * @author Anthony Short
	 * @param $css
	 */
	public static function import_process()
	{
		if( $settings = Scaffold::$css->find_at_group('grid') )
		{
			$groups = $settings['groups'];
			$settings = $settings['values'];
			
			# You really should only have one @grid
			if(count($groups) > 1)
			{
				Scaffold::error('Layout module can only use one @grid rule');
			}
	
			# Make sure the groups have all the right properties
			self::check_grid($groups[0],$settings);
			
			# The number of columns
			$cc = $settings['column-count'];
			
			# The tyopgraphic baseline
			$bl = $settings['baseline'];
			
			# The left gutter on grid columns
			$lgw = (isset($settings['left-gutter-width'])) ? $settings['left-gutter-width'] : 0;
			
			# The right gutter on grid columns
			$rgw = (isset($settings['right-gutter-width'])) ? $settings['right-gutter-width'] : 0;
			
			# The combined gutter widths
			$gw	= $settings['gutter-width'] = $lgw + $rgw;
			
			# The total width of the grid
			$grid = $settings['grid-width'];
	
			# The grid width minus all the gutter widths
			$netgridwidth = $grid - $cc * $gw;
			
			# The width of a single column
			$cw = floor($netgridwidth / $cc);

			self::$grid_settings = array
			(
				'column_width' 			=> $cw,
				'gutter_width' 			=> $gw,
				'left_gutter_width' 	=> $lgw,
				'right_gutter_width' 	=> $rgw,
				'grid_width' 			=> $grid,
				'baseline' 				=> $bl
			);
		
			/**
			 * Set each of the column widths as Constants. They 
			 * can be accessed like this: $columns_1
			 */
			if(class_exists('Constants'))
			{
				for ($i = 1; $i <= $cc; $i++)
				{
					Constants::set('columns_' . $i, ($i * $cw) + (($i * $gw) - $gw) . 'px');
				}
			}

			/**
			 * Set them as constants we can use in the css. They can
			 * be accessed like this: $column_count
			 */
			foreach(self::$grid_settings as $key => $value)
			{
				Constants::set($key,$value . 'px');
			}
			
			# Set this seperately as it doesn't need px added to it
			self::$grid_settings['column_count'] = $cc;
			Constants::set('column_count',$cc);
			
			/**
			 * Create the grid background image
			 */
			$img = self::create_grid_image($cw, $bl, $lgw, $rgw);
			Scaffold::$css->string .= "=grid-overlay{background:url('".$img."');} .grid-overlay { +grid-overlay; }";
			
			/**
			 * Include the mixin templates so you can access each of
			 * the grid properties with mixins like this: +columns(4);
			 */
			$mixins = Scaffold::find_file('templates/mixins.css');
			$classes = Scaffold::find_file('templates/classes.css');
			 
			if( $mixins !== false AND $classes !== false )
			{
				Scaffold::$css->string .= file_get_contents($mixins);
				Scaffold::$css->string .= file_get_contents($classes);
			}
		}
	}

	/**
	* Generates the background grid.png
	*
	* @author Anthony Short
	* @param $cl Column width
	* @param $bl Baseline
	* @param $gw Gutter Width
	* @return null
	*/
	private static function create_grid_image($cw, $bl, $lgw, $rgw)
	{
		# Path to the image
		$file = "{$lgw}_{$cw}_{$rgw}_{$bl}_grid.png";
			
		if( ( $cache = Scaffold_Cache::find('Layout/' . $file) ) === false)
		{
			Scaffold_Cache::create('Layout');

			$image = ImageCreate($cw + $lgw + $rgw,$bl);
			
			$colorWhite		= ImageColorAllocate($image, 255, 255, 255);
			$colorGrey		= ImageColorAllocate($image, 200, 200, 200);
			$colorBlue		= ImageColorAllocate($image, 240, 240, 255);
			
			# Draw left gutter
			Imagefilledrectangle($image, 0, 0, ($lgw - 1), $bl, $colorWhite);
			
			# Draw column
			Imagefilledrectangle($image, $lgw, 0, $cw + $lgw - 1, $bl, $colorBlue);
			
			# Draw right gutter
			Imagefilledrectangle($image, ($lgw + $cw + 1), 0, $lgw + $cw + $rgw, $bl, $colorWhite);
		
			# Draw baseline
			imageline($image, 0, ($bl - 1 ), $lgw + $cw + $rgw, ($bl - 1), $colorGrey);
			
			$cache = Scaffold_Cache::find('Layout') . '/' . $file;
			
			ImagePNG($image, $cache);
		    
		    # Kill it
		    ImageDestroy($image);
	    }
	    
	    return Scaffold::url_path($cache);
	}
	
	/**
	 * Checks if all the needed settings are present in a group
	 *
	
	 * @param $group
	 * @return boolean
	 */
	private static function check_grid($group,$settings)
	{
		if(!isset($settings['column-count']))
		{
			Scaffold::error('Missing property from @grid - <strong>column-count</strong>');
		}
		
		elseif(!isset($settings['baseline']))
		{
			Scaffold::error('Missing property from @grid - <strong>baseline</strong>');
		}
		
		elseif(!isset($settings['grid-width']))
		{
			Scaffold::error('Missing property from @grid - <strong>grid-width</strong>');
		}
		
		else
		{
			return true;
		}
	}
}