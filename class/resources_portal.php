<?php
# Основной класс-родитель по работе с порталом
class resources_portal{
	protected	$curl;
	public	$errorDomainAvailible = false;
	public	$result; // парсинг
	
	public function __construct( $domain ){
        if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }
        $this->curl = curl_init();
		if ( $this->isDomainAvailible( $domain ) === false ){	// Проверяем доступность портала
			 $this->errorDomainAvailible = true;
			 echo "Ошибка доступа к ". $domain;
			return;
		}
		require_once dirname(__DIR__) . '/class/simple_html_dom.php';
    }
	# Возвращает true, если домен доступен, false если нет
	public function isDomainAvailible( $domain )
	{
		curl_setopt($this->curl, CURLOPT_URL, $domain );
		curl_setopt($this->curl, CURLOPT_HEADER, true);
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_USERPWD, "DOMAIN/MukhamadieVA:3pflwFhnthpf,");
		curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, __DIR__ .'/cookie.txt'); // сохранять куки в файл
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, __DIR__ .'/cookie.txt');// читаем куки
		curl_setopt($this->curl,CURLOPT_CONNECTTIMEOUT,10);
		curl_setopt($this->curl,CURLOPT_NOBODY,true);
		
		//echo $this->curl;
		// проверка валидности
		return $this->check_cURL_errors ();
	}
	# Проверим запрос cURL на наличие ошибок
	public function check_cURL_errors ()
	{
		if(curl_exec($this->curl) === false){
			//echo 'Ошибка curl: ' . curl_error($ch). "</br>";
			curl_close($this->curl);
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