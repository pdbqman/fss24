<style>
.alert i{
	padding-right:20px;
}
</style>
<?php
# Подключаем разного рода функции
include  __DIR__ . "/class/other_fss.php";
// ПЕРЕМЕННЫЕ
$year_m			= 	OtherFssRu::this_kvartal_and_year();
$last_year_m	= 	OtherFssRu::last_kvartal_and_year();

$DATETIME		= 	date("Y-m-d H:i:s");
$default_sum	= 	2; // сумма для оплаты
$old_f4_date	= 	null;
$aktcia			=	null;// Действует ли акция?
$upolnomochka	= 	null;// Тариф уполномочка?
if( isset($_GET['p']) && !empty($_GET['p']) ){	
	$message_txt = "";
	$count = base64_decode($_GET['p']);
	if ($count == 1) 
		$message_txt = $count." расчет";
	if ($count >= 2 && $count <= 4) 
		$message_txt = $count." расчета";
	if ($count >= 5) 
		$message_txt = $count." расчетов";
	//echo "Количество отчетов: ".base64_decode($_GET['p']);
	if ($count == 1){
		$default_sum 	= 249;
		$aktcia			= 0;
	}
	if ($count > 1){
		$default_sum 	= 99*$count;
		$upolnomochka	= 1;
	}
}
if( isset($_GET['t']) && !empty($_GET['t']) ){
	# ТЕЛЕФОН
	$PHONE		= OtherFssRu::validate_input( base64_decode($_GET["t"]) );
	$targets	= $PHONE."_".$count;
	//echo "</br>Телефон: ".$PHONE;
}
if( isset($_POST['button_create_f4']) && !empty($_POST['button_create_f4']) ){
	$message_txt	= "1 расчет";
	$REG_NUM		= OtherFssRu::validate_input($_POST['REG_NUM_']);
	$NAME			= htmlspecialchars(strip_tags($_POST['NAME']), ENT_COMPAT, 'cp1251'); // сделаем это еще раз на всякий
	$PHONE			= OtherFssRu::validate_input($_POST['PHONE']);
	$EMAIL			= OtherFssRu::validate_input($_POST['EMAIL']);
	$INN			= OtherFssRu::validate_input($_POST['INN']);
	
	if( isset($_POST['KPP']) && !empty($_POST['KPP']) ){
		$KPP			= OtherFssRu::validate_input($_POST['KPP']);
	} else $KPP = "";
	$KPS_NUM		= OtherFssRu::validate_input($_POST['KPS_NUM']);
	$OKVED			= OtherFssRu::validate_input($_POST['OKVED']);
	$RATE_MIS		= OtherFssRu::validate_input($_POST['RATE_MIS']);
	$CADDR			= htmlspecialchars(strip_tags($_POST['CADDR']), ENT_COMPAT, 'cp1251'); // сделаем это еще раз на всякий
	$CEO			= htmlspecialchars(strip_tags($_POST['CEO']), ENT_COMPAT, 'cp1251'); // сделаем это еще раз на всякий
	$T1R1C2			= OtherFssRu::validate_input($_POST['T1R1C2']);
	$LIKV			= OtherFssRu::validate_input($_POST['LIKV']);
	$targets		= $REG_NUM."_".$year_m['YEAR']."_".$year_m['KVARTAL']."_".$PHONE;
	// -------------------------------------------------------
	// Проверим есть ли запись за этот квартал уже в базе?
	$item = DB::getRow("SELECT * FROM `null_xml` WHERE `REG_NUM` = :REG_NUM AND `YEAR` = :YEAR AND `KVARTAL` = :KVARTAL"
						,array('REG_NUM' => $REG_NUM, 'YEAR' => $year_m['YEAR'], 'KVARTAL' => $year_m['KVARTAL']) 
	);
	//print_r($item);
	
	// Если записи не было заводим ее
	if( empty($item) ){
		if($PROGRAM_HELP)echo "</br>Если записи не было заводим ее";
		$sql = "INSERT INTO `null_xml` SET 
				`REG_NUM` = :REG_NUM,
				`YEAR`	= :YEAR,	`KVARTAL`	= :KVARTAL,		`DATETIME` 	= :DATETIME,
				`NAME`	= :NAME,	`PHONE`		= :PHONE,		`EMAIL`		= :EMAIL,
				`INN`	= :INN,		`KPP`		= :KPP,			`KPS_NUM`	= :KPS_NUM,
				`OKVED` = :OKVED,	`RATE_MIS`	= :RATE_MIS,	`CADDR` 	= :CADDR,
				`CEO`	= :CEO,		`T1R1C2`	= :T1R1C2,		`LIKV` 		= :LIKV
		";
		$insert_id = DB::add($sql, array('REG_NUM' => $REG_NUM, 'YEAR'	=> $year_m['YEAR'], 
						'KVARTAL' => $year_m['KVARTAL'], 'DATETIME' => $DATETIME,
						'NAME'	=> $NAME,	'PHONE' 	=> $PHONE,		'EMAIL'		=> $EMAIL,
						'INN'	=> $INN,	'KPP'		=> $KPP,		'KPS_NUM'	=> $KPS_NUM,
						'OKVED' => $OKVED,	'RATE_MIS'	=> $RATE_MIS,	'CADDR' 	=> $CADDR,
						'CEO' 	=> $CEO, 	'T1R1C2' 	=> $T1R1C2, 	'LIKV' 		=> $LIKV
		));
		if($PROGRAM_HELP)
			if($insert_id)echo "</br>Ваш отчет успешно создан ".$insert_id;
	}
	else{ // Или обновляем ее
		if($PROGRAM_HELP)echo "</br>Обновляем вашу запись ".$item['id'];
		$sql = "UPDATE `null_xml` SET 
				`NAME`	= :NAME,	`PHONE`		= :PHONE,		`EMAIL`		= :EMAIL,
				`INN`	= :INN,		`KPP`		= :KPP,			`KPS_NUM`	= :KPS_NUM,
				`OKVED` = :OKVED,	`RATE_MIS`	= :RATE_MIS,	`CADDR` 	= :CADDR,
				`CEO`	= :CEO,		`T1R1C2`	= :T1R1C2,		`LIKV` 		= :LIKV,
				`DATETIME` = :DATETIME
				
				WHERE `id` = :id";
		$update = DB::set($sql, array('id' => $item['id'], 'DATETIME' => $DATETIME,
						'NAME'	=> $NAME,	'PHONE' 	=> $PHONE,		'EMAIL'		=> $EMAIL,
						'INN'	=> $INN,	'KPP'		=> $KPP,		'KPS_NUM'	=> $KPS_NUM,
						'OKVED' => $OKVED,	'RATE_MIS'	=> $RATE_MIS,	'CADDR' 	=> $CADDR,
						'CEO' 	=> $CEO, 	'T1R1C2' 	=> $T1R1C2, 	'LIKV' 		=> $LIKV
		));
		if($PROGRAM_HELP)
			if ( $update == true)echo "</br>Ваш отчет ".$REG_NUM." обновлен!";
			else echo "Обновить ваш отчет не удалось!";
	}
	// -------------------------------------------------------
	
	// Высчитываем оплату
	$item = DB::getRow("SELECT * FROM `null_xml` WHERE `REG_NUM` = :REG_NUM AND `OPLATA` = :OPLATA ORDER BY `DATETIME` DESC LIMIT 1", array('REG_NUM' => $REG_NUM, 'OPLATA' => 1));
	//print_r($item);
	
	// Если оплаты никогда не было, то 50 руб
	if( empty($item) ){
		if ($DATETIME <= $last_year_m['YEAR']."-".$last_year_m['KVARTAL']."-20"){
			$default_sum 	= 49;
			$aktcia			= 1;
		}
		else {
			$default_sum 	= 249;
			$aktcia			= 0;
			if($PROGRAM_HELP)echo "</br>Акция действует только до 20 числа последнего отчетного месяца";
		}
	}
	else{// Если нашли старую оплату, то 250 руб
		$default_sum = 249;
		// Запомним дату последнего оплаченного отчета
		$old_f4_date = $item['DATETIME'];
	}
	// -------------------------------------------------------
}                          
?>
<div class="container text-center mt-5">
	<div id="manuallyf4" class="mb-4">
		<h2 class="mb-2">Оплата расчетов 4-ФСС</h2>
		<p class="mb-4">Воспользуйтесь безопасной оплатой от Яндекса, с гарантией возврата средств</p>
		<div class="d-block pb-4">
			<span class="alert alert-primary d-inline" role="alert">
				<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
				Вы сдаете <?php echo $message_txt; ?> по форме 4-ФСС. Необходимо произвести оплату.
			</span>
		</div>
		<?php
		if( !empty($old_f4_date) ){
			echo '<div class="d-block pt-2"><span class="alert alert-info d-inline" role="alert">
					<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
					Ваш предыдущий оплаченный отчет от '.date("d.m.Y H:i", strtotime($old_f4_date))
				.'</span></div>';
		}
		else {
			if( $aktcia	=== 1 )
				echo '<div class="d-block pt-2"><span class="alert alert-success d-inline" role="alert">
							<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
							Акция от сайта FSS24.ru - 49 руб. за отчет</span></div>';
			if( $aktcia	=== 0 )
				echo '<div class="d-block pt-2"><span class="alert alert-danger d-inline" role="alert">
							<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
							По условию акция "Первый отчет - 49 руб." действует только до 20 числа текущего месяца </span></div>';
		}
		if( $upolnomochka === 1)
			echo '<div class="d-block pt-2"><span class="alert alert-success d-inline" role="alert">
			<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
			Выбран тариф "Уполномочка" 99 руб. за 1 отчет</span></div>';
		?>
	</div>

	<iframe src="https://money.yandex.ru/quickpay/shop-widget?writer=seller
			&targets=<?php echo $targets;?>
			&targets-hint=
			&default-sum=<?php echo $default_sum;?>
			&button-text=11
			&payment-type-choice=on
			&mobile-payment-type-choice=on
			&phone=on
			&hint=
			&successURL=http%3A%2F%2Ffss24.ru%2Findex.php%3Fpage%3Doplata_yandex
			&quickpay=shop
			&account=41001991764737" 
			width="423" height="222" frameborder="0" allowtransparency="true" scrolling="no">
	</iframe>
	<small id="emailHelp" class="form-text text-muted">Во время оплаты Яндекс потребует ввести номер телефона, это необходимо для гарантии возврата денежных средств</br>Если Вы оплачиваете с мобильного телефона, то вводите тот номер, который указывали при заполнении отчета.</br>Спасибо!</small>

	<a href="./lib/download_xml.php?REG_NUM=<?php echo $REG_NUM;?>" class="btn btn-primary mt-3">скачать XML файл отчета</a>
	<br>
	<a href="./doverka.doc" class="btn btn-info mt-3">скачать бланк доверенности</a>
</div>