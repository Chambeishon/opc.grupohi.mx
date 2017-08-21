<?php
//en ejecucion
//require_once("C:Apache24/htdocs/opc-pruebas/sgwc/npv/class.phpmailer.php");
require_once('D:/xampp/htdocs/opc.grupohi.mx/sgwc/npv/class.phpmailer.php');
//CORREO
$mail             = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth   = true;
//$mail->SMTPSecure = "ssl";
$mail->Host       = "mail.hermesconstruccion.com.mx";
$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
$mail->Password   = "hz9dzt";
$mail->Port       = 25;
$mail->From       = "ContratosDeConcesiones@grupohi.mx";
$mail->FromName   = utf8_decode("Módulo de Contratos de Concesiones");

//ARREGLO DE CORREOS
$array_correos = array(
    'khernandezz@grupohi.mx'
	,'oaguayo@grupohi.mx'
	);

//CONEXION OPI
function conectar_sql_server(){
	$serverName = '172.20.74.3\GHIAPP'; //serverName\instanceName
	$connectionInfo = array( "Database"=>"opi", "UID"=>"oaguayo", "PWD"=>"2014_opc7");
	//$conn = sqlsrv_connect( $serverName, $connectionInfo);

	if(!$link=sqlsrv_connect($serverName,$connectionInfo)){
		$mail->Subject    = "Mensaje de conexion OPI APV";
		$texto="No se pudo conectar al servidor opi correo actividades proximas a vencer %s\n";
		$mail->Body		= $texto;
		$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
		foreach($array_correos as $a_correo):
			$mail->AddBCC($a_correo);
		endforeach;
		if(!$mail->Send()) {
			echo "El correo no ha sido enviado: ".$mail->ErrorInfo;
		}
		else {
			echo "El correo ha sido enviado";
		}
		exit();
	}

	return $link;
}

$link = conectar_sql_server();


$sql_select ="SELECT DISTINCT(idproyecto) FROM doc_contrato";
$res_select = sqlsrv_query($link,$sql_select);


$idproyectos = array();
while($idp = sqlsrv_fetch_array($res_select)):
	$idproyectos[] = $idp;
endwhile;

foreach ($idproyectos as $id):
	$idproyecto = $id["idproyecto"];
	$params=array(array($idproyecto,SQLSRV_PARAM_IN));
	$stmt = sqlsrv_query($link,'{CALL sp_doc_notificacion_mensaje_dias (?)}',$params);
	while( $dato = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ):
	if(isset($dato["mensaje"])):
		echo $dato["mensaje"];
	else:
		$idprogramacion = $dato["idprogramacion"];
		$cuerpo = $dato["cuerpo"];
		$color = $dato["color"];
		$idnivel = $dato["idnivel"];
		$descripcion = $dato["descripcion"];
		$query0= "SELECT * FROM vw_doc_programacion WHERE idprogramacion=".$idprogramacion." AND idestado_actividad NOT IN (1,6,5)";
		$result3 = sqlsrv_query($link,$query0);
		$row3 = sqlsrv_fetch_array($result3);
		$mail->Subject    = "Actividad P-".$idprogramacion." ".ucwords(strtolower($descripcion))." (".ucwords(strtolower($row3["nombre_proyecto"]))."-".$row3["numero_contrato"].")";
		$mail->ClearAllRecipients();
		$mail->IsHTML(true);
		$mail->Body	= '<html>
		<div style="padding:10px;border:solid 1px #ccc; font-family:Arial,Helvetica,sans-serif;font-size:12px;">
		<span style="color:#444444;font-size:16px;">
<strong>SE LE NOTIFICA QUE LA ACTIVIDAD P-'.$idprogramacion.' CORRESPONDIENTE AL CONTRATO '.$row3["numero_contrato"].' DEL PROYECTO '.$row3["nombre_proyecto"].' SE ENCUENTRA EN EL SIGUIENTE ESTADO: '.$descripcion.'</strong>
</span><br><br>'.$cuerpo.'<br><p style="color:#444444;font-size:12px;"><span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada para recibir mensajes.<br><br></span><i>Mensaje enviado autom&aacute;ticamente desde el M&oacute;dulo de Operaci&oacute;n e Infraestructura.</i></p><img src="http://intranet.grupohi.mx/ghi_mail.png"></div>';
		echo 'Para la idprogramacion='.$idprogramacion.' se le enviarian correo a las sig personas:<br>';
		$query = "select * from vw_doc_notificacion_usuario
	WHERE idprogramacion=".$idprogramacion."
	AND idnivel>=".$idnivel." and idnotificacion_color=".$color."";
		$result2 = sqlsrv_query($link,$query);
		while( $dato2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC) ) :
			echo '<span style="color:'.$dato2["color"].'">'.$dato2["correo_usuario"].'</span><br>';
			//Quitar comentarios para enviar notificaciones a usuarios
			$mail->addCC($dato2["correo_usuario"]);
		endwhile;
		$mail->Priority = $idnivel;
		$mail->AddBCC('khernandezz@grupohi.mx');
		$mail->AddBCC('oaguayo@grupohi.mx');
		$mail->Send();
	endif;
	endwhile;
endforeach;


?>
