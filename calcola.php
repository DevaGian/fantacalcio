<?php
	function substituction($index,$subs)
	{
	 if($subs==0){	 
		if($index == 0)
			$info=getResult($str,$squadra,11,$form); //guarda il secondo portiere
		elseif($index > 0 and $index <= $form['dif'])
			$info=getResult($str,$squadra,12,$form); 
		elseif($index > $form['dif'] and $index <= ($form['dif'] + $form['cen']))
			$info=getResult($str,$squadra,14,$form);
		elseif($index > $form['cen'] and $index <= ($form['cen'] + $form['att']))
			$info=getResult($str,$squadra,16,$form);
		elseif($index == 12)
			$info=getResult($str,$squadra,13,$form);
		elseif($index == 14)
			$info=getResult($str,$squadra,15,$form);
		elseif($index == 16)
			$info=getResult($str,$squadra,17,$form);
		else //se anche le seconde riserve non hanno giocato viene restituito 0
			$info = array('vote' => 0, 'giocatore' => "nessuno");
	 }
	 elseif($subs=1){
	 	if($index > 0 and $index <= $form['dif'])
			$info=getResult($str,$squadra,13,$form); 
		elseif($index > $form['dif'] and $index <= ($form['dif'] + $form['cen']))
			$info=getResult($str,$squadra,15,$form);
		elseif($index > $form['cen'] and $index <= ($form['cen'] + $form['att']))
			$info=getResult($str,$squadra,17,$form);	
	 }
	 else
	 	$info = array('vote' => 0, 'giocatore' => "nessuno");
	}
	function getResult($str,$squadra,$index,$form)
	{
		$info = array('vote' => -1, 'giocatore' => $squadra[$index]); //'vote' contiene il voto del giocatore, 'giocatore' il nome
		//var_dump($giocatore);exit;
		$regex = "/".$squadra[$index].".*?<div class=\"inParameter fvParameter\">\d+[<|\.][\d|\/]/i"; //espressione regolare che estrapola una tabella
		//echo $regex; exit;
		preg_match_all($regex, $str, $result, PREG_SET_ORDER); //estrapola la tabella dei voti del giocatore
		/*if($giocatore == "Fetfatzidis G"){
			echo $result[0][0]; exit;}
		//var_dump($result[0]);*/
		//var_dump($result);exit;
		if(count($result)<1){ //se il giocatore non viene trovato si va a vedere nelle riserve		
			if($index == 0)
				$info=getResult($str,$squadra,11,$form); //guarda il secondo portiere
			elseif($index > 0 and $index <= $form['dif'])
				$info=getResult($str,$squadra,12,$form); 
			elseif($index > $form['dif'] and $index <= ($form['dif'] + $form['cen']))
				$info=getResult($str,$squadra,14,$form);
			elseif($index > $form['cen'] and $index <= ($form['cen'] + $form['att']))
				$info=getResult($str,$squadra,16,$form);
			elseif($index == 12)
				$info=getResult($str,$squadra,13,$form);
			elseif($index == 14)
				$info=getResult($str,$squadra,15,$form);
			elseif($index == 16)
				$info=getResult($str,$squadra,17,$form);
			else //se anche le seconde riserve non hanno giocato viene restituito 0
				$info = array('vote' => 0, 'giocatore' => $squadra[$index]);
		}
		else{ //se è stato trovato un giocatore vengono eseguitle elabroazioni di sorta
			if(is_numeric(substr($result[0][0], strlen($result[0][0])-3)) and !is_numeric(substr($result[0][0], strlen($result[0][0])-4))) //estrapola il fantavoto se è una cifra con la virgola
				$info['vote'] = substr($result[0][0], strlen($result[0][0])-3);
			elseif(is_numeric(substr($result[0][0], strlen($result[0][0])-4))) //estrapola il fantavoto se son due cifre con la virgola
				$info['vote'] = substr($result[0][0], strlen($result[0][0])-4);
			elseif(is_numeric(substr($result[0][0], strlen($result[0][0])-4, 2))) //estrapola il fantavoto se è intero a 2 cifre
				$info['vote'] = substr($result[0][0], strlen($result[0][0])-4, 2);		
			else															//estrapola il fantavoto se è intero ad una cifra
				$info['vote'] = substr($result[0][0], strlen($result[0][0])-3, 1);
		}
		return $info;
	}
	
	function getGoal($result)
	{
		if($result < 66)
			return 0;
		elseif($result >= 66 and $result < 72)
			return 1;
		elseif($result >= 72 and $result < 78)
			return 2;
		elseif($result >= 78 and $result < 84)
			return 3;
		elseif($result >= 84 and $result < 90)
			return 4;
		elseif($result >= 90 and $result < 96)
			return 5;
		elseif($result >= 96 and $result < 102)
			return 6;
		elseif($result >= 102 and $result < 108)
			return 7;
		elseif($result >= 108 and $result < 114)
			return 8;
		elseif($result >= 114 and $result < 120)
			return 9;
		elseif($result >= 120)
			return 10;
	}
	$form_a = array("dif" => $_POST['dif_a'], "cen" => $_POST['cen_a'], "att" => $_POST['att_a']);
	$form_b = array("dif" => $_POST['dif_b'], "cen" => $_POST['cen_b'], "att" => $_POST['att_b']);
	$squadra_a = array();
	$squadra_b = array();
	$ris_a = array();
	$ris_b = array();
	for($k=0; $k<17; $k++){
		$squadra_a[$k] = $_POST['a_'.$k];
		$squadra_b[$k] = $_POST['b_'.$k];
	}	
	
	$arr=file("http://www.gazzetta.it/calcio/fantanews/voti/serie-a-2014-15/"); //estrapola tutti i voti dal sito della gazzetta
	$str="";
	foreach($arr as $piece)
		$str.=$piece;
	$str=str_replace(PHP_EOL,null,$str); //elimina i caratteri newline
	$sum_a=1;
	$sum_b=0;
	$table = "<table border='1'>";
	$table .= "<tr><td colspan='2'>".$_POST['gioca_a']."</td><td colspan='2'>".$_POST['gioca_b']."</td></tr>";
	
	for($k=0; $k<11; $k++){		
		$info_a = getResult($str,$squadra_a,$k,$form_a);
		$info_b = getResult($str,$squadra_b,$k,$form_b);
		/*var_dump($info_a);
		echo '<br>';*/
		$table .= "<tr><td>".$info_a['giocatore']."</td><td>".$info_a['vote']."</td><td>".$info_b['giocatore']."</td><td>".$info_b['vote']."</td></tr>";
		$sum_a += $info_a['vote'];
		$sum_b += $info_b['vote'];
	}
	$table .= "<tr><td>Totale</td><td>".$sum_a."</td><td>Totale</td><td>".$sum_b."</td>";
	$table .= "<tr><td>Goal</td><td>".getGoal($sum_a)."</td><td>Goal</td><td>".getGoal($sum_b)."</td>";
	echo $table;
?>
