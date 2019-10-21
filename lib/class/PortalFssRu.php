<?php
/*
*********************************************************************************************************
Алгоритм поиска данных:
	- Введенный регномер ищем в БД сайта
	- Если не нашли ищем на portal.fss.ru (нужна 2 таблица, но если не получилось то 1 таблица)
	- Если portal.fss.ru:
					- не доступен то DaData
					- если не нашли ничего (пробуем ручной ввод но 99% что отчет не пройдет)
**********************************************************************************************************
*/
# Парсинг сайта portal.fss.ru
# Host: portal.fss.ru
# User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0
# Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
# Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3
# Accept-Encoding: gzip, deflate
# Referer: http://portal.fss.ru/
# Content-Type: application/x-www-form-urlencoded
# Cookie: JSESSIONID=d3f12c2f593660d3184dc06ff626; _ga=GA1.2.72731860.1550168559; _gid=GA1.2.1008375075.1550168559; _gat=1
# Upgrade-Insecure-Requests: 1
include_once dirname(__DIR__) . "/class/resources_FSS.php";

class PortalFssRu extends resources_FSS{
	private	$domain				= "http://portal.fss.ru";
	private $url_auth			= "http://portal.fss.ru/fss/auth";
	private $url_admin			= "http://portal.fss.ru/fss/insurant/admininsurants";
	# Тело запроса: formaction=search&p=%24filter.getPageNumber%28%29&inn=&kpp=&regNumber=0274016740&ogrn=&codDb=&kpr=0&name=&fioManager=&address=&state=-1
	private $search_filter		= null; // post запрос 
	# Первый массив для наименования элементов таблицы
	private	$table_strah_name	= array();
	# Второй массив для значений элементов таблицы
	private	$table_strah_data	= array();
	# Выбрана первая или вторая таблица?
	public $two_or_one			= null;
		
	public function __construct( $login ){	
		if( $login == 'admin')$auth	= 	"login=%D1%84%D0%B8%D0%BB%D0%B8%D0%B0%D0%BB12&password=1";
		if( $login == 'user')$auth	= 	"login=district13&password=p555777q";
		
		parent::__construct( $this->domain );
		
		// Проверяем авторизацию на портале/авторизуемся
		if ( $this->auth_portal_fss_ru( $auth ) === false ){// Ошибка!
			$this->errorDomainAvailible = true;
			return;
		}
    }
	public function __destruct(){
        if (is_resource($this->curl)) {
            curl_close($this->curl);
        }
		unset($this->result);
    }
	# Поиск регнормера по ИНН и КПП
	public function search_regnum( $inn, $kpp, &$array ){
		# --- Переменные -------------
		$search		= null;
		$table_one	= null;
		# ----------------------------
		unset($this->table_strah_name);
		unset($this->table_strah_data);
		$this->search_filter = "formaction=search&inn={$inn}&kpp={$kpp}";
		$search = $this->search_portal_fss_ru( $this->search_filter );
		if ( empty($search) ){															// Проверим поиск
			$this->errorDomainAvailible = true;
			return;
		}
		# Находим первую мини-таблицу
		$table_one = $this->table_one( $search );
		# Заполняем массивы значениями (парсим)
		$this->select_info_strahovatel_tableone( $table_one );
		# Выгружаем данные
		if( isset($this->table_strah_data) && !empty($this->table_strah_data) )
			$array = $this->table_strah_data;
		
		# Выводим на экран
		//$this->print_info_strahovatel_tableone( $this->table_strah_name, $this->table_strah_data );
	}
	# Поиск данных по страхователю через РЕГНОМЕР
	public function auto_regnum( $regNumber, &$regArr ){
		if( !isset($regNumber) || empty($regNumber))
			return;
		
		$this->search_filter = "formaction=search&regNumber={$regNumber}";
		$search = $this->search_portal_fss_ru( $this->search_filter );// Поиск по портале
		//var_dump($search);
		if ( empty($search) ){// Проверим поиск
			$this->errorDomainAvailible = true;
			return;
		}
		# Начинаем парсить HTML
		# Находим первую мини-таблицу
		$table_one	= $this->table_one( $search );
		//echo $table_one;
		# Находим вторую таблицу - основную
		if($table_one){
			$table_two	= $this->table_two( $table_one );
			//echo $table_two;
			# Переходим к чтению данных из таблиц
			# У нас есть две таблица: мини таблица и главная таблица, нужно их распарсить
			# Проверим 2 таблицу, если с ней все нормально то работаем с ней
			# иначе работаем с 1 таблицей
			/*if ( strlen($table_two) < 2000 ) {
				# 1 таблица
				//echo $table_one;
				$this->two_or_one = 1;
				# Заполняем массивы значениями (парсим)
				$this->select_info_strahovatel_tableone( $table_one );
				# Выводим на экран
				//$this->print_info_strahovatel_tableone();
			}
			else{*/
				# 2 таблица
				//echo $table_two;
				$this->two_or_one = 2;
				# Заполняем массивы значениями (парсим)
				$this->select_info_strahovatel_tabletwo( $table_two );
				# Выводим на экран
				//$this->print_info_strahovatel_tabletwo();
			//}
			# Поиск данных страхователя, сохраняем данные
			$this->save_regArr( $regArr );
		}
		else return false;
	}
	# АВТОРИЗАЦИЯ на портале
	public function auth_portal_fss_ru( $auth )
	{
		curl_setopt($this->curl, CURLOPT_URL, $this->url_auth);
		curl_setopt($this->curl, CURLOPT_FAILONERROR, true);
		curl_setopt($this->curl, CURLOPT_HEADER, 0);
		curl_setopt($this->curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0");
		curl_setopt($this->curl, CURLOPT_REFERER, $this->domain);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1); // возвратить то что вернул сервер
		curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, 1); // следовать за редиректами
		curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);// таймаут4
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, dirname(__FILE__).'/cookie.txt'); // сохранять куки в файл
		curl_setopt($this->curl, CURLOPT_COOKIEFILE,  dirname(__FILE__).'/cookie.txt');
		curl_setopt($this->curl, CURLOPT_POST, 1 ); // использовать данные в post
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $auth);
		// проверка валидности
		//var_dump(curl_exec($this->curl));
		return $this->check_cURL_errors ( $this->curl );
	}
	# Поиск основных данных страхователя
	public function search_portal_fss_ru( $post ) {
		curl_setopt($this->curl, CURLOPT_URL, $this->url_admin);
		curl_setopt($this->curl, CURLOPT_POST, true ); // использовать данные в post
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->search_filter);
		# После поиска (Первая таблица после поиска И сам поиск)
		// проверка валидности
		if ( $this->check_cURL_errors ( $this->curl ) )
			return curl_exec($this->curl);
	}
	# Находим первую мини-таблицу
	public function table_one( $search ) {
		# Начинаем парсить HTML
		# Находим первую мини-таблицу
		$this->simple_html_dom( $search );
		$table_one = $this->result->find('table[id=reportTable]', 0); 					// ******* Здесь ПЕРВАЯ мини таблица
		//echo $table_one;
		# 1 таблица
		if ( empty($table_one) ){									// Проверим на валидность
			$this->errorDomainAvailible = true;
			return;
		}
		return $table_one;
	}
	# Находим вторую таблицу - основную
	public function table_two( $table_one ) {
		# В мини-таблице находим id страхователя. 
		# ID нам нужен для второй таблицы
		# Находим первый <td onclick="getCard(10930661,0274016740)">
		//var_dump( $table_one);
		$td_onclick = $table_one->find('tbody td', 0); 
		# Находим onclick
		$getCard = $td_onclick->onclick;
		# Находим сам id
		$str = strstr(trim($getCard), '(');		//	убираем getCard					-- (10930661,0274016740)
		$str = strstr(trim($str), ',', true);	//	убираем все после запятой		-- (10930661
		$id_strah = substr(trim($str), 1);		//	убираем скобку ( самую первую 	-- 10930661
		# Запрашиваем вторую таблицу - основную
		$table_two = $this->select_table_two( $id_strah ); 								// ******* Здесь ВТОРАЯ основная таблица
		if ( empty($table_two) ){									// Проверим на валидность
			$this->errorDomainAvailible = true;
			return;
		}
		return $table_two;
	}
	# Запрашиваем вторую таблицу - основную
	public function select_table_two( $id_strah ){
		# http://portal.fss.ru/fss/admin/insurant-card?id=10930661&regnum=49290720
		# Не удалось выяснить что такое regnum=49290720, но работаит и без него
		$url_table_two = 'http://portal.fss.ru/fss/admin/insurant-card?id='. $id_strah ;
		# Переходим на страницу
		curl_setopt($this->curl, CURLOPT_URL, $url_table_two);
		if ( $this->check_cURL_errors ( $this->curl ) ){
			# Парсим страницу со второй таблицей
			$this->simple_html_dom( curl_exec($this->curl) );
			# парсим вторую таблицу
			$table_two = $this->result->find('table', 8);
			if ( !empty( $table_two ) )
				return $table_two;
			else return false;
		}
		else return false;
	}
	# Парсим первую таблицу, заполняем массивы
	public function select_info_strahovatel_tableone( $table ){
		$this->simple_html_dom( $table );
		
		$i=0;
		foreach($this->result->find('thead td') as $key => $value){
			$this->table_strah_name[$i] = $this->delete_space_text( $value->plaintext );
			$i++;
		}
		$i=0;
		foreach($this->result->find('tbody td') as $key => $value){
			$this->table_strah_data[$i] = $this->delete_space_text( $value->plaintext );
			$i++;
		}
	}
	# Вывод 1 таблицы (мини таблица)
	public function print_info_strahovatel_tableone(){
		
		# Полный вывод все таблицы № 1 (мини таблица)
		/*for($y=0;$y<count($this->table_strah_name);$y++){
			echo $y. ") ". $this->table_strah_name[$y]. " - ". $this->table_strah_data[$y]."</br>";
		}*/
		
		# Вывод необходимых строк 
		echo $this->table_strah_name[2]		.": ".	$this->table_strah_data[2]	."</br>";//Рег.№
		echo $this->table_strah_name[10]	.": ".	$this->table_strah_data[10]	."</br>";//Наименование
		echo $this->table_strah_name[13]	.": ".	$this->table_strah_data[13]	."</br>";//Дата регистрации
		echo $this->table_strah_name[1]		.": ".	$this->table_strah_data[1]	."</br>";//Код БД
		echo $this->table_strah_name[3]		.": ".	$this->table_strah_data[3]	."</br>";//ИНН
		echo $this->table_strah_name[4]		.": ".	$this->table_strah_data[4]	."</br>";//КПП
		echo $this->table_strah_name[5]		.": ".	$this->table_strah_data[5]	."</br>";//ОГРН
		echo $this->table_strah_name[14]	.": ".	$this->table_strah_data[14]	."</br>";//Адрес
		echo $this->table_strah_name[7]		.": ".	$this->table_strah_data[7]	."</br>";//ОКВЭД
		echo $this->table_strah_name[15]	.": ".	$this->table_strah_data[15]	."</br>";//Директор
		echo $this->table_strah_name[8]		.": ".	$this->table_strah_data[8]	."</br>";//Класс риска
	}

	# Парсим вторую таблицу, заполняем массивы
	public function select_info_strahovatel_tabletwo( $table ){
		$this->simple_html_dom( $table );	
		$i=0;
		foreach($this->result->find('tbody td') as $key => $value){
			if (!empty($value) and strlen($value) > 1) {
				if (($key % 2) == 0)
					$this->table_strah_name[$i] = $this->delete_space_text( $value->plaintext );
				else
					$this->table_strah_data[$i] = $this->delete_space_text( $value->plaintext );
				$i++;
			}
		}
	}
	#  Вывод 2 таблицы (главная таблица)
	public function print_info_strahovatel_tabletwo(){
		# Полный вывод
		/*for($y=0;$y<90;$y++){
			echo $y. ") ". $this->table_strah_name[$y]. " - ". $this->table_strah_data[$y]."</br>";
		}*/
		
		# Вывод необходимых строк 
		
		echo $this->table_strah_name[2]		.": ".	$this->table_strah_data[3]	."</br>"; // Сокращение
		echo $this->table_strah_name[54]	.": ".	$this->table_strah_data[55]	."</br>"; // Дата постановки на учёт
		echo $this->table_strah_name[42]	.": ".	$this->table_strah_data[43]	."</br>"; // Код подчиненности
		echo $this->table_strah_name[46]	.": ".	$this->table_strah_data[47]	."</br>"; // Региональное отделение
		echo $this->table_strah_name[0]		.": ".	$this->table_strah_data[1]	."</br>"; // Наименование
		echo $this->table_strah_name[12]	.": ".	$this->table_strah_data[13]	."</br>"; // ИНН
		echo $this->table_strah_name[14]	.": ".	$this->table_strah_data[15]	."</br>"; // КПП
		echo $this->table_strah_name[24]	.": ".	$this->table_strah_data[25]	."</br>"; // ОГРН(ИП)
		echo $this->table_strah_name[4]		.": ".	$this->table_strah_data[5]	."</br>"; // Адрес
		echo $this->table_strah_name[28]	.": ".	$this->table_strah_data[29]	."</br>"; // ОКВЭД
		echo $this->table_strah_name[6]		.": ".	$this->table_strah_data[7]	."</br>"; // Директор
		echo $this->table_strah_name[72]	.": ".	$this->table_strah_data[73]	."</br></br>";//тариф по соц.страх. от несч.случ.(%)
	}
	# Поиск данных страхователя, сохраняем данные
	public function save_regArr( &$regArr ){
		if( $this->two_or_one == 2){ // Если выбрана вторая таблица
			$regArr['NAME_MINI'] 	= $this->table_strah_data[3];// Сокращение
			$regArr['NAME'] 		= $this->table_strah_data[1];// Наименование
			
			$regArr['REG_NUM']	 	= $this->table_strah_data[39];// Регномер
			$regArr['KPS_NUM']	 	= $this->table_strah_data[43];// Код подчиненности
			$regArr['KPS_NUM_NAME'] = $this->table_strah_data[47];// Региональное отделение
			
			$regArr['INN'] 			= $this->table_strah_data[13];// ИНН
			$regArr['KPP'] 			= $this->table_strah_data[15];// КПП
			$regArr['OGRN'] 		= $this->table_strah_data[25];// ОГРН(ИП)
			$regArr['OKVED'] 		= $this->table_strah_data[29];// ОКВЭД
			$regArr['RATE_MIS'] 	= $this->table_strah_data[73];// тариф по соц.страх. от несч.случ.(%)
			$regArr['CADDR']		= $this->table_strah_data[5];// Адрес
			$regArr['CEO']			= $this->table_strah_data[7];// Директор
			
			$regArr['date_uchet']	= $this->table_strah_data[55];// Дата постановки на учёт
		}
		else {// Если выбрана первая таблица
			if(strlen( $this->table_strah_data[3] ) == 12)$asdf="3"; // Если длина ИНН 12 символов
			else $asdf="1";
			if(strlen( $this->table_strah_data[1] )	==	3){
				$this->table_strah_data[1] = "0".$this->table_strah_data[1].$asdf;
			}
			else 
				$this->table_strah_data[1] = $this->table_strah_data[1].$asdf;
			
			$regArr[0]	= "";
			$regArr[1]	= $this->table_strah_data[10]	; //Наименование
				
			$regArr[2]	= $this->table_strah_data[2]	; //Рег.№
			$regArr[3]	= $this->table_strah_data[1]	; //Код БД
			$regArr[4]	= "";
				
			$regArr[5]	= $this->table_strah_data[3]	; //ИНН
			$regArr[6]	= $this->table_strah_data[4]	; //КПП
			$regArr[7]	= $this->table_strah_data[5]	; //ОГРН
			$regArr[8]	= $this->table_strah_data[7]	; //ОКВЭД
			$regArr[9]	= $this->table_strah_data[8]	; //Класс риска
			$regArr[10] = $this->table_strah_data[14]	; //Адрес
			$regArr[11] = $this->table_strah_data[15]	; //Директор
			
			$regArr[12] = $this->table_strah_data[13]	; //Дата регистрации
		}
	}
	# Поиск всех доверок
	public function check_doverka( &$regArrDov ){
		$url = "http://portal.fss.ru/fss/services/accountinfo-representative";
		$href = "";
		
		curl_setopt($this->curl, CURLOPT_URL, $url);
		if ( $this->check_cURL_errors( $this->curl ) ){
			$this->simple_html_dom( curl_exec($this->curl) );
			# Начинаем парсить HTML
			# Находим таблицу доверок
			if( !empty($this->result) ){
				$table = $this->result->find('table[id=representativeOrgsTable]',0); 		// ******* Таблица с доверками
				foreach($table->find('tr') as $key_tr => $value_tr){
					foreach($value_tr->find('td') as $key_td => $value_td){
						if($key_td == 9 ){
							$href = $value_td->find('a', 0)->getAttribute('href');
							if( $href == "javascript:void(0)" ){
								$href = "";
							}
							else {
								$href = "http://portal.fss.ru" . $href;
								$href = "<a href=\"$href\" target=\"_blank\" title=\"скачать доверенность\">Скачать доверенность</a>";
							}
							$regArrDov[$key_tr][$key_td] = $href;
						}
						else {
							$regArrDov[$key_tr][$key_td] = $value_td->plaintext;
						}
					}
				}
			}
		}
	}
	# Удалить пробел из начала и конца
	public function delete_space_text( $text ){
		$new_str = strtr( $text, array_flip(get_html_translation_table(HTML_ENTITIES, ENT_QUOTES)) ); 
		return trim($new_str, chr(0xC2).chr(0xA0));
	}
}
?>