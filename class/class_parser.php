<?php
/*
*********************************************************************************************************

**********************************************************************************************************
*/
class class_parser{
	private $domain = null;
	private $curl 	= null;
	
	private function db_standart_parse (){
		if (!extension_loaded('curl')) {
            throw new \ErrorException('cURL library is not loaded');
        }
        $this->curl = curl_init();
		
		curl_setopt($this->curl, CURLOPT_URL, $this->domain );
		curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:66.0) Gecko/20100101 Firefox/66.0");
		curl_setopt($this->curl, CURLOPT_USERPWD, "DOMAIN/MukhamadieVA:3pflwFhnthpf,");
		curl_setopt($this->curl, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
		curl_setopt($this->curl, CURLOPT_COOKIEJAR, __DIR__ .'/cookie.txt'); // сохранять куки в файл
		curl_setopt($this->curl, CURLOPT_COOKIEFILE, __DIR__ .'/cookie.txt');// читаем куки
		$result = curl_exec($this->curl);
		if (curl_errno($this->curl)) {
			echo 'Error:' . curl_error($this->curl);
		}
		curl_close($this->curl);
		
		#simple_html_dom
		require_once dirname(__DIR__) . '/class/simple_html_dom.php';
		
		return $result;
	}
	# Функция парсер Технологические базы данных
	public function db_list_shema (){
		echo '<script type="text/javascript">console.log("!!!!!Обновляю таблицу..");</script>';
		# Переменные
		$db_shema_ARR	=	array();
		
		# Адрес
		$this->domain = "http://ufa-webdbot02.fc.uralsibbank.ru/apex/f?p=106:1:0::";
		# cURL
		$result = $this->db_standart_parse();

		# Начинаем парсить HTML
		# Находим таблицу со схемами
		$search = new simple_html_dom();
		$search->load($result);
		$table = $search->find('#apexir_DATA_PANEL table', 1); 					// ******* Здесь наша таблица
		if ( !empty($table) ){													// Проверим на валидность
			//echo "<div id='db_list'><table class='table table-sm table-bordered table-striped' style='background:#fff; font-size:65% !important;'>";
			foreach($table->find('tr') as $key_tr => $value_tr){
				//echo "<tr>";
				foreach($value_tr->find('td') as $key_td => $value_td){
					if(strlen($value_td->plaintext)>0){
						$db_shema_ARR[$key_tr-1][$key_td-1] = $value_td->plaintext;
						//echo "<td>" . $value_td->plaintext . "</td>";
					}
				}
				//echo "</tr>";
			}
			//echo "</table></div>";
		}
		unset($table);
		unset($search);
		unset($result);
		unset($ch);
		
		$sql = "INSERT INTO `db_list_shema` SET 
				`db_name` 			= :db_name,
				`db_distorted`		= :db_distorted,	
				`db_date_update`	= :db_date_update,		
				`db_system`			= :db_system,
				`db_owner`			= :db_owner,		
				`db_phone_owner`	= :db_phone_owner,
				`db_server`			= :db_server,
				`db_copy_project`	= :db_copy_project,
				`db_business`		= :db_business,
				`db_comment`		= :db_comment,
				`db_customer`		= :db_customer,
				`db_phone_customer`	= :db_phone_customer,
				`db_tns`			= :db_tns
		";
		if( $db_shema_ARR ){
			#Очистим всю таблицу перед загрузкой новых данных
			$trancate = "TRUNCATE TABLE db_list_shema";
			$insert_id = DB::set($trancate);
			
			for($i=0;$i<count($db_shema_ARR);$i++){
				$insert_id = DB::add($sql, array(	
						'db_name' 			=>		str_replace(' ', '', $db_shema_ARR[$i][0]),
						'db_distorted'		=>		$db_shema_ARR[$i][1]	,
						'db_date_update'	=>		$db_shema_ARR[$i][2]	,
						'db_system'			=>		$db_shema_ARR[$i][3]	,
						'db_owner'			=>		$db_shema_ARR[$i][4]	,
						'db_phone_owner'	=>		$db_shema_ARR[$i][5]	,
						'db_server'			=>		$db_shema_ARR[$i][6]	,
						'db_copy_project'	=>		$db_shema_ARR[$i][7]	,
						'db_business'		=>		$db_shema_ARR[$i][8]	,
						'db_comment'		=>		$db_shema_ARR[$i][9]	,
						'db_customer'		=>		$db_shema_ARR[$i][10]	,
						'db_phone_customer'	=>		$db_shema_ARR[$i][11]	,
						'db_tns'			=>		str_replace(' ', '', $db_shema_ARR[$i][12]),	// Удаляем вообще все пробелы				
				));
			}
			unset($db_shema_ARR);
			unset($insert_id);
			unset($trancate);
			unset($sql);
		}
	}
	
	public function display_db_list_shema(){
		echo "<table class='table table-dark table-hover table-sm table-bordered table-striped' style='font-size:65% !important;'>
				<thead class='thead-dark'>
					<tr>
					  <th>#</th>
					  <th>Имя базы</th>
					  <th><a title='Искажена'>И..</a></th>
					  <th>Дата обнов.</th>
					  <th>Система</th>
					  <th>Владелец оригинала</th>
					  <!--th>Тлф. владельца</th-->
					  <th>Сервер</th>
					  <!--th>Копия для проекта</th>
					  <th>Бизнес</th-->
					  <th>Комментарий</th>
					  <!--th>Заказчик копии</th>
					  <th>Тлф. заказчика</th-->
					  <th>Tns строка</th>
					</tr>
			  </thead>
			  <tbody>";	
					# Вывод списка из БД
					$item = DB::getAll("SELECT * FROM `db_list_shema`");
					
					foreach($item as $key_tr => $value_tr){
						echo "<tr>";
						/*foreach($value_tr as $key_td => $value_td){
							echo "<td>" . $value_td . "</td>";
						}*/
						# преобразуем ФИО
						if( strlen($value_tr['db_owner'])>20 ){
							$FIO = explode(" ", $value_tr['db_owner']);
							$fio_str = $FIO[0] . " " . substr($FIO[1], 0, 2) . "." . substr($FIO[2], 0, 2) . ".";
						}
						else $fio_str = $value_tr['db_owner'];

						# Выводим
						echo "<td>" . $value_tr['id'] . "</td>";
						echo "<td>" . $value_tr['db_name'] . "</td>";
						echo "<td style='width:10px !important;'>" . $value_tr['db_distorted'] . "</td>";
						echo "<td style='width:80px !important;'>" . $value_tr['db_date_update'] . "</td>";
						echo "<td style='width:60px !important;'>" . $value_tr['db_system'] . "</td>";
						echo "<td style='width:130px !important;'>" . $fio_str . "<br>" . $value_tr['db_phone_owner'] . "</td>";
						echo "<td>" . $value_tr['db_server'] . "</td>";
						echo "<td>" . $value_tr['db_comment'] . "</td>";
						echo "<td style='font-size:10px; width:470px !important;'>" . $value_tr['db_tns'] . "</td>";//substr($value_tr['db_tns'], 0, 75)
						echo "</tr>";
					}
					unset($item);
			echo "</tbody>
			</table>";
	}
}	
?>