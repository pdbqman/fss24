<style>
table {
        width:100%;
        text-align:center;
        border-collapse:collapse;
        border-bottom:1px solid #E5E5E5;
        border-top:1px solid #E5E5E5;
        margin-bottom:20px;
}
table td {
        padding:4px 6px;
        border-right:1px solid #E5E5E5;
}
table tr:hover {
        background-color:#E6E5FF;
}
table th {
        padding: 8px 5px;
        color: #1f1f1f;
        text-align: left;
        border-right: 1px solid #E5E5E5;
        font-weight: normal;
        background: #efefef;
}
table tr {
        border-left:1px solid #E5E5E5;
        border-top:1px solid #E5E5E5;
}
</style>
<?php
/*********************************************************************
$mysqltime1 = date ("Y-m-d H:i:s","04.04.2016 15:10");
$a = strptime('04.04.2016 15:10', '%d.%m.%Y'); 
var_dump($a);
$timestamp = mktime(0, $a['tm_min'], $a['tm_hour'], $a['tm_month'], $a['tm_day'], 1900 + $a['tm_year'],0);
echo $timestamp." - ".date ("Y-m-d H:i:s",$timestamp);*/

$OPLATA="1";
$PROVERKA_F4FSS="0";
$DOWNLOAD_XML="0";
$F4="0";
$DATETIME=date("Y-m-d");
include  __DIR__ . "/class/simple_html_dom.php";

/**********************************************************************/
$YEAR=date("Y");
$m = date("m");
if($m == '01')$YEAR=intval($YEAR)-1;
if($m == '02' or $m == '03' or $m=='04'){
	$KVARTAL = '03';
	$KVARTAL_2 = 1;
}
if($m == '05' or $m == '06' or $m=='07'){
	$KVARTAL = '06';
	$KVARTAL_2 = 2;
}
if($m == '08' or $m == '09' or $m=='10'){
	$KVARTAL = '09';
	$KVARTAL_2 = 3;
}
if($m == '11' or $m == '12' or $m=='01'){
	$KVARTAL = '12';
	$KVARTAL_2 = 4;
}
/**********************************************************************/
include ('./config_db.php');
$sql="
SELECT 
  `id`, `YEAR`, `KVARTAL`, 
  `OPLATA`, `DOWNLOAD_XML`, 
  `PROVERKA_F4FSS`, 
  `REG_NUM`, `KPS_NUM`, 
  `INN`, `OGRN`, `OKVED`, 
  `RATE_MIS`, `SHIFR`, `PHONE`, 
  `CADDR`, `DATETIME`, 
  `NAME`, `NAME_ORG`
FROM `null_xml` 
WHERE 
  YEAR=:YEAR 
  AND OPLATA=:OPLATA
  AND KVARTAL=:KVARTAL
  AND DOWNLOAD_XML=:DOWNLOAD_XML
  AND F4=:F4
ORDER BY DATETIME
";
$stmt=$db->prepare($sql);
$stmt->bindParam(':KVARTAL', $KVARTAL);
$stmt->bindParam(':YEAR', $YEAR);
$stmt->bindParam(':OPLATA', $OPLATA);
$stmt->bindParam(':DOWNLOAD_XML', $DOWNLOAD_XML);
$stmt->bindParam(':F4', $F4);
$stmt   -> execute();
$result =  $stmt->fetchAll();
/**********************************************************************/
$i=0;
echo "<h3><u>Отправить отчеты</u></h3>";

?>
<form action="./doverka.php" method="post">
<!---->
	<button class="button red" name="download_all" value="1" type="submit" style="float:left;">Скачать доверки</button>
<!---->
</form>
<br /><br />
<form action="" method="post" class="null_xml">
<!---->
	<button class="button red" name="download_all" value="1" type="submit" style="float:left;">Скачать новые отчеты</button>
<!---->
</form>
<br /><br />
<form action="" method="post" class="null_xml">
<!---->
	<button class="button red" name="f4_all" value="1" type="submit" style="float:left;">Отправить новые отчеты</button>
<!---->
</form>
<br /><br />

<?php
if(!empty($_POST['f4_all'])){
	$dir = realpath('./nullxmltof4'); 
	$f = scandir($dir);
	$nullxmltof4=array();
	foreach ($f as $file){
		if(preg_match('/\.(ef4)/', $file)){
			echo $file.'<br/>';
			$nullxmltof4[] = $file;
		}
	}
	var_dump($nullxmltof4);
	$target_url = 'http://f4.fss.ru/uploadfile';//'http://f4.fss.ru/fss/upload';//
	foreach($nullxmltof4 as $asd){		
		$filename='/nullxmltof4/' . $asd;
		$file_name_with_full_path=__DIR__ .$filename;

		$post = array('filein'=>'@'.$file_name_with_full_path . ';filename=' . $asd);//.';type=application/xhtml+xml');
		//$post = array('name="filein";filename=' . $asd);//.';type=application/xhtml+xml');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$target_url);
		curl_setopt($ch, CURLOPT_HEADER, 1); // получать заголовки
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:61.0) Gecko/20100101 Firefox/61.0');
		curl_setopt($ch, CURLOPT_REFERER, 'http://f4.fss.ru/fss/upload');
		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Говорим скрипту, чтобы он следовал за редиректами которые происходят во время авторизации	
		$result=curl_exec ($ch);
		if (curl_errno($ch)) { 
            print "Error: " . curl_error($ch); 
        }
		else { 
            //var_dump($result);
			if (!empty($result)){
			  $html = str_get_html($result);
			  if (!empty($html)) {
				/*$content = $html->find('.identifier');
				
				if (!empty($content)) {
				  foreach($content as $e){
					echo $e->outertext;
				  }
				}*/
				$IDENTIFIER="".$html->find('span[id=identifier]', 0)->innertext."";
				
				$id_regnum = explode("-", $IDENTIFIER);
				$stmt = $db->prepare("UPDATE null_xml SET IDENTIFIER=:IDENTIFIER WHERE REG_NUM=:REG_NUM AND YEAR=:YEAR AND KVARTAL=:KVARTAL");
				$stmt->bindParam(':YEAR', $YEAR);
				$stmt->bindParam(':KVARTAL', $KVARTAL);
				$stmt->bindParam(':REG_NUM', $id_regnum[4]);
				$stmt->bindParam(':IDENTIFIER', $IDENTIFIER);
				$stmt->execute();
				echo '<a href="http://f4.fss.ru/fss/statusreport?id='.$IDENTIFIER.'" target="_blank">'.$IDENTIFIER."</a><br />";
			  }
			}
            curl_close($ch); 
        } 
	}
}
/**********************************************************************/
if(!empty($_POST['download_all'])){
	if(empty($result))echo "новые отчеты не найдены<br />";
	else{
		foreach($result as $row){
			$CADDR = iconv("utf-8","windows-1251", $row['CADDR']);
			$CEO = iconv("utf-8","windows-1251", "ФИО");
			$NAME = iconv("utf-8","windows-1251", "ИП");
			$doc = '<?xml version="1.0" encoding="windows-1251"?><?F4FORM version="0.9" appname="portal.fss.ru"?><F4REPORT xmlns="http://fz122.fss.ru" xmlns:xsd="http://www.w3.org/2001/XMLSchema-instance" xsd:schemaLocation="http://fz122.fss.ru/doc/f4form_2015.xsd"><TITLE REG_NUM="'.$row['REG_NUM'].'" KPS_NUM="'.$row['KPS_NUM'].'" INN="0278195240" TaxType="'.$row['SHIFR'].'" TaxType2="00" TaxType3="00" T1R1C2="0" OGRN="1130280000355" QUART_NUM="'.$row['KVARTAL'].'" YEAR_NUM="'.$row['YEAR'].'" NAME="'.$NAME.'" CRE_DATE="2016-02-14" CEO="'.$CEO.'" CADDR="'.$CADDR.'"/><F4INF1 FssDebt="0" InsDebt="0"/><F4INFO OKVED="'.$row['OKVED'].'" RATE_MIS="'.$row['RATE_MIS'].'" T7R34C1="0" IS_INV="0"/></F4REPORT>';
			$name = $row['REG_NUM']."_".$row['YEAR']."_".$row['KVARTAL'].".xml";
			#название файла , если файл с таким именем уже есть он в моем примере перезапишется
			$path = __DIR__ . '/nullxml/' . $name;
			#константа __DIR__ содержит место текущего нахожения , тоесть где находится файл который вы запускаете 
			#состовляем строку полного пути нового файла
			file_put_contents($path,$doc);
			#помещаем наш документ в файл
			
			$DOWNLOAD_XML="1";
			$stmt = $db->prepare("UPDATE null_xml SET DOWNLOAD_XML=:DOWNLOAD_XML WHERE REG_NUM=:REG_NUM AND YEAR=:YEAR AND KVARTAL=:KVARTAL");
			$stmt->bindParam(':YEAR', $YEAR);
			$stmt->bindParam(':KVARTAL', $KVARTAL);
			$stmt->bindParam(':REG_NUM', $row['REG_NUM']);
			$stmt->bindParam(':DOWNLOAD_XML', $DOWNLOAD_XML);
			$stmt->execute();
		}
		echo "Отчеты закачены<br />";
	}
}
/**********************************************************************/
echo count($result);
if(count($result)<=0)echo "новые отчеты не найдены<br />";
else{
	echo "<table style='width:1200px;text-align:left;'><th>№</th><th>Регномер</th><th>Регномер</th><th>Телефон</th><th>Время сдачи</th>";
	foreach($result as $row){
		$i++;
		$adress_ssilki='<a href="https://fss24.ru/lib/download_xml.php?REG_NUM='.$row['REG_NUM'].'">'.$row['REG_NUM'].'</a> - <span style="font-size:10px;">'.$row['CADDR'].'</span>';
		echo "<tr><td>".$i."</td><td>".$adress_ssilki."</td><td>".$row['REG_NUM']."</td><td>".$row['PHONE']."</td><td>".$row['DATETIME']."</td></tr>";
	}
	echo "</table>";
}
/**********************************************************************/
echo "<h3><u>Проверка отчетов (только не сданные)</u><h3/>";
$sql="
SELECT 
  REG_NUM, 
  KPS_NUM, 
  INN, 
  OGRN, 
  OKVED, 
  RATE_MIS, 
  SHIFR, 
  PHONE, 
  CADDR,
  DATETIME,
  IDENTIFIER  
FROM `null_xml` 
WHERE 
  YEAR=:YEAR 
  AND OPLATA=:OPLATA
  AND KVARTAL=:KVARTAL
  AND PROVERKA_F4FSS=:PROVERKA_F4FSS
  AND F4=:F4
ORDER BY DATETIME
";
$stmt=$db->prepare($sql);
$stmt->bindParam(':KVARTAL', $KVARTAL);
$stmt->bindParam(':YEAR', $YEAR);
$stmt->bindParam(':OPLATA', $OPLATA);
$stmt->bindParam(':PROVERKA_F4FSS', $PROVERKA_F4FSS);
$stmt->bindParam(':F4', $F4);
$stmt   -> execute();
$result =  $stmt->fetchAll();
/**********************************************************************/
$u=0;
$urlTo1 = 'http://f4.fss.ru/fss/office'; // Куда данные послать
echo '<div style="text-align:left;display:inline-block;margin-top:10px;"><table class="contentTable" style="font-size:13px;"><tr>';
echo "
    <th>№</th>
    <th>Регномер</th>
    <th>Время сдачи</th>
	<th>Идентификатор файла ДО</th>
	<th>Идентификатор файла ПОСЛЕ</th>
	<th>Год</th>
	<th>Квартал</th>
	<th>Статус</th>
	<th>Код ошибки</th>
	<th>Ошибка</th>
	<th>Дата начала</th>
	<th>Дата завершения</th>
	<th>Направлено в ТО ФСС</th>
</tr>";
foreach($result as $row){
	$u++;
	$adress_ssilki='<a href="https://fss24.ru/lib/download_xml.php?REG_NUM='.$row['REG_NUM'].'">'.$row['REG_NUM'].'</a>';
	echo '<tr><td>'.$u.'</td><td>'.$adress_ssilki.'</td><td>'.$row['DATETIME'].'</td><td><a href="http://f4.fss.ru/fss/statusreport?id='.$row['IDENTIFIER'].'" target="_blank">'.$row['IDENTIFIER'].'</a></td>';
	$reg = $row['REG_NUM'];// Логин
	$post1 = 'regnum='.$reg.'&period=5';// POST данные авторизации (укажите правильно)

	$ch = curl_init(); // Инициализация сеанса
	curl_setopt($ch, CURLOPT_URL, $urlTo1);
	curl_setopt($ch, CURLOPT_HEADER, 1); // получать заголовки
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
	curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36');
	curl_setopt($ch, CURLOPT_REFERER, 'http://yandex.ru');
	curl_setopt($ch, CURLOPT_POST,1);
	//curl_setopt($ch, CURLOPT_COOKIEJAR,$_SERVER['DOCUMENT_ROOT'].'/cookiefile.txt'); // записываем/запоминаем куки
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post1); // куда посылаем пост первый раз
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Говорим скрипту, чтобы он следовал за редиректами которые происходят во время авторизации
	$result = curl_exec($ch);

	$array=array();//null
	$i = 0;
	if (!empty($result)) {
		$html = str_get_html($result);
		if (!empty($html)) {
			$content = $html->find('table', 3)->find('td');
			//echo $content;
			if (!empty($content)) {
			  foreach($content as $e){
				$array[$i] = strip_tags(trim($e->outertext));
				$i++;
			  }
			}
		}
	}

	if(count($array) > 9){
		if($array[12]=="Успешно" and $array[10]==$YEAR and $array[11]==$KVARTAL_2){
				$F4="1";
				$mysqltime1 = date ("Y-m-d H:i:s", $array[15]);
				$mysqltime2 = date ("Y-m-d H:i:s", $array[16]);
				
				$sqlup="UPDATE null_xml 
						SET F4=:F4, ID_F4=:ID_F4, YEAR_F4=:YEAR_F4,	KVARTAL_F4=:KVARTAL_F4, STATUS_F4=:STATUS_F4,
						NUMERROR_F4=:NUMERROR_F4, ERROR_F4=:ERROR_F4, DATE1_F4=:DATE1_F4, DATE2_F4=:DATE2_F4, RO_F4=:RO_F4
						WHERE REG_NUM=:REG_NUM AND YEAR=:YEAR AND KVARTAL=:KVARTAL";
				$stmt = $db->prepare($sqlup);
				$stmt->bindParam(':YEAR', $YEAR);
				$stmt->bindParam(':KVARTAL', $KVARTAL);
				$stmt->bindParam(':REG_NUM', $row['REG_NUM']);
				$stmt->bindParam(':F4', $F4);
				$stmt->bindParam(':ID_F4', $array[9]);
				$stmt->bindParam(':YEAR_F4', $array[10]);
				$stmt->bindParam(':KVARTAL_F4', $array[11]);
				$stmt->bindParam(':STATUS_F4', $array[12]);
				$stmt->bindParam(':NUMERROR_F4', $array[13]);
				$stmt->bindParam(':ERROR_F4', $array[14]);
				$stmt->bindParam(':DATE1_F4', $mysqltime1);
				$stmt->bindParam(':DATE2_F4', $mysqltime2);
				$stmt->bindParam(':RO_F4', $array[17]);
				$stmt->execute();
				
				$array[9] = '<a href="http://f4.fss.ru/fss/statusreport?id='.$array[9].'" target="_blank">'.$array[9].'</a>';
				echo '<td>'.$array[9].'</td>';
				echo '<td>'.$array[10].'</td>';
				echo '<td>'.$array[11].'</td>';
				echo '<td>'.$array[12].'</td>';
				echo '<td>'.$array[13].'</td>';
				echo '<td>'.$array[14].'</td>';
				echo '<td>'.$array[15].'</td>';
				echo '<td>'.$array[16].'</td>';
				echo '<td>'.$array[17].'</td>';
		}
		else{
			for($y=9;$y<18;$y++){
				if($y%9==0){
				$array[$y] = '<a href="http://f4.fss.ru/fss/statusreport?id='.$array[$y].'" target="_blank">'.$array[$y].'</a>';
				echo "<td>$array[$y]</td>";
				}
				else echo "<td>$array[$y]</td>";
			}
		}
		echo "</tr>";
	}
	else echo '<td>ничего не найдено</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>';
}
echo "</table></div>";
/*
$DOWNLOAD_XML="1";
$stmt = $db->prepare("UPDATE null_xml SET DOWNLOAD_XML=:DOWNLOAD_XML WHERE REG_NUM=:REG_NUM AND YEAR=:YEAR AND KVARTAL=:KVARTAL");
$stmt->bindParam(':YEAR', $YEAR);
$stmt->bindParam(':KVARTAL', $KVARTAL);
$stmt->bindParam(':REG_NUM', $row['REG_NUM']);
$stmt->bindParam(':DOWNLOAD_XML', $DOWNLOAD_XML);
$stmt->execute();
*/
?>