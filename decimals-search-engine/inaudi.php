<?php

/************************************************************************/
/*																		*/
/*																		*/
/*						 DIGITS SEARCH ENGINE							*/
/*						   "Giacomo Inaudi"								*/
/*																		*/
/*				Copyright 2012/2014 - Totodunet							*/
/*																		*/
/*			License : CC-BY-NC-SA 4.0 International						*/
/*			(https://creativecommons.org/licenses/by-nc-sa/4.0/)   		*/
/*																		*/
/*			Université de La Rochelle									*/
/*			University of La Rochelle									*/
/*																		*/
/************************************************************************/

	//POST METHOD
	
	if(isset($_POST)&&!empty($_POST)){
		
		if(!empty($_POST['number'])){
		
			//OPEN FILE
			switch($_POST['number']){
				case 'sqrt2':$decimals=fopen('path to file','r');break;
				case 'ln2':$decimals=fopen('path to file','r');break;
				case 'ln10':$decimals=fopen('path to file','r');break;
				case 'phi':$decimals=fopen('path to file','r');break;
				case 'mascheroni':$decimals=fopen('path to file','r');break;
				case 'e':$decimals=fopen('path to file','r');break;
				case 'zeta3':$decimals=fopen('path to file','r');break;
				default:$decimals=fopen('path to file','r');break;
			}
			$time_start;
			$duration;
			
			//CLASSIC SEARCH OF A DECIMAL
			if(!empty($_POST['d'])&&is_numeric($_POST['d'])){
				fseek($decimals,intval($_POST['d'])+1);
				$decimal=fgetc($decimals);
				if($_POST['d']==1)
					echo '<p>The first decimal is <span style="color:red;">'.$decimal.'</span></p>';
				else if($_POST['d']==2)
					echo '<p>The second decimal is <span style="color:red;">'.$decimal.'</span></p>';
				else if($_POST['d']==3)
					echo '<p>The third decimal is <span style="color:red;">'.$decimal.'</span></p>';
				else
					echo '<p>The '.number_format($_POST['d'],0,'.',' ').'<sup>th</sup> decimal is <span style="color:red;">'.$decimal.'</span></p>';
				if(!empty($_POST['display'])&&$_POST['display']=="true"){
					$content;
					if($_POST['d']<1000000000){
						if($_POST['d']>999999000){
							$content=fread($decimals,1000000000-$_POST['d']);
							echo '<p>'.number_format(1000000000-$_POST['d'],0,'.',' ').' next decimals :</p>';
						}
						else{
							$content=fread($decimals,1000);
							echo '<p>1 000 next decimals :</p>';
						}
						echo '<textarea cols="50" rows="20" readonly>'.$content.'</textarea>';
					}
				}
			}
			
			//SEARCH OF A STRING
			if(isset($_POST['q'])&&is_numeric($_POST['q'])){
				$q=$_POST['q'];
				$start=0;
				if(!empty($_POST['start'])&&is_numeric($_POST['start'])&&$_POST['start']>=0&&$_POST['start']<1000000000)
					$start=$_POST['start'];
				$position=0;
				$stpos;
				$nb_boucle=$start;
				$pas=floor((1000000000-$start)/100000);
				$rest=(1000000000-$start)%100000;
				$time_start=microtime(true);
				while($nb_boucle<1000000000&&$position==0){
					$content;
					fseek($decimals,2+$nb_boucle);
					if(($nb_boucle-$start>=$pas*100000||$pas==0)&&$rest>0){
						$content=fread($decimals,$rest);
					}
					else{
						$content=fread($decimals,100000);
					}
					$stpos=strpos($content,$q);
					if($stpos!==false)
						$position=$stpos+$nb_boucle+1;
					unset($content);
					$nb_boucle+=100000;
				}
				$duration=microtime(true)-$time_start;
				//if found
				if($position>0){
					if($position>3){
						if(substr($position,-2)!=1&&substr($position,-1)==1)
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>st position</p>";
						else if(substr($position,-2)!=1&&substr($position,-1)==2)
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>nd position</p>";
						else if(substr($position,-2)!=1&&substr($position,-1)==3)
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>rd position</p>";
						else
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>th position</p>";
					}
					else if($position==1)
						echo "<p>Found at the first position</p>";
					else if($position==2)
						echo "<p>Found at the second position</p>";
					else
						echo "<p>Found at the third position</p>";
					if($duration>0)
						echo "<p style=\"color:green;\">Found in ".round($duration,8)."s - Speed : ".number_format(round(($position-$start)/$duration),0,'.',' ')." digits/s</p>";
					echo '<div style="margin-top:20px;">';
					echo '<input type="hidden" name="'.$q.'"/>';
					echo '<input type="submit" name="'.$position.'" class="next" value="NEXT" onclick="javascript:next()"/></div>';
				}
				else
					echo "<p>Not found !</p>";
				
			}
			
			//COUNT AN OCCURENCE
			if(isset($_POST['c'])&&is_numeric($_POST['c'])){
				$c=$_POST['c'];
				$start=0;
				$count=0;
				$end=1000000000;
				if(!empty($_POST['start'])&&is_numeric($_POST['start'])&&$_POST['start']>=0&&$_POST['start']<1000000000)
					$start=$_POST['start'];
				if(!empty($_POST['end'])&&is_numeric($_POST['end'])&&$_POST['end']>$start&&$_POST['end']<=1000000000)
					$end=$_POST['end'];
				$nb_boucle=$start;
				$pas=floor(($end-$start)/100000);
				$rest=($end-$start)%100000;
				$time_start=microtime(true);
				while($nb_boucle<$end){
					fseek($decimals,2+$nb_boucle);
					if(($nb_boucle-$start>=$pas*100000||$pas==0)&&$rest>0)
						$content=fread($decimals,$rest);
					else
						$content=fread($decimals,100000);
					$count+=substr_count($content,$c);
					$nb_boucle+=100000;
					unset($content);
				}
				$duration=microtime(true)-$time_start;
				//if found
				if($count>0){
					echo "<p>'".$c."' was found <span style=\"color:red;\">".number_format($count,0,'.',' ')."</span> times</br>between ".number_format($start,0,'.',' ')." and ".number_format($end,0,'.',' ')."</p>";
					if($duration>0)
						echo "<p style=\"color:green;\">Counted in ".round($duration,8)."s - Speed : ".number_format(round(($end-$start)/$duration),0,'.',' ')." digits/s</p>";
				}
				else
					echo "<p>Not found !</p>";
			}
			
			//CLOSE FILE
			fclose($decimals);
			
		}
		
	}
	
	//GET METHOD
	
	if(isset($_GET)&&!empty($_GET)){
		
		if(!empty($_GET['number'])){
		
			//OPEN FILE
			switch($_GET['number']){
				case 'sqrt2':$decimals=fopen('path to file','r');break;
				case 'ln2':$decimals=fopen('path to file','r');break;
				case 'ln10':$decimals=fopen('path to file','r');break;
				case 'phi':$decimals=fopen('path to file','r');break;
				case 'mascheroni':$decimals=fopen('path to file','r');break;
				case 'e':$decimals=fopen('path to file','r');break;
				case 'zeta3':$decimals=fopen('path to file','r');break;
				default:$decimals=fopen('path to file','r');break;
			}
			$time_start;
			$duration;
			
			//CLASSIC SEARCH OF A DECIMAL
			if(!empty($_GET['d'])&&is_numeric($_GET['d'])){
				fseek($decimals,intval($_GET['d'])+1);
				$decimal=fgetc($decimals);
				if($_GET['d']==1)
					echo '<p>The first decimal is <span style="color:red;">'.$decimal.'</span></p>';
				else if($_GET['d']==2)
					echo '<p>The second decimal is <span style="color:red;">'.$decimal.'</span></p>';
				else if($_GET['d']==3)
					echo '<p>The third decimal is <span style="color:red;">'.$decimal.'</span></p>';
				else
					echo '<p>The '.number_format($_GET['d'],0,'.',' ').'<sup>th</sup> decimal is <span style="color:red;">'.$decimal.'</span></p>';
				if(!empty($_GET['display'])&&$_GET['display']=="true"){
					$content;
					if($_GET['d']<1000000000){
						if($_GET['d']>999900000){
							$content=fread($decimals,1000000000-$_GET['d']);
							echo '<p>'.number_format(1000000000-$_GET['d'],0,'.',' ').' next decimals :</p>';
						}
						else{
							$content=fread($decimals,100000);
							echo '<p>100 000 next decimals :</p>';
						}
						echo '<textarea rows="25" cols="100" readonly>'.$content.'</textarea>';
					}
				}
			}
			
			//SEARCH OF A STRING
			if(isset($_GET['q'])&&is_numeric($_GET['q'])){
				$q=$_GET['q'];
				$start=0;
				if(!empty($_GET['start'])&&is_numeric($_GET['start'])&&$_GET['start']>=0&&$_GET['start']<1000000000)
					$start=$_GET['start'];
				$position=0;
				$stpos;
				$nb_boucle=$start;
				$pas=floor((1000000000-$start)/100000);
				$rest=(1000000000-$start)%100000;
				$time_start=microtime(true);
				while($nb_boucle<1000000000&&$position==0){
					$content;
					fseek($decimals,2+$nb_boucle);
					if($nb_boucle==$pas*100000&&$rest>0){
						$content=fread($decimals,$rest);
					}
					else{
						$content=fread($decimals,100000);
					}
					$stpos=strpos($content,$q);
					if($stpos!==false)
						$position=$stpos+$nb_boucle+1;
					unset($content);
					$nb_boucle+=100000;
				}
				$duration=microtime(true)-$time_start;
				//if found
				if($position>0){
					if($position>3){
						if(substr($position,-2)!=1&&substr($position,-1)==1)
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>st position</p>";
						else if(substr($position,-2)!=1&&substr($position,-1)==2)
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>nd position</p>";
						else if(substr($position,-2)!=1&&substr($position,-1)==3)
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>rd position</p>";
						else
							echo "<p>Found at <span style=\"color:red;\">".number_format($position,0,'.',' ')."</span>th position</p>";
					}
					else if($position==1)
						echo "<p>Found at the first position</p>";
					else if($position==2)
						echo "<p>Found at the second position</p>";
					else
						echo "<p>Found at the third position</p>";
					if($duration>0)
						echo "<p style=\"color:green;\">Found in ".round($duration,8)."s - Speed : ".number_format(round(($position-$start)/$duration),0,'.',' ')." digits/s</p>";
					echo '<div style="margin-top:20px;"><form method="get" action"inaudi.php">';
					echo '<input type="hidden" name="number" value="'.$_GET['number'].'"/>';
					echo '<input type="hidden" name="start" value="'.$position.'"/>';
					echo '<input type="hidden" name="q" value="'.$q.'"/>';
					echo '<input type="submit" value="NEXT"/></form></div>';
				}
				else
					echo "<p>Not found !</p>";
				
			}
			
			//COUNT AN OCCURENCE
			if(isset($_GET['q'])&&is_numeric($_GET['c'])){
				$c=$_GET['c'];
				$start=0;
				$count=0;
				$end=1000000000;
				if(!empty($_GET['start'])&&is_numeric($_GET['start'])&&$_GET['start']>=0&&$_GET['start']<1000000000)
					$start=$_GET['start'];
				if(!empty($_GET['end'])&&is_numeric($_GET['end'])&&$_GET['end']>$start&&$_GET['end']<=1000000000)
					$end=$_GET['end'];
				$nb_boucle=$start;
				$pas=floor(($end-$start)/100000);
				$rest=($end-$start)%100000;
				$time_start=microtime(true);
				while($nb_boucle<$end){
					fseek($decimals,2+$nb_boucle);
					if($nb_boucle==$pas*100000&&$rest>0)
						$content=fread($decimals,$rest);
					else
						$content=fread($decimals,100000);
					$count+=substr_count($content,$c);
					$nb_boucle+=100000;
					unset($content);
				}
				$duration=microtime(true)-$time_start;
				//if found
				if($count>0){
					echo "<p>'".$c."' was found <span style=\"color:red;\">".number_format($count,0,'.',' ')."</span> times</br>between ".number_format($start,0,'.',' ')." and ".number_format($end,0,'.',' ')."</p>";
					if($duration>0)
						echo "<p style=\"color:green;\">Counted in ".round($duration,8)."s - Speed : ".number_format(round(($end-$start)/$duration),0,'.',' ')." digits/s</p>";
				}
				else
					echo "<p>Not found !</p>";
			}
			
			//CLOSE FILE
			fclose($decimals);
			
		}
		
	}
	
	
	/*OPTIMIZE THE SEARCHING SPEED OF A GREAT STRING IN SEVERAL STRINGS
	
		Example : 	subject			 -> ...4589632145741236
					string to search -> 23658
					
				1) On coupe la chaîne récupérée du nombre de caractère de la chaîne à trouver en partant de la fin : 41236
				2) On enlève un caractère sur les deux chaînes : 	1236 et 2365	puis on réitère tant qu'il y a pas similitude ou qu'il reste toujours des caractères	+1
																	236 et 236	Bingo !																					+1 = +2
																	Incrémentation totale : 5-2 = 3 ; on doit partir de 3 caractères avant la fin
	
	*/
	function cbn_incremente($string, $pattern){
		//incrémentation maximale = longueur de la chaîne recherchée
		$increment=strlen($pattern);
		//coupe la chaîne de décimales à la taille de la chaîne recherchée à partir de la fin
		$string=substr($string,strlen($string)-$increment);
		//variable booléenne de similitude (initialisé à false)
		$stop=false;
		//tant qu'il y a pas similitude et que les chaînes ne sont pas nulles
		while($increment!=0&&!$stop){
			$increment--;
			//on coupe les chaînes de 1 caractère
			$string=substr($string,1);
			$pattern=substr($pattern,0,$increment);
			//si similitude => stoppe la boucle et calcule l'incrémentation à prendre en compte pour la prochaine recherche
			if($string==$pattern){
				$stop=true;
			}
		}
		return $increment;
	}

?>