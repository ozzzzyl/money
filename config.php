<?php


error_reporting(0); // ������� ������


// ______________ ����������� � ���� _______________________________


$db=mysqli_connect("localhost","root","","zendloto"); // ����, ����, ������, ����


if(mysqli_connect_errno()){ echo '��������� ����������� � ����'; exit; }
mysqli_query($db,"SET NAMES 'cp1251'");


$ssl='0'; // ���� ���������� SSL (https://), �� 1, ����� 0
if($ssl==1){ $https='https'; }
else{ $https='http'; }


$start_data='22.05.2018 10:00'; // ���� ������
$time_move=0; // ������� ���� +- ����
date_default_timezone_set('Europe/Moscow') or die('no time_zone'); // ����� ������


$admin_id=1; // ����� UID ������ �� ���� ������ � ������� USERS
$admin_pass='zend'; // ������ � �������. �� ���������� ���� ��� ����

$admin_ip=array(); // �������������. ������ ����� IP ��� ����� � ������� � �������. ���� ����� ������� � ������ IP, �� �� �������
/* �������� � ��� 2 IP
$admin_ip[]='127.0.0.1';
$admin_ip[]='189.43.20.140';
*/


$p_hash='45635345'; // ����������� ������� �� ������ �� ���� ��� ����. ���� ��� ��������.


$admin_up=1; // ��������� ������ ��������� ����������� ���������� ��������� � ��� �������. 0 - ���, 1 - ��.
$admin_pb=1; // ��������� ������ ������ ������ ��������� � ��� �������. 0 - ���, 1 - ��.
$admin_wa=1; // ��������� ������ �������� ������� ��������� � ��� �������. 0 - ���, 1 - ��.
$admin_pm=1; // ��������� ������ ���������� ����������� ��������� ������������ � ��� �������. 0 - ���, 1 - ��.


$im_max_m=20; // ������������ ���������� ��������� �� ������������ � ��������� �� 1 ��� - ������ �� ��������.


$on_time=30; // ����� ������� ����� ������� ����� �� ������ ������, ���� ���������. (�� �� ��������)


$noip_1=1; // ����������� � ������ IP. 0 - ���������, 1 - ���������
$noip_2=1; // ���� � ������ IP. ����� ��� ����������� ������ IP, � ����� ������ � ��������� �������� � ��������� IP. 0 - ��������, 1 - ��������
$noip_3=1; // ����������� � IP ������������� �� 1-3 ����� � �����. 0 - ���������, 1 - ���������
// ���������� ��������������, ����� ���� ���� � �����: 127.0.0.100, 127.0.0.101, 127.0.0.2, 127.0.0.194
$many_nets=1; // ����������� � ���������� ��������. 0 - ���������, 1 - ���������
// ��� ����������� ����� �������������� ���������, ��������� $noip_1=0; $noip_2=0; $noip_3=0; $many_nets=0; � ����� ������� �� �������
// �����������������, ����� � ���� � users ������� ���� �������, ���������� ��� � ������ ����, ��� ��������� �������.
// ���� � ��� 3 ��������, �� �����, ��������, ���: $my_ids=array(1,5,42);
$my_uids=array(1);


$d_mess_old=12441600; // ������� ��������� ��������� ������ ����� �������� � ���������. 12441600 - �������
$h_time=7; // ������� ���� ������� ������ ���������� ������ �������������� �� ������� request. ����� ����� ��� ������� ��������
$rp_time=31; // ������� ���� ������� ������� ����������� ����������
$last_w_c=7; // ������� ��������� ������ ���������� � ����������


$up_min_sum=2; // ����������� ����� ����������
$w_bonus_reg=1; // ������� �������� ������, ���������� ��� ����������� (��� ������ ������). 1 - ��, 0 - ���
$w_to_b_up=0; // �� ����� ����� ���� ���������, ����� ����� ���� ��������. ���� �� ����, �� ������ 0
$w_time=0; // ����� ����� ����� ����� �������� ��������� ������� (� ��������). ���� ����� 24 ����, �� $w_time=86400;


$reg_mode=0; // 0 - ����������� ����� ULOGIN.RU, 1 - ����������� �������� (���� ������ ���������� ������ �������. ����������� ����)

$bonus_reg=0.2; // ����� ��� �����������


// ______________ ������ "�������� �����" _______________________________


$bonus_gif_items=20; // ���������� ������������ �������
$bonus_gif_min=10; // ����������� ����� � ��������
$bonus_gif_max=100; // ������������ ����� � ��������
$bonus_gif_time=72000; // ����� ������� ������ ����� �������� ����� �٨ ���. 86400 - 24 ����, 72000 - 20 �����, 3600 - 1 ���
$bonus_gif_text='����� ������� 1 ��� � 20 �����.'; // �����
$bonus_gif_g=1; // 0 - ����� �������� ��� ������ ���
// 1 - ������ ��, ��� ��������� �� ������ ��
// 2 - �� ��� ����� � �� - ������ �����������, � ��������� ������� �������� ������ ���
// 3 - �� ��� ����� � �� - ������ ������ �����������, � ��������� ������� ������� ������ ���� �������� ������ $bonus_gif_up=0;
$bonus_gif_up=0; // �� ������� ������ ��������� ������ ������������, ����� �������� �����
$bonus_gif_bet_time=86400; // ������ ����� ������ ���� ������ ���� ���� ������ � ������� ���������� ������� � ��������. ���� �� �����, �� ������ 0
$bonus_gif_bet_sum=0.10; // � ����� ����� ������ ���� �� �������, ����� ���� ������� ������. �� ���� �� ������, ��� ����������� ������ � ����


// ______________ ����˨� PAYEER _______________________________


$accountNumber='P****'; // ����� ��������
$m_shop='****'; // ID ��������
$m_key='****'; // ��� ���������� ��������� ����
$apiId='****'; // ��� ������ API ID
$apiKey='****'; // ��� ������ ��������� ����


// ______________ ����˨� FREE-KASSA _______________________________


$fk_id='****'; // ID �������� (��������)
$fk_secret='****'; // ��������� ����
$fk_secret_2='****'; // ��������� ���� 2
$fk_wallet_id='****'; // F ����˨� ��� ������
$fk_api_key='****'; // API KEY ��� ������ ����� F ����˨�
$fk_mode=1; // ���������� � ������� �� F ����˨� ��� ������. 0 - ���, 1 - ��


// �����������. �������� ��� ������ ����� FREE-KASSA. ����� ��������. ������� ��� https://www.fkwallet.ru/docs#wallet_cashout

$fk_payeer=5; // �������� ��� ������ �� PAYEER
$fk_yandex=0; // �������� ��� ������ �� ������.������
$fk_qiwi=5;   // �������� ��� ������ �� QIWI


// ______________ ODNOKLASSNIKI ������ ���������� ��� ����� _______________________________


$ok_id='123456'; // ID ����������
$ok_key='123456'; // ��������� ���� ����������
$ok_secret='123456'; // ��������� ���� ����������
$ok_url=$https.'://'.$_SERVER['HTTP_HOST'].'/pages/login.php?network=odnoklassniki'; // �� �������


// ______________ FACEBOOK ������ ���������� ��� ����� _______________________________


$fb_id='123456'; // ID ����������
$fb_secret='123456'; // ��������� ����
$fb_url=$https.'://'.$_SERVER['HTTP_HOST'].'/pages/login.php?network=facebook'; // �� �������


// ______________ VKONTAKTE ������ ���������� ��� ����� _______________________________


$vk_id='123456'; // ID ����������
$vk_secret='123456'; // ��������� ����
$vk_url=$https.'://'.$_SERVER['HTTP_HOST'].'/pages/login.php?network=vkontakte'; // �� �������


// ______________ ������ _______________________________


$vk_group_id='123456'; // ID ������ ���������
$vk_group_link='https://vk.com/public123456'; // ������ �� ������ ���������. ���� ���, �� �������.
$vk_at=''; // ���� ������� �� ����������
$ok_group_link='https://ok.ru/group/53789227221144'; // ������ �� ������ � ��������������. ���� ���, �� �������.
$fb_group_link='https://www.facebook.com/'; // ������ �� ������ � FACEBOOK. ���� ���, �� �������.
$site_logo='<span class="head_logo_1">ZEND</span> <span class="head_logo_2">LOTO</span>'; // ���� �����
$site_logo_under='������ ������� �������'; // ������ �����
$site_email='support@zend-loto.com'; // ����� �� �������� "��������"
$news_copy='� ���������, ������� "ZEND LOTO".'; // ������� � ��������


// ______________ ��������� ����������� ������ _______________________________


$to_ref_1=5; // �������� �������� 1-�� ������ �� ������ ������������ ��������
$to_ref_2=2; // �������� �������� 2-�� ������ �� ������ ������������ ��������
$to_ref_3=1; // �������� �������� 3-�� ������ �� ������ ������������ ��������


$room_dont=0; // ���� ������ �������� �� ��������� ������ ��� ������ ����������.
// �� ������, ���� ����� ��������� �� � ���� ���� ��� 1-2% � ������� � ��� ��� �� �������� - ����� ������ �������


$room_first=0; // ����� ������� ����� ������� ������� ��������, ����� ��� ������ ��� ��-�������� � ������ ��������.

$room_wins=0; // 1 - �������� ����� �����������, 0 - ���������. ����� � ������� � ���������� ������� �����
// "ID ���������� ����������� � ������� ��������"


$room=array();
// ������ �������
$room[1]['min']=0.10; // ����������� ����� ������
$room[1]['max']=5; // ������������ ����� ����� ������
$room[1]['to_admin']=12; // �������� ������
$room[1]['users']=2; // ����� ������� ������� ��������� ������
$room[1]['text']='������ �������� ����� ���� ������'; // �����
$room[1]['timer']=30; // ������
// ������ �������
$room[2]['min']=1; // ����������� ����� ������
$room[2]['max']=200; // ������������ ����� ����� ������
$room[2]['to_admin']=12; // �������� ������
$room[2]['users']=2; // ����� ������� ������� ��������� ������
$room[2]['text']='������ �������� ����� ���� ������'; // �����
$room[2]['timer']=30; // ������
// ������ �������
$room[3]['min']=10; // ����������� ����� ������
$room[3]['max']=2000; // ������������ ����� ����� ������
$room[3]['to_admin']=12; // �������� ������
$room[3]['users']=2; // ����� ������� ������� ��������� ������
$room[3]['text']='������ �������� ����� ���� ������'; // �����
$room[3]['timer']=30; // ������


// ______________ ��������� ������������� ������ _______________________________


$fix_r_1=5; // �������� �������� 1-�� ������ �� ������ ������������ ��������
$fix_r_2=2; // �������� �������� 2-�� ������ �� ������ ������������ ��������
$fix_r_3=1; // �������� �������� 3-�� ������ �� ������ ������������ ��������


// � ���� ������ ������� 20 ������
// ���� ������� �� ����� - ������� Ũ ������
// ���� ������� ����� - �������� ������
// $fix[1] - 1 - ����� id ������� � ���� ������ � ������� room_fix


$fix=array();
// 1 �������
$fix[1]['bet']=0.10; // ������
$fix[1]['players']=3; // ����� ����������
$fix[1]['winners']=1; // ������� ����� ��������
$fix[1]['to_admin']=12; // �������� ������
// 2 �������
$fix[2]['bet']=0.20; // ������
$fix[2]['players']=3; // ����� ����������
$fix[2]['winners']=1; // ������� ����� ��������
$fix[2]['to_admin']=12; // �������� ������
// 2 �������
$fix[3]['bet']=0.5; // ������
$fix[3]['players']=3; // ����� ����������
$fix[3]['winners']=1; // ������� ����� ��������
$fix[3]['to_admin']=12; // �������� ������
// 4 �������
$fix[4]['bet']=1; // ������
$fix[4]['players']=3; // ����� ����������
$fix[4]['winners']=1; // ������� ����� ��������
$fix[4]['to_admin']=12; // �������� ������
// 5 �������
$fix[5]['bet']=2; // ������
$fix[5]['players']=3; // ����� ����������
$fix[5]['winners']=1; // ������� ����� ��������
$fix[5]['to_admin']=12; // �������� ������
// 6 �������
$fix[6]['bet']=4; // ������
$fix[6]['players']=3; // ����� ����������
$fix[6]['winners']=1; // ������� ����� ��������
$fix[6]['to_admin']=12; // �������� ������
// 7 �������
$fix[7]['bet']=8; // ������
$fix[7]['players']=3; // ����� ����������
$fix[7]['winners']=1; // ������� ����� ��������
$fix[7]['to_admin']=12; // �������� ������
// 8 �������
$fix[8]['bet']=16; // ������
$fix[8]['players']=3; // ����� ����������
$fix[8]['winners']=1; // ������� ����� ��������
$fix[8]['to_admin']=12; // �������� ������
// 9 �������
$fix[9]['bet']=32; // ������
$fix[9]['players']=3; // ����� ����������
$fix[9]['winners']=1; // ������� ����� ��������
$fix[9]['to_admin']=12; // �������� ������
// 10 �������
$fix[10]['bet']=64; // ������
$fix[10]['players']=3; // ����� ����������
$fix[10]['winners']=1; // ������� ����� ��������
$fix[10]['to_admin']=12; // �������� ������


// ______________ �������� ����� _______________________________


// �������� ������ ����
$inc=array('profile','contest_ref','contest_bet','faq','news','contacts','room','fix');
// �������� ��� �������� � �������
$inc_cab=array('cab','bonus','bup','w','refs','dialog');
// �������� ��� ������
$inc_adm=array('a_stat','a_users','a_options','a_rp','a_up','a_w','a_news_list','a_news_edit','im');


// ______________ ������ �� ������� �������� _______________________________


$profile_link=array();
$profile_link['vkontakte']='https://vk.com/id';
$profile_link['odnoklassniki']='https://ok.ru/profile/';
$profile_link['facebook']='https://www.facebook.com/app_scoped_user_id/';


// ______________ ����� �� �������  _______________________________


$time=time()+$time_move*3600;


// ______________ room_status _______________________________


if(isset($_GET['room_status'])){


$r_q=mysqli_query($db,"SELECT * FROM room_status LIMIT 1") or die();
$r_m=mysqli_fetch_assoc($r_q);


foreach($r_m as $r_mi=>$r_mv){
if(preg_match('#room\_[0-9a-z]+$#',$r_mi)){
echo 'if(document.getElementById("head_'.$r_mi.'")!=null && document.getElementById("head_'.$r_mi.'").innerHTML!=\''.$r_mv.'\'){
document.getElementById("head_'.$r_mi.'").innerHTML=\''.$r_mv.'\'; }';
}
}


exit;
}


ini_set('session.use_cookies','On'); ini_set('session.use_trans_sid','Off'); ini_set('session.gc_maxlifetime',31536000);
ini_set('session.cookie_lifetime',31536000); session_set_cookie_params(31536000, '/');
ini_set('session.cookie_httponly',1); // ������ �� ����� ����
if(preg_match('/ajax/',$_SERVER['REQUEST_URI']) || preg_match('/pages/',$_SERVER['REQUEST_URI'])){ ini_set('session.save_path','../ses/'); }
else{ ini_set('session.save_path','ses/'); }
session_start();


if(!isset($_SESSION)){ echo 'no session'; exit; }


// ______________ ����� _______________________________


if(isset($_GET['exit']) && !empty($_SESSION['uid'])){
mysqli_query($db,"DELETE FROM online WHERE uid='".$_SESSION['uid']."'");
unset($_SESSION['uid']);
unset($_SESSION['sponsor']);
unset($_SESSION['admin_ip']);
}


$u_id=0;
if(!empty($_SESSION['uid']) && $_SESSION['uid']>0){
$u_id=$_SESSION['uid'];
}


if(empty($_SESSION['mobile'])){ $mobile=0; }
else{ $mobile=1; }


if($u_id>0){
$mydq=mysqli_query($db,"SELECT * FROM users WHERE uid='$u_id' LIMIT 1") or die('cant get mydm');
$mydm=mysqli_fetch_assoc($mydq);


if(empty($mydm)){
echo 'no my data found';
exit;
}


if($mydm['ban']==1){
unset($_SESSION['uid']);
echo '<div style="text-align:center;color:#ff0000;font-family:arial;font-size:16px;">�� �������� !</div>';
exit;
}


if(!isset($mydm['balance']) || $mydm['balance']<0){
unset($_SESSION['uid']);
echo '<div style="text-align:center;color:#ff0000;font-family:arial;font-size:16px;">������ ������ ���� '.$mydm['balance'].'<br>�������� ������.<br>ID: '.$mydm['uid'].'</div>';
exit;
}


}


// ____________________________ IP _______________________________


if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'),'unknown'))
$ip=getenv('HTTP_CLIENT_IP');
elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown'))
$ip=getenv('HTTP_X_FORWARDED_FOR');
elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv("REMOTE_ADDR"), 'unknown'))
$ip=getenv('REMOTE_ADDR');
elseif(!empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown'))
$ip=$_SERVER['REMOTE_ADDR'];
else{$ip='unknown';}


if(empty($ip) || $ip=='unknown'){ exit; }
$ip=preg_replace('#[^0-9\.]+#i','',$ip);
if(strlen($ip)>15){ exit; }


// ____________________________ ������ �������� _______________________________


if(!empty($_POST) && $_SERVER['REQUEST_URI']!='/im/dialog_ajax.php'){
$rpost='';
foreach($_POST as $post_i=>$post_v){
$rpost.=$post_i.'='.$post_v.',';
}
$rpost=preg_replace('#[^a-z0-9\.\,\:\=\-\+\_\&]+#i','',$rpost);
$rpost=substr($rpost,0,200);
if(!empty($rpost)){
$rurl=preg_replace('#[^a-z0-9\.\?\/\:\=\-\_\&]+#i','',$_SERVER['REQUEST_URI']);
$rurl=substr($rurl,0,200);
$rref=preg_replace('#[^a-z0-9\.\?\/\:\=\-\_\&]+#i','',$_SERVER['HTTP_REFERER']);
$rref=substr($rref,0,200);
mysqli_query($db,"INSERT request (rdate1,rdate2,ruid,rlogin,ravatar,rip,rb,rpost,rurl,rref) VALUES ('$time','".date('d-m-y H:i:s')."','".$u_id."','".$mydm['login']."','".$mydm['avatar']."','$ip','".$mydm['balance']."','$rpost','$rurl','$rref')");
}
}


?>
