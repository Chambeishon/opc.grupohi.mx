<?php

include_once('C:/xampp56/htdocs/opc.grupohi.mx/sgwc/npv/Net/SSH2.php');
require_once('C:/xampp56/htdocs/opc.grupohi.mx/sgwc/npv/class.phpmailer.php');

//include_once('Net/SSH2.php');
//require_once("class.phpmailer.php");

//CORREO
$mail             = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth   = true;
//$mail->SMTPSecure = "ssl";
$mail->Host       = "mail.hermesconstruccion.com.mx";
$mail->Port       = 25;
$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
$mail->Password   = "hz9dzt";
$mail->From       = "soporte_sgw@grupohi.mx";
$mail->FromName   = "ATM";
$mail->Body		= 'qwerty';

//ARREGLO DE CORREOS
$array_correos = array(
	'lahernandezg@grupohi.mx',
	'mmendiola@grupohi.mx');

	foreach($array_correos as $a_correo):
			$mail->AddCC($a_correo);
		endforeach;
		if(!$mail->Send()) {
			echo "El correo no ha sido enviado: ".$mail->ErrorInfo;
		}
		else {
			echo "El correo ha sido enviado";
		}
		exit();