<?php

//file must be in the install dir
include "/home/chaosvpn_user/cvswork/servup/serv.cfg";


global $wwwRoot; 
global $appRoot;  

include($appRoot . 'includes/globals.php');
include($appRoot . 'includes/htmlhead.php');
include($appRoot . 'includes/sort.php');
include($appRoot . 'includes/tagcloud.php');
include($appRoot . 'includes/functions.php');


global $listPath;
global $typePath;


//global $sortMethod;
//$sortMethod = 'typerank'; 
//$sortMethod = 'grouptyperank';



//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////

//define error codes
global $e_1;
$e_1 = 'Didnt exit gracefully after displaying html';
global $e_2;
$e_2 = 'Could not open service list file, expecting: ' . $listPath;


function systemErrorHandler( $linenum, $errmsg ){
    print '<pre style="line-height: 2em;">';
    printf("==> Error line %s: %s\n\n", $linenum, $errmsg);
    //debug_print_backtrace();
    print '</pre>';
    exit($errmsg);
}


function userErrorHandler( $errmsg ){
	print "<font color='red'>" . $errmsg . "</font></br>";
	print_html();
}
function userWarnHandler( $errmsg ){
	print "<font color='red'>" . $errmsg . "</font></br>";
}

//hack to remove blank lines
//file_put_contents(,
//                  implode('', file('tagged.txt', FILE_SKIP_EMPTY_LINES)));



//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////


//if the sort method was changed, we need to remember that.
if( $_POST and $_POST['sortMethod'] ){

	// i dont really even know what i want to do in here.
}


/////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
//  if all they did is click the sort button, then just sort
/////////////////////////////////////////////////////////////////////


if( ($_POST['sort-up'] == 'up') or ($_POST['sort-down'] == 'down') ){

   #probably not the nicest way to do this oh well
  if( $_POST['sort-up'] == 'up'){ 
	$post_sort = 'up';
  }
  else{
	$post_sort = 'down';
  }

  $post_existingIP = trim( $_POST['existingIP'] );
  $post_existingPort = trim( $_POST['existingPort'] );

   #probably redundant check
  if( $post_sort ){

	//pass variables we need to the function
	listSort( $post_sort, $post_existingIP, $post_existingPort, $listPath, $typePath, $define_minKarma, $define_maxKarma, $sortMethod );

        print_html();
  }

}


/////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
// if they clicked the "delete" button then we will do that
////////////////////////////////////////////////////////////////////

if( $_POST['delete'] ){
	
  print 'To delete: please enter the passphrase associated with this record and click DELETE.';

  //update some fields so the user knows what they are deleting...
  $post_ip = trim($_POST['existingIP']);
  $post_port = trim($_POST['existingPort']);

  //print_stuff
  print_html();

}


//if this is second delete round, go here
//this is gross and should be fixed
if( $_POST['deleteFinal'] ){

  $post_ip = trim($_POST['ip']);
  $post_port = trim($_POST['port']);
  $post_passphrase = trim($_POST['passphrase']);


  if( $listFilehandle = file($listPath, FILE_SKIP_EMPTY_LINES) ){

	$key;

	$lineCount = count($listFilehandle) - 1;
	$deleteFlag = 0;
        for($lineNumber = 0; $lineNumber <= $lineCount; $lineNumber++){

		$listFilehandle[$lineNumber] = explode('|',  $listFilehandle[$lineNumber]);

		//debug
		//print $listFilehandle[$lineNumber][0] . '-ip-' . $post_ip . '<br />';
		//print $listFilehandle[$lineNumber][1] . '-port-' . $post_port . '<br />';
		//print $listFilehandle[$lineNumber][9] . '-passphrase-' . hash("md5", $post_passphrase) . '<br />';

		if( ($post_ip == $listFilehandle[$lineNumber][0]) and ($post_port == $listFilehandle[$lineNumber][1]) and (hash('md5', 
$post_passphrase) == trim($listFilehandle[$lineNumber][9]) ) ){
			
			//if we are here that means we have a match
			//zero out this record in the array then print some message
			$listFilehandle[$lineNumber] = '';
			$deleteFlag = 1;
		}
		else{
			//do nothing if non matching
		}

	}


	//check flags and print message
	if( $deleteFlag == 1 ){
		print "Deleting.<br />";
	}
	else{
		print "It appears you have entered an incorrect passphrase.<BR />";
	}


	//reusing terrible code im so sorry

        //reassemble haha this is not the right way to do this
        $tmpVar1 = count($listFilehandle) - 1;

     	for($iterator = 0; $iterator <= $tmpVar1; $iterator++){
		$listFilehandle[$iterator] = implode('|', $listFilehandle[$iterator]);// . "\n";
	}
	file_put_contents($listPath,$listFilehandle);

  }
  else{
	systemErrorHandler(__LINE__, "Could not open filehandle for deleting.");
  }


  print_html();
}





///////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////
//if there was an actual submission, do things here.

if($_POST){
 

  $post_ip = trim($_POST['ip']);
  $post_port = trim($_POST['port']);
  $post_name = trim($_POST['name']);
  $post_service = trim($_POST['service']);
  $post_customService = trim($_POST['customService']);
  $post_sort = trim($_POST['sort']);
  $post_add_servType = $_POST['add_servType'];
  $post_group = trim($_POST['group']);
  $post_passphrase = trim($_POST['passphrase']);


  //check the fields to make sure they exist
  if( ! $post_ip ){
    userErrorHandler("Please enter an IP address.");
  }
  else{
	  if( $post_ip == '127.0.0.1' ){
		userErrorHandler("Please enter a valid ip in the format xxx.xxx.xxx.xxx, and not 127.0.0.1 :)");
	  }
	  if( ! preg_match('/^(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]|[0-9])'.'\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9][0-9]?|[0-9])$/' , $post_ip) ){
	        userErrorHandler("Please enter a valid ip in the format xxx.xxx.xxx.xxx");
	  }
  }



  if( ! $post_port ){
    userErrorHandler("Please enter a port number.");; 
    error();
  }
  else{
	  if( ! preg_match('/^([0-9]{1,6})$/' , $post_port) ){
	        userErrorHandler("Please enter a port number using only digits.");;
	  }
  }



  if( ! $post_name ){
  	userErrorHandler("Please enter a description of the service.");
  }
  else{
	  if( ! preg_match('/^([A-Za-z0-9]|\s|[.]|[_]|[-]|[\/]|[\:])*$/', $post_name) ){
		userErrorHandler("Please enter only alphanumeric values, colons, forward slashes, dots, minus, underscores or whitespace for the descriptive name.");
	  }
  }



  if( ! $post_group ){
        //we dont actually care if they dont enter a group name.
  }
  else{
          if( ! preg_match('/^([A-Za-z0-9]|\s)*$/', $post_group) ){
                userErrorHandler("Please enter only alphanumeric values or whitespace for the group name.");
          }
  }



  //open the service type file. check if this value exists inthe file. if yes, all good.
  $flag_doesServiceExistInFile = 0;

  if( $typeFilehandle = file($typePath) ){

	  foreach($typeFilehandle as $line){
        	  if( chop($line) == $post_service ){
                	  $flag_doesServiceExistInFile = 1;
                  }
                  else{
                          //do nothing
                  }
          }
          fclose($typeFilehandle);
  }
  else{
          systemErrorHandler( __LINE__ , "could not open service type file");
  }



  //if custom is defined, use that. otherwise use the selection.
  if( $post_customService ){
	if( ! preg_match('/^([A-Za-z0-9]|[ ]|[,])*$/', $post_customService) ){
		userErrorHandler("Please enter only alphanumeric values, commas, or spaces for your custom service type. Or, select one 
from the list.");
	}
	else{
		$post_service = $post_customService;
		
		//if this is set then the user checked the "add to list" checkbox
		if($post_add_servType){

			//we aren't going to add this entry to the list if it already exists
			if($flag_doesServiceExistInFile == 0){
			
				//open file and append
				file_put_contents( $typePath , $post_customService . "\n" , FILE_APPEND);
			}
			else{
				//warn i guess?
				userWarnHandler("This service type already exists in the list. Continuing...");
			}
		}
	}
  }
  else{

	if( $flag_doesServiceExistInFile == 0 ){
		
		//if the value is 'Select type...' then the user didnt enter anything
		if($post_service == 'Select type...'){
			//user never entered a service type.
			$post_service = '';
		}
		else{
			userErrorHandler("I dont know what you picked for a service, but it wasn't from the selection list.");
		}
	}
  }
  

	
  //htmlentities
  //$post_ip = htmlentities($_POST['ip']);
  //$post_port = htmlentities($_POST['post']);
  //$post_name = htmlentities($_POST['name']);




  
  //////////////////////////////////////////////////////////////////////
  //////////////////////////////////////////////////////////////////////
  // so we made it this far. time to start doing things
  ///////



  //slurp file into an array
  if( $listFilehandle = file($listPath) ){

	  //check if record already exists
	  foreach($listFilehandle as $line){
	
		//compare the a substring of the $line (ip and port) to the user input.
		//if it exists already, error
		$line = explode('|', $line);
	  	if(  $line[0] . $line[1] == $post_ip . $post_port ){
			userErrorHandler("This IP and Port already exists. Please enter something else.");
		}
		else{
			//take no action if not matching
		}

	  }

  }
  else{
	//the listFilehandle doesnt exist... :/
	systemErrorHandler( __LINE__, $e_2);
  }

  ///////

 
  //lets check if this host is UP
  $socket = fsockopen( $post_ip , $post_port, $errno, $errstr, 5 );
  if ( ! $socket ) {
        userErrorHandler("This host and port does not seem to be up. Please ensure the host is up before adding it to the list.");
  }
  else {
        echo "Host verified. Adding to the master list.<br />";
	$string =  "\n" . 
	$post_ip . '|' . 
	$post_port . '|' . 
	$post_name . '|' . 
	1 . '|' . 		//site up
	1 . '|' .		//numberoftimesup
	1 . '|' .		//numberoftimeschecked
	$post_service . '|' .	//service type
	1 . '|' . 		//bubble initial value
	$post_group . '|' .	//group for sorting. can be network, anything.
	hash('md5', $post_passphrase);//hash to verify owner
  }

  $result =  file_put_contents( $listPath , $string . "\n" , FILE_APPEND);
  if( $result ){

	//if we are here then it worked
  }
  else{
	systemErrorHandler( __LINE__, "Unable to write to the service list file!");
  }


 
}








function print_stuff(){

  global $listPath;
  global $typePath;
 
  $listFilehandle = file($listPath, FILE_SKIP_EMPTY_LINES);

  if( $listFilehandle ){

	//heading 
        print '<table width="90%"><tr><td width="15%"><b>Submitted Services: </b></td>';

	//sort options can go here later
	print '<td width="85%" align="right"></td></tr></table>';

	//data
	print '<table border="0" width="90%" cellspaceing="0">';
        print '<tr bgcolor="#FF7F50">
		<td width="15%">IP/Hostname</td>
		<td width="4%">Port</td>
		<td width="5%">Type</td>
		<td width="3%">Status</td>	
		<td width="5%">Uptime</td>
		<td width="35%">Description</td>
		<td width="5%">Sort</td>
		<td width="5%">Group</td>
		<td width="3%">&nbsp;</td>
	</tr>';

	 //alternates for each record to make nice colorings
	$colorCounter = 0;
	 //we use this to see if the Type has changed
	$type = 'firstlinelol';


	foreach( $listFilehandle as $line ){
		
		$line = explode('|',$line);
		
		//ignore whitespace
		if( preg_match("/^\\s*$/", $line[0]) ){
			continue;
		}		

		//check if the type has changed (and that this isnt the first line)
		if( ($type != $line[6]) and ($type != 'firstlinelol') ){
			print '<tr><td><a name="' . $type . '"></a>&nbsp;</td></tr>';
			//$colorCounter = $colorCounter = 0;
		}
		$type = $line[6];


		//the assumption is six fields in the csv. 
		//ip, port, service name, yes/no for up/down, numberOfTimesUp, numberofTimesChecked, type, and karma	

		print '<form action="serv.php" method="post" name="formName' . $line[0] . $line[1]  . '">';
		print '<tr';
		
		
		if( ($colorCounter % 2) == 0 ){
			print ' bgcolor="#C0C0C0">';
		} else {
			print ' bgcolor="#CDCDFF">';
		}
		

		//do something for http and https. blech. lazy.
		if( ($line[6] == 'http') or ($line[6] == 'https') ){		
			print '<td width="15%"><a href="http://' . $line[0] . '">' . $line[0] . '</a></td>'; 	//ip
		}
		else{
			print '<td width="15%">' . $line[0] . '</td>';   //ip
		}

		print '<td width="5%">'  . $line[1] . '</td>';	//port
		print '<td width="5%">'  . $line[6] . '</td>';	//type

		if($line[3]==0){  //the third = is intentional
			print "<td width='5%'><b><font color='red'>down!</font></b></td>";
		}
		else{
			if($line[3]==1){
				print "<td width='5%'><b><font color='green'>up!</font></b></td>";
			}
			else{
				//there should be no other choices
				print "<td width='5%'></td>";
			}
		}

		print "<td width='5%'>";
		print number_format((($line[4] / $line[5]) * 100), 0) . '%';
		print "</td>";
		
		// this is the description fiels
		// add <a> tags around http links
	
		// figured this was better in a function, add_link_markup()
		////match http links, copied from stackoverflow LOL
		//$pattern = '/(http:\/\/[a-z0-9\.\/?=&]+)/i';
		//$replacement = '<a href="$1" target="_blank">$1</a>';
		// $source = preg_replace($pattern, $replacement, $line[2]);
		




  		print '<td width="40%">' . add_link_markup($line[2]) . '</td>';

		//this section is for the sorting buttons
		print '<td width="10%">';
		//print '<input type="image" value="up" src="img/arrow-up-2.png" name="sort-up" onclick=\'document.getElementById("sortUpOrDown' . $line[0] . $line[1]  . '").value = "up"; document.getElementById("formName' . $line[0] . $line[1]  . '").submit();\'/>';
		//print '<input type="image" value="down" src="img/arrow-down-2.png" name="sort-down" onclick=\'document.getElementById("sortUpOrDown' . $line[0] . $line[1]  . '").value = "down"; document.getElementById("formName' . $line[0] . $line[1]  . '").submit();\'/>';
		print '<input type="submit" value="up" name="sort-up" >';
		print '<input type="submit" value="down" name="sort-down" >';
		
		print '&nbsp;&nbsp;&nbsp;' . $line[7];
		//print '<input type="hidden" id="sortUpOrDown' . $line[0] . $line[1]  . '" name="sortUpOrDown" value="">';
		print '<input type="hidden" name="existingIP" value="' . $line[0] . '">';
		print '<input type="hidden" name="existingPort" value="' . $line[1] . '">';
		print '<input type="hidden" name="delete" id="delete' . $line[0] . $line[1]  . '" >';
		print '</td>';


		//network label
		print '<td width="5%">' . $line[8] . '</td>';	

                print '<td width="3%">';
		print '<input type="submit" value="delete" name="delete">';
                //
		//print '<input type="image" value="a" src="img/application-exit-4.png" name="delete" onclick=\'document.getElementById("delete' . $line[0] . $line[1]  . '").value = "delete"; document.getElementById("formName' . $line[0] . $line[1]  . '").submit();\'/>';
                //
		print '</td>';

		print '</tr>';
                print "</form>";



		$colorCounter++;
	
	}
	fclose($listFilehandle);

	print '</table>';
	
  
  }
  else{
	systemErrorHandler( __LINE__, $e_2);
  }



//why is this here
die();
}








////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
//basic html code
function print_html(){
?>


<table width="100%">
<tr>
<td width="50%">

<form name="test" action="serv.php" method="post">
<br />
<table>
	<tr>
		<td colspan=2><b>Submit a service to the list:</b></td>
	</tr>
	<tr>
		<td width="10%">IP address</td>
		<td width="90%"><input size="30%" type="text" name="ip" value="<?php global $post_ip; if($post_ip){ print htmlentities($post_ip); } ?>"></td>
	</tr>
	<tr>
		<td width="10%">Port</td>
		<td width="90%"><input size="30%" type="text" name="port" value="<?php global $post_port; if($post_port){ print htmlentities($post_port); } ?>"></td>
	</tr>
	<tr>
		<td width="10%">Type</td>
		<td width="90%">
		<input type="text" name="customService" value="<?php global $post_customService; if($post_customService){ print htmlentities($post_customService); } ?>">
		<select name="service">
		<option selected="true" style="display:none;">Select type...</option>
		<?php
			//////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////
			//generate the select list with the various servies that have been added

			global $typePath;
			
			if( $typeFilehandle = file($typePath) ){
				
				foreach($typeFilehandle as $line){
                                        echo '<option value="' . chop($line) . '">' . chop($line) . '</option>';
                                }
				fclose($typeFilehandle);
				
			}
			else{
				systemErrorHandler( __LINE__ , "could not open service type file");
			}	

			//////////////////////////////////////////////////////////////////////////
			//////////////////////////////////////////////////////////////////////////
		?>
		</select>
		<input type="checkbox" name="add_servType" value="1">Add to the list?
		</td>
	</tr>
	<tr>
		<td width="10%">Description</td>
		<td width="90%"><input size="30%" type="text" name="name" value="<?php global $post_name; if($post_name){ print htmlentities($post_name); } ?>"></td>
	</tr>
	<tr>
		<td width="10%">Group</td>
		<td width="90%"><input size="30%" type="text" name="group" value="<?php global $post_group; if($post_group){ print htmlentities($post_group); } ?>"></td>
	</tr>
	<tr>
		<td width="10%">Passphrase</td>
		<td width="90%"><input size="30%" type="text" name="passphrase" value=""></td>
	</tr>
	<tr>
		<td><input width="75%" type="submit" name="submit" value="Submit"></td>
		<?php if( $_POST['delete'] ){ print '<td><input type="submit" name="deleteFinal" value="DELETE"></td>'; } ?>
	</tr>

</table>
</form>

</td>
<td width="50%">

<?php global $listPath; tagcloud($listPath); ?>

</td>
</tr>
</table

<BR />
<BR />

<?php


print_stuff();
exit(); //exit gracefully
systemErrorHandler(__LINE__, $e_1);
//end the print_html() function
}


/////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////





print_html();

?>
