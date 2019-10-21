<!-- Подключаю ВАЛИДАЦИЮ JS -->
<?php
	$file_path = ".//lib/ajax/validate_form.js";
	$js = $file_path."?".md5_file($file_path);
?>
<script src="<?php echo $js;?>"></script>

<script>
// Проверка только при изменении поля или ухода с поля
document.getElementById('PHONE').addEventListener('change', ValidatePHONE, false);
document.getElementById('PHONE').addEventListener('blur', ValidatePHONE, false);

document.getElementById('EMAIL').addEventListener('blur', ValidateEmail, false);
document.getElementById('EMAIL').addEventListener('change', ValidateEmail, false);

document.getElementById('REG_NUM_').addEventListener('blur', ValidateREG_NUM, false);
document.getElementById('REG_NUM_').addEventListener('change', ValidateREG_NUM, false);

document.getElementById('INN').addEventListener('blur', ValidateINN, false);
document.getElementById('INN').addEventListener('change', ValidateINN, false);

document.getElementById('KPP').addEventListener('blur', ValidateKPP, false);
document.getElementById('KPP').addEventListener('change', ValidateKPP, false);

document.getElementById('KPS_NUM').addEventListener('blur', ValidateKPS_NUM, false);
document.getElementById('KPS_NUM').addEventListener('change', ValidateKPS_NUM, false);

document.getElementById('OKVED').addEventListener('blur', ValidateOKVED, false);
document.getElementById('OKVED').addEventListener('change', ValidateOKVED, false);

document.getElementById('RATE_MIS').addEventListener('blur', ValidateRATE_MIS, false);
document.getElementById('RATE_MIS').addEventListener('change', ValidateRATE_MIS, false);

document.getElementById('CADDR').addEventListener('blur', ValidateCADDR, false);
document.getElementById('CADDR').addEventListener('change', ValidateCADDR, false);

document.getElementById('CEO').addEventListener('blur', ValidateCEO, false);
document.getElementById('CEO').addEventListener('change', ValidateCEO, false);

document.getElementById('T1R1C2').addEventListener('blur', ValidateT1R1C2, false);
document.getElementById('T1R1C2').addEventListener('change', ValidateT1R1C2, false);

document.getElementById('LIKV').addEventListener('blur', ValidateLIKV, false);
document.getElementById('LIKV').addEventListener('change', ValidateLIKV, false);


/*******************************************************************************/
// Запоминаю РЕГНОМЕР
function loadSettings() {
	$('#REG_NUM').each(function(i,o) {
		$(o).val(localStorage[$(o).attr('id')])
	});
}

function saveSettings() {
	$('#REG_NUM').each(function(i,o) {
		if (document.getElementById('REG_NUM').value.length != 0){
			localStorage[$(o).attr('id')] = $(o).val();
		}
		else localStorage[$(o).attr('id')] = "";
	});
}
$(document).ready(function() {
	$(window).on("unload", function(e) {
		saveSettings();
	});
	loadSettings();
});

// Проверка введения РЕГНОМЕРА
/*******************************************************************************/
$('#button_f4fss').click(function() {
	var reg_num = document.getElementById('REG_NUM').value;
	reg_num = reg_num.replace(/\s/g, '');

	if( reg_num.length != 10 ){
		alert("Введите регномер фсс");
		return false;
	}
	// Запомним что было нажатие на кнопку
	localStorage.setItem('click', '1');
});

// Проверка формы после нажатия на поиск регномера
/*******************************************************************************/
$(document).ready(function() {
	// Делаем поле регномер не активным при успешном автозаполнении что бы случайно не вводили фигню
	// Если была нажата кнопка или перезагружена страница
	if ( (localStorage.getItem('click') == "1" || document.getElementById('REG_NUM').value.length > 0)
		&& document.getElementById('REG_NUM_').value.length > 0
		&& document.getElementById('INN').value.length > 0
		&& document.getElementById('KPP').value.length > 0
	){
		$("#REG_NUM_").attr('readonly', true);
	}
	// Если было нажатие на кнопку button_f4fss
	// При загрузке документа, если поля не пустые то тоже выводим валидацию
	if ( localStorage.getItem('click') == "1" 
		|| document.getElementById('PHONE').value.length > 0
		|| document.getElementById('EMAIL').value.length > 0
		//|| document.getElementById('REG_NUM_').value.length > 0
		|| document.getElementById('INN').value.length > 0
		|| document.getElementById('KPP').value.length > 0
		|| document.getElementById('KPS_NUM').value.length > 0
		|| document.getElementById('OKVED').value.length > 0
		|| document.getElementById('RATE_MIS').value.length > 0
		|| document.getElementById('CADDR').value.length > 0
		|| document.getElementById('CEO').value.length > 0
		|| document.getElementById('T1R1C2').value.length > 0
		//|| document.getElementById('LIKV').value.length > 0
	){
		// Показать div с данными
		$('#hand_input').css('display','block');
		
		// Найдем РЕГНОМЕР
		var reg_num = document.getElementById('REG_NUM').value;
		reg_num = reg_num.replace(/\s/g, '');
		// Проверим заполнен ли он
		if( reg_num.length == 10 ){
			ValidatePHONE();
			ValidateEmail();
			
			ValidateREG_NUM();
			ValidateKPP(); // Поставил выше чем проверка ИНН
			ValidateINN();
			
			ValidateKPS_NUM();
			ValidateOKVED();
			ValidateRATE_MIS();
			ValidateCADDR();
			ValidateCEO();
			ValidateT1R1C2();
			ValidateLIKV();
		}
		delete localStorage["click"]; // Удаление значения
	}
	else $('#hand_input').css('display','none');
});

// Проверка действий по нажатию на кнопку "Сдать расчет 4-ФСС"
/*******************************************************************************/
$('#button_create_f4').click(function() {
	var reg_num = document.getElementById('REG_NUM').value;
	reg_num = reg_num.replace(/\s/g, '');
	
	var reg_num_ = document.getElementById('REG_NUM_').value;
	reg_num_ = reg_num_.replace(/\s/g, '');
	
	var INN = document.getElementById('INN').value;
	INN = INN.replace(/\s/g, '');
	INN = INN.replace(/[\_]/g, '');
	
	if (reg_num == INN || reg_num_ == INN){alert('ИНН не может быть таким же как и Регномер!'); return false;}
	if( reg_num.length != 10 
		|| ValidatePHONE()	== false
		|| ValidateEmail()	== false
		|| ValidateREG_NUM()== false
		|| ValidateINN()	== false
		|| ValidateKPS_NUM()== false
		|| ValidateOKVED()	== false
		|| ValidateRATE_MIS()== false
		|| ValidateCADDR()	== false
		|| ValidateCEO()	== false
		|| ValidateT1R1C2()	== false
		|| ValidateLIKV()	== false
		|| (INN.length == 10 && ValidateKPP() == false)
	){
		return false;
	}
});
</script>
