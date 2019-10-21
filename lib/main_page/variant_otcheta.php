<style>
.maxi i{
	color: #f8b500;
	left:15px;
	top:10px;
	font-size:25px;
	position:absolute;
	color:rgba(0,64,133,.2)
}
.mini{
	padding: 3px;
	font-size:13px;
	text-align:left;
}
.mini i{
	color: #f8b500;
	float: left;
	padding:4px 10px;
}
.variant{
	display:inline-block;
	width:500px;
}
.variant span i{
	font-size:50px;
	color: #6c757d;
}
</style>
<div class="container text-center mt-5">
	<div id="manuallyf4" >
		<h2 class="mb-4">Выберите вариант сдачи расчета 4-ФСС</h2>
		
		<span class="variant">
			<span class="d-block mb-3 primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></span>
			<button class="btn btn-success ml-auto mr-2 btn-lg" onclick="location.href = 'index.php?page=fss_otchet_create'">Создать нулевой расчет 4-ФСС</button>
			<small id="emailHelp" class="form-text text-muted">Нулевой расчет создается автоматически</small>
			<div class="alert alert-info mt-3 maxi" role="alert">
				<i class="fa fa-hand-pointer-o" aria-hidden="true"></i>
				<b>Создать расчет 4-ФСС</b></br>автоматическая форма заполнения расчета, Вы указываете только свой Регистрационный номер ФСС
			</div>
		</span>
		<span class="variant">
			<span class="d-block mb-3"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
			<button class="btn btn-primary ml-auto mr-2 btn-lg" onclick="location.href = 'index.php?page=fss_otchet'">Загрузить XML расчеты 4-ФСС</button>
			<small id="emailHelp" class="form-text text-muted">В том числе и уточненные расчеты</small>
			<div class="alert alert-info mt-3 maxi" role="alert">
				<i class="fa fa-hand-pointer-o" aria-hidden="true"></i>
				<b>Загрузить расчеты 4-ФСС</b></br>Вы загружаете ваши отчеты 4-ФСС в формате XML(например из 1С). После загрузки мы их сдаем
			</div>
		</span>
		
		<div class="alert alert-dark mt-4 mini" role="alert">
			<i class="fa fa-star" aria-hidden="true"></i>
			<b><a href="http://portal.fss.ru/fss/services/f4input" target="_blank">portal.fss.ru</a></b> - официальный сайт Фонда социального страхования для создания расчетов по форме 4-ФСС в формате XML. На сайте portal.fss.ru вы можете создать свой расчет и выгрузить его в формате XML. Далее, используя наш сайт, вы можете благополучно сдать свой расчет используя наш сервис "загрузки xml расчетов"
		</div>
		<div class="alert alert-dark mini" role="alert">
			<i class="fa fa-star" aria-hidden="true"></i>
			Если вы не знаете как сдать расчет по форме 4-ФСС или не успеваете по срокам, то сдайте нулевой расчет(он создается автоматически), таким образом вы избегаете штрафов, а позже можете отправить уточненный расчет совершенно бесплатно!
		</div>
		
		<div class="alert alert-primary mini" role="alert">
			<i class="fa fa-star" aria-hidden="true"></i>
			Нулевой отчет может сдать каждый! Даже если ваш расчет таковым не является! Далее, в любое время, вы можете сдать уточненный расчет
		</div>
		<div class="alert alert-primary mini" role="alert">
			<i class="fa fa-star" aria-hidden="true"></i>
			Уточненные расчеты принимаются бесплатно(при условии что первичный расчет был сдан через наш сайт)
		</div>
	</div>
</div>