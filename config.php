<?php

/**
 * Production Mode
 *
 * TRUE for production, FALSE for development. In development the cache is always
 * refreshed each time you reload the CSS. In production, the cache is locked
 * and will only be recache if these conditions are met:
 *
 *		1. One of the files in the request has changed
 *		2. The cache lifetime has expired (set below)
 *
 * If the cache lifetime has expired, Scaffold will refresh the flags and
 * check each file requested for changes. If no file has changed, it uses
 * the same output file again and waits till the cache lifetime is up again.
 *
 * This means the load on your server will be much less when the site is live.
 *
 * Also, in production mode, errors are disabled and any modules which change
 * the output will not be available - like the Typography module. 
 */
define('SCAFFOLD_PRODUCTION',false);

/**
 * Document Root
 *
 * The document root for the server. If you're server doesn't set the $_SERVER['DOCUMENT_ROOT']
 * variable (I'm looking at you Windows) you can manually enter in the server path 
 * to the document root. Most of the time, you won't need to touch this.
 */
$config['document_root'] = $_SERVER['DOCUMENT_ROOT'];

/**
 * System Folder
 *
 * The path to the system folder relative to where the front controller. You shouldn't
 * need to change this unless you are moving folders around. If you're calling Scaffold
 * from another class or script, you'll still need to set this. You can make it relative,
 * an absolute file path, or even relative to the document root and Scaffold
 * will take care of the rest. 
 */
$config['system'] = './';

/**
 * Cache Folder
 *
 * Sets the cache path. By default, this is inside of the system folder.
 * You can set it to a custom location here. I wouldn't recommend setting
 * this to any other folder, it just isn't usually necessary as Scaffold
 * can take care of everything internally.
 *
 * You will probably set this if you're using Scaffold within another framework.
 */
$config['cache'] = './cache/';

/**
 * Cache Lifetime
 *
 * This has no effect when no in production mode.
 *
 * Scaffold can cache it's flags, config values and more to save on 
 * rendering time when a file isn't being recached and just being delivered
 * to the browser. Rather than finding the config files, loading modules,
 * loading flags, checking modified times etc. It can just skip straight
 * to outputting the CSS.
 *
 * This value, in seconds, determines how long the internal cache will last.
 *
 * Setting this to false means that Scaffold will check to see if the original
 * files are modified every time, resulting in a slightly slower parsing time
 * when in production.
 */
$config['cache_lifetime'] = 3600; // 3600 will check if it needs to recache every hour

/**
 * Disable Flags
 *
 * Speeds up the processing, but you can't use cache flags
 */
$config['disable_flags'] = false;

/**
 * Enable Logging
 *
 * If you enable logging, errors, warnings and messages will be saved to the logs directory
 * inside the system folder.
 */
$config['enable_log'] = true;

/**
 * Error Threshold
 *
 * Set the minimum log level required to be displayed as an error. 0 will display
 * only error messages, 1 will display error AND warning messages etc.
 *
 * For example. A non-existent CSS file is an level 0 message (an error), whereas
 * a missing image is a level 1 message (a warning). Setting this to 1 will display
 * these missing images as errors.
 *
 * 0 = error
 * 1 = warning
 * 2 = message
 * 3 = debug
 */
$config['error_threshold'] = 1;

/**
 * Use PHP compression
 *
 * If you don't want to use the .htaccess to gzip your files, you can have Scaffold
 * do it for you before it outputs the CSS to the browser. Not that this takes longer
 * to do than to just use Apache to compress your components.
 *
 * Set this from 1-9, with 9 being the highest compression, and false being no compression.
 */
$config['gzip_compression'] = false;