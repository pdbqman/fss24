<?php
# Основной класс-родитель по работе с  сайтами ФСС
class resources_FSS{
	public	$curl;
	public	$errorDomainAvailible = false;
	public	$result; // парсинг
	
	public function __construct( $domain ){
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }
        $this->curl = curl_init();
		if ( $this->isDomainAvailible( $this->curl, $domain )	=== false ){	// Проверяем доступность портала
			 $this->errorDomainAvailible = true;
			 echo "Ошибка доступа к ". $domain;
			return;
		}
		//echo curl_exec( $this->curl );
		require_once dirname(__DIR__) . '/class/simple_html_dom.php';
    }
	# Возвращает true, если домен доступен, false если нет
	public function isDomainAvailible( $ch, $domain )
	{
		curl_setopt($ch,CURLOPT_URL, $domain);
		curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($ch,CURLOPT_HEADER,true);
		curl_setopt($ch,CURLOPT_NOBODY,true);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		// проверка валидности
		return $this->check_cURL_errors ( $ch );
	}
	# Проверим запрос cURL на наличие ошибок
	public function check_cURL_errors ( $ch )
	{
		if(curl_exec($ch) === false){
			//echo 'Ошибка curl: ' . curl_error($ch). "</br>";
			curl_close($ch);
			return false;
		}
		else{
			//echo "нет ошибки";
			return true;
		}
	}
	# Подключаем simple_html_dom
	public function simple_html_dom( $search ){
		$this->result = new simple_html_dom();
		$this->result->load($search);
		$this->result->save();
		if( empty($this->result) )
			return;
	}
}
?>