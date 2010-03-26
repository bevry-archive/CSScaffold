<?php

/**
 * CSS Utilities
 *
 * Has methods for interacting with the CSS string
 * and makes it very easy to find properties and values within the css
 * 
 * @package CSScaffold
 * @author Anthony Short
 */
class Scaffold_CSS
{
	/**
	 * Server path to this CSS file
	 *
	 * @var string
	 */
	public $path;
	
	/**
	 * The name of this CSS file
	 *
	 * @var string
	 */
	public $file;
	
	/**
	 * The string of CSS code
	 *
	 * @var string
	 */
	public $string;
	
	/**
	 * Constructor
	 *
	 * @param $file
	 * @return void
	 */
	public function __construct($file)
	{
		$this->path = dirname($file);
		$this->file = $file;
		$this->string = $this->remove_inline_comments(file_get_contents($file));
	}
	
	/**
	 * Returns the CSS string when treated as a string
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->string;
	}

	/**
	 * Compresses down the CSS file. Not a complete compression,
	 * but enough to minimize parsing time.
	 *
	 * @return string $css
	 */	
	public function compress($css)
	{		
		# Remove comments
		$this->string = $this->remove_comments($this->string);

		# Remove extra white space
		$this->string = preg_replace('/\s+/', ' ', $css);
		
		# Remove line breaks
		$this->string = preg_replace('/\n|\r/', '', $css);
	}
	
	/**
	 * Removes inline comments
	 *
	 * @return return type
	 */
	public function remove_inline_comments($css)
	{
		 return preg_replace('#(\s|$)//.*$#Umsi', '', $css);
	}

	/**
	 * Removes css comments
	 *
	 * @return string $css
	 */
	public function remove_comments($css)
	{
		$css = $this->convert_entities('encode', $css);
		$css = trim(preg_replace('#/\*[^*]*\*+([^/*][^*]*\*+)*/#', '', $css));
		$css = $this->convert_entities('decode', $css);
		$css = $this->remove_inline_comments($css);

		return $css;
	}

	/**
	 * Finds CSS 'functions'. These are things like url(), embed() etc.
	 *
	 * @author Anthony Short
	 * @param $name
	 * @param $capture_group
	 * @return array
	 */
	public function find_functions($name, $capture_group = "")
	{
		$regex =
		"/
			{$name}
			(
				\s*\(\s*
					( (?: (?1) | [^()]+ )* )
				\s*\)\s*
			)
		/sx";

		if(preg_match_all($regex, $this->string, $match))
		{
			return ($capture_group == "") ? $match : $match[$capture_group];
		}
		else
		{
			return array();
		}
	}

	/**
	 * Finds @groups within the css and returns
	 * an array with the values, and groups.
	 *
	 * @author Anthony Short
	 * @param $group string
	 * @param $css string
	 */
	public function find_at_group($group, $remove = true)
	{
		$found = array();
		
		$regex = 
		"/
			# Group name
			@{$group}
			
			# Flag
			(?:
				\(( [^)]*? )\)
			)?
			
			[^{]*?

			(
				([0-9a-zA-Z\_\-\@*&]*?)\s*
				\{	
					( (?: [^{}]+ | (?2) )*)
				\}
			)

		/ixs";
			
		if(preg_match_all($regex, $this->string, $matches))
		{
			$found['groups'] = $matches[0];
			$found['flag'] = $matches[1];
			$found['content'] = $matches[4];
						
			foreach($matches[4] as $key => $value)
			{
				// Remove comments to prevent breaking it
				$value = $this->remove_comments($value);

				foreach(explode(";", substr($value, 0, -1)) as $value)
				{
					// Encode any colons inside quotations
					if( preg_match_all('/[\'"](.*?\:.*?)[\'"]/',$value,$m) )
					{
						$value = str_replace($m[0][0],str_replace(':','#COLON#',$m[0][0]),$value);
					}

					$value = explode(":", $value);	
					
					// Make sure it's set
					if(isset($value[1]))
					{
						$found['values'][trim($value[0])] = str_replace('#COLON#', ':', Scaffold::unquote($value[1]));
					}
				}
			}
			
			// Remove the found @ groups
			if($remove === true)
			{
				$this->string = str_replace($found['groups'], array(), $this->string);	
			}

			return $found;		
		}
		
		return false;
	}
	
	/**
	 * Finds selectors which contain a particular property
	 *
	 * @author Anthony Short
	 * @param $css
	 * @param $property string
	 * @param $value string
	 */
	public function find_selectors_with_property($property, $value = ".*?")
	{		
		if(preg_match_all("/([^{}]*)\s*\{\s*[^}]*(".$property."\s*\:\s*(".$value.")\s*\;).*?\s*\}/sx", $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Finds all properties with a particular value
	 *
	 * @author Anthony Short
	 * @param $property
	 * @param $value
	 * @param $css
	 * @return array
	 */
	public function find_properties_with_value($property, $value = ".*?")
	{		
		# Make the property name regex-friendly
		$property = Scaffold_Utils::preg_quote($property);
		$regex = "/ ({$property}) \s*\:\s* ({$value}) /sx";
			
		if(preg_match_all($regex, $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}
		
	/**
	 * Finds a selector and returns it as string
	 *
	 * @author Anthony Short
	 * @param $selector string
	 * @param $css string
	 */
	public function find_selectors($selector, $recursive = "")
	{		
		if($recursive != "")
		{
			$recursive = "|(?{$recursive})";
		}

		$regex = 
			"/
				
				# This is the selector we're looking for
				({$selector})
				
				# Return all inner selectors and properties
				(
					([0-9a-zA-Z\_\-\*&]*?)\s*
					\{	
						(?P<properties>(?:[^{}]+{$recursive})*)
					\}
				)
				
			/xs";
		
		if(preg_match_all($regex, $this->string, $match))
		{
			return $match;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Finds all properties within a css string
	 *
	 * @author Anthony Short
	 * @param $property string
	 * @param $css string
	 */
	public function find_property($property)
	{ 		
		if(preg_match_all('/('.Scaffold_Utils::preg_quote($property).')\s*\:\s*(.*?)\s*\;/sx', $this->string, $matches))
		{
			return (array)$matches;
		}
		else
		{
			return array();
		}
	}
	
	/**
	 * Check if a selector exists
	 *
	 * @param $name
	 * @return boolean
	 */
	public function selector_exists($name)
	{
		return preg_match('/'.preg_quote($name).'\s*?({|,)/', $this->string);
	}
		
	/**
	 * Removes all instances of a particular property from the css string
	 *
	 * @author Anthony Short
	 * @param $property string
	 * @param $value string
	 * @param $css string
	 */
	public function remove_properties($property, $value)
	{
		return preg_replace('/'.$property.'\s*\:\s*'.$value.'\s*\;/', '', $this->string);
	}
	
	/**
	 * Encodes or decodes parts of the css that break the xml
	 *
	 * @author Anthony Short
	 * @param $css
	 * @return string
	 */
	public function convert_entities($action = 'encode', $css = false)
	{
		if($css === false)
			$css =& $this->string;
		
		$css_replacements = array(
			'"' => '#SCAFFOLD-QUOTE#',
			'>' => '#SCAFFOLD-GREATER#',
			'&' => '#SCAFFOLD-PARENT#',
			'data:image/PNG;' => '#SCAFFOLD-IMGDATA-PNG#',
			'data:image/JPG;' => "#SCAFFOLD-IMGDATA-JPG#",
			'data:image/png;' => '#SCAFFOLD-IMGDATA-PNG#',
			'data:image/jpg;' => "#SCAFFOLD-IMGDATA-JPG#",
			'http://' => "#SCAFFOLD-HTTP#",
		);
		
		switch ($action)
		{
		    case 'decode':
		        $this->string = str_replace(array_values($css_replacements),array_keys($css_replacements), $this->string);
		        break;
		    
		    case 'encode':
		        $this->string = str_replace(array_keys($css_replacements),array_values($css_replacements), $this->string);
		        break;  
		}
		
		return $css;
	}

}