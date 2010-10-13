<?php

/**
 * Validate
 **/
class Validate
{

	/**
	 * Validation Errors
	 *
	 * @var array
	 */
	public static $errors;

	public static function display()
	{
		if( Scaffold::option('validate') )
		{					
			# Get the validator options from the config
			$validator_options = Scaffold::$config['Validate']['options'];
			
			# Add our options
			$validator_options['text'] = Scaffold::$output;
			$validator_options['output'] = 'soap12';
			
			# Encode them
			$validator_options = http_build_query($validator_options);
			
			$url = "http://jigsaw.w3.org/css-validator/validator?$validator_options";
			
			# The Curl options
			$options = array
			(
				CURLOPT_URL 			=> $url,
				CURLOPT_RETURNTRANSFER 	=> 1,
			);
			
			# Start CURL
			$handle = curl_init();
			curl_setopt_array($handle, $options);
			$buffer = curl_exec($handle);
			curl_close($handle);
			
			# If something was returned
			if (!empty($buffer))
			{
				# Simplexml doesn't like colons
				$buffer = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $buffer);
				
				# Let it be xml!
			    $results = simplexml_load_string($buffer);
			    $is_valid = (string)$results->envBody->mcssvalidationresponse->mvalidity;
				
				# Oh noes! Display the errors
			    if($is_valid == "false")
			    {
			    	$errors = $results->envBody->mcssvalidationresponse->mresult->merrors;
			    	
			    	foreach($errors->merrorlist->merror as $key => $error)
			    	{
			    		$line = (string)$error->mline;
			    		$message = trim((string)$error->mmessage);
			    		$near = (string)$error->mcontext;
			    		
			    		self::$errors[] = array('line' => $line, 'near' => $near, 'message' => $message);
			    		
			    		Scaffold::log("Validation Error on line {$line} near {$near} => {$message}",1);
			    	}
			    }
			}
		}
	}
} 
