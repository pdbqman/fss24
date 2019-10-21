<!-- Подключаю ВАЛИДАЦИЮ JS -->
<script src="/lib/ajax/validate_form.js"></script>
<script>
// Костыль для КПП, что бы не валидировать
function ResetKPP(KPP){
	document.getElementById('KPP').className = "form-control";
}
document.getElementById('INN').addEventListener('blur', ValidateINN, false);
document.getElementById('INN').addEventListener('change', ValidateINN, false);
// Костыль для КПП, что бы не валидировать
document.getElementById('INN').addEventListener('blur', ResetKPP, false);
document.getElementById('INN').addEventListener('change', ResetKPP, false);
// Но если что, валидируем
document.getElementById('KPP').addEventListener('blur', ValidateKPP, false);
document.getElementById('KPP').addEventListener('change', ValidateKPP, false);
/*******************************************************************************/
// Запоминаю ИНН и КПП
function loadSettings() {
	$('#INN').each(function(i,o) {
		$(o).val(localStorage[$(o).attr('id')])
	});
}

function saveSettings() {
	$('#INN').each(function(i,o) {
		if (document.getElementById('INN').value.length != 0){
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
// Проверка формы после нажатия на поиск регномера
/*******************************************************************************/
$(document).ready(function() {
	var INN = document.getElementById('INN').value;
	INN = INN.replace(/\s/g, '');
	INN = INN.replace(/[\_]/g, '');
	
	var KPP = document.getElementById('KPP').value;
	KPP = KPP.replace(/\s/g, '');
	KPP = KPP.replace(/[\_]/g, '');

	// При загрузке документа, если поля не пустые то тоже выводим валидацию
	if ( INN.length == 10 ){
		ValidateINN();
		document.getElementById('KPP').className = "form-control";
	}
	if ( INN.length == 12 ){
		ValidateINN();
		document.getElementById('KPP').className = "form-control";
	}
	if ( KPP.length == 9 )ValidateKPP();
});
$('#button_f4fss').click(function() {
	var INN = document.getElementById('INN').value;
	INN = INN.replace(/\s/g, '');
	INN = INN.replace(/[\_]/g, '');

	if( INN.length != 10 && INN.length != 12){
		alert("Введите ИНН!");
		return false;
	}
});
</script>