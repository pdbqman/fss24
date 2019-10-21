<!-- HTML -->
<?php
	// Постоянный блок справки
	include __DIR__ . "/main_page/4.check_status_reg.php";
?>
<style>
#manuallyf4{
	border: 10px solid #d1d1d1;
	padding:0;
	margin:0;
	heigth:auto;
	display: inline-block;
}
table{
	font-size:14px;
}
.container i.fa{
	padding-right:15px;
}
.container  .alert{
	padding-left: 10px;
}
</style>
<div class="container text-center mt-5">
	<div id="manuallyf4" >
	<?php
		if(		isset($_POST['regNumber']) 
			&& 	strlen(str_replace(" ", "", $_POST['regNumber'])) == 10
		){
			# Класс для работы со шлюзом приема расчетов по форме 4-фсс
			include_once  __DIR__ . "/class/F4_FSS.php";

			# Класс для работы со шлюзом приема расчетов по форме 4-фсс
			$f4_fss = new F4_FSS();
			//$regNumber	= 	"5801274052";
			#--------------------------------------------------
			# Все отчеты за год
			#--------------------------------------------------
			# Провекра статуса отчета страхователя за год (выводим таблицу полностью)
			$table = $f4_fss->check_regnum( str_replace(" ", "", $_POST['regNumber']) );
			if ( strlen($table) < 900){
					// Это основное уведомление (синий)
					// Убираю границу
					echo '
						<style>
							#manuallyf4{
								border: 0px solid #d1d1d1;
							}
						</style>';
					// Вывожу сообщение
					echo '<div class="alert alert-primary" role="alert">
							Отчеты на сайте ФСС не найдены.
							</br> Проверьте правильность Регистрационного номера ФСС <b>'. 
							str_replace(" ", "", $_POST['regNumber']).'</b> и повторите поиск позднее
					</div>';
			}
			else{
				echo'
					<style>
						.alert{
							margin-top:-80px;
							position:absolute;
							left:50%;
							width:500px;
							margin-left:-250px;
						}
					</style>
					<div class="alert alert-success d-inline" role="alert">
						<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
						Данные по Регномеру <b>'. str_replace(" ", "", $_POST['regNumber']) .'</b> успешно найдены!
					</div>';
				echo $table;
			}
		}
		else{
			// Убираю границу
			echo '<style>#manuallyf4{border: 0px solid #d1d1d1;}</style>';
			
			// Это уведомление об опасности (красный)
			// Вывожу сообщение
			echo '<div class="alert alert-danger mb-0" role="alert" >
					<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
					Вы не ввели регномер!
				  </div>';
		}
	?>
	</div>
</div>