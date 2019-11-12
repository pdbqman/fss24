<?php
	// -----> БЛОК ПРОВЕРКИ ДЛЯ AJAX ВЫЗОВА
	if(isset($_POST["tbl_name"])){
		$tbl_name 	= $_POST["tbl_name"];
		# Сессии 
		session_start();
		# Установим часовой пояс
		ini_set('date.timezone', 'Asia/Yekaterinburg');
		// --->> БАЗА ДАННЫХ
		# Подключаем БД
		include  __DIR__ . "/class/data_base.php";
		// <<--- БАЗА ДАННЫХ
	}
	$tbl_update = false;
	if(isset($_POST["tbl_update"])){
		if ($_POST["tbl_update"] == true){
			$tbl_update = true;
		}
	}	
	// ---< БЛОК ПРОВЕРКИ ДЛЯ AJAX ВЫЗОВА
?>
<style>
#db_list{
	padding: 3px;
	display: inline-block;
	background:#454d55;
	width:1100px;
	margin-bottom:20px;
}
#db_list table{margin-bottom:0px;}

.my_info_top{
	position: absolute;
	top: 5%;
	font-size: 13px;
	margin-left: 5px;
}
.my_info_bottom{
	position: absolute;
	bottom: 5%;
	font-size: 13px;
	margin-left: 5px;
}

</style>
<?php
	# Конфигурация таблиц
	if(isset($_GET["tbl_name"]))
		$tbl_name 	= $_GET["tbl_name"];

	include  __DIR__ . "/class/db_config.php";
	$db_config = new db_config( $tbl_name, $tbl_update );
?>
<!-- Begin ------ БЛОК С КОНФИГУРАЦИЯМИ ТАБЛИЦ ------ -->
<div class="container-fluid mt-5">
   <div class="row">
      <div class="col">
		<?php $db_config->html_header_full(); ?>
	  </div>
	</div>
</div>
<?php 
	# Удаляем
	unset($db_config);
?>
<!-- End --------------------------------------------- -->

<div class="container-fluid mt-2"> 
   <div class="row"> 
      <div class="col">
		<div id='db_list'>
			<?php 
				$method = "display_" . $tbl_name;
				class_parser::$method(); 
			?>
		</div>
	  </div>
	</div>
</div>