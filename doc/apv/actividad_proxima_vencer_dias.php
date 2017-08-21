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
'oaguayo@grupohi.mx'
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


$sql_select ="SELECT DISTINCT(c.idproyecto),g.nombre_proyecto FROM doc_contrato AS c INNER JOIN grl_proyecto g ON c.idproyecto=g.idproyecto;";
$res_select = sqlsrv_query($link,$sql_select);


$idproyectos = array();
while($idp = sqlsrv_fetch_array($res_select)):
	$idproyectos[] = $idp;
endwhile;

foreach ($idproyectos as $id):
	$idproyecto = $id["idproyecto"];
	$n_proyecto = $id["nombre_proyecto"];
	//busca correo
	$query = "select correo_usuario from vw_doc_notificacion_usuario WHERE idprogramacion in (select idprogramacion from vw_doc_programacion WHERE 	idproyecto=".$idproyecto." AND idestado_actividad NOT IN (1,6,5) AND (fecha < CONVERT(char(10),getdate(),126) OR fecha = CONVERT(char(10),DATEADD(day,15,GetDate()),126) 	OR fecha = CONVERT(char(10),DATEADD(day,45,GetDate()),126) OR fecha = CONVERT(char(10),DATEADD(day,46,GetDate()),126)) ) group by correo_usuario";

	$result2 = sqlsrv_query($link, $query);
	while( $dato2 = sqlsrv_fetch_array( $result2, SQLSRV_FETCH_ASSOC) ) :
		$correo=$dato2["correo_usuario"];
		//procedimiento
		$params=array(array($idproyecto,SQLSRV_PARAM_IN),array($correo,SQLSRV_PARAM_IN));
		$stmt = sqlsrv_query($link,'{CALL sp_doc_notificacion_resumen_vence_x_usuario (?,?)}',$params);
		$html='<html>
		<div style="padding:10px;border:solid 1px #ccc; font-family:Arial,Helvetica,sans-serif;font-size:12px;">
		<span style="color:#444444;font-size:16px;">
<strong>SE LE NOTIFICA QUE TIENE LAS SIGUIENTES ACTIVIDADES PENDIENTES DE ATENDER DEL PROYECTO '.$n_proyecto.'</strong>
</span><br><br>
<table style="text-align:left;font-size:10px;color:#000" width="100%">
	<thead>
	<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
		<th>Id</th>
		<th>Actividad</th>
		<th>Contrato</th>
		<th>Descripci&oacute;n</th>
		<th>Fecha Limite</th>
		<th>Estado</th>
	</tr>
	</thead>
	<tbody>';
		while( $dato = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ):
			if(isset($dato["mensaje"])):
				//echo $dato["mensaje"];
			else:
				$idprogramacion = $dato["idprogramacion"];
				$cuerpo = $dato["cuerpo"];
				$color = $dato["color"];
				$idnivel = $dato["idnivel"];
				$descripcion = $dato["descripcion"];
				$nombre_proyecto = $dato["nombre_proyecto"];
				$numero_contrato = $dato["numero_contrato"];
				$correo_usuario = $dato["correo_usuario"];
				$html .= $cuerpo;
			endif;
		endwhile;
		//echo $correo_usuario."<br>";
		$html .= '</tbody></table>
		<br><p style="color:#444444;font-size:12px;"><span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada para recibir mensajes.<br><br></span><i>Mensaje enviado autom&aacute;ticamente desde el M&oacute;dulo de Operaci&oacute;n e Infraestructura.</i></p><img src="http://intranet.grupohi.mx/ghi_mail.png"></div></html>';

		//datos del correo
		$mail->Subject    = "Resumen de Actividades Pendientes de Atender del Proyecto ".$nombre_proyecto;
		$mail->ClearAllRecipients();
		$mail->IsHTML(true);
		$mail->Body	= $html;

		//Quitar comentarios para enviar notificaciones a usuarios
		$mail->AddAddress($correo_usuario);
		//echo $correo_usuario;
		//echo $html."<br><br><br>";

		$mail->Priority = $idnivel;
		//$mail->AddBCC('khernandezz@grupohi.mx');
		$mail->AddBCC('oaguayo@grupohi.mx');
		$mail->Send();
	endwhile;

endforeach;


?>
