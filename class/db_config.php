<?php
include_once dirname(__DIR__) . "/class/class_parser.php";

# Класс для работы с конфигурациями БД
class db_config extends class_parser{
	# Выбрана таблица
	private $tbl_name			=	null;
	# Поля таблицы
	private $config_select_arr	= array();
	# SQL запрос
	private	$sql				=	null;
		
	public function __construct( $tbl_name, $update ){	
		# Сохраним наименование таблицы
		$this->tbl_name = $tbl_name;
		
		# Вытаскиваем конфигурацию таблицы
		$this->config_select_arr = $this->config_select( $tbl_name );
		
		# $this->config_select_arr['date_for_update'] = Время последнего обновления таблицы + Время через которое нужно обновлять таблицу
		# Сравним с текущим временем
		if ( (strtotime("now") > strtotime($this->config_select_arr['date_for_update'])) || $update == 1 ){
			# Парсим !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
			parent::$tbl_name(); // вызов ф-ии например db_list_shema
			# Если нужно обновить конфигурации
			$this->config_update ( $tbl_name );
			# Вытаскиваем конфигурацию таблицы
			$this->config_select_arr = $this->config_select( $tbl_name );
		}
    }
	public function __destruct(){
		unset($this->config_select_arr);
		unset($this->sql);
    }
	
	# Форматируем дату из SQL в PHP
	public function format_date_sql_for_php( $date, $format = "d.m.Y H:i" ){
		# Преобразуем в нужный формат
		return date( $format, strtotime( $date ) );
	}
	# Функция для управления конфигурациями таблиц
	public function config_select( $tbl_name ){
		# Переменные
		$this->sql	= " SELECT *, ADDTIME(`date`, `time_for_update`) as date_for_update 
					FROM `config_update` 
					WHERE tbl_name ='$tbl_name'";								// Запрос
		$date	= date("Y-m-d H:i:s");											// Текущая дата для метки обновления
		
		# Ищем конфиги для таблицы
		$item = DB::getRow( $this->sql );
		
		# Если нет конфигов, то заводим их
		if( !$item ){
			$sql_insert = "INSERT INTO `config_update` SET 
					`tbl_name` 		= :tbl_name,
					`date`			= :date,	
					`user_ip`		= :user_ip,		
					`user_name`		= :user_name
			";
			$insert_id = DB::add($sql_insert, array(
				'tbl_name'		=>	$tbl_name,
				'date'			=>	$date,
				'user_ip'		=>	$_SERVER['REMOTE_ADDR'],
				'user_name'		=>	$_SESSION["login"]
			));
			
			$item = DB::getRow( $this->sql );
		}
		
		return $item;
	}
	
	# Функция для обновления конфигураций таблиц
	public function config_update( $tbl_name ){
		$this->sql = "UPDATE `config_update` 
				SET
					`date`			= :date,
					`user_ip`		= :user_ip,
					`user_name`		= :user_name
				WHERE tbl_name = '$tbl_name'
		";												// Запрос
		
		$date	= date("Y-m-d H:i:s");					// Текущая дата для метки обновления
		
		$update = DB::set($this->sql, array(
			'date'		=> $date, 
			'user_ip'	=> $_SERVER['REMOTE_ADDR'],
			'user_name'	=> $_SESSION["login"],
		));
	}
	
	public function html_header_full(){
		# НАИМЕНОВАНИЕ ТАБЛИЦЫ
		if ($this->config_select_arr['tbl_caption'] == null )
			$header = "Название еще не придумано"; 
		else 
			$header = $this->config_select_arr['tbl_caption'];
		# Дата ОБНОВЛЕНИЯ
		if ( $this->config_select_arr['time_for_update'] == null )
			$title="Дата следующего обновления не установлено!"; 
		else 
			$title="Следующее обновление: " . $this->format_date_sql_for_php($this->config_select_arr['date_for_update']) . " (через каждые " . $this->format_date_sql_for_php($this->config_select_arr['time_for_update'], "H:i") . " ч.)";
					
		echo "
		<!-- НАИМЕНОВАНИЕ ТАБЛИЦЫ -->
		<h4>" . $header . "
			<!-- ВВЕРХНЯЯ ССЫЛКА -->";
		if ( $this->config_select_arr['parent_link'] != null ){
			echo "
			<span class=\"my_info_top\">
				<a class=\"badge badge-info\" href=\"" . $this->config_select_arr['parent_link'] . "\" title=\"Технологические базы данных\" target=\"_blank\">Источник</a>
			</span>";
		}
		echo "
			<!-- НИЖНЯЯ ССЫЛКА -->
			<span class=\"my_info_bottom\">
				<!-- ОБНОВЛЕНИЕ -->
				<a class=\"badge badge-success\" href=\"#\" title=\"" . $title ." \"> " .
						# Форматируем дату
						$this->format_date_sql_for_php($this->config_select_arr['date'])
				. "</a>
				<!-- ПОЛЬЗОВАТЕЛЬ -->
				<a class=\"badge badge-secondary\" href=\"#\" title=\"Пользователь и ip адрес: " . $this->config_select_arr['user_ip'] . "\"> " . 
					$this->config_select_arr['user_name'] 
				. "</a>
				<!-- КНОПКА ОБНОВЛЕНИЯ -->
				<a href=\"#\" id=\"button_" . $this->tbl_name . "\" class=\"badge badge-danger\" title=\"Обновить принудительно\">
					<i class=\"fa fa-refresh\" aria-hidden=\"true\"></i> Update
				</a>
			</span>
		</h4>";
	}
}
?>