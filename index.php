<?php
include "encrypt.php";
$allow_method = ['get_file_info', 'download_file'];
$method = $_GET['method'];
if (!in_array($method, $allow_method)) {
    echo json_encode(['code' => -1 , 'msg' =>'method not found']);exit;
}

$allow_user_token = ['123456'];
$token = $_SERVER['token'] ? $_SERVER['token'] : '123456';
if (!in_array($token, $allow_user_token)) {
    echo json_encode(['code' => -1 , 'msg' =>'token invalid']);exit;
}

$file_list = [];
switch ($method)
{
    case 'get_file_info':
        $key = '111111111111111111111111';
        $company_name = ssl_decrypt($_GET['token'], $key);
        $file_path = '/var/www/nextcloud/data/admin/files/'.$company_name;
        if (empty($company_name) || !is_dir($file_path)) {
            echo json_encode(['code' => -1 , 'msg' =>'token invalid']);exit;
        }
        recurDir($file_path);
	    foreach($file_list as &$file) {
		    $file = strstr($file, $company_name);
        }
        echo json_encode(['code' => 0 , 'msg' =>'', 'data' => $file_list]);
        break;
    case 'download_file':
	$file_name = $_GET['fileName'];
	download($file_name);
}


function recurDir($pathName)
{
    global $file_list;
    //判断传入的变量是否是目录
    if(!is_dir($pathName) || !is_readable($pathName)) {
        return null;
    }
    //取出目录中的文件和子目录名,使用scandir函数
    $allFiles = scandir($pathName);
    //遍历他们
    foreach($allFiles as $fileName) {
        //判断是否是.和..因为这两个东西神马也不是。。。
        if(in_array($fileName, array('.', '..'))) {
            continue;
        }
        //路径加文件名
        $fullName = $pathName.'/'.$fileName;
        //如果是目录的话就继续遍历这个目录
        if(is_dir($fullName)) {
            //将这个目录中的文件信息存入到数组中
            recurDir($fullName);
        }else {
            //如果是文件就先存入临时变量
            $file_list[] = $fullName;
        }
    }
}
function download($file_name)
{
	//filesize();
	header("Content-Type: application/octet-stream");
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: 41");
	header("X-Accel-Redirect:/protected_files/{$file_name}");
	true;
}
