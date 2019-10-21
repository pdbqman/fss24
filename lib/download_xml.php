<?php
# Подключаем разного рода функции
include  __DIR__ . "/class/other_fss.php";
# Подключаем БД
include  __DIR__ . "/class/data_base.php";
// ПЕРЕМЕННЫЕ
$year_m			= 	OtherFssRu::this_kvartal_and_year();
// Имя файла
$fileName = $_GET['REG_NUM']."_".$year_m['YEAR']."_".$year_m['KVARTAL'].".xml";
// Проверим есть ли запись за этот квартал уже в базе?
$row = DB::getRow("SELECT * FROM `null_xml` WHERE `REG_NUM` = :REG_NUM AND `YEAR` = :YEAR AND `KVARTAL` = :KVARTAL"
					,array('REG_NUM' => $_GET['REG_NUM'], 'YEAR' => $year_m['YEAR'], 'KVARTAL' => $year_m['KVARTAL']) 
);
//print_r($row);
// Если записи не было заводим ее
if( !empty($row) ){
	$CADDR 	= iconv("utf-8","windows-1251", $row['CADDR']);
	$CEO 	= iconv("utf-8","windows-1251", $row['CEO']);
	$NAME 	= iconv("utf-8","windows-1251", $row['NAME']);
	
	$fileData = '<?xml version="1.0" encoding="WINDOWS-1251"?><?F4FORM version="0.93" appname="portal.fss.ru"?><F4REPORT xmlns="http://fz122.fss.ru" xmlns:xsd="http://www.w3.org/2001/XMLSchema-instance" xsd:schemaLocation="http://fz122.fss.ru http://fz122.fss.ru/doc/f4form_2017_3.xsd">
<TITLE 
	REG_NUM="'.$row['REG_NUM'].'" 
	KPS_NUM="'.$row['KPS_NUM'].'" 
	YEAR_NUM="'.$year_m['YEAR'].'" 
	QUART_NUM="'.$year_m['KVARTAL'].'" 
	LIKV="'.$row['LIKV'].'" 
	NAME="'.$NAME.'"
	INN="'.$row['INN'].'"
	KPP="'.$row['KPP'].'"
	PHONE="'.$row['PHONE'].'"
	CADDR="'.$CADDR.'"
	EMAIL="'.$row['EMAIL'].'"
	OKVED="'.$row['OKVED'].'" 
	T1R1C2="'.$row['T1R1C2'].'" 
	CEO="'.$CEO.'" 
	CRE_DATE="'.date('Y-m-d').'"/>
<F4INFO RATE_MIS="'.$row['RATE_MIS'].'"></F4INFO>
</F4REPORT>';
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="' . $fileName . '"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Pragma: public');
	header('Content-Length: ' . strlen($fileData));
	ob_clean();
	flush();
	echo $fileData;
	exit;
}
?>