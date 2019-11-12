<?php
function sdportal_person_search( $ch, $fio){
	$str = '';
	
	$str = urlencode($fio);
	$str = 'https://sdportal/person/search?q='.$str;
	curl_setopt($ch, CURLOPT_URL, $str);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  __DIR__ .'/cookie.txt');// читаем куки
	$json = json_decode( curl_exec($ch), false, 512, JSON_BIGINT_AS_STRING );
	//var_dump($json);// вывод
	return $json;
}
function Portal_bk_encode_fio( $fio ){
	$encode = 	rtrim(
					ltrim(
						str_replace('\\', '%', 
							str_replace(" ", "%2C%20", json_encode($fio))
						)
					, '"')
				, '"');
	return $encode;
}

function check_service_name( $tmp_name, $created, &$SERVICE_NAME, $bool_serv_name ){
	# Если есть с чем сверять
	if ( count($SERVICE_NAME)>=1 ){
		# Сверим с предыдущими линками
		for($t=0; $t<count($SERVICE_NAME); $t++){
			if ( $SERVICE_NAME[$t] == $tmp_name ){
				$bool_serv_name = true;	// Нашли совпадение
			}
		}
	}
	# Если совпанений нет то записываем в массив
	if ( $bool_serv_name == false ){
		# Записываем значение
		$SERVICE_NAME[count($SERVICE_NAME)] = $tmp_name;
		
		if( count($SERVICE_NAME) > 1 )
			echo "<br />";
		echo "<a href='#' class = 'link-articles' title = 'Дата создания: $created'>".$tmp_name."</a>";
	}
}
	
	$username = 'MukhamadieVA';
	$password = '3pflwFhnthpf,';
	$url="https://sdportal/";
	/*
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, 'https://yandex.ru');
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	//curl_setopt($ch, CURLOPT_HEADER,1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_PROXY, 'ufa-bc.fc.uralsibbank.ru:8080');
	curl_setopt($ch, CURLOPT_PROXYUSERPWD, "MukhamadieVA:3pflwFhnthpf,");
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	echo $result;
	curl_close($ch);
	*/

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://sdportal');
	//curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERPWD, "DOMAIN/MukhamadieVA:3pflwFhnthpf,");
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_NTLM);
	curl_setopt($ch, CURLOPT_COOKIEJAR, __DIR__ .'/cookie.txt'); // сохранять куки в файл
	curl_setopt($ch, CURLOPT_COOKIEFILE, __DIR__ .'/cookie.txt');// читаем куки
	/*
	$result = curl_exec($ch);
	if (curl_errno($ch)) {
		echo 'Error:' . curl_error($ch);
	}
	echo $result;

	curl_close($ch);
	*/

	/*
	$response = curl_exec($ch);
	if (curl_errno($ch)) die(curl_error($ch));
	$dom = new DomDocument();
	$dom->loadHTML($response);
	$tokens = $dom->getElementsByTagName("meta");
	for ($i = 0; $i < $tokens->length; $i++)
	{
		$meta = $tokens->item($i);
		if($meta->getAttribute('name') == 'csrf-token')
		$token = $meta->getAttribute('content');
	}
	echo $token."<br>";// это токен
	*/
/**************************************************/
	// ПОИСК id по ФИО
	$fio = 'Мусин Мурад Ахметович';
	
	// Инфа по пользователю
	$yummy		= sdportal_person_search( $ch, $fio );
	$txt 		= explode( ",", $yummy[0]->text );
	$encode_fio	= Portal_bk_encode_fio( $fio );
	
	//echo "<br/>". $yummy[0]->id;
	//echo "<br/>". $yummy[0]->text;
	//class="tabl_user_info"
			
	echo '
	<div class="user_info_button">
		<div id="inform_user"><i class="fa fa-user-circle"></i><small>Личные данные</small></div>
		<div class="div_user_info">
			<table class="table table-sm table-dark mb-1">
				<tr> 
					<td rowspan="4">
						<div class="div_circle rounded-circle">
							<img src="https://sdportal/main/get_user_pic/'. $txt[2] .'" width=90>
						</div>
					</td>
					<td>'. $yummy[0]->job_title .' ('. ltrim($txt[4]) .') </td>
				</tr>
				<tr> 
					<td>'. $yummy[0]->dep .'</td>
				</tr>
				<tr> 
					<td><a href="http://bk/info/NikPers/default.asp?txtSelect=seleAll&findMode=all&txtFind='. $encode_fio .'" target="_blank"><i>'. $txt[0] . $txt[1] .'</i></a></td>
				</tr>
				<tr> 
					<td>Тел: '. $txt[3] .'</td>
				</tr>
			</table>
		</div>
	</div>
	';
/**************************************************
	//Поиск нарядов
	$search = $yummy[0]->id;
	$search = $search . ';';
	$search = $search . $fio;
	$search = urlencode($search);
	$search = str_replace("+", "%20", $search);
	//echo $search;
	$url='https://sdportal/workorder.json?filter%5Blimit%5D=200&filter%5Bon_page%5D=10&filter%5Bopened%5D=true&filter%5Boverdue%5D=false&filter%5Biam_ispolinel%5D=false&filter%5Biam_init%5D=false&filter%5Bnot_assigned%5D=false&filter%5Bstatus%5D=&filter%5Bwor_id%5D=&filter%5Blink_type%5D=&filter%5Bwor_cat%5D=&filter%5Bwor_requestor_per_oid%5D=&filter%5Bserv%5D=&filter%5Bass_workgroup%5D=&filter%5Bextorg_oid%5D=&filter%5Bassign_oid%5D='. $search .'&filter%5Bdate_reg%5D=&filter%5Bdate_reg_to%5D=&filter%5Bcl_date%5D=&filter%5Bcl_date_to%5D=&filter%5Bwor_description%5D=&filter%5Bsostav_number%5D=&filter%5Bonly_my_teams%5D=false';
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_COOKIEFILE,  __DIR__ .'/cookie.txt');// читаем куки
	// вывод
	$json = curl_exec($ch);

	$yummy = json_decode($json);
	$b = json_decode($yummy->data);

// table
echo '<div id="table_workorder"><table class="table table-striped table_workorder" style="font-size:67% !important;">
 <thead class="thead-dark">
    <tr>
      <th scope="col">Наряд</th>
      <th scope="col">Связанный объект</th>
      <th scope="col">МП</th>
      <th scope="col">Инициатор</th>
      <th scope="col">Плановое начало</th>
      <th scope="col">Крайний срок</th>
      <th scope="col">Тема</th>
    </tr>
  </thead>
  <tbody>';
foreach ($b as $i=>$row){
	if(!empty($row)){
		$mpoject = substr($row->sostav_number, 0, strpos($row->sostav_number, " "));
		$fio = str_replace(",", "", $row->requestor_fio);
		$encode_fio	= Portal_bk_encode_fio( $fio );
		# преобразуем ФИО
		if( strlen($fio)>20 ){
			$FIO = explode(" ", $fio);
			$fio_str = $FIO[0] . " " . substr($FIO[1], 0, 2) . "." . substr($FIO[2], 0, 2) . ".";
		}
		else $fio_str = $fio;
		
		#-------> Найдем информацию по заказчику, что бы вывести в ссылке краткую информацию
		$yummy 	= sdportal_person_search( $ch, $fio);
		$txt 	= explode(",", $yummy[0]->text);
		$title 	= $yummy[0]->job_title . ':<br/><i>' . $txt[0] . $txt[1] . '</i><br/>' . $txt[3] . '<br/>' . $txt[4];
		//echo $title;
		#--< Найдем информацию по заказчику
		
		echo '<tr>';
		echo '<td><a href="https://sdportal/workorder/'. $row->wor_id .'" target="_blank" data-toggle="tooltip" data-html="true" title="'. $row->dop_info .'">'.$row->wor_id .'</a></td>';
		echo '<td>'.$row->relation->type.'</td>';
		echo '<td><a href="https://sdportal/mprojects/'. $mpoject .'" target="_blank">'. $mpoject .'</a></td>';
		echo '<td><a href="http://bk/info/NikPers/default.asp?txtSelect=seleAll&findMode=all&txtFind='. $encode_fio .'" data-toggle="tooltip" data-html="true" title="'. $title .'" target="_blank">'. $fio_str .'</a></td>';
		echo '<td>'.date("d.m.Y", strtotime(substr($row->reg_date, 0, 10))).'</td>';
		echo '<td>'.date("d.m.Y", strtotime(substr($row->deadline, 0, 10))).'</td>';
		echo '<td>'.$row->wor_description.'<td>';
		echo '</tr>';
	}
}
echo '</tbody></table></div>';
*************************************************/
if (curl_errno($ch)) print curl_error($ch);
curl_close($ch);
/*****************************************************/
?>
<?php
class PDOConnection {
    private	$dbh;
	public	$not_error = 1;
	public	$str_error = "";

    function __construct($db_username, $db_password, $dbtns) {
        try {
            $this->dbh = new PDO("oci:dbname=" . $dbtns . ";charset=utf8", $db_username, $db_password, array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, //PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
        } catch (PDOException $e) {
			$this->str_error = $e->getMessage();
			$this->not_error = 0;
        }
    }

    public function select($sql) {
		$sql_stmt = $this->dbh->prepare($sql);
		$sql_stmt->execute();
		$result = $sql_stmt->fetchAll(PDO::FETCH_ASSOC);
		return $result;
    }

    public function insert($sql) {
        $sql_stmt = $this->dbh->prepare($sql);
        try {
            $result = $sql_stmt->execute();
        } catch (PDOException $e) {
            trigger_error('Error occured while trying to insert into the DB:' . $e->getMessage(), E_USER_ERROR);
        }
        if ($result) {
            return $sql_stmt->rowCount();
        }
    }

    function __destruct() {
        $this->dbh = NULL;
    }

}
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
// Читаем TNSNAMES.ORA из сети
//$data = file_get_contents("\\\\fc.uralsibbank.ru\\ufa-dfs01\\Ibso\\network\\ADMIN\\TNSNAMES.ORA");

// ТНСку закинуть в базу, зачем ее постоянно читать из файла? **
$data = file_get_contents("./lib/TNSNAMES.ORA");
$search = explode("\r\n", $data);
// Временное хранилище паролей (будет перенесено в БД MySQL) **
$login_pass_db = array( 
		array("RTL.SD", 	"IBS" , "juehtw123"),
		array("RTL.TEST", 	"IBS" , "juehtw123"),
		array("RTL.KGO2", 	"IBS" , "juehtw123"),
		array("RTL13", 		"IBS" , "kbvjyfl"),
		array("RTL.CKK2", 	"IBS" , "ckk2546"),
		array("RTL.PERF", 	"IBS" , "gfnx2019")
		
);
# Подключение к БД
$dbh = null;
$temp_find_shema	= true; // Признак того что мы нашли логин и пароль к нашей схеме
$temp_db_error		= true; // Признак того что ошибка при подключении ОТСУТСТВУЕТ!

echo '<div class="db_info_button">
		<div id="db_oracle"><i class="fa fa-database"></i><small>База данных</small></div>
		<div class="div_db_info">
	';
echo '<table id="oracle_db" class="table table-dark table-striped" style="font-size:80% !important;"><tbody>';
for($i=0;$i<count($search);$i++){
	if( strlen($search[$i])< 2 )$i++;							// Сдвигаем позицую/Следующая запись
	$search[$i]		= str_replace(' ', '', $search[$i]);		// Удаляем вообще все пробелы
	$str_pos 		= strpos($search[$i], "=");					// Позиция символа
	# Наименование схемы
	$str_name_shema	= substr($search[$i], 0, $str_pos);			// Обрезаем строку
	# Сама ТНСка
	$dbtns			= substr($search[$i], ($str_pos+1));		// Обрезаем строку
	# Обнулим признак подключения
	$temp_find_shema = false;
	# По умолчанию ошибки при подключении нет
	$temp_db_error	= true;
	# Узнаем логин и пароль к схеме
	for($y=0;$y<count($login_pass_db);$y++){
		if( $str_name_shema == $login_pass_db[$y][0] ){
			#Нашли подходящую пару логин/пароль для подключения
			$temp_find_shema = true;
			# Подключаемся к ораклу
			$dbh = new PDOConnection( $login_pass_db[$y][1], $login_pass_db[$y][2], $dbtns);
			if ( $dbh->not_error == 1  ){
				# Запрос
				$select_sql = 'select NAME, CREATED, CONTROLFILE_TIME, PLATFORM_NAME  FROM V$DATABASE';
				$oracle_data = ($dbh->select($select_sql))[0];
				foreach( $oracle_data as $key=>$value ){
					$db_data[$key] = $value;
				}
			}
			else {
				# Запоминаем что ошибка при подключении
				$temp_db_error = false; // Ошибка подключения
				//var_dump( $dbh->str_error );
			}
			$y = count($login_pass_db);
		}
	}

	//var_dump($db_data);
	echo "<tr><td>";
	echo "<div class='db_shema'>";
		echo "<h6>".$str_name_shema."<hr></h6>";
		# Если мы смогли подключиться к базе, то покажем данные
		if ( $temp_find_shema == true && $temp_db_error == true){
			echo "<small>создано: ".$db_data['CREATED']."</small>";
			$HOST 		= explode("HOST=", $search[$i]);
			$HOST 		= explode(")", $HOST[1]);
			$PORT 		= explode("PORT=", $search[$i]);
			$PORT 		= explode(")", $PORT[1]);
			echo "<br><small>".$HOST[0].":".$PORT[0]."</small>";
			
			# Готовим второй запрос для детальных сведений
			$select_sql="SELECT 
					  SYS_CONTEXT ( 'userenv', 'CURRENT_USER' )    curr_user
					, SYS_CONTEXT ( 'userenv', 'DB_DOMAIN' )       db_domain
					, SYS_CONTEXT ( 'userenv', 'HOST' )            host
			FROM dual";
			$oracle_data = ($dbh->select($select_sql))[0];
			foreach( $oracle_data as $key=>$value ){
				$db_data[$key] = $value;
			}
			# Узнаем сколько пользователей онлайн
			$select_sql='select count(osuser) as osuser from(
					select b.osuser
					from v$session b
					where b.type=\'USER\'
					group by b.osuser)';
			$oracle_data = ($dbh->select($select_sql))[0];
			foreach( $oracle_data as $key=>$value ){
				$db_data_count_users[$key] = $value;
			}
			# Количество сессий
			$select_sql='select count(b.sid) as sid
				from v$session b where b.type=\'USER\'';
			$oracle_data = ($dbh->select($select_sql))[0];
			foreach( $oracle_data as $key=>$value ){
				$db_data_count_sid[$key] = $value;
			}
			# Oracle метрики 
			$select_sql='select METRIC_NAME, VALUE
				from SYS.V_$SYSMETRIC
				where METRIC_NAME IN (\'Database CPU Time Ratio\', \'Database Wait Time Ratio\') 
				AND INTSIZE_CSEC = (select max(INTSIZE_CSEC) from SYS.V_$SYSMETRIC)';
			$oracle_data = ($dbh->select($select_sql))[0];
			foreach( $oracle_data as $key=>$value ){
				$db_data_metric[0][$key] = $value;
				//echo $key . " - " . $value;
			}
			$oracle_data = ($dbh->select($select_sql))[1];
			foreach( $oracle_data as $key=>$value ){
				$db_data_metric[1][$key] = $value;
				//echo $key . " - " . $value;
			}
			#DB_LINK
			$select_sql='select host, created from ALL_DB_LINKS';
			$oracle_data = ($dbh->select($select_sql));
			for($p=0; $p<count($oracle_data); $p++){
				foreach( $oracle_data[$p] as $key=>$value ){
					$db_link_data[$p][$key] = $value;
					//echo "</br>".$value;
				}
			}
			//var_dump($oracle_data);
			echo '<span class="custom help">';
						//echo '<img src="./img/Info.png" alt="Помощь" height="20" width="20" />';
						echo '<i class="fa fa-info-circle"></i>';		
			echo "<table class='db_mini_table table-borderless'>
					<tr><td>ПЛАТФОРМА</td><td>".$db_data['PLATFORM_NAME']."</td></tr>
					<tr><td>СЛУЖБА</td><td>".$db_data['NAME'].".".$db_data['DB_DOMAIN']."</td></tr>
					<tr><td>ХОСТ</td><td>".$db_data['HOST']."</td></tr>
					<tr><td>ПОЛЬЗОВАТЕЛЕЙ</td><td>".$db_data_count_users['OSUSER']."</td></tr>
					<tr><td>СЕССИЙ</td><td>".$db_data_count_sid['SID']."</td></tr>
					<tr><td>Oracle Wait Time</td><td>".round($db_data_metric[0]['VALUE'],2)."%";
						if ( round($db_data_metric[0]['VALUE'],2) >= 69.9 )echo "<i class=\"fa fa-thumbs-o-up\"></i>";
						else echo "<i class=\"fa fa-thumbs-o-down\"></i>";
					echo "</td></tr>";
					echo "<tr><td>Oracle CPU Time</td><td>".round($db_data_metric[1]['VALUE'],2)."%";
						if ( round($db_data_metric[1]['VALUE'],2) <= 39.1 )echo "<i class=\"fa fa-thumbs-o-up\"></i>";
						else echo "<i class=\"fa fa-thumbs-o-down\"></i>";
					echo "</td></tr>";
					# -------> ЛИНКИ / DB_LINK <------ #
					# Переменные
					$SERVICE_NAME		= array();	// Массив линков (что бы не было повторов)
					$bool_serv_name		= false;	// Признак того что найдено совпадение в массиве
					
					echo "<tr>
							<td>ЛИНКИ</td><td>";
						# Обход по количеству линков на схеме
						for($p=0; $p<count($oracle_data); $p++){
							$str		= str_replace(' ', '', $db_link_data[$p]['HOST']);	// Удаляем вообще все пробелы
							$created	= $db_link_data[$p]['CREATED'];
							$pos 		= stripos($str, 'extproc');							// Находим вхождение что бы выбросить эту запись
							# Если такой записи нет то хорошо
							if( $pos === false ){
								# Если строка короткая, то это упращенная запись
								if ( strlen($str)<=30 ){
									# Вывод на экран без повторов. Бробежимся по предыдущим именам, что бы не было совпадения при выводе
									check_service_name( $str, $created, $SERVICE_NAME, $bool_serv_name );
								}
								# Если строка из ТНС'ки то берем только часть из SERVICE_NAME
								else{
									# Разбивает строку с помощью разделителя 
									$temp_service = explode("SERVICE_NAME=", $str);
									# Если разбивка прошла удачно и индекс 1 существует 
									if ( array_key_exists('1', $temp_service) ){
										# Конечное имя линка
										$tmp_name = substr($temp_service[1], 0, -3);
										# Вывод на экран без повторов. Бробежимся по предыдущим именам, что бы не было совпадения при выводе
										check_service_name( $tmp_name, $created, $SERVICE_NAME, $bool_serv_name );
									}
								}
							}
						}
					echo "</td></tr>";
					
					unset( $SERVICE_NAME );
					# -------< ЛИНКИ / DB_LINK >------ #
			
			echo "</table>";
			echo '</span>';
			echo "</div>";// .db_shema
			
			echo '<section>
				<a href="#button" class="indikator"></a>
				<span></span>
			</section>';
			
			
			echo '<div class="arrow-2">
				<div class="arrow-2-top"></div>
				<div class="arrow-2-bottom"></div>
			</div>';
		}
		else{
			// Нет данных для подключения
			if( !$temp_find_shema ){
				echo "
					<small>нет данных для подключения</small>
					<small>укажите логин и пароль</small>
				";
			}
			// Ошибка при подключении
			if( !$temp_db_error ){
				echo "
					<small>ошибка подключения</small>
					<small>проверьте логин и пароль</small>
				";
			}
			echo "</div>";
			
			echo '<section>
				<a href="#button" class="indikator_red"></a>
				<span></span>
			</section>';
		}
	echo "</td></tr>";
	// Отключаемся от оракла
	unset($dbh);
}
echo '</tbody></table>';
echo '</div></div>';




////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////
/*$str_data_base	= "RTL.SD = (DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(HOST=ibsort11.fc.uralsibbank.ru)(PORT=1521))(CONNECT_DATA=(SERVICE_NAME=rtlcl206.rtl.ufa.usb)))";
# Обрезаем строку слева до символа = тем самым получим название схемы
$str_pos 		= strpos($str_data_base, "=");					// Позиция символа
$str_name_shema	= trim( substr($str_data_base, 0, $str_pos) );	// Обрезаем строку и удаляем пробелы
echo $str_name_shema. "<br />";
*/
/*echo "<br />";
$dbh = new PDOConnection();
$select_sql = 'select NAME, CREATED, CONTROLFILE_TIME, PLATFORM_NAME  FROM V$DATABASE';
$oracle_data = ($dbh->select($select_sql))[0];
foreach( $oracle_data as $key=>$value ){
	echo $key .": ". $value ."<br>";
}

$select_sql="SELECT 
SYS_CONTEXT ( 'userenv', 'CURRENT_USER' )        curr_user
, SYS_CONTEXT ( 'userenv', 'DB_DOMAIN' )           db_domain
, SYS_CONTEXT ( 'userenv', 'HOST' )                host
FROM dual";
$oracle_data = ($dbh->select($select_sql))[0];
foreach( $oracle_data as $key=>$value ){
	echo $key .": ". $value ."<br>";
}*/
//$dbh->insert($insert_sql);

?>	  
	  
	  
	  