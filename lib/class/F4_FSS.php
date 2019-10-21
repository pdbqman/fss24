<?php
include_once dirname(__DIR__) . "/class/resources_FSS.php";
# Работа с файлами
//include_once dirname(__DIR__) . "\class\FSS24_FILE.php";

# Класс для работы со шлюзом приема расчетов по форме 4-фсс
class F4_FSS extends resources_FSS{
	private	$domain				= "http://f4.fss.ru";
	private $url_check_regnum	= "http://f4.fss.ru/fss/office";
	private $url_upload_xml		= "http://f4.fss.ru/uploadfile";
	private $url_id_file		= "http://f4.fss.ru/fss/statusreport?id="; // Идентификатор файла
	public	$fss24_files		= null; // Это обьект класса FSS24_FILE (для работы с файлами)
	public function __construct()
	{
		parent::__construct( $this->domain );
		
		//$this->fss24_files = new FSS24_FILE();
    }
	# Провекра статуса отчета страхователя за год
	public function check_regnum($regNumber)
	{
		# --- Переменные -------------
		$buf_table = '<table style="border-width:thin;border-spacing:1px;border-style:outset;border-color:grey;border-collapse:separate;">';
		# ----------------------------
		$post = "regnum=". $regNumber ."&period=5";
		
		curl_setopt($this->curl, CURLOPT_URL, $this->url_check_regnum);
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
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
		// проверка валидности
		if ( $this->check_cURL_errors ( $this->curl ) ){
			$this->simple_html_dom( curl_exec($this->curl) );
			$table = $this->result->find('table table', 2); 					// ******* Здесь таблица c Идентификаторами файлов (статус отчета)
			$this->simple_html_dom( $table );
			
			foreach($table->find('tr') as $key => $tr) {
				if( $key < 7 ){
					// Ищу ссылки, что бы их подменить и сделать валидными путем добавления http://f4.fss.ru
					foreach($tr->find('a') as $a_href) {
						//echo $a_href->href."</br>"; 
						$a_href->href = "http://f4.fss.ru".$a_href->href; // Замена ссылки
					}
					$buf_table = $buf_table . $tr->outertext;
				}
			}
			// Делаю это здесь т.к. по нормальному пока не получилось
			$buf_table = str_replace("href", "target='_blank' href", $buf_table);
			
			return $buf_table."</table>";
			//return $table;
		}
		else return false;
	}
	# Проверяем самую свежую (первую) запись - должен быть текущий квартал
	public function check_one_regnum($regNumber, &$regArr)
	{
		$this->check_regnum( $regNumber );
		$temp_search_tr = $this->result->find('tr',1);
		# Парсим строку temp_search_tr
		if ($temp_search_tr){
			foreach($temp_search_tr->find('td') as $key => $value)
				$regArr[$key] = $value->plaintext;
		}
	}
	public function check_regnum_one($regNumber, $regYear, $regKvartal, $regDateTime, $regDateTimeEnd, &$regArrStatus)
	{
		# --- Переменные -------------
		$year			= false;
		$kvartal		= false;
		$datetime		= false;
		$datetimeEnd	= false;
		$search_str_for_parse = 0;
		$i = 1;
		$temp_search_str = 0;
		
		$url = "";
		$temp_search_url = "";
		
		$tr = "";
		$temp_search_tr = "";
		# ----------------------------
		$this->check_regnum($regNumber);
		$thead_tr = $this->result->find('tr', 0);
		# В цикле ищем нужную строку
		while ( $this->result->find('tr',$i) ) {
			$temp_search_tr = $this->result->find('tr',$i);
			# Парсим строку temp_search_tr
			foreach($temp_search_tr->find('td') as $key => $value){
				if( $key == 0 )
					$temp_search_url = $value->plaintext; // Идентификатор файла (создаем полную ссылку)
				# Смотрим год
				if( $key == 1 && $value->plaintext == $regYear )
					$year = true;
				# Смотрим квартал
				if( $key == 2 && $value->plaintext == $regKvartal )
					$kvartal = true;
				# "Дата начала" >= тому времени когда отчет отправлен, а максимальная дата (regDateTimeEnd) >=  чем "Дата начала"
				if( $key == 6  && (strtotime($value->plaintext) >= $regDateTime) && ($regDateTimeEnd >= strtotime($value->plaintext)) )
					$datetime = true;
				# Проверяем "Дата завершения" с максимальной которую мы установили (regDateTimeEnd)
				/*if( $key == 7 && (strtotime($value->plaintext) <= $regDateTimeEnd ) ){
					$datetimeEnd = true;
					echo "sdasasdasdasdasdasdasd";
				}*/
			}
			if ( $year && $kvartal && $datetime /*&& $datetimeEnd*/ ){
				$temp_search_str = $i;
				$url = $temp_search_url;
				$tr = $temp_search_tr;
				$year			= false;
				$kvartal		= false;
				$datetime		= false;
				$datetimeEnd	= false;
				
				$search_str_for_parse = 1;
			}
			$i++;
		}
		/*if ( $search_str_for_parse == 1 ){
			
		}*/
	}
	# Отправляем отчеты из массива $upload_ef4, а полученные идентификаторы запишем в $ID_file
	public function upload_xml( &$upload_ef4, &$ID_file )
	{
		foreach($upload_ef4 as $key => $file) {
			$cfile = new CURLFile(realpath($file));
			$post = array (
					  'filein' => $cfile
					  );      
			curl_setopt($this->curl, CURLOPT_URL, $this->url_upload_xml);
			curl_setopt($this->curl, CURLOPT_HEADER, 0); // получать заголовки
			curl_setopt($this->curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($this->curl, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0');
			curl_setopt($this->curl, CURLOPT_REFERER, 'http://f4.fss.ru/fss/upload');
			curl_setopt($this->curl, CURLOPT_POST, 1 ); // использовать данные в post
			curl_setopt($this->curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, false); // следовать за редиректами
			curl_setopt($this->curl, CURLOPT_SAFE_UPLOAD, true);
			curl_setopt($this->curl, CURLOPT_POST, 1 ); // использовать данные в post
			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $post);
			//$a = curl_exec($this->curl);
			// проверка валидности
			if ( $this->check_cURL_errors ( $this->curl ) ){
				# Парсим полученный url
				$this->simple_html_dom( curl_exec($this->curl) );
				# Ищем основную таблицу
				$span = $this->result->find('span[id=identifier]',0);/*<span id="identifier" style="font-family:Tahoma">0245-8831-5043-01-5029002087</span>*/
				# Запишем в массив
				$ID_file[$key] = $span->plaintext;
				//echo $span->plaintext."</br>";
			}
			unset($cfile);
			unset($post);
		}
	}
	# Найдем отчет по идентификатору
	public function status_idFile( &$ID_file, &$regArrStatus ){		
		foreach($ID_file as $key_ID => $ID_f) {
			$url = $this->url_id_file . $ID_f;
			//echo $url."</br>";
						
			curl_setopt($this->curl, CURLOPT_URL, $url);
			curl_setopt($this->curl, CURLOPT_HEADER, 0); // получать заголовки
			curl_setopt($this->curl, CURLOPT_POST, 1 ); // использовать данные в post
			// проверка валидности
			if ( $this->check_cURL_errors ( $this->curl ) ){
				# Парсим полученный url
				$this->simple_html_dom( curl_exec($this->curl) );

				# Ищем основную таблицу
				$table = $this->result->find('table',2); 					// ******* Развернутая таблица c Идентификаторами файлов (статус отчета)
				$this->simple_html_dom( $table );
				
				# Проверим наличие не валидных статусов
				//$this->monitor_status( $table );
				# --> 1.Парсим и заполняем
				# Парсим таблицу
				# Сначала заполняем массив из 5 строк "Успешно" и проверим каждую строку
					# Проверим на существование 5 строку (она должна быть и должна равняться "Успешно")
					# Если 5 строка "Успешно", а одна из строк с первой по четвертую не равна "Успешно" то парсим дальше
				foreach($table->find('tr tr') as $key => $value){
					# Регномер
					$regArrStatus[$key_ID][0] = substr($ID_f, -10);
					# Идентификатор отчета
					$regArrStatus[$key_ID][1] = $ID_f;
					if( $key == 4 && $value->plaintext == "Успешно" ){
						# Таблица целиком 
						$regArrStatus[$key_ID][2] = $table->outertext;
					}
					# --> Если нашли ошибку
					if( $value->plaintext != "Успешно" ){
						# --> Заново парсим всю таблицу что бы узнать ошибку
						foreach($table->find('tr') as $key_td => $value_td){
							if( $key_td > 0 ){
								foreach($value_td->find('td') as $key_a => $value_a){
									if($key_a == 2 && $value_a->plaintext != "Успешно" )
										$regArrStatus[$key_ID][3] = $value_a->plaintext; // Ошибка
									if($key_a == 5 && !empty($value_a->plaintext) )
										$regArrStatus[$key_ID][4] = $value_a->plaintext; // Код ошибки
									if($key_a == 6 && !empty($value_a->plaintext) )
										$regArrStatus[$key_ID][5] = $value_a->plaintext; // Описание ошибки
								}
							}
						}# --< Заново парсим всю таблицу что бы узнать ошибку
					}# --< Если нашли ошибку
				}# --< 1.Парсим и заполняем
			}
			else echo "Ошибка поиска состояния по идентификатору";
			unset($url);
			unset($table);
		}//foreach($ID_file as $key_ID => $ID_f)
	}
	public function monitor_status( $table )
	{
		foreach($table->find('tr tr') as $key => $value){
			if ( $value->plaintext == "Отправлен на обработку" ){
				sleep(30);
				# Рекурсия
				//$this->monitor_status( $table );
			}
		}
	}
}
?>