<?php
# Функция для валидации
function validate_mu_input( $input_name ){
	$valid_name = trim($input_name);
	// вырезаем теги
	$valid_name = strip_tags($valid_name);
    //конвертируем специальные символы в мнемоники HTML
	$valid_name = htmlspecialchars($valid_name,ENT_QUOTES);
	
	$search  = array('+', '(', ')', ' ');
	$replace = '';
	$valid_name = str_replace($search, $replace, $valid_name);
	
	return $valid_name;
}
function parse_portal( $REG_NUM ){
	// Наш массив с результом
	$regArr = array();
	
	// **** Парсинг сайта portal.fss.ru
	# Парсинг сайта portal.fss.ru
	include  __DIR__ . "/class/PortalFssRu.php";
	$fss = new PortalFssRu( 'admin' );
	# Ищу данные используя регномер
	$fss->auto_regnum( $REG_NUM, $regArr );
	//var_dump($regArr);
	return $regArr;
}

# Проверка того что введен регномер для поиска данных
if( isset($_POST['REG_NUM']) ){
	$REG_NUM = validate_mu_input($_POST['REG_NUM']);
	// Наш массив с результом парсинга portal.fss.ru
	$regArr = array();
	
	// -----------------------------------------------------
	// 2019.07.21
	// Сначала берем данные с БД сайта, проверяем параметры CEO и NAME, т.к. раньше они не заполнялись
	// Если они пусты то парсим сайт
	# Поиск регнормера в БД
	$item = DB::getRow("SELECT * FROM `null_xml` WHERE `REG_NUM` = ? ORDER BY DATETIME DESC", $REG_NUM);
	//print_r($item);
	if( !empty( $item ) ){
		if($PROGRAM_HELP)echo "Найден в БД ".$item['id'];
		// Если пусто значение CEO или NAME
		if( 	(!isset($item['CEO'])	|| empty($item['CEO']))
			||	(!isset($item['NAME'])	|| empty($item['NAME'])) 
		)$regArr = parse_portal( $REG_NUM );
		else 
			$regArr = $item;
	}
	else
		$regArr = parse_portal( $REG_NUM );
	// -----------------------------------------------------
}
// Форма <!-- HTML -->
include __DIR__ . "/main_page/fss_otchet_create.php";
?>