<?php

define("ID",		0);
define("CHANNEL",	1);
define("PSEUDO",	2);
define("TEMOIN",	3);
define("MESSAGE",	4);
define("MAX",		5);


// does the actual 'html' and 'sql' sanitization. customize if you want. 
function sanitizeText($text) 
{ 
    $text = str_replace("<", "&lt;", $text); 
    $text = str_replace(">", "&gt;", $text); 
    $text = str_replace("\"", "&quot;", $text); 
    $text = str_replace("'", "&#039;", $text); 
    
    // it is recommended to replace 'addslashes' with 'mysql_real_escape_string' or whatever db specific fucntion used for escaping. However 'mysql_real_escape_string' is slower because it has to connect to mysql. 
    $text = addslashes($text); 

    return $text; 
} 



$action = $_POST['action'];

switch ($action) {
    case 'post':
	post();
        break;
    case 'get':
	get();
        break;
    case 'attempt':
	attempt();
        break;
    case 'login':
	login();
        break;
    case 'alive':
	alive();
        break;
}

function get() {
	clear();

	if (isset($_POST['channel']) && isset($_POST['channel'][0])) {
		$listNew = '';
		$listLogin = '';
		$channel = $_POST["channel"];
		$exclude = $_POST["exclude"];

		if (($handle = getHandler("r")) !== FALSE) {
			while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
				if (count($data) == MAX) {
					if ($data[CHANNEL] == $channel && (float) ($data[ID]) > (float)($exclude)) {
						$listNew .= '{';
						$listNew .= '	"id": "' . $data[ID] . '",';
						$listNew .= '	"pseudo": "' . $data[PSEUDO] . '",';
						$listNew .= '	"msg": "' . str_replace('"',"'",$data[MESSAGE]) . '",';
						$listNew .= '	"temoin": "' . str_replace('"',"'",$data[TEMOIN]) . '"';
						$listNew .= '},';
					}
				} else {
					//var_dump($data);
				}
			}
			fclose($handle);

			if (($handle = getHandler("r", "users.csv")) !== FALSE) {
				while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
					if (count($data) == MAX - 1) {
						if ($data[CHANNEL] == $channel) {
							$listLogin .= '{';
							$listLogin .= '	"id": "' . $data[ID] . '",';
							$listLogin .= '	"pseudo": "' . $data[PSEUDO] . '",';
							$listLogin .= '	"temoin": "' . str_replace('"',"'",$data[TEMOIN]) . '"';
							$listLogin .= '},';
						}
					} else {
						//var_dump($data);
					}
				}
				fclose($handle);
				echo('{ "messages": ['. trim($listNew,',') .'], "users": ['. trim($listLogin,',') .'] }');
			} else {
				echo('WAIT');
			}

		} else {
			echo('WAIT');
		}
	}
}

function post() {
	if (($handle = getHandler("a+")) !== FALSE) {
		fputcsv($handle, array(str_replace(".","",getTime()), sanitizeText($_POST["channel"]), sanitizeText($_POST["pseudo"]), sanitizeText($_POST["temoin"]), sanitizeText($_POST["msg"])));
		fclose($handle);
		echo('OK');
	} else {
		echo('WAIT');
	}
}

function clear() {
	$time = getTime();
	$csv = '';
	
	if (($handle = getHandler("r")) !== FALSE) {
		while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
			//echo('['.$time.'-'.$data[ID].']');
			if ((((float)($time) - (float)($data[ID])) / 1200 / 1000000) < 1) { //1200 secondes = 20 minutes
				$csv .= implode(",",$data) . chr(10);
			}
		}
		fclose($handle);
		file_put_contents('messages.csv', $csv);
	}
	
	$csv = '';
	if (($handle = getHandler("r", "users.csv")) !== FALSE) {
		while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
			if ((((float)($time) - (float)($data[ID])) / 300 / 1000000) < 1) { //300 secondes = 5 minutes
				$csv .= implode(",",$data) . chr(10);
			}
		}
		fclose($handle);
		file_put_contents('users.csv', $csv);
	}
}

function getHandler($mode = "r", $file = "messages.csv") {
	if (($handle = fopen($file, $mode)) !== FALSE)
		if (flock($handle, LOCK_EX))
			return $handle;
	return false;
}

function getTime() {
	return number_format(microtime(true),6,'','');
}

function attempt() {
	if (isset($_POST['channel']) && isset($_POST['channel'][0])) {
		$list = '';
		$channel = $_POST["channel"];

		if (($handle = getHandler("r", "users.csv")) !== FALSE) {
			while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
				if (count($data) == MAX - 1) {
					if ($data[CHANNEL] == $channel) {
						$list .= '{';
						$list .= '	"id": "' . $data[ID] . '",';
						$list .= '	"pseudo": "' . $data[PSEUDO] . '",';
						$list .= '	"temoin": "' . str_replace('"',"'",$data[TEMOIN]) . '"';
						$list .= '},';
					}
				} else {
					//var_dump($data);
				}
			}
			fclose($handle);
			echo('{ "users": ['. trim($list,',') .'] }');
		} else {
			echo('WAIT');
		}
	}
}
function login() {
	$time = getTime();

	if (($handle = getHandler("a+", "users.csv")) !== FALSE) {
		fputcsv($handle, array($time, sanitizeText($_POST["channel"]), sanitizeText($_POST["pseudo"]), sanitizeText($_POST["temoin"])));
		fclose($handle);
		echo($time);
	} else {
		echo('WAIT');
	}
}
function alive() {
	$time = getTime();
	$csv = '';
	
	if (isset($_POST['id']) && isset($_POST['id'][0])) {
		if (($handle = getHandler("r", "users.csv")) !== FALSE) {
			while (($data = fgetcsv($handle, 2000, ",")) !== FALSE) {
				if ($data[ID] == $_POST['id']) {
					$data[ID] = $time;
				}
				$csv .= implode(",",$data) . chr(10);
			}
			fclose($handle);
			file_put_contents('users.csv', $csv);
		}
	}
	
	echo($time);
}




