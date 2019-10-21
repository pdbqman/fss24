<?php
# Класс для работы с файлами на сайте FSS24.ru
class FSS24_FILE {
	# Дирректория где лежат подписанные отчеты .ef4
	public	$dir_EF4	= 	'\nullxmltof4';
	private $type_EF4	=	'ef4';

	
	# Чтение/Поиск файлов из папки
	public function read_all_file( $dir, $type_file, &$array )
	{
		$f = scandir( realpath( $dir ) );
		foreach ($f as $file){
			if(preg_match("/\.(". $type_file .")/", $file)){
				$array[] = str_replace ("'", "", $dir . "'\'" . $file);
				//echo str_replace ("'", "", $dir . "'\'" . $file)."</br>";
			}
		}
	}
	# Чтение/Поиск только .ef4 файлов из папки
	public function read_all_ef4( &$regArrEF4 )
	{
		$this->read_all_file( dirname(__DIR__) . $this->dir_EF4, $this->type_EF4, $regArrEF4 );
	}
}
?>