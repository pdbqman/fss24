<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Отключает КЕШ -->
	<meta http-equiv="Cache-Control" content="private">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<style>
body{
	font-size:12px;
}
</style>	
<?php
// --->> БАЗА ДАННЫХ
# Подключаем БД
include_once  __DIR__ . "/class/data_base.php";
// <<--- БАЗА ДАННЫХ
# Парсинг сайта portal.fss.ru
include_once  __DIR__ . "/class/PortalFssRu.php";
# Подключаем разного рода функции
include_once  __DIR__ . "/class/other_fss.php";
# Класс для работы со шлюзом приема расчетов по форме 4-фсс
include_once  __DIR__ . "/class/F4_FSS.php";

// ПЕРЕМЕННЫЕ
$year_m			= 	OtherFssRu::this_kvartal_and_year();

?>
<div class="container-fluid mt-2" style="max-width:1200px;">
    <div class="row">
<form action="./doverka.php" method="post">
<!---->
	<button class="button red" name="download_all" value="1" type="submit" style="float:left;">Скачать доверки</button>
<!---->
</form>
    </div>
</div>

<div class="container-fluid mt-5" style="max-width:1200px;">
    <div class="row">
	<?php
	$sql="
	SELECT 	a.REG_NUM, a.KPS_NUM, a.PHONE, a.EMAIL, a.DATETIME, 
			b.STATUS, b.DATE_END, b.AHREF
	FROM 
		`null_xml` as a LEFT JOIN `doverka` as b ON a.REG_NUM = b.REG_NUM
	WHERE 
		a.YEAR= :YEAR 
		AND a.KVARTAL=:KVARTAL
		AND a.OPLATA=:OPLATA
		AND a.F4=:F4
	ORDER BY a.DATETIME
	";
	$item = DB::getAll($sql, array(	'YEAR'		=> $year_m['YEAR'], 
									'KVARTAL' 	=> $year_m['KVARTAL'],
									'OPLATA'	=> 1,
									'F4'		=> 0
								));
	//print_r($item);
	//echo count($item);
	if(count($item)<=0)echo "Новые отчеты не найдены<br />";
	else{
		# Класс для работы со шлюзом приема расчетов по форме 4-фсс
		$f4_fss = new F4_FSS();
		
		echo '<table class="table table-striped table-sm table-hover">
		<thead>
		<tr>
		  <th scope="col">#</th>
		  <th scope="col">Регномер</th>
		  <th scope="col">Отделение</th>
		  <th scope="col">Телефон</th>
		  <th scope="col">Время сдачи</th>
		  <th scope="col">Статус</th>
		  <th scope="col">Дата окон.</th>
		  <th scope="col">pdf</th>
		  <th scope="col">Год</th>
		  <th scope="col">Кв.</th>
		  <th scope="col">Статус</th>
		  <th scope="col">Код ошибки</th>
		  <th scope="col">Ошибка</th>
		  <th scope="col">Начало сдачи</th>
		  <th scope="col">Конец сдачи</th>
		  <th scope="col">Отделение</th>
		</tr>
		</thead>
		<tbody>';
		$i=0;
		foreach($item as $row){
			$i=$i+1;
			$time = strtotime($row['DATETIME']);
			$DATETIME = date("d.m.y H:i", $time);
			
			
			$time = strtotime($row['DATE_END']);
			$DATE_DOV= date("d.m.Y", $time);
			
			# Провекра статуса отчета страхователя за год (выводим таблицу полностью)
			$f4_fss->check_one_regnum($row['REG_NUM'], $regArr);
			
			$adress_ssilki='<a href="https://fss24.ru/lib/download_xml.php?REG_NUM='.$row['REG_NUM'].'">'.$row['REG_NUM'].'</a>';
			echo "<tr>
				<td>".$i."</td>
				<td>".$adress_ssilki."<br>".$row['REG_NUM']."</td>
				<td>".$row['KPS_NUM']."</td>
				<td>".$row['PHONE']."<br>".$row['EMAIL']."</td>
				<td>".$DATETIME."</td>
				<td>".$row['STATUS']."</td>
				<td>".$DATE_DOV."</td>
				<td>".$row['AHREF']."</td>
				<td>".$regArr[1]."</td>
				<td>".$regArr[2]."</td>
				<td><a href=\"http://f4.fss.ru/fss/statusreport?id=".$regArr[0]."\" target=\"_blank\">".$regArr[3]."</a></td>
				<td>".$regArr[4]."</td>
				<td>".$regArr[5]."</td>
				<td>".$regArr[6]."</td>
				<td>".$regArr[7]."</td>
				<td>".$regArr[8]."</td>
				
			</tr>";
		}
		echo "</tbody></table>";
	}
	?>
    </div>
</div>
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</body>
</html>