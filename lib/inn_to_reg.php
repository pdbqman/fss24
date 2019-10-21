<style>
#manuallyf4{
	border: 10px solid #d1d1d1;
	padding:0;
	margin:0;
	heigth:auto;
	display: inline-block;
}
table thead {
    background-color: #2159A3 !important;
	border: solid 1px #184A8D;
	text-align: center;
    vertical-align: middle;
    text-decoration: none;
    font: bold 11px Tahoma;
    color: #f3f7f9 !important;
}
table tbody tr td {
	border: solid 1px #184A8D;
    border-collapse: collapse;
    padding: 5px 10px;
    font: normal 12px Tahoma;
    color: #474747;
    border-bottom: solid 1px #cccccc;
    vertical-align: middle;
}
#text{
    font-size: 14px;
    font-family: Roboto,Verdana,Helvetica,Arial,sans-serif;
    font-weight: 400;
    text-align: left;
    text-autospace: none;
    text-indent: 0;
    margin: 0;
}
.form_input_reg{width:500px;}
label{
	font-weight: 700;
	display: inline-block;
    max-width: 100%;
    margin-bottom: 5px;
	color: #484848;
	font-size:14px;
}
.alert{
	margin-top:-80px;
	position:absolute;
	padding-left:15px;
	left:50%;
	width:500px;
	margin-left:-250px;
}
.container i.fa{
	padding-right:15px;
}
#check_status_reg em{color:red;font-weight: 700;}
</style>
<div id="check_status_reg">
	<h2>Регистрационный номер ФСС</h2>
	<h5>Для поиска регистрационного номера Фонда социального страхования необходимо указать ИНН и КПП
		</br>Узнать свой ИНН и КПП, можно сайте nalog.ru в разделе - 
		<a href="https://egrul.nalog.ru/index.html" target="_blank">ПРЕДОСТАВЛЕНИЕ СВЕДЕНИЙ ИЗ ЕГРЮЛ/ЕГРИП</a>
	</h5>
	<div class = "form_input_reg center">
		<form role="form" action="" method="POST">
			<div class="form-row">
				<div class="col-md-6 mb-3">
					<label for="INN">Введите ИНН<em>*</em></label>
					<input type="text" class="form-control" name="INN" id="INN" placeholder="ИНН">
				</div>
				<div class="col-md-6 mb-3">
					<label for="KPP">Введите КПП</label>
					<input type="text" class="form-control" name="KPP" id="KPP" placeholder="КПП">
					<small class="form-text text-muted">КПП необязательное поле</small>
				</div>
			</div>
			<input type="submit" id="button_f4fss" class="btn btn-primary btn-lg" value="Поиск регномера ФСС">
		</form>
	</div>
</div>
<div class="container text-center" style="margin-top:105px;">
<?php
if( !empty($_POST['INN']) && isset($_POST['INN']) ){
	$inn = str_replace(" ", "", $_POST['INN']);
	$inn = str_replace("_", "", $inn);
	$kpp = str_replace(" ", "", $_POST['KPP']);
	$kpp = str_replace("_", "", $kpp);
	//echo $inn;
	echo'<div id="manuallyf4" >';
	if( strlen($inn) == 10 || strlen($inn) == 12 ){
		/*******************************************************************************
		********************** Парсинг сайта portal.fss.ru *****************************
		********************************************************************************/
		# Парсинг сайта portal.fss.ru
		include_once  __DIR__ . "/class/PortalFssRu.php";
		$search_regnum = array();
		$fss = new PortalFssRu( 'admin' );
		$fss->search_regnum( $inn, $kpp, $search_regnum );
		if( isset( $search_regnum ) && !empty( $search_regnum ) ){
			echo'
			<div class="alert alert-success d-inline" role="alert">
				<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>
				Данные по ИНН: <b>'. $inn .'</b> и КПП: <b>'. $kpp .'</b> успешно найдены!
			</div>';
			
			echo '<table><thead>
				  <tr>
					  <td>№</td>
					  <td>Филиал</td>
					  <td>Рег.№</td>
					  <td>ИНН</td>
					  <td>КПП</td>
					  <td>ОГРН</td>

					  <td>ОКВЭД</td>
					  <td>Класс риска</td>

					  <td>Наименование</td>
					  <td>Бюдж.орг.</td>
					  <td>Состояние</td> 
					  <td>Дата регистрации</td>
				  </tr>
			  </thead><tbody>';
			$count = 0;
			foreach($search_regnum as $key => $value){
				if ($count === 0) echo '<tr>';
				else if ($count == 19)
				{
					echo '</tr>';
					$count=0;
				}
				if ($count < 14 && $count != 7 && $count != 9){
					if ($count == 2)
						echo '<td><b>'.$value.'</b></td>';
					else
						echo '<td>'.$value.'</td>';
				}
				$count++;
			}
			echo '</tbody></table>';
		}
		else {
			echo '<style>#manuallyf4{border: 0px solid #d1d1d1;}</style>';
			echo '<div class="alert alert-warning mb-5" role="alert" >
				Повторите ввод! Некорректный ИНН или КПП
			</div>';
		}
	}
	elseif (empty($inn)){
		// Это уведомление об опасности (красный)
		// Вывожу сообщение
		echo '<style>#manuallyf4{border: 0px solid #d1d1d1;}</style>';
		echo '<div class="alert alert-danger mb-5" role="alert" >
			Вы не ввели ИНН!
		</div>';
	}
	else {
		echo '<style>#manuallyf4{border: 0px solid #d1d1d1;}</style>';
		echo '<div class="alert alert-warning mb-5" role="alert" >
			Повторите ввод! Некорректный ИНН или КПП
		</div>';
	}
	echo '</div>';
}
?>
	<div id="text" class="mt-5">
		<p>Российскому контрагенту вне зависимости от количества обособленных подразделений присваивается только один идентификационный номер 
		налогоплательщика (ИНН) (п. 7 ст. 84 НК РФ, п. 6 приказа ФНС России от 29.06.2012 № ММВ-7-6/435@).</p>
		<p>Филиалы, представительства, а также иные обособленные подразделения не являются самостоятельными юридическими лицами.</p>
		<p>ИНН присваивается только налогоплательщику-организации, а не обособленным подразделениям.</p>
		<p>Следовательно, у всех обособленных подразделений контрагента может быть только один ИНН, а КПП у каждого обособленного подразделения 
		будет свой.</p>
		<p>КПП присваивается по каждому основанию постановки на учет, в том числе по месту нахождения самой организации, ее обособленных 
		подразделений (ОП), земельных участков и иной недвижимости, транспорта (п. 7 приказа ФНС России от 29.06.2012 № ММВ-7-6/435@).</p>
		<p>Согласно п. 5 приказа ФНС России от 29.06.2012 № ММВ-7-6/435@ КПП состоит из трех частей:</p>
		<ul>
			<li>NNNN (4 знака) – код налогового органа, который осуществил постановку на учет организации по месту обособленного 
			подразделения организации, расположенного на территории РФ;</li>
			<li>PP (2 знака) – причина постановки на учет (учета сведений);</li>
			<li>XXX (3 знака) – порядковый номер постановки на учет (учета сведений) в налоговом органе по соответствующему основанию.</li>
		</ul>
		<p>Таким образом, даже если два обособленных подразделения будут поставлены на учет в одном налоговом органе, третья часть 
		номера (порядковый номер) у них будет различна.</p>
		<p>Следовательно, у обособленного подразделения в качестве ИНН указывается ИНН головной организации, а в качестве КПП – тот КПП, 
		который присвоен именно этому подразделению при постановке на учет.</p>
	</div>
</div>