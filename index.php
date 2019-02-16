<?php

require_once("config.php");


if($ssl==1 && empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "off"){
header('Location: https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
exit;
}


// ______________________ ÇÀÏÈÑÜ ÄÀÍÍÛÕ ÀÂÒÎĞÈÇÀÖÈÈ, ÇÀÎÄÍÎ ÎÁÍÎÂËßÅÌ, ×ÒÎÁÛ ÍÅ ÂÛÊÈÍÓËÎ ____________________


if($u_id>0 && !empty($mydm['uid']) && (empty($_SESSION['nat']) || $_SESSION['nat']<$time-3600)){
$_SESSION['nat']=$time;
$aq=mysqli_query($db,"SELECT id FROM auth ORDER BY id DESC LIMIT 1") or die ('cant id auth');
$am=mysqli_fetch_row($aq);
if(!empty($am[0])){
mysqli_query($db,"DELETE FROM auth WHERE id<".($am[0]-10));
mysqli_query($db,"OPTIMIZE TABLE auth") or die('cant optimize auth');
}
$browser=$_SERVER['HTTP_USER_AGENT'];
$browser=substr($browser,0,200);
$browser=preg_replace('#[^a-z0-9\/\ \.\,\;\(\)]+#i','',$browser);
mysqli_query($db,"INSERT INTO auth (date,date_word,uid,login,avatar,ip,browser) VALUES ('$time','".date('d.m.y H:i:s',$time)."','".$mydm['uid']."','".$mydm['login']."','".$mydm['avatar']."','$ip','$browser')");
mysqli_query($db,"UPDATE users SET date2='$time',date2_word='".date('d.m.y H:i:s',$time)."',ipcame='$ip' WHERE uid='$u_id' LIMIT 1") or die('cant auth users date2');
mysqli_query($db,"UPDATE room_status SET auth='$time' LIMIT 1") or die('cant auth room_status');
}


// ______________________ ÏÅĞÅÊËŞ×ÅÍÈÅ ÍÀ ÌÎÁÈËÜÍÓŞ ÂÅĞÑÈŞ ____________________


if(isset($_GET['mobile']) && ($_GET['mobile']==0 || $_GET['mobile']==1)){
$_SESSION['mobile']=$_GET['mobile'];
$mobile=$_GET['mobile'];
}


$page='';
if(!empty($_GET['page'])){
$page=preg_replace("#[^a-z\_\-0-9]+#i",'',$_GET['page']);
}


// ______________________ ÏÅĞÅÕÎÄ ĞÅÔÅĞÀËÀ ____________________


if(!empty($_GET['ref']) && empty($_SESSION['ref_id']) && empty($_SESSION['uid'])){
session_unset();
$_GET['ref']=preg_replace("#[^0-9]+#i",'',$_GET['ref']);
if(!empty($_GET['ref']) && strlen($_GET['ref'])<7){
$ref_q=mysqli_query($db,"SELECT uid FROM users WHERE uid='".$_GET['ref']."' LIMIT 1") or die('cant set referer');
$ref_m=mysqli_fetch_row($ref_q);
if(!empty($ref_m[0])){
$_SESSION['ref_id']=$ref_m[0];
}
}
}


// ______________________ ÎÒÊÓÄÀ ÏĞÈØ¨Ë ____________________


if(empty($_SESSION['uid']) && !isset($_SESSION['urlfrom']) && !empty($_SERVER['HTTP_REFERER'])){
$urlfrom=preg_replace('#[^a-z0-9\.\?\/\:\=\-\_\&]+#i','',$_SERVER['HTTP_REFERER']);
$urlfrom=substr($urlfrom,0,200);
$_SESSION['urlfrom']=$urlfrom;
}


// ______________________ ÄÀÍÍÛÅ ____________________


$datas=mysqli_query($db,"SELECT * FROM data LIMIT 1") or die('cant select data');
$d=mysqli_fetch_assoc($datas);
if(!isset($d['users'])){ exit('data error'); }


// ______________________ ÎÍËÀÉÍ - ÎÁÍÎÂËÅÍÈÅ ____________________


if(!empty($mydm['uid'])){
mysqli_query($db,"UPDATE users SET date2='$time' WHERE uid='$u_id' LIMIT 1") or die('cant update date2');
$ioq=mysqli_query($db,"SELECT id FROM online WHERE uid='".$mydm['uid']."' LIMIT 1") or die ('cant online 1');
$iom=mysqli_fetch_row($ioq);
$ion=1;
if(empty($iom[0])){
mysqli_query($db,"INSERT INTO online (ip,last_time,uid,login,avatar) VALUES ('$ip','".($time+$on_time*60)."','".$mydm['uid']."','".$mydm['login']."','".$mydm['avatar']."')") or die ('cant online 2');
$ion=0;
}
if($ion==1){
mysqli_query($db,"UPDATE online SET last_time='".($time+$on_time*60)."' WHERE uid='".$mydm['uid']."' LIMIT 1") or die ('cant online 3');
}
}


// ______________________ ÎÍËÀÉÍ - ÑÏÈÑÎÊ ____________________


$who_online='';
$who_td=0;
$who_count=0;


$who_q=mysqli_query($db,"SELECT uid,login,avatar FROM online");
while($who_m=mysqli_fetch_row($who_q)){
$who_count++;
if($who_td==0){ $who_online.='<tr>'; }
$p_url=md5($who_m[0].$p_hash);
$p_url=$who_m[0].'_'.substr($p_url,0,4);
$who_online.='<td class="head_who_online_td"><a target="_blank" href="/profile/'.$p_url.'"><div><img src="'.$who_m[2].'"><span>'.$who_m[1].'</span></div></a></td>';
$who_td++;
if($who_td>3){ $who_td=0; $who_online.='</tr>'; }
}


if($who_count==0){ $who_online='<tr><td colspan="3">Îíëàéí íèêîãî íåò</td></tr>'; }


// ______________________ ÎÍËÀÉÍ - ÓÄÀËÅÍÈÅ ____________________


if($d['online_dt']<$time){
mysqli_query($db,"DELETE FROM online WHERE last_time<$time");
mysqli_query($db,"OPTIMIZE TABLE online") or die('cant optimize online');
mysqli_query($db,"UPDATE data SET online_dt='".($time+$on_time*60)."' LIMIT 1") or die('cant update online_dt');
}


// ______________________ ÏÅĞÅÂÎÄ ÍÀ F ÊÎØÅË¨Ê ____________________


if($d['fk_b']>=50 && $d['fk_time']<$time && $fk_mode==1){
mysqli_query($db,"UPDATE data SET fk_time='".($time+60)."' LIMIT 1") or die('cant data');
$fk_send=@file_get_contents('http://www.free-kassa.ru/api.php?merchant_id='.$fk_id.'&s='.md5($fk_id.$fk_secret_2).'&action=payment&currency=fkw&amount='.$d['fk_b']);
$fk_send=iconv('utf-8','cp1251',$fk_send);
preg_match('#Çàÿâêà\ îòïğàâëåíà#',$fk_send,$fk_send_m);
if(!empty($fk_send_m)){
mysqli_query($db,"UPDATE data SET fk_b=0 LIMIT 1") or die('cant data');
}
//else{ echo $fk_send; }
}


// ______________________ ÅÆÅÄÍÅÂÍÀß ÇÀÄÀ×À ____________________


$now_d=date('j',$time);


//___________________________________ ÏĞÎÑÌÎÒĞÛ Â×ÅĞÀ, ÑÅÃÎÄÍß, ÂÑÅÃÎ _________________________________________________


if($now_d==$d['day']){
mysqli_query($db,"UPDATE data SET v_n=v_n+1,v_a=v_a+1 LIMIT 1") or die('cant update views');
}


if($now_d!=$d['day']){
// ÑÒÈĞÀÅÌ ÑÒÀĞÛÅ ÇÀÏÈÑÈ REQUEST
mysqli_query($db,'DELETE FROM request WHERE rdate1<'.($time-86400*$h_time));
// ÑÒÈĞÀÅÌ ÑÒÀĞÛÅ ÇÀÏÈÑÈ ĞÅÔÅĞÀËÜÍÛÕ ÍÀ×ÈÑËÅÍÈÉ
mysqli_query($db,'DELETE FROM refs_profit WHERE date<'.($time-86400*$rp_time));
// ÎÁÍÓËßÅÌ ÊÎËÈ×ÅÑÒÂÎ ÍÎÂÛÕ ÇÀĞÅÃÈÑÒĞÈĞÎÂÀÍÍÛÕ Ó×ÀÑÒÍÈÊÎÂ
mysqli_query($db,"UPDATE data SET users_today=0,day=$now_d,v_y=v_n,v_n=1,v_a=v_a+1 LIMIT 1") or die('cant update');
// ÑÒÈĞÀÅÌ ÑÒÀĞÛÅ ÂÛÈÃĞÛØÈ ÎÁÛ×ÍÎÉ ÊÎÌÍÀÒÛ
$rw_a=array();
$rw_q=mysqli_query($db,"SELECT num,id FROM room_winners ORDER BY id DESC") or die ('cant select room_winners');
while($rw_m=mysqli_fetch_row($rw_q)){
if(!isset($rw_a[$rw_m[0]])){ $rw_a[$rw_m[0]]=array(0,0); }
if($rw_a[$rw_m[0]][0]<15){
$rw_a[$rw_m[0]][0]++;
$rw_a[$rw_m[0]][1]=$rw_m[1];
}
}
foreach($rw_a as $rw_i=>$rw_v){
if($rw_v[0]==15){
mysqli_query($db,"DELETE FROM room_winners WHERE num=".$rw_i." AND id<".$rw_v[1]);
}
}
// ÑÒÈĞÀÅÌ ÑÒÀĞÛÅ ÂÛÈÃĞÛØÈ ÔÈÊÑÈĞÎÂÀÍÍÎÉ ÊÎÌÍÀÒÛ
$rfw_a=array();
$rfw_q=mysqli_query($db,"SELECT num,id FROM room_fix_winners ORDER BY id DESC") or die ('cant select room_fix_winners');
while($rfw_m=mysqli_fetch_row($rfw_q)){
if(!isset($rfw_a[$rfw_m[0]])){ $rfw_a[$rfw_m[0]]=array(0,0); }
if($rfw_a[$rfw_m[0]][0]<50){
$rfw_a[$rfw_m[0]][0]++;
$rfw_a[$rfw_m[0]][1]=$rfw_m[1];
}
}
foreach($rfw_a as $rfw_i=>$rfw_v){
if($rfw_v[0]==50){
mysqli_query($db,"DELETE FROM room_fix_winners WHERE num=".$rfw_i." AND id<".$rfw_v[1]);
}
}
// ÓÄÀËßÅÌ ÑÒÀĞÛÅ ÑÎÎÁÙÅÍÈß _______________
mysqli_query($db,"DELETE FROM dialog WHERE ddate<".($time-$d_mess_old)) or die('cant delete old dialog');
// ÎÏÒÈÌÈÇÈĞÓÅÌ ÒÀÁËÈÖÛ
mysqli_query($db,"OPTIMIZE TABLE request,refs_profit,room_status,room_winners,room_fix_winners,users,news,data,dialog") or die('cant optimize common');
// ÑÒÈĞÀÅÌ ÑÒÀĞÛÅ ÑÅÑÑÈÈ
$ses_t=$time-2*86400; // ×ÅĞÅÇ ÑÊÎËÜÊÎ ÄÍÅÉ ÓÄÀËßÒÜ ÑÅÑÑÈŞ ÍÅÀÊÒÈÂÍÎÃÎ ÏÎËÜÇÎÂÀÒÅËß
$ses_d=opendir('ses/');
while($ses_f=readdir($ses_d)){
if($ses_f!='.' && $ses_f!='..' && $ses_f!='.htaccess' && $ses_f!='index.html' && filemtime('ses/'.$ses_f)<$ses_t){
@unlink('ses/'.$ses_f);
}}
closedir($ses_d);
}


require("pages/head.php");



$pads=0;


if($mobile==0){ $left_width='width="210px"'; $pads=15; }


echo '<a name="tomenu"></a>
<table class="common" width="100%" cellpadding="0px" cellspacing="'.$pads.'px">
<tr>
<td class="left"'.$left_width.'>';


require("pages/left.php");


if($mobile==0){ echo '</td><td class="center">'; }
else{ echo '</td><tr><td class="center">'; }


// ______________________ ÑÒĞÀÍÈÖÛ ÑÀÉÒÀ ____________________


$nay=0;


if(!empty($page)){
if(in_array($page,$inc)){ $nay=1; require('pages/'.$page.'.php'); }
elseif($nay==0 && $u_id>0 && in_array($page,$inc_cab)){
$nay=1;
if($page=='dialog'){ require('im/dialog.php'); }
else{ require('cabinet/'.$page.'.php'); }
}
elseif($nay==0 && $u_id==0 && $page=='login'){ $nay=1; require('pages/login.php'); }
elseif($nay==0 && $u_id>0 && $u_id==$admin_id && in_array($page,$inc_adm)){
$nay=1;
$is_admin=1;
echo '<div class="a_common">';
if(empty($_SESSION['admin_ip']) || $_SESSION['admin_ip']!=$ip){ $is_admin=0; require('admin/a_auth.php'); }
if($is_admin==1){
$a_menu='<div class="a_menu_div">
<a class="a_menu_0" href="/?page=a_stat">Ñòàòèñòèêà</a>
<a class="a_menu_0" href="/?page=a_users">Ïîëüçîâàòåëè</a>
<a class="a_menu_0" href="/?page=a_up">Ïîïîëíåíèÿ</a>
<a class="a_menu_0" href="/?page=a_w">Âûïëàòû</a>
<a class="a_menu_0" href="/?page=a_rp">Ğåôåğàëüíûå</a>
<a class="a_menu_0" href="/?page=a_news_list">Íîâîñòè</a>
<a class="a_menu_0" href="/?page=a_options">Íàñòğîéêè</a>
</div>
<div id="a_message" class="a_message"></div>';
$a_menu=str_replace("\r\n",'',$a_menu);
$a_menu=str_replace("\n",'',$a_menu);
$a_menu=str_replace('0" href="/?page='.$page,'1" href="/?page='.$page,$a_menu);
echo $a_menu;
if($page=='im'){ require('im/im.php'); }
else{ require('admin/'.$page.'.php'); }
}
echo '</div>';
}

}


if($nay==0){ require('pages/main.php'); }


echo '</td></tr></table>';


if($u_id>0 && $mobile==0){
if($mydm['nrc']!=0 && $mydm['h_nrc']!=1){
$upcs='nrc=0';
$nrc_v=substr($mydm['nrc'],-2);
$nrc_1=array(2,3,4,22,23,24,32,33,34,42,43,44,52,53,54,62,63,64,72,73,74,82,83,84,92,93,94);
$nrc_2=array(1,21,31,41,51,61,71,81,91);
$nrc_w='×ÅËÎÂÅÊ';
if(in_array($nrc_v,$nrc_1)){ $nrc_w='×ÅËÎÂÅÊÀ'; }
elseif(in_array($nrc_v,$nrc_2)){ $nrc_w='×ÅËÎÂÅÊ'; }
$td_nrc='<td><div class="upper_nrc_div"><div class="upper_nrc_title">Íîâûå ğåôåğàëû</div><div class="upper_nrc_value">'.$mydm['nrc'].' '.$nrc_w.'</div></div></td>';
}
if($mydm['nrs']!=0 && $mydm['h_nrs']!=1){
if(empty($upcs)){ $upcs='nrs=0'; }
else{ $upcs.=',nrs=0'; }
$td_nrs='<td><div class="upper_nrs_div"><div class="upper_nrs_title">Ğåôíà÷èñëåíèÿ</div><div class="upper_nrs_value">'.$mydm['nrs'].' ĞÓÁ.</div></div></td>';
}
if(!empty($upcs)){
mysqli_query($db,"UPDATE users SET $upcs WHERE uid='$u_id' LIMIT 1") or die('cant update upcs');
echo '<div id="upper" class="upper" style="display:block;" onClick="this.style.display=\'none\';"><table align="center" cellpadding="0px" cellspacing="0px"><tr>'.$td_nrc.$td_nrs.'</tr></table></div>';
}
}


if($u_id>0){


if(!empty($_POST['mess']) && !empty($mydm['mess'])){
mysqli_query($db,"UPDATE users SET mess='' WHERE uid='$u_id' LIMIT 1");
$mydm['mess']='';
}


if(!empty($mydm['mess'])){


echo '
<form id="form_mess" style="display:none;" method="POST"><input type="hidden" name="mess" value="1"></form>

<div id="mess" class="mess" onClick="with(document.getElementById(\'form_mess\')){ submit(); }">

<div class="mess_common">
<table align="center" cellpadding="0px" cellspacing="0px">
<tr>
<td class="mess_title"><div>Ñîîáùåíèå îò àäìèíèñòğàöèè</div></td></td>
<td class="mess_close"><div title="Çàêğûòü" onClick="with(document.getElementById(\'form_mess\')){ submit(); }">X<div/a></td>
</tr>
<tr>
<td colspan="2" class="mess_text"><div>'.$mydm['mess'].'</div></td>
</tr>
</table>
</div>

</div>';
}
}


echo '<div class="footer">';


echo '
<a href="#" onclick="doGTranslate(\'ru|ar\');return false;" class="gflag nturl" style="background-position:-100px -0px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30"/></a>
<a href="#" onclick="doGTranslate(\'ru|az\');return false;" class="gflag nturl" style="background-position:-500px -600px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|be\');return false;" class="gflag nturl" style="background-position:-0px -600px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|en\');return false;" class="gflag nturl" style="background-position:-0px -0px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|fr\');return false;" class="gflag nturl" style="background-position:-200px -100px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|de\');return false;" class="gflag nturl" style="background-position:-300px -100px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|ko\');return false;" class="gflag nturl" style="background-position:-0px -200px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|ru\');return false;" class="gflag nturl" style="background-position:-500px -200px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>
<a href="#" onclick="doGTranslate(\'ru|uk\');return false;" class="gflag nturl" style="background-position:-100px -400px;"><img src="//gtranslate.net/flags/blank.png" height="30" width="30" /></a>

<style type="text/css">
a.gflag {vertical-align:middle;font-size:32px;padding:1px 0;background-repeat:no-repeat;background-image:url(//gtranslate.net/flags/32.png);}
a.gflag img {border:0;}
a.gflag:hover {background-image:url(//gtranslate.net/flags/32a.png);}
#goog-gt-tt {display:none !important;}
.goog-te-banner-frame {display:none !important;}
.goog-te-menu-value:hover {text-decoration:none !important;}
body {top:0 !important;}
#google_translate_element2 {display:none!important;}
</style>

<div id="google_translate_element2"></div>
<script type="text/javascript">
function googleTranslateElementInit2() { new google.translate.TranslateElement({pageLanguage: \'ru\',autoDisplay: false}, \'google_translate_element2\');}
</script>
<script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit2"></script>
';
?>


<script type="text/javascript">
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('6 7(a,b){n{4(2.9){3 c=2.9(\"o\");c.p(b,f,f);a.q(c)}g{3 c=2.r();a.s(\'t\'+b,c)}}u(e){}}6 h(a){4(a.8)a=a.8;4(a==\'\')v;3 b=a.w(\'|\')[1];3 c;3 d=2.x(\'y\');z(3 i=0;i<d.5;i++)4(d[i].A==\'B-C-D\')c=d[i];4(2.j(\'k\')==E||2.j(\'k\').l.5==0||c.5==0||c.l.5==0){F(6(){h(a)},G)}g{c.8=b;7(c,\'m\');7(c,\'m\')}}',43,43,'||document|var|if|length|function|GTranslateFireEvent|value|createEvent||||||true|else|doGTranslate||getElementById|google_translate_element2|innerHTML|change|try|HTMLEvents|initEvent|dispatchEvent|createEventObject|fireEvent|on|catch|return|split|getElementsByTagName|select|for|className|goog|te|combo|null|setTimeout|500'.split('|'),0,{}))
</script>


<?php


if($mobile==0){
echo '<div class="footer_own">© '.date('Y').'
<a href="//www.free-kassa.ru/"><img src="/images/16.png"></a>
<a href="https://www.fkwallet.ru"><img src="https://www.fkwallet.ru/assets/2017/images/btns/iconsmall_wallet9.png" title="Êğèïòîâàëşòíûé êîøåë¸ê"></a>
<a href="https://payeer.com/" target="_blank"><img src="/images/payeer_b.png"></a>
</div>';
}


echo '</div>';


echo '</body></html>';


?>
