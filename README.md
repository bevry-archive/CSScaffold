# Scaffold CSS Framework

Scaffold is a CSS framework and pre-processor that lets you extend the CSS language easily. You just pass your CSS files through Scaffold and you have access to all of the new functionality.

## Requirements

PHP5+

## Setup

Put the scaffold folder somewhere on your web server. Then link to your CSS files in your HTML like so:

	<link rel="stylesheet" href="scaffold/index.php?f=/stylesheets/master.css" />
	
You can pass through multiple files at once by separating the file names with a comma. 

	<link rel="stylesheet" href="scaffold/index.php?f=/stylesheets/master.css,/stylesheets/another.css" />
	
And you can define a base path

	<link rel="stylesheet" href="scaffold/index.php?d=/stylesheets/&f=master.css,another.css,type.css" />

### Automatically parse your CSS using .htaccess

You can use a .htaccess file to automatically pass any requests to CSS files to Scaffold. You might use something like this:

	<IfModule mod_rewrite.c> 
		RewriteEngine	on
		RewriteCond		%{REQUEST_FILENAME}		-f
		RewriteCond 	%{REQUEST_URI}			\.css$
		RewriteRule 	^(.+)$ 					scaffold/index.php?f=%{REQUEST_URI}&%{QUERY_STRING}
	</IfModule> 
	
You would place this in the CSS directory (which also contains the scaffold directory), and any CSS files requested from this directory or deeper are passed through Scaffold first. Explaining this .htaccess file, or getting it to work with your exact setup is out of the scope of this documentation, as it will vary in each environment depending on how you are using Scaffold.

### Configuration

The config for Scaffold itself, is called config.php and is inside the scaffold folder. The configuration for each module is stored within the config folder inside the scaffold folder.

To get Scaffold to work correctly, you may need to modify some of the settings inside config.php.

## Usage

Once you've setup Scaffold, and you can see your CSS files being parsed by it, you'll want to know what you can do. The bulk of Scaffold's functionality is contained within **modules** and **extensions**. 

#### Modules

Modules are classes which use hooks within Scaffold to do various things to the CSS. In 2.0, however, they can hook into the core functionality of Scaffold to do just about anything they want. You might want to do custom caching or modify the files before processing.

#### Extensions

The extensions are simple PHP functions that Scaffold uses to create custom properties and CSS-style functions (like url()).

### Included Modules

It wouldn't be much of a framework if it didn't include a lot of useful modules. Scaffold includes:

#####Absolute URL
Convert url paths in your CSS into absolute path relative to the document root.

#####Constants
Set and use constants within your CSS. You can even use an xml file to load constants from an outside source.

#####Extensions
Enables the use of extensions allowing you to create custom properties and CSS functions without needing an entire module

#####Firelog
Outputs various information to Firebug if you have the FirePHP extension installed.

#####Flags
Have different caches depending on certain conditions. For example, you can set certain CSS to only appear in the morning, or only show to particular browser.

#####Formatter
Minify your CSS before it's cached to save on size, or format your CSS in a way that it's easily readable.

#####Gradient
Create gradient background images right within your CSS that are created on the fly. It makes creating buttons a snap.

#####Import
Lets you include other CSS files to be compiled as one file. This means it's sent to the browser as one file instead of many. It's way better than the standard @import

#####Iteration
Use PHP-style for loops to generate CSS

#####Layout
Generate grid CSS classes and mixins on the fly. It creates Blueprint-style grid classes based on a custom grid.

#####Mixins
Extend selectors using base sets of properties that can take parameters.

#####Nested Selectors
Nest selectors as complex as you'd like. 

#####Time
Create custom time periods where certain CSS will be displayed - like Christmas or Halloween.

#####Typography
Generate a specimen sheet of all the HTML elements and the styles your CSS applies to them.

#####Validate
Use the W3C CSS validator to validate your CSS.

### Included Functions

#####rand(from,to)

Lets you generate a random number between two other numbers
#####baseline(n)

Using the layout module, this multiplies the baseline by n 
#####baseline_round(n)

Similar to the above, but you can put in any number, and it will round to the nearest baseline unit.
#####calc(expression)

Evaluate math expressions.
#####cmyk(c,m,y,k)

Input CMYK values, and the output is a simple hex value.
#####cmyka(c,m,y,k,alpha)

Similar to the above, but you can define an alpha value.
#####embed(path)

Embed an image in the CSS. Used as a replacement for url()
#####enumerate(string,from,to,seperator)

Generate a string from one value to another.
#####hsl(hue,saturation,brightness)

Returns a simple hex value
#####hsla(hue,saturation,brightness,alpha)

Well, you get the point.

### Included Properties

#####background-gradient: (vertical|horizontal), size, from, to

Create a gradient background image.
#####image-replace:url()

Replaces text with an image.

## Example

Here's what some CSS written with Scaffold might look like:

	+global-reset;
	+html5-reset;
	
	/**
	 * Include all the extra files
	 */
	@include 'buttons.css';
	
	/**
	 * This defines the layout. It creates mixins and constants we
	 * can use, as well as generating a png to use as a grid overlay 
	 */
	@grid
	{
		grid-width:940;
		right-gutter-width:20;
		column-count:16;
		baseline:18;
	}
	
	/**
	 * Constants used throughout the design
	 */
	@constants
	{
		border			:#d9d9d9;
		grey			:#666;
		//light_blue	:#e5f9ff;
		light_blue		:rgba(56,161,195,0.08);
		light_grey		:#7d7d7d;
		link_blue		:#4375d1;
		red				:#c31919;
		third			:calc(($grid_width - (2 * $gutter_width)) / 3)px;
	}

	// Define some mixins
	=rounded
	{
		+border-radius(5px);
	}
	
	=bordered
	{
		border:1px solid $border;
	}
	
	=box
	{
		+rounded;
		background:$light_blue;
	}

	.tweet
	{
		+third-unit;
		
		&:last-child
		{ 
			+last; 
		}
		
		&:nth-child(odd)
		{
			top:-10px;
		}
		
		@for $i from 1 to 3
		{
			&:nth-child($i)
			{
				blockquote
				{
					-webkit-transform:rotate(rand(-3,3)deg) scale( calc( rand(1,20) * 0.01 + 1 ) );
				}
			}
		}
		
		blockquote 
		{
			+box;
			+rounded;
			padding:baseline(1);
			color:$light_grey;
		}
		
		img
		{
			height:50px;
			width:50px;
			+rounded;
			+absolute(false,false,0,0);
		}
		
		a
		{
			background:url(images/bg-twitter.png) left top no-repeat;
			display:block;
			padding:baseline(0.5) 60px 0 43px;
			text-align:right;
			text-decoration:none;
			height:50px;
			float:right;
			line-height:15px;
		}
		
		span
		{
			display:block;
			color:$light_grey;
		}
	}


## Constants

Constants are set like so:

	@constants
	{
		name:value;
	}
	
And then used like a PHP variable:

	#id
	{
		height:$name;
	}
	
Which will output

	#id
	{
		height:value;
	}
	
You can define a path to an XML file to set constants automatically from there as well.

## Flags and Time

Using flags, we can display parts of our CSS only if certain conditions are met:

	@flag(morning)
	{
		background:$light_blue;
	}

	@flag(night)
	{
		background:$dark_blue;
	}

To create flags, you need to create a module to set flags during the initialisation hook. This may be changed in a future release. You can create your own time-based flags inside the time config file located at scaffold/config/Time.php

## Import

Using import is easy.

	@include 'myfile.css';
	
It will use the path of the current CSS file as the base, but you can do absolute paths too:

	@include '/stylesheets/components/myfile.css';
	
## Iteration

This lets you use PHP-style for loops

	@for $n from 1 to 10
	{
		.columns-$n
		{
			width: calc($n + 100)px;
		}
	}
	
## Layout

The layout module creates CSS classes and Scaffold mixins to make creating layouts easy.

	@grid
	{
		grid-width:940;
		right-gutter-width:10;
		left-gutter-width:10;
		column-count:16;
		baseline:18;
	}
	
Then we can do this:

	#id
	{
		+columns(4);
	}
	
Which might output this:

	#id
	{
		width:300px;
	}
	
*columns* is a mixin that the Layout module creates. To see the rest, take a look inside /scaffold/modules/Layout/templates/

## Nested Selectors

You can do simple nesting:
	
	#id
	{
		a 
		{
			color:blue;
		}
	}
	
Or do more complex nesting

	.tweet
	{
		+third-unit;

		&:nth-child(odd)
		{
			top:-10px;
		}
	
		@for $i from 1 to 3
		{
			&:nth-child($i)
			{
				blockquote
				{
					-webkit-transform:rotate(rand(-3,3)deg) scale( calc( rand(1,20) * 0.01 + 1 ) );
				}
			}
		}
	}
	
The & symbol represents the parent element of the current selector. This way you can keep all the selectors for single id or class in one group.

## Mixins

Mixins are groups of properties you can inject into selectors that can take parameters and use conditionals. Using mixins might look like this:

	// Define the mixin
	=mixin_name
	{
		height:50px;
		width:50px;
	}
	
	#id
	{
		+mixin_name;
	}
	
This will output

	#id
	{
		height:50px;
		width:50px;
	}
	
You can get more complicated and add parameters too

	=my_mixin($color)
	{
		height:50px;
		width:50px;
		background:$color;	
	}
	
	#id
	{
		+my_mixin(#eee);
	}
	
Which will output

	#id
	{
		height:50px;
		width:50px;
		background:#eee;
	}
	
The parameters can take default values too, so you might use it like this:

	=my_mixin($background = '#eee')
	{
		background:$eee;
	}
	
	#id
	{
		+my_mixin;
	}
	
	#id2
	{
		+my_mixin(red);
	}
	
Which will output

	#id
	{
		background:#eee;
	}
	
	#id2
	{
		background:red;
	}
	
To get even more powerful, you can add conditional statements

	=my_awesome_mixin($color,$padding = false)
	{
		border:$color;
		
		@if($padding === true)
		{
			padding:10px;
		}
	}

I think you get the idea. Mixins can contain any chunk of code you want, not just properties. They can include entire nested selector groups. 

There are a lot of mixins already included with Scaffold. They are stored in scaffold/mixins/. These mixins are all included by default, so you don't have to include them yourself. You can change this behaviour in the mixins config.

## Creating Gradients

The gradient module is still young, so it can only do from one color to another for now, but this is how it is used:

	#id
	{
		background-gradient:direction,size,from(#hex),to(#hex);
	}
	
So a working example might look like this:

	#id
	{
		background-gradient:vertical,25,from(#000),to(#fff);
	}
	
This will create a gradient that is 25px high from black to white. You can use 'horizontal' insted of vertical and the size will determine how wide the gradient is.

In the next release, I plan on adding color stops. 

## Creating custom functions

To create a custom function, you need to place a PHP file with the name of your function inside /scaffold/extensions/functions/ inside one of the 3 phase folders.

The phase folders allow you to determine the order the functions are parsed.

Here's a simple example that creates the embed function

	function Scaffold_embed($file)
	{
		$path = Scaffold::find_file( Scaffold_Utils::unquote($file) );
	
		# Couldn't find it
		if($path === false)
			return false;

		$data = 'data:image/'.pathinfo($path, PATHINFO_EXTENSION).';base64,'.base64_encode(file_get_contents($path));
	
		return "url(" . $data . ")";
	}
	
You need to call the PHP function the same as the name of the file (which is the name of the CSS function) and prefix it with Scaffold_ to avoid conflicts.

You return the string that will replace the function string. So in this case, url($data) will replace the embed() string.

Above your functions, you can set a variable called $unique.

	$unique = true;

	function Scaffold_rand($from,$to)
	{
		return rand($from, $to);
	}
	
This means every found function will be parsed separately. By default, this is false, and functions that Scaffold finds that are exactly the same, will be combined and replaced at the same time.

## Creating custom properties

The properties work the same way as the functions. You place a file named whatever you want your property to be called, inside /scaffold/extensions/properties.

	function Scaffold_image_replace($url)
	{
		$url = preg_replace('/\s+/','',$url);
		$url = preg_replace('/url\\([\'\"]?|[\'\"]?\)$/', '', $url);

		$path = Scaffold::find_file($url);
	
		if($path === false)
			return false;
																		
		// Get the size of the image file
		$size = GetImageSize($path);
		$width = $size[0];
		$height = $size[1];
	
		// Make sure theres a value so it doesn't break the css
		if(!$width && !$height)
		{
			$width = $height = 0;
		}
	
		// Build the selector
		$properties = "background:url(".Scaffold::url_path($path).") no-repeat 0 0;
			height:{$height}px;
			width:{$width}px;
			display:block;
			text-indent:-9999px;
			overflow:hidden;";

		return $properties;
	} 
	
For properties, you want to return a string of properties that will replace this custom one.

##Having trouble?

Make sure you read the documentation. Twice. Then Google it. If you're still having trouble, feel free to contact me at csscaffold@me.com.

If you find a bug, put it in the issues section on Github. 

##License

Copyright (c) 2009, Anthony Short <csscaffold@me.com>
http://github.com/anthonyshort/csscaffold
All rights reserved.

This software is released under the terms of the New BSD License.
http://www.opensource.org/licenses/bsd-license.php
