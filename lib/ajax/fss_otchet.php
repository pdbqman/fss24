<script>
function ValidateEmail(mail)
{
 if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('email').value))
  {
	document.getElementById('email').className = "form-control is-valid";
	return (true)
  }
  else{
	document.getElementById('email').className = "form-control is-invalid";
	//alert('Не корректный Email адрес!');
	return (false)
  }	
}
function ValidatePhone(phone){
	if ( (/^[\+]\d{1}\s[\(]\d{3}[\)]\s\d{3}\s\d{2}\s\d{2}$/.test(document.getElementById('phone').value)
		 || /^[\+]\d{1}\d{3}\d{3}\d{2}\d{2}$/.test(document.getElementById('phone').value))
		 && (document.getElementById('phone').value.length == 18
		 || document.getElementById('phone').value.length == 12)
	){
		document.getElementById('phone').className = "form-control is-valid";
		return (true)
	}
	else{
		document.getElementById('phone').className = "form-control is-invalid";
		//if (document.getElementById('phone').value != "+7 (___) ___ __ __"){
			//alert('Не корректный номер телефона');
			//alert(document.getElementById('phone').value);
			//alert(document.getElementById('phone').value.length);
		//}
		return (false);
	}	
}

function roundPlus(x, n) { //x - число, n - количество знаков 
  if(isNaN(x) || isNaN(n)) return false;
  var m = Math.pow(10,n);
  return Math.round(x*m)/m;
}

function handleFileSelectMulti(evt) {
  // Очистим содержимое где php - span после загрузки
  document.getElementById('output_php').innerHTML = "";
  document.getElementById('outputMulti').innerHTML = "";
  var files = evt.target.files; // FileList object
  
  // Количество выбранных файлов
  var numFiles = files.length;
  if ( numFiles > 20 ){
	  alert('Выбрано больше 20 файлов! Загружайте частями по 20 файлов!'); 
	  // Сбросим все файлы
	  document.getElementById("fileMulti").value = "";
	  // Сбросим h4 после JS
	  document.getElementById('output_js_h4').innerHTML = "";
	  // Сбросим label
	  document.getElementById("label_file").innerHTML = "Выберите файлы xml...";
	  document.getElementById("label_file").style.color = "red";
	  return;
  }
  
  for (var i = 0, j = 1, err = 0, f; f = files[i]; i++) {
    var reader = new FileReader();
    // Closure to capture the file information.
    reader.onload = (function(theFile) {
      return function(e) {
        // Render thumbnail.
        var span = document.createElement('span');
	  
	  // Only process xml files.
	  if (!theFile.type.match('xml.*')) {
		span.className = "alert alert-danger";
		span.innerHTML = ['<i class="fa fa-thumbs-down good"></i>', j,'. Файл: <b>',theFile.name,'</b> не будет загружен! (',((theFile.type) ? theFile.type : (theFile.name.split('.').pop())),', ', roundPlus(theFile.size/1024, 1),' Kбайт)</br>'].join('');
		document.getElementById('outputMulti').insertBefore(span, null);
		err = err + 1;
	  }
	  else{
		span.className = "alert alert-success";
		span.innerHTML = ['<i class="fa fa-thumbs-o-up good"></i>', j,'. Файл: <b>',theFile.name,'</b>, готов к загрузке (',(theFile.name.split('.').pop()),', ', roundPlus(theFile.size/1024, 1),' Kбайт)</br>'].join('');
		document.getElementById('outputMulti').insertBefore(span, null);
	  }
	  j = j + 1;
	  
		var elem = document.getElementById("label_file");
		elem.style.color = "green";
		
		var txt_messg = "";
		if ((i-err) == 1) txt_messg = "Выбран "+(i-err)+" файл!";
		if ((i-err) >= 2 && (i-err) <= 4) txt_messg = "Выбрано "+(i-err)+" файла!";
		if ((i-err) >= 5) txt_messg = "Выбрано "+(i-err)+" файлов!";
		
		elem.innerHTML = txt_messg+" Все готово к загрузке!";
		// Установим h4 после JS
		document.getElementById('output_js_h4').innerHTML = txt_messg+" Все готово к загрузке!";
      };		
    })(f);

    // Read in the image file as a data URL.
    reader.readAsDataURL(f);
  }
}


document.getElementById('phone').addEventListener('change', ValidatePhone, false);
document.getElementById('phone').addEventListener('blur', ValidatePhone, false);
document.getElementById('email').addEventListener('change', ValidateEmail, false);
document.getElementById('fileMulti').addEventListener('change', handleFileSelectMulti, false);

document.getElementById('button_f4fss').addEventListener('click', ValidatePhone, false);
</script>

<script type="text/javascript">
function loadSettings() {
	$('#phone').each(function(i,o) {
		$(o).val(localStorage[$(o).attr('id')])
	});
	$('#email').each(function(i,o) {
		$(o).val(localStorage[$(o).attr('id')])
	});
}

function saveSettings() {
	$('#phone').each(function(i,o) {
		if (document.getElementById('phone').value.length != 0){
			localStorage[$(o).attr('id')] = $(o).val();
		}
		else localStorage[$(o).attr('id')] = "";
	});
	$('#email').each(function(i,o) {
		localStorage[$(o).attr('id')] = $(o).val();
	});
}
$(document).ready(function() {
	$(window).on("unload", function(e) {
		saveSettings();
	});
	loadSettings();
	
	if (document.getElementById('email').value.length >= 8)ValidateEmail();
	if (document.getElementById('phone').value.length >= 12)ValidatePhone();
	
	if ( 		document.getElementById('email').value.length == 0
			&&	document.getElementById('phone').value.length == 0
	)delete localStorage["err"]; // Удаление значения
	
	var err = localStorage.getItem('err');
	if (err == "1"){
		if (!ValidatePhone()){
			document.getElementById('phone').className = "form-control is-invalid";
		}
		if (!ValidateEmail()){
			document.getElementById('email').className = "form-control is-invalid";
		}
	}
});

$('#button_f4fss').click(function() {
	if ($(this).hasClass('disabled')) {
        console.log('Кнопка заблокирована');
    } 
	else {
		if (!ValidatePhone()){
			alert('Не корректный номер телефона!');
			localStorage.setItem('err', '1');
		}
		else if(!ValidateEmail()){
			alert('Не корректный Email адрес!');
			localStorage.setItem('err', '1');
		}
		else if(document.getElementById('fileMulti').files.length == 0){
			alert('Необходимо выбрать файлы для отправки!');
		}
		else{
			//очищаем все хранилище
			localStorage.clear();
			delete localStorage["err"]; // Удаление значения
		}
	}
});
</script>