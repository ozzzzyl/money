<?php
ini_set('max_execution_time','300');
ini_set('memory_limit','100000000');

$text='\$admin\_id';

function read_all_files($text){
$z=0;
$root='.';
$files=array('files'=>array(), 'dirs'=>array());
$directories=array();
$last_letter=$root[strlen($root)-1];
$root=($last_letter == '\\' || $last_letter == '/') ? $root : $root.DIRECTORY_SEPARATOR;
$directories[]=$root;
while (sizeof($directories)){
$dir=array_pop($directories);
if($handle= opendir($dir)){
while (false !== ($file=readdir($handle))){
if($file == '.' || $file == '..'){
continue;
}
$file=$dir.$file;
if(is_dir($file)){
$directory_path=$file.DIRECTORY_SEPARATOR;
array_push($directories, $directory_path);
$files['dirs'][]=$directory_path;
}
elseif(is_file($file)){
$g=file_get_contents($file);
if(!preg_match("#search#mi",$file) && preg_match("#$text#mi",$g)){
$z++;
$file=str_replace("\\","/",$file);
$file=str_replace("./","",$file);
echo $file.'<br>';
}}}}}
return $z;
}
$m=read_all_files($text);

if($m==0){ echo '<div align=center style=padding-top:200px;font-size:24px;font-family:arial;font-weight:normal;color:red>ме мюидемн</div>'; }

?>
