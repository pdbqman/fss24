<?php
//echo "Доверки";
# Парсинг сайта portal.fss.ru
include_once  __DIR__ . "/class/PortalFssRu.php";
// --->> БАЗА ДАННЫХ
# Подключаем БД
include  __DIR__ . "/class/data_base.php";
// <<--- БАЗА ДАННЫХ

$regArrDov	=	array();
$sql = "INSERT INTO `doverka` SET 
			`REG_NUM` 	= :REG_NUM,
			`NAME`		= :NAME,	
			`STATUS`	= :STATUS,		
			`DATE_BEGIN`= :DATE_BEGIN,
			`DATE_END`	= :DATE_END,		
			`AHREF`		= :AHREF,
			`DATESYSTEM`= :DATESYSTEM
";

$fss = new PortalFssRu( 'user' );
$fss->check_doverka( $regArrDov );

if( $regArrDov ){
	#Очистим всю таблицу перед загрузкой новых данных
	$trancate = "TRUNCATE TABLE doverka";
	$insert_id = DB::set($trancate);
	
	for($i=2;$i<count($regArrDov);$i++){
		$REG_NUM 	= $regArrDov[$i][2];
		$NAME 		= $regArrDov[$i][3];
		//echo htmlspecialchars($regArrDov[$i][4]);
		if ($regArrDov[$i][4] == "&#x2714;")
			 $STATUS = 1;
		else $STATUS = 0;
		
		$date = date_create($regArrDov[$i][5]);
		$DATE_BEGIN = date_format($date, 'Y-m-d');
		
		$date = date_create($regArrDov[$i][7]);
		$DATE_END	= date_format($date, 'Y-m-d');
		
		$AHREF		= $regArrDov[$i][9];
		$DATESYSTEM = date('Y-m-d H:i:s');
		
		//echo $REG_NUM . " - " . $NAME . " - " . $STATUS . " - " . $DATE_BEGIN . " - " . $DATE_END . " - " . $AHREF;
		$insert_id = DB::add($sql, array(	'REG_NUM'	=> $REG_NUM, 
											'NAME'		=> $NAME,	
											'STATUS' 	=> $STATUS,		
											'DATE_BEGIN'=> $DATE_BEGIN,
											'DATE_END'	=> $DATE_END,	
											'AHREF'		=> $AHREF,
											'DATESYSTEM'=> $DATESYSTEM
		));
	}
	unset($fss);
}
	header("refresh: 1; url = http://fss24.ru/lib/down1.php"); 
?>