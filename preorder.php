<?php


require ("config.php");


if(empty($_POST['type']) || ($_POST['type']!='payeer' && $_POST['type']!='fk')){
echo 'preorder_can=0;';
exit;
}


if(empty($_POST['sum'])){
echo 'preorder_can=0;';
exit;
}


$sum=preg_replace("#[^0-9\.\,]+#",'',$_POST['sum']);
$sum=str_replace(',','.',$sum);
$sum=preg_replace("#\.+#",'.',$sum);
$sum=number_format($sum,2,'.','');


if(empty($sum) || !is_numeric($sum)){
echo 'preorder_can=0;alert("¬ведите корректную сумму");';
exit;
}


if($sum<$up_min_sum){
echo 'preorder_can=0;alert("—умма не меньше '.$up_min_sum.'");';
exit;
}


if($sum>20000){
echo 'preorder_can=0;alert("—умма не больше 20000");';
exit;
}


// ______________________ PAYEER ______________________


if($_POST['type']=='payeer'){


$m_orderid=time();
$m_curr='RUB';
$m_desc='cGF5bWVudA==';


$m_amount=$sum;


$arHash=array(
$m_shop,
$m_orderid,
$m_amount,
$m_curr,
$m_desc,
$m_key
);


$sign=strtoupper(hash('sha256',implode(':',$arHash)));


echo '
document.getElementById("payeer_order_id").value="'.$m_orderid.'";
document.getElementById("payeer_amount").value="'.$m_amount.'";
document.getElementById("payeer_sign").value="'.$sign.'";
with(document.getElementById("payeer_up_form")){ submit(); }
';


}


// ______________________ FREE-KASSA ______________________


if($_POST['type']=='fk'){


$sign=md5($fk_id.':'.$sum.':'.$fk_secret.':'.$u_id);


echo '
document.getElementById("fk_amount").value="'.$sum.'";
document.getElementById("fk_sign").value="'.$sign.'";
with(document.getElementById("fk_up_form")){ submit(); }
';

}


?>
