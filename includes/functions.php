<?php


//wraps links in <a> tags
function add_link_markup($source){

	$suffix = array(
		'hack',
		'dn42',
		'de',
		'com',
		'org',
		'net',
		'ca'
	);

	$source = explode(' ', $source);

	foreach($source as &$j){
	
		//match http links
	        $pattern = '/(http:\/\/[a-z0-9\:\.\/\?\=\&]+)/i';
	        $replacement = '<a href="$1">$1</a>';
	        $j  = preg_replace($pattern, $replacement, $j);
	
		//match link without the http opener that match common suffixes. fix later if becomes performance issue
		foreach($suffix as $i){
	
			$pattern = 	'/([a-z0-9\.\_\-]+)' .			//match regular url stuff
					'(\.' . $i . ')' .			//this is our suffix
					'([a-z0-9\:\/\?\.\=\&]*)$/i'; 		//followed by more url chars if exist
			$replacement = '<a href="http://$1$2$3" target="_blank">$1$2$3</a>';
			$j  = preg_replace($pattern, $replacement, $j);
		}	
	}
	$source = implode(' ', $source);

return $source;
}

?>
