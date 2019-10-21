<?php
/*ini_set('error_reporting', E_NOTICE);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);*/
$PROGRAM_HELP = 0;
/************************************
**	Переменная 'page', для навигации
*************************************/
if(!isset($_GET['page'])){
	$page = 'index';
}
else{
	$page = addslashes(strip_tags(trim($_GET['page'])));
}

if(!isset($_GET['dir'])){
	$dir = null;
}
else{
	$dir = addslashes(strip_tags(trim($_GET['dir'])));
}

// --->> БАЗА ДАННЫХ
# Подключаем БД
include  __DIR__ . "/lib/class/data_base.php";
// <<--- БАЗА ДАННЫХ
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<!-- Отключает КЕШ -->
	<meta http-equiv="Cache-Control" content="private">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	
	<!-- шрифт для таймера на главной -->
	<link href="https://fonts.googleapis.com/css?family=Comfortaa:700&amp;subset=cyrillic" rel="stylesheet">
	
	<!-- иконки/icon -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
	<?php
	$file_path = "./style/style.css";
	$css = $file_path."?".md5_file($file_path);
	?>
	<link rel="stylesheet" type="text/css" href=<?php echo $css;?> />
	
	<link type="image/x-icon" href="img/favicon.ico" rel="shortcut icon">
	
    <title>Сдать отчет ФСС срочно · Форма 4-ФСС · ВНиМ и НС · Регистрационный номер</title>
	
	<!-- Yandex.Metrika counter -->
	<script type="text/javascript" >
	   (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
	   m[i].l=1*new Date();k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
	   (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

	   ym(54537451, "init", {
			clickmap:true,
			trackLinks:true,
			accurateTrackBounce:true,
			webvisor:true
	   });
	</script>
	<noscript><div><img src="https://mc.yandex.ru/watch/54537451" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->
</head>
<body>
	<?php
	// Постоянный блок navbar
	include  __DIR__ . "/lib/main_page/1.navbar.php";

	// Основная страница
	if( $dir != null)
		$filename = __DIR__ . "/lib/".$dir."/".$page.".php";
	else
		$filename = __DIR__ . "/lib/".$page.".php";
	// Если такого файла нет то подменим 404 страницей
	if(file_exists( $filename ) == false)
		$filename = __DIR__ . "/lib/err404.php";
	include $filename;

	// Постоянный блок справки
	include __DIR__ . "/lib/main_page/8.spravka.php";

	// Постоянный блок footer
	include  __DIR__ . "/lib/main_page/7.footer.php";
	?>
	<!-- Bootstrap -->
	<!--script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script-->
    <!--script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script-->
	
	<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
	
	<script src="/lib/ajax/maskedinput/jquery.maskedinput.min.js"></script>
	
	<script>
		// Для блика кнопки	  
		$('.t580__btn').addClass('autoflash').append('<div class="flash lighting" style="height: 60px;width: 40px;top: 0px;left: -140px;"></div>');
	</script>
	<script type="text/javascript">
	$(function() {
		//элемент, к которому необходимо добавить маску
		$("#regNumber").mask("9999 99 99 99");
		$("#REG_NUM").mask("9999 99 99 99");
		$("#REG_NUM_").mask("9999 99 99 99");
		$("#phone").mask("+7 (999) 999 99 99");
		$("#PHONE").mask("+7 (999) 999 99 99");
		$("#KPP").mask("9999 99 999");
		$("#INN").mask("9999 99 99 99 ?99");
		$("#OKVED").mask("99?.99.99");
		$("#RATE_MIS").mask("9?.9");
		$("#T1R1C2").mask("9?99");
    });
	</script>

	<!-- подключаем AJAX -->
	<?php
		// Основная страница
		$filename = __DIR__ . "/lib/ajax/".$page.".php";
		// Если такого файл существует, то откроем его
		if(file_exists( $filename ) == true)
			include $filename;
	?>
</body>
</html>