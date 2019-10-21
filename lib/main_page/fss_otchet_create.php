<style>
#manuallyf4{
	border: 10px solid #d1d1d1;
	padding:20px 40px;
	margin:0;
	heigth:auto;
	display: inline-block;
	width: 700px;
	font-family: 'Open Sans',Arial,Helvetica,sans-serif;
}
label{
	font-weight: 700;
	display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
	color: #484848;
	font-size:14px;
}
#manuallyf4 em{color:red;font-weight: 700;}
#manuallyf4 p{font-size:13px;text-align:center;}
.container i.fa{
	padding-right:20px;
}
.alert{padding: 15px;}

</style>
<div id="check_status_reg">
	<h2>Создать нулевой расчет 4-ФСС</h2>
	<h5>Необходимо ввести свой Регистрационный номер ФСС, <b>данные заполнятся автоматически</b></h5>
	<div class = "form_input_reg center">
		<form class="form-inline" role="form" action="" method="POST">
		  <div class="form-group mx-sm-3 mb-2">
			<input type="text" class="form-control input-lg input__field--kyo" name="REG_NUM" id="REG_NUM" placeholder="0274 02 21 54" onfocus="this.className='input_focus'" onblur="this.className='input_text'"><!--onkeyup="this.value = this.value.replace (/\D/gi, '')" pattern="[0-9]{10}" -->
		  </div>
		  <input type="submit" id="button_f4fss" class="btn btn-lg btn-primary mb-2 button__field--kyo blue" value="Заполнить отчет">
		</form>
		<span class="input__label-content input__label-content--kyo">Введите регномер ФСС (<a href="index.php?page=inn_to_reg" target="_blank">я не знаю свой Регномер</a>)</span>
	</div>
</div>
<hr>
<?php

?>
<div id="hand_input" class="container text-center mt-4">
	<?php 
	if( empty( $regArr ) ){
				echo '<div class="row" style="display: block;">
					<div style="display: inline-block; text-align:justify; width:700px;">
						Пожалуйста, нажмите кнопку "Заполнить отчет" повторно, если данные все так же 
						<br>не загружаются, то заполните данные вручную (возможно проблемы с сервером).
						<br><br>!!! Необходимо учитывать, что если Вы не можете <a href="http://portal.fss.ru/fss/insurant/searchInn" target="_blank"><b>найти свой Регномер в базе ФСС</b></a>, 
						<br>то это может означать, что ФСС недавно поставил Вашу организацию на учет.  
						<br>В таком случае, заполнять отчет вручную не нужно, так как ФСС его не сможет принять, 
						<br>ввиду отсутствия Вашей организации в базе данных ФСС.
						<br><br>Для более детальной информации, Вы можете позвонить нам или в ваше отделение ФСС.
						<br>Приносим свои извинения за доставленные неудобства!
					</div>
				 </div>';
	}else{
		if( isset($regArr['KPS_NUM_NAME']) ){
			echo '<h5 class="mb-4">Проверьте правильность заполнения полей!</h5>';
			echo '<div class="row" style="display: block;">
					<div class="alert alert-primary d-inline" role="alert">
						<i class="fa fa-check-square" aria-hidden="true"></i>
						Отчет будет направлен в: <b>'.$regArr['KPS_NUM_NAME'].'</b>
					</div>
				 </div>';
		}
		else {
			if( isset( $regArr['RO_F4'] ) && !empty( $regArr['RO_F4'] ) ){
				echo '<h5 class="mb-4">Проверьте правильность заполнения полей!</h5>';
				echo '<div class="row" style="display: block;">
					<div class="alert alert-primary d-inline" role="alert">
						<i class="fa fa-check-square" aria-hidden="true"></i>
						Отчет будет направлен в: <b>'.$regArr['RO_F4'].'</b>
						(<a href="index.php?page=inn_to_reg" target="_blank">Проверить</a>)
					</div>
				 </div>';
			}
			else echo '<h5 class="mb-4">Проверьте правильность заполнения полей!</h5>';
		}
	}
	?>
	<div id="manuallyf4" class="mt-4">
		<form role="form" class="text-left" action="index.php?page=oplata" method="POST">
			<?php if( isset($regArr['NAME']))echo "<p><b>". htmlspecialchars_decode($regArr['NAME']) ."</b></p>";?>
			<div class="form-row" style="height:1px;">
				<input type="text" class="invisible" name="NAME" value="<?php if( isset($regArr['NAME']))echo htmlspecialchars(strip_tags($regArr['NAME']), ENT_COMPAT, 'cp1251');?>">
				<!--input type="text" class="invisible" name="REG_NUM_" value="<?php //if( isset($_POST['REG_NUM']))echo $_POST['REG_NUM'];?>"-->
			</div>
			<div class="form-row">
				<div class="col-md-5 mb-3">
					<label for="PHONE">Мобильный номер телефона<em>*</em></label>
					<input type="text" class="form-control" name="PHONE" id="PHONE" placeholder="+7 (999) 123 45 67" value="<?php if( isset($regArr['PHONE']))echo $regArr['PHONE'];?>">
					<small class="form-text text-muted">Необходим для Вашего куратора ФСС</small>
				</div>
				<div class="col-md-7 mb-3">
					<label for="EMAIL">Электронная почта<em>*</em></label>
					<input type="text" class="form-control" name="EMAIL" id="EMAIL" placeholder="example@mail.ru" value="<?php if( isset($regArr['EMAIL']))echo $regArr['EMAIL'];?>">
					<small class="form-text text-muted">Необходим для Вашего куратора ФСС</small>
				</div>
			</div>
			
			<div class="form-row">
				<div class="col-md-4 mb-3">
					<label for="REG_NUM_">Регномер ФСС<em>*</em></label>
					<input type="text" class="form-control" name="REG_NUM_" id="REG_NUM_" placeholder="0274 02 21 54" value="<?php if( isset($regArr['REG_NUM']))echo $regArr['REG_NUM'];?>" >
				</div>
				<div class="col-md-5 mb-3">
					<label for="INN">ИНН<em>*</em></label>
					<input type="text" class="form-control" name="INN" id="INN" placeholder="0276 07 46 27" value="<?php if( isset($regArr['INN']))echo $regArr['INN'];?>" ><!--pattern="([0-9]{10})|([0-9]{12})" onkeyup="this.value = this.value.replace (/\D/gi, '')"--> 
					<small class="form-text text-muted"><a href="https://egrul.nalog.ru/index.html" target="_blank">* узнать свой ИНН</a></small>
				</div>
				<div class="col-md-3 mb-3">
					<label for="KPP">КПП<em>*</em></label>
					<input type="text" class="form-control" name="KPP" id="KPP" placeholder="0277 01 001" value="<?php if( isset($regArr['KPP']))echo $regArr['KPP'];?>">
					<small class="form-text text-muted"><a href="https://egrul.nalog.ru/index.html" target="_blank">* узнать свой КПП</a></small>
				</div>
			</div>
			
			<div class="form-row">
				<div class="col-md-5 mb-3">
					<label for="KPS_NUM">Код подчиненности<em>*</em></label>
					<input type="text" class="form-control" name="KPS_NUM" id="KPS_NUM" placeholder="02121" value="<?php if( isset($regArr['KPS_NUM']))echo $regArr['KPS_NUM'];?>" onkeyup="this.value = this.value.replace (/\D/gi, '')"><!--pattern="[0-9]{3,5}" -->
					<small class="form-text text-muted"><a href="index.php?page=inn_to_reg" target="_blank">* узнать код подчиненности</a></small>
					
				</div>
				<div class="col-md-5 mb-3">
					<label for="OKVED">ОКВЭД<em>*</em></label>
					<input type="text" class="form-control" name="OKVED" id="OKVED" placeholder="72.2" value="<?php if( isset($regArr['OKVED']))echo $regArr['OKVED'];?>">
				</div>
				<div class="col-md-2 mb-3">
					<label for="RATE_MIS">Тариф<em>*</em></label>
					<input type="text" class="form-control" name="RATE_MIS" id="RATE_MIS" placeholder="0.2" value="<?php if( isset($regArr['RATE_MIS']))echo $regArr['RATE_MIS'];?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="col-md-12 mb-3">
					<label for="CADDR">Адрес регистрации<em>*</em></label>
					<input type="text" class="form-control" name="CADDR" id="CADDR" placeholder="423800, РФ, Татарстан Респ, Тукаевский р-н, Набережные Челны г, Камала б-р, д. 8, офис 122" value="<?php if( isset($regArr['CADDR']))echo htmlspecialchars(strip_tags($regArr['CADDR']), ENT_COMPAT, 'cp1251');?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="col-md-12 mb-3">
					<label for="CEO">Ф.И.О. руководителя</label>
					<input type="text" class="form-control" name="CEO" id="CEO" placeholder="Фамилия Имя Отчество" value="<?php if( isset($regArr['CEO']))echo htmlspecialchars(strip_tags($regArr['CEO']), ENT_COMPAT, 'cp1251');?>">
				</div>
			</div>
			
			<div class="form-row">
				<div class="col-md-6 mb-3">
					<label for="T1R1C2">Численность работников<em>*</em></label>
					<input type="text" class="form-control" name="T1R1C2" id="T1R1C2" placeholder="1" value="<?php if( isset($regArr['T1R1C2']))echo $regArr['T1R1C2'];?>">
					<small class="form-text text-muted">Среднесписочная численность работников</small>
				</div>
				<div class="col-md-6 mb-3">
					<label for="LIKV">Прекращение деятельности</label>
					<select id="LIKV" name="LIKV" class="form-control">
						<option value="0" <?php if( isset($regArr['LIKV']) && $regArr['LIKV'] == "0" )echo 'selected="selected"';?>>Нет</option>
						<option value="1" <?php if( isset($regArr['LIKV']) && $regArr['LIKV'] == "1" )echo 'selected="selected"';?>>Да</option>
					</select>
				</div>
			</div>
			<input type="submit" id="button_create_f4" name="button_create_f4" class="btn btn-primary btn-lg" value="Сдать расчет 4-ФСС">
		</form>
	</div>
</div>