<?php


//////////////////////////////////////////////////
//////////////////////////////////////////////////




  function sortByOrder($a, $b) {

  	//sort first by type
  	if( $b[6] == $a[6] ){
  		return $b[7] - $a[7];
  	}
  
	//then by rank/karma
  	return strcmp($b[6], $a[6]);
  }





//////////////////////////////////////////////////
//////////////////////////////////////////////////




  function listSort( $post_sort, $post_existingIP , $post_existingPort, $listPath, $typePath, $define_minKarma, $define_maxKarma, $sortMethod ){


  	if( $listFilehandle = array_filter(file($listPath, FILE_SKIP_EMPTY_LINES)) ){
  
		//this is going to be our key/value variable
		$key;

		$lineCount = count($listFilehandle) - 1;
                for($lineNumber = 0; $lineNumber <= $lineCount; $lineNumber++){

			//unset blank lines
	                if( preg_match("/^\\s*$/", $listFilehandle[$lineNumber]) ){
        			unset($listFilehandle[$lineNumber]);
				continue;
			}
			

                        $listFilehandle[$lineNumber] = explode('|',  $listFilehandle[$lineNumber]);

                        if(   $listFilehandle[$lineNumber][0] .  $listFilehandle[$lineNumber][1] == $post_existingIP . $post_existingPort ){
                                
				//if the user an port exists, we are going to increment the sort value
                         	//break apart the array to deal with just the column we need
				//$listFilehandle[$lineNumber] = explode('|', $listFilehandle[$lineNumber]);
				
				if($post_sort == 'up'){
					//define var as max
					if( ($listFilehandle[$lineNumber][7] + 1) <= $define_maxKarma){
						$listFilehandle[$lineNumber][7] = $listFilehandle[$lineNumber][7] + 1;
					}
				}
				else{
                                        //define var as min
                                        if( ($listFilehandle[$lineNumber][7] - 1) >= $define_minKarma){
						//the assumption that 'else' is 'down' is probably a bad one
						$listFilehandle[$lineNumber][7] = $listFilehandle[$lineNumber][7] - 1;
					}
				}

				//reassemble the array
				//$listFilehandle[$lineNumber] = implode('|', $listFilehandle[$lineNumber]) . "\n";
								
                        }
                        else{
                                //take no action if not matching
                        }
			
		

                  }
		  fclose($listFilehandle);


		  //actual sort line
		  usort($listFilehandle, 'sortByOrder');

	
		  //reassemble haha this is not the right way to do this
		  $tmpVar1 = count($listFilehandle) - 1;

		  for($iterator = 0; $iterator <= $tmpVar1; $iterator++){
		  	$listFilehandle[$iterator] = implode('|', $listFilehandle[$iterator]) . "\n";

			//the nightmare debug
			//print_r($listFilehandle);
			//print "<BR /><BR />" . $iterator . "<BR /><BR />";
		  }

 		  //write the file
                  file_put_contents($listPath,$listFilehandle);

          }
          else{
                //the listFilehandle doesnt exist...hopefully this never happens because stupid 
                //systemErrorHandler( __LINE__, $e_2);
          }

  }




//////////////////////////////////////////////////
//////////////////////////////////////////////////




?>
