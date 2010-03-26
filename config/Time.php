<?php

/**
 * Time Module
 */
 
/** 
 * Time offset from GMT
 *
 * Adjust the offset from GMT time for the time module so that the flags
 * accurately trigger according to the timezone of where you live.
 */
$config['offset'] = +11;

/**
 * Time Flags
 *
 * Here you can create special flags for different times of the day, 
 * week, month or year. 
 */
$config['flags'] = array
(
	'tuesday' => array
	(
		'day' => 'Tuesday' 
	),
	
	'night' => array
	(
		'hour' => 23
	),

	# Morning is the name of the flag
	'morning' => array
	(
		# Then we can set date, day, hour, month, week or year
		'hour' => array
		(
			'from' => '5',
			'to'   => '11'
		)
	)
);