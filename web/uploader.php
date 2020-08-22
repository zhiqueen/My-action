<?php
// 判断是否为POST请求且POST参数和FILES非空
if (!empty($_GET) || empty($_POST) || empty($_FILES) 
|| empty($_POST['token']) || empty($_POST['location'])){http_response_code('405');die("Method Not Allowed. or Check Arguements please!\n");}

// Token
$token = '123123';
if ($_POST['token'] !== $token){http_response_code('403');die("403 Forbidden. Invalid Access Token!\n");}

// 只取文件后缀名
function fileext($filename){
	return substr(strrchr($filename, '.'), 1);
}

// 只取文件名
function perfix($filename){
	return substr(basename($filename),0,strrpos(basename($filename),'.'));
}

function unzip($zipfile, $path){
    $zip = new ZipArchive;
    if ($zip->open($zipfile)) {
        $zip->extractTo($path);
        $zip->close();
        return unlink($zipfile);
    } else {
        return false;
    }
}

$type = array("zip", "rar", "img", "raw", "gz", "tar", "tgz" ,"vmdk", "qrow", "bin");
$fileext = fileext($_FILES['file']['name']);
$target_location = $_POST['location'];
$target_file_name = date("m-d-H:i") .'-'. perfix($_FILES['file']['name']) . '.' . fileext($_FILES['file']['name']);

// 保存Log日志
$fname = $_FILES['file']['name'];
$fsize = $_FILES['file']['size'];
$ftype = $_FILES['file']['type'];
$ferror = $_FILES['file']['error'];
$log_text = date("m-d-H:i") .'  '. "Requset: FileName: $fname FileType: $ftype FileError: $ferror FileSize: $fsize ToLocation: $target_location ToName: $target_file_name \n";
file_put_contents("upload_log.txt", $log_text, FILE_APPEND);


// 判断上传文件类型
if(in_array($fileext, $type)){
	if(is_uploaded_file($_FILES['file']['tmp_name'])){
		$flag = move_uploaded_file($_FILES['file']['tmp_name'], $target_location.'/'.$target_file_name);
		if($flag){
			echo("Success upload\n");
		}else{
			die("Failed\n");
		}
	}
}else{
die("Invalid Type\n");
}

//$dir = iconv("UTF-8", "GBK", $target_location);

if (!file_exists($target_location)){
    mkdir ($dir,0777,true);
}

if($fileext == "zip"){
    if(unzip($target_location.'/'.$target_file_name, $target_location)){
        die("Success unzip\n");
    }else{
    die("Success upload but unzip faild");
    }
}
?>
