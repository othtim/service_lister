<?php


function tagcloud($path){


	//holds the arrays
	//$serviceHolder;	
	//$serviceAssoc;

	file_put_contents($path, preg_replace( '/\R+/', "\n", file_get_contents($path) ));

	$serviceList = file($path, FILE_SKIP_EMPTY_LINES);	//open the service list file

	//iterate through once to blank everything
	foreach($serviceList as $s){
		$s2 = explode("|", $s);
		$s2 = $s2[6];
		$serviceHolder[$s2] = 0;
	}

	//iterate through each host in the list to get counts
	foreach($serviceList as $service){
		$service2 = explode("|", $service);
		if(isset($service2[6])){
			continue;
		}
		$service2 = $service2[6];
		$serviceHolder[$service2]++;	
	}
	
	arsort($serviceHolder);	//sort key=>value pairs High to Low

	$serviceAssoc[0] = '';
	$serviceAssoc[1] = '';
	$serviceAssoc[2] = '';
	$serviceAssoc[3] = '';
	$serviceAssoc[4] = '';
	$serviceAssoc[5] = '';
	$serviceAssoc[6] = '';
	$serviceAssoc[7] = '';
	$serviceAssoc[8] = '';
	$serviceAssoc[9] = '';
	$serviceAssoc[10] = '';
	$serviceAssoc[11] = '';
	$serviceAssoc[12] = '';
	$serviceAssoc[13] = '';
	$serviceAssoc[14] = '';	

	//loop to assign order
	$counter = 0;
	foreach($serviceHolder as $key => $value){
		//print $key;
		$serviceAssoc[$counter] = $key;
		$counter++;
	}
	

	print '
	<table border="0" width="">
	<tr>
		<td><a href="#' . $serviceAssoc[9] . '"><font size="">' . $serviceAssoc[9] . '</font></a></td>
		<td><a href="#' . $serviceAssoc[8] . '"><font size="">' . $serviceAssoc[8] . '</font></a></td>
		<td><a href="#' . $serviceAssoc[3] . '"><font size="+1">' . $serviceAssoc[3] . '</font></a></td>
		<td><a href="#' . $serviceAssoc[7] . '"><font size="">' . $serviceAssoc[7] . '</font></a></td>
		<td><a href="#' . $serviceAssoc[14] . '"><font size="-1">' . $serviceAssoc[14] . '</font></a></td>
	</tr>
	<tr>
                <td><a href="#' . $serviceAssoc[5] . '"><font size="+2">' . $serviceAssoc[5] . '</font></a></td>
		<td><a href="#' . $serviceAssoc[1] . '"><font size="+3">' . $serviceAssoc[1] . '</font></a></td>
                <td><a href="#' . $serviceAssoc[0] . '"><font size="+4">' . $serviceAssoc[0] . '</font></a></td>
                <td><a href="#' . $serviceAssoc[2] . '"><font size="+3">' . $serviceAssoc[2] . '</font></a></td>
                <td><a href="#' . $serviceAssoc[6] . '"><font size="+1">' . $serviceAssoc[6] . '</font></a></td>
        </tr>
        <tr>
                <td><a href="#' . $serviceAssoc[10] . '"><font size="">' . $serviceAssoc[10] . '</font></a></td>
		<td><a href="#' . $serviceAssoc[4] . '"><font size="+2">' . $serviceAssoc[4] . '</font></a></td>
                <td><a href="#' . $serviceAssoc[11] . '"><font size="">' . $serviceAssoc[11] . '</font></a></td>
                <td><a href="#' . $serviceAssoc[12] . '"><font size="-1">' . $serviceAssoc[12] . '</font></a></td>
                <td><a href="#' . $serviceAssoc[13] . '"><font size="-1">' . $serviceAssoc[13] . '</font></a></td>
        </tr>
	</table>
	';
	


}





?>
