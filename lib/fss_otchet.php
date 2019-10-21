<style>
#manuallyf4{
	padding:0;
	margin:0;
	heigth:auto;
	display: inline-block;
}
.custom-file-label{text-align:left;}
.custom-file-label::after {
		content: "Обзор" !important;
}
#manuallyf4 form{
	width:600px;
	display: inline-block;

}
.alert{
	margin:5px 0 !important;
	display: inline-block;
	width:600px;
	padding: 3px 10px;
	font-size:14px;
	text-align:left;
}
#span_oplata{
	padding:0px;
}
#oplata_f4fss{
	padding-left:45px;
}
#span_oplata i.fa{
	position:absolute;
	margin-top:15px;
	margin-left:8px;
}
#form_2{
	margin-top:-100px;
	height:1px;
	overflow:hidden;
}
.good{
	padding: 3px 20px 3px 10px;
	font-size:13px;
	text-align:left;
}
</style>
<div class="container text-center mt-5">
<div id="manuallyf4" >
	<h2>Загрузить готовые XML отчеты по форме 4-ФСС</h2>
	<form role="form" action="" method="POST" id="form_1" class="mb-3" enctype="multipart/form-data"><!--index.php?page=fss_otchet-->
		<div class="form-row">
			<div class="col-md-6 mb-3">
				<label for="email">Email адрес</label>
				<input type="email" class="form-control" name="email" id="email" placeholder="name@mail.ru">
				<small id="emailHelp" class="form-text text-muted">На ваш Email мы отправим квитанции</small>
			</div>
			<div class="col-md-6 mb-3">
				<label for="phone">Номер мобильного телефона</label>
				<input type="tel" class="form-control" name="phone" id="phone" placeholder="+7 (917) 123 45 67">
				<small id="emailHelp" class="form-text text-muted">При возникновении вопросов свяжемся с Вами</small>
			</div>
		</div>
		<div class="form-group">
			<div class="custom-file">
			  <input type="file" id="fileMulti" name="fileMulti[]" class="custom-file-input" multiple>
			  <label class="custom-file-label" for="customFileLang" id="label_file">Выберите файлы xml...</label>
			</div>
		</div>
		<input type="submit" name="button_f4fss" id="button_f4fss" class="btn btn-primary btn-lg" value="Сдать отчеты">
	</form>

	<form role="form" id="form_2" action="index.php?page=oplata&p=<?php echo base64_encode(count($_FILES['fileMulti']['name']));?>&t=<?php echo base64_encode($_POST['phone']);?>" method="POST">
		<!--input type="submit" name="oplata_f4fss" id="oplata_f4fss" class="btn btn-danger btn-lg invisible" style="margin-top:-110px;" value="Перейти к оплате">
		<i class="fa fa-cc-visa" aria-hidden="true"></i-->
		<span class="btn btn-danger btn-lg invisible" id="span_oplata">
			<i class="fa fa-cc-visa"></i>
			<input type="submit" name="oplata_f4fss" id="oplata_f4fss" class="btn btn-danger btn-lg" value="Перейти к оплате">
		</span>
		<small id="emailHelp" class="form-text text-muted">Оплата через сервис Яндекс</small>
	</form>
</div>
</div>
<div class="container text-center"><h4 id="output_js_h4"><!-- Здесь будет текст --></h4><div id="outputMulti"></div></div>
<div class="container text-center"><div id="output_php">
<?php
function validate_mu_input( $input_name ){
	$valid_name = trim($input_name);
	// вырезаем теги
	$valid_name = strip_tags($valid_name);
    //конвертируем специальные символы в мнемоники HTML
	$valid_name = htmlspecialchars($valid_name,ENT_QUOTES);
	return $valid_name;
}
if(isset($_POST['button_f4fss'])){
	//base64_decode - декодирует данные, кодированные MIME base64
	//base64_encode - кодирует данные в MIME base64
	if($_FILES['fileMulti']['error'][0]== 4) {
		echo "<span class='alert alert-warning' style='padding:10px;'>Необходимо выбрать файлы!</span>"; 
	}
	else{
		if(!empty($_POST["email"]) && !empty($_POST["phone"]) && isset($_FILES['fileMulti'])){
			# Email
			$email=validate_mu_input( $_POST["email"] );
			# ТЕЛЕФОН
			$phone=validate_mu_input( $_POST["phone"] );
			// Замена символов
			$search  = array('+', '(', ')', ' ');
			$replace = '';
			$phone = str_replace($search, $replace, $phone);
			if(strlen($phone) != 0 && strlen($email) != 0 && $_FILES){
				//echo "Email: ". $email . "</br>";
				//echo "Phone: ". $phone . "</br>";
				//print_r($_FILES);
				# СОЗДАЕМ ДИРЕКТОРИЮ для пользователя по номеру телефона
				$path = "./upload/".date('Y_m_d')."_".$phone;
				#Сначала проверим, может данная папка уже создана
				if (!file_exists($path)){
					#Создаем директорию
					if (!mkdir($path , 0777, true)){
						die('Не удалось создать директории...');
					}
				}
				#Копируем файлы
				for($i=0;$i<count($_FILES['fileMulti']['name']);$i++) {
					if( $_FILES['fileMulti']['type'][$i] == "text/xml" ){
						if( move_uploaded_file( $_FILES['fileMulti']['tmp_name'][$i], $path."/".mb_convert_encoding( $_FILES['fileMulti']['name'][$i],  "Windows-1251", "utf-8" ) ) ){
							if($i == 0){
								echo "<h4>Файлы успешно загружены!</h4>";
								echo '<script type="text/javascript">
									document.getElementById("button_f4fss").className = "btn btn-success btn-lg invisible";
									document.getElementById("span_oplata").className = "btn btn-danger btn-lg visible";
									
									document.getElementById("email").disabled = true;
									document.getElementById("phone").disabled = true;
									document.getElementById("fileMulti").disabled = true;
									document.getElementById("label_file").disabled = true;
									
									document.getElementById("label_file").style.color = "green";
									document.getElementById("label_file").innerHTML = "Файлы загружены! Необходимо оплатить!";
									
									document.getElementById("form_2").style.height = "auto";
								</script>';
							}
							echo "<span class='alert alert-primary'><i class='fa fa-upload good' aria-hidden='true'></i>". ($i+1) .". Успешно загружен: <b>".$_FILES['fileMulti']['name'][$i]."</b></span>";
						}
						else echo "<br />Ошибка при загрузке!";
					}
					else{
						echo "<span class='alert alert-danger'><i class='fa fa-times-circle good' aria-hidden='true'></i>
							". ($i+1) .". Несоответствие формата! Файл <b>".$_FILES['fileMulti']['name'][$i]."</b> не загружен!
							</span>";
					}
			   }
			}
		}
	}
}
?>
</div></div>