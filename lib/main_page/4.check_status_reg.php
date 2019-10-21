<div id="check_status_reg">
	<h2>Узнайте статус вашего расчета 4-ФСС</h2>
	<h5>Справка для уточнения статуса расчета 4-ФСС выдается только для тех расчетов, которые сданы в электронном виде.</br>Данные предоставлены официальным сайтом ФСС - <a href="http://f4.fss.ru/">шлюз приема расчетов 4-ФСС с ЭП</a></h5>
	<div class = "form_input_reg center">
		<form class="form-inline" role="form" action="index.php?page=check_regnum" method="POST">
		  <div class="form-group mx-sm-3 mb-2">
			<input type="text" class="form-control input-lg input__field--kyo" name="regNumber" id="regNumber" placeholder="0274 02 21 54" onfocus="this.className='input_focus'" onblur="this.className='input_text'">
			<!--label class="input__label input__label--kyo" for="input-19">
				<span class="input__label-content input__label-content--kyo">Введите регномер ФСС (<a href="#">узнать Регномер</a>)</span>
			</label-->
		  </div>
		  <!--button type="submit" class="btn btn-lg btn-primary mb-2 button__field--kyo blue">Проверить</button    onClick='location.href="index.php?page=check_regnum"' -->
		  <input type="submit" id="button_f4fss" class="btn btn-lg btn-primary mb-2 button__field--kyo blue" value="Проверить">
		</form>
		<span class="input__label-content input__label-content--kyo">Введите регномер ФСС (<a href="index.php?page=inn_to_reg">я не знаю свой Регномер</a>)</span>
	</div>
</div>