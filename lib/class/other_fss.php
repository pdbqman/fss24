<?php 
class OtherFssRu {
	public static function this_kvartal_and_year(){
		$year_m = array();
		
		$YEAR=date("Y");
		$m = date("m");
		if($m == '02' or $m == '03' or $m=='04')
			$KVARTAL = '03';
		if($m == '05' or $m == '06' or $m=='07')
			$KVARTAL = '06';
		if($m == '08' or $m == '09' or $m=='10')
			$KVARTAL = '09';
		if($m == '11' or $m == '12' or $m=='01')
			$KVARTAL = '12';
		if($m == '01')$YEAR=intval($YEAR)-1;
		
		$year_m['YEAR'] 	= $YEAR;
		$year_m['KVARTAL'] 	= $KVARTAL;
		
		return $year_m;
	}
	public static function validate_input( $input_name ){
		$valid_name = trim($input_name);
		// вырезаем теги
		$valid_name = strip_tags($valid_name);
		//конвертируем специальные символы в мнемоники HTML
		$valid_name = htmlspecialchars($valid_name,ENT_QUOTES);
		
		// Замена символов
		$search  = array('+', '(', ')', ' ');
		$replace = '';
		$valid_name = str_replace($search, $replace, $valid_name);

		return $valid_name;
	}
	public static function last_kvartal_and_year(){
		$year_m = array();
		
		$YEAR=date("Y");
		$m = date("m");
		if($m == '02' or $m == '03' or $m=='04')
			$KVARTAL = '04';
		if($m == '05' or $m == '06' or $m=='07')
			$KVARTAL = '07';
		if($m == '08' or $m == '09' or $m=='10')
			$KVARTAL = '10';
		if($m == '11' or $m == '12' or $m=='01')
			$KVARTAL = '01';
		if($m == '12')$YEAR=intval($YEAR)+1;
		
		$year_m['YEAR'] 	= $YEAR;
		$year_m['KVARTAL'] 	= $KVARTAL;
		
		return $year_m;
	}
}
?>