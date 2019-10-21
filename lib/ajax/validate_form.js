function ValidateEmail(mail)
{
	var EMAIL = document.getElementById('EMAIL').value;
	if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(EMAIL) && EMAIL.length >= 8){
		document.getElementById('EMAIL').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('EMAIL').className = "form-control is-invalid";
		return (false)
	}
}
function ValidatePHONE(PHONE){
	var PHONE = document.getElementById('PHONE').value;
	PHONE = PHONE.replace(/\s/g, '');
	PHONE = PHONE.replace(/[\+]/g, '');
	PHONE = PHONE.replace(/[\(]/g, '');
	PHONE = PHONE.replace(/[\)]/g, '');
	
	if ( /^\d{11}$/.test(PHONE) && PHONE.length == 11 ){
		document.getElementById('PHONE').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('PHONE').className = "form-control is-invalid";
		return (false);
	}
}
function ValidateREG_NUM(REG_NUM){
	var REG_NUM = document.getElementById('REG_NUM_').value;
	REG_NUM = REG_NUM.replace(/\s/g, '');
	REG_NUM = REG_NUM.replace(/[\_]/g, '');
	if ( REG_NUM.length == 10){
		document.getElementById('REG_NUM_').className = "form-control is-valid";
		return (true);
	}else{
		document.getElementById('REG_NUM_').className = "form-control is-invalid";
		return (false);
	}
}
function ValidateINN(INN){
	var INN = document.getElementById('INN').value;
	INN = INN.replace(/\s/g, '');
	INN = INN.replace(/[\_]/g, '');
	
	var KPP = document.getElementById('KPP').value;
	KPP = KPP.replace(/\s/g, '');
	KPP = KPP.replace(/[\_]/g, '');

	if ( 	( INN.length >= 10 && /^\d{10}$/.test(INN) ) 
		||	( INN.length <= 12 && /^\d{12}$/.test(INN) )
	){
		// Если ИНН 10-значный, то делаем активным поле КПП 
		if(INN.length == 10){
			document.getElementById("KPP").disabled = false;
			if(KPP.length == 9)document.getElementById('KPP').className = "form-control is-valid";
			else document.getElementById('KPP').className = "form-control is-invalid";
		}
		// Если ИНН 12-значный, то делаем неактивным поле КПП и стираем значение
		if(INN.length == 12){
			document.getElementById("KPP").disabled = true;
			document.getElementById('KPP').value	= "";
			document.getElementById('KPP').className = "form-control";
		}
		
		document.getElementById('INN').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('INN').className = "form-control is-invalid";
		return (false);
	}
}
function ValidateKPP(KPP){
	var KPP = document.getElementById('KPP').value;
	KPP = KPP.replace(/\s/g, '');
	KPP = KPP.replace(/[\_]/g, '');
	
	var INN = document.getElementById('INN').value;
	INN = INN.replace(/\s/g, '');
	INN = INN.replace(/[\_]/g, '');
	
	if( INN.length == 10){
		if ( /^\d{9}$/.test(KPP) && KPP.length == 9 ){
			document.getElementById('KPP').className = "form-control is-valid";
			return (true);
		}
		else{
			document.getElementById('KPP').className = "form-control is-invalid";
			return (false);
		}
	}
	if( INN.length == 12){
		//Делаем КПП неактивным
		document.getElementById("KPP").disabled = true;
		document.getElementById('KPP').className = "form-control";
		return (true);
	}
	
	if( KPP.length == 0){
		document.getElementById('KPP').className = "form-control is-invalid";
		return (false);
	}
}
function ValidateKPS_NUM(KPS_NUM){
	var KPS_NUM = document.getElementById('KPS_NUM').value;
	KPS_NUM = KPS_NUM.replace(/\s/g, '');

	if (	( KPS_NUM.length >= 3 && KPS_NUM.length <= 5 )
		&&	(/^\d{3}$/.test(KPS_NUM) || /^\d{4}$/.test(KPS_NUM) || /^\d{5}$/.test(KPS_NUM))
	){
		document.getElementById('KPS_NUM').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('KPS_NUM').className = "form-control is-invalid";
		return (false);
	}
}
function ValidateOKVED(OKVED){
	var OKVED = document.getElementById('OKVED').value;
	OKVED = OKVED.replace(/\s/g, '');
	OKVED = OKVED.replace(/[\_]/g, '');
	// Узнаем последний символ строки (это точка?)
	if( OKVED.toString().slice(-1) == '.'){
		//Нам нужно убрать последнюю точку
		OKVED = OKVED.substring(0, OKVED.length - 1);
		// Тут прикол, у нас же две точки)) надо и вторую убрать
		if( OKVED.toString().slice(-1) == '.')OKVED = OKVED.substring(0, OKVED.length - 1);
	}
	/*
	структура:
	XX класс
	XX.X подкласс
	XX.XX группа
	XX.XX.X подгруппа
	XX.XX.XX вид
	*/
		if ( 	( OKVED.length >= 2 && OKVED.length <= 8 )
			&& (/^\d{2}$/.test(OKVED)					// XX класс
			|| 	/^\d{2}[\.]\d{1}$/.test(OKVED)			// XX.X подкласс
			|| 	/^\d{2}[\.]\d{2}$/.test(OKVED)			// XX.XX группа
			|| 	/^\d{2}[\.]\d{2}[\.]\d{1}$/.test(OKVED)	// XX.XX.X подгруппа
			|| 	/^\d{2}[\.]\d{2}[\.]\d{2}$/.test(OKVED)	// XX.XX.XX вид
			)
		){
			document.getElementById('OKVED').className = "form-control is-valid";
			return (true);
		}
		else{
			document.getElementById('OKVED').className = "form-control is-invalid";
			return (false);
		}
}

function ValidateRATE_MIS(RATE_MIS){
	var RATE_MIS = document.getElementById('RATE_MIS').value;
	RATE_MIS = RATE_MIS.replace(/\s/g, '');
	RATE_MIS = RATE_MIS.replace(/[\_]/g, '');
	// Узнаем последний символ строки (это точка?)
	if( RATE_MIS.toString().slice(-1) == '.'){
		//Нам нужно убрать последнюю точку
		RATE_MIS = RATE_MIS.substring(0, RATE_MIS.length - 1);
	}

	if( 	(RATE_MIS.length >= 1 && RATE_MIS.length <= 3)
		&& 	( /^\d{1}$/.test(RATE_MIS) || /^\d{1}[\.]\d{1}$/.test(RATE_MIS) )
	){

		document.getElementById('RATE_MIS').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('RATE_MIS').className = "form-control is-invalid";
		return (false);
	}
}
// Адрес регистрации	
function ValidateCADDR(CADDR){
	var CADDR = document.getElementById('CADDR').value;
	if( CADDR.length >= 30 ){
		document.getElementById('CADDR').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('CADDR').className = "form-control is-invalid";
		return (false);
	}
}
// Ф.И.О. руководителя
function ValidateCEO(CEO){
	var CEO = document.getElementById('CEO').value;
	if( CEO.length >= 5 ){
		document.getElementById('CEO').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('CEO').className = "form-control is-invalid";
		return (false);
	}
}
// Среднесписочная численность работников
function ValidateT1R1C2(T1R1C2){
	var T1R1C2 = document.getElementById('T1R1C2').value;
	T1R1C2 = T1R1C2.replace(/\s/g, '');
	T1R1C2 = T1R1C2.replace(/[\_]/g, '');
	
	if( T1R1C2.length >= 1 && T1R1C2.length <= 3 ){
		document.getElementById('T1R1C2').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('T1R1C2').className = "form-control is-invalid";
		return (false);
	}
}

// Среднесписочная численность работников
function ValidateLIKV(LIKV){
	var LIKV = document.getElementById('LIKV').value;
	LIKV = LIKV.replace(/\s/g, '');
	
	if( LIKV == "0" || LIKV == "1" ){
		document.getElementById('LIKV').className = "form-control is-valid";
		return (true);
	}
	else{
		document.getElementById('LIKV').className = "form-control is-invalid";
		return (false);
	}
}