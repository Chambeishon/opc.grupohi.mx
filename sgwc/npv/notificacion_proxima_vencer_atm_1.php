<?php

include_once('C:xampp/htdocs/dev-opc.grupohi.mx/sgwc/npv/Net/SSH2.php');
require_once('C:xampp/htdocs/dev-opc.grupohi.mx/sgwc/npv/class.phpmailer.php');

//include_once('Net/SSH2.php');
//require_once("class.phpmailer.php");

//CORREO
$mail             = new PHPMailer();
$mail->IsSMTP();
$mail->SMTPAuth   = true;
//$mail->SMTPSecure = "ssl";
$mail->Host       = "mail.hermesconstruccion.com.mx";
$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
$mail->Password   = "hz9dzt";
$mail->Port       = 25;
$mail->From       = "soporte_sgw@grupohi.mx";
$mail->FromName   = "ATM";

//ARREGLO DE CORREOS
$array_correos = array(
    'khernandezz@grupohi.mx',
	'oaguayo@grupohi.mx');

//FUNCIONES
function consulta_clases()
{
	$link = conectar_sql_server();
	$query = "select idbase,id,indicador,idtiempo,valor_tiempo from sgwc_notificacion_pvencer where indicador='C' and idbase=1";
	$result = mssql_query($query);
	$datos = array();
	while($dato = mssql_fetch_assoc($result)):
		$datos[]=$dato;
	endwhile;
	return $datos;
}

function consulta_tipos()
{
	$link = conectar_sql_server();
	$query = "select idbase,id,indicador,idtiempo,valor_tiempo from sgwc_notificacion_pvencer where indicador='T' and idbase=1";
	$result = mssql_query($query);
	$datos = array();
	while($dato = mssql_fetch_assoc($result)):
		$datos[]=$dato;
	endwhile;
	return $datos;
}

function consulta_subtipos()
{
	$link = conectar_sql_server();
	$query = "select idbase,id,indicador,idtiempo,valor_tiempo from sgwc_notificacion_pvencer where indicador='S' and idbase=1";
	$result = mssql_query($query);
	$datos = array();
	while($dato = mssql_fetch_assoc($result)):
		$datos[]=$dato;
	endwhile;
	return $datos;
}

function consulta_tarea_notificada($tareaID)
{
	$link = conectar_sql_server();
	$query = "select tareaID from sgwc_tarea_pendiente_notificada where idbase=1 AND tareaID=".$tareaID;
	$result = mssql_query($query);
	$datos = array();
	while($dato = mssql_fetch_assoc($result)):
		$datos[]=$dato;
	endwhile;
	return $datos;
}

function consulta_correos()
{
	$link = conectar_sql_server();
	$query = "select correo from sgwc_notificacion where idbase=1 ";
	$result = mssql_query($query);
	$datos = array();
	while($dato = mssql_fetch_assoc($result)):
		$datos[]=$dato;
	endwhile;
	return $datos;
}

$consulta_clases=consulta_clases();
$consulta_tipos=consulta_tipos();
$consulta_subtipos=consulta_subtipos();
$c_correos=consulta_correos();

//CONEXION POPI
function conectar_sql_server(){
	if(!($link=mssql_connect('172.20.74.3\GHIAPP', 'oaguayo', '2014_opc7'))){
		$mail->Subject    = "Mensaje de conexion POPI NPV";
		$texto="Error de conexion: a servidor popi correo incidencias proximas a vencer %s\n";
		$mail->Body		= $texto;
		$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
		foreach($array_correos as $a_correo):
			$mail->AddBCC($a_correo);
		endforeach;
		if(!$mail->Send()) {echo "Mensaje de conexion CANADA-Mailer Error: " . $mail->ErrorInfo;}
		else {echo "Mensaje de conexion servidir popi correo incidencias proximas a vencer";}
		exit;
		exit();
	}

	if(!mssql_select_db("popi", $link)){
		$mail->Subject    = "Mensaje de conexion POPI NPV";
		$texto="Error de conexion: a base de datos popi correo incidencias proximas a vencer %s\n";
		$mail->Body		= $texto;
		$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
		foreach($array_correos as $a_correo):
			$mail->AddBCC($a_correo);
		endforeach;
		if(!$mail->Send()) {echo "Mensaje de conexion CANADA-Mailer Error: " . $mail->ErrorInfo;}
		else {echo "Mensaje de conexion base de datos popi correo incidencias proximas a vencer";}
		exit;
		exit();
	}
	return $link;

}

//CONEXION CANADA
$ssh = new Net_SSH2('192.99.17.91:22');
if (!$ssh->login('sgwc_incidencias', 'P"L;cKYGt-Z$')){
		$mail->Subject    = "Mensaje de conexion CANADA NPV";
		$texto="Error de conexion: a servidor canada correo incidencias proximas a vencer%s\n";
		$mail->Body		= $texto;
		$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
		foreach($array_correos as $a_correo):
			$mail->AddBCC($a_correo);
		endforeach;
		if(!$mail->Send()) {echo "Mensaje de conexion CANADA-Mailer Error: " . $mail->ErrorInfo;}
		else {echo "Mensaje de conexion servidor CANADA correo incidencias proximas a vencer";}
		exit;
	exit('Failed');
}

else
{
	if (!$ssh->exec('mysql -h 192.99.17.91 -u swgc_incidencias -p 3=7!8wY3-%x7 atm')){
    	$mail->Subject    = "Mensaje de conexion CANADA NPV";
		$texto="Error de conexion: a base de datos canada correo incidencias proximas a vencer %s\n";
		$mail->Body		= $texto;
		$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
		foreach($array_correos as $a_correo):
			$mail->AddBCC($a_correo);
		endforeach;
		if(!$mail->Send()) {echo "Mensaje de conexion CANADA-Mailer Error: " . $mail->ErrorInfo;}
		else {echo "Mensaje de conexion base de datos CANADA correo incidencias proximas a vencer";}
		exit;
	exit('Login to MySQL Failed');
	}
	else{

		//BUSCA TAREAS PARA LAS CLASES
		$connect2 = conectar_sql_server();
		$hora_actual=date('Y-m-d H:i:s');

		$result=$ssh->exec('mysql -uswgc_incidencias -p3=7!8wY3-%x7 atm -e "SELECT concat(\"[]\",at.tareaID)tareaID ,concat(\"[]\",atc.claseID)claseID, concat(\"[]\",att.tipoID)tipoID, concat(\"[]\",ats.subtipoID)subtipoID, concat(\"[]\",(TIME_TO_SEC(SEC_TO_TIME(TIMESTAMPDIFF(SECOND,\"'.$hora_actual.'\",at.fecha_llegada))) DIV 60)) AS tiempo, concat(\"[]\",REPLACE(pc.carretera,\",\",\"  \"))carretera, concat(\"[]\",REPLACE(atc.title,\",\",\"  \"))clase, concat(\"[]\",REPLACE(att.title,\",\",\"  \"))tipo, concat(\"[]\",REPLACE(ats.title,\",\",\"  \"))subtipo, concat(\"[]\",REPLACE(at.descripcion,\",\",\"  \"))descripcion, concat(\"[]\",REPLACE(at.PKinicial,\".\",\"+\"))pkinicial, concat(\"[]\",REPLACE(at.PKfinal,\".\",\"+\"))pkfinal ,concat(\"[]\",at.fecha_conocimiento)fecha_conocimiento ,concat(\"[]\",at.fecha_llegada)fecha_llegada ,concat(\"[]\",REPLACE(ps.sector,\",\",\"  \"))sector ,CASE WHEN REPLACE(pt.tramo,\",\",\"  \") IS NULL THEN \"[]-\" ELSE concat(\"[]\",REPLACE(pt.tramo,\",\",\"  \")) END AS tramo ,concat(\"[]\",DATE_FORMAT(at.fecha_conocimiento,\"%d-%m-%Y %H:%i:%s\"))conocimiento, concat(\"[]\",DATE_FORMAT(at.fecha_llegada,\"%d-%m-%Y %H:%i:%s\"))llegada, concat(\"[]\",at.fuente_informacion)fuente_informacion FROM agenda_tareas AS at left join atm.puntospk_carreteras pc on (at.carreteraID=pc.carreteraID) left join atm.agenda_tareas_subtipos ats on (at.subtipoID=ats.subtipoID) left join atm.agenda_tareas_tipos att on (ats.tipoID=att.tipoID) left join atm.agenda_tareas_clases atc on (att.claseID=atc.claseID) left join atm.puntospk_sectores ps on (at.sectorID=ps.sectorID) left join atm.puntospk_tramos pt on (at.plataformaID=pt.plataformaID and ((at.pkinicial>=pt.pkINI) and (at.pkinicial<=pt.pkFIN))) WHERE at.activa=1 AND at.fin_actuacion=\"0000-00-00 00:00:00\" ;"');

		$array = explode("[]", $result);
		//CORREO CLASES
		for ($tareaID=1,$claseID=2,$tipoID=3,$subtipoID=4,$tiempo=5,$carretera=6,$clase=7,$tipo=8,$subtipo=9,$desc=10,$pki=11,$pkf=12,$fecha_c=13,$fecha_l=14,$sector=15,$tramo=16,$con=17,$llegada=18,$fuente=19; $tareaID<=(sizeof($array)-1),$claseID<=(sizeof($array)-1),$tipoID<=(sizeof($array)-1),$subtipoID<=(sizeof($array)-1),$tiempo<=(sizeof($array)-1),$carretera<=(sizeof($array)-1),$clase<=(sizeof($array)-1),$tipo<=(sizeof($array)-1),$subtipo<=(sizeof($array)-1),$desc<=(sizeof($array)-1),$pki<=(sizeof($array)-1),$pkf<=(sizeof($array)-1),$fecha_c<=(sizeof($array)-1),$fecha_l<=(sizeof($array)-1),$sector<=(sizeof($array)-1),$tramo<=(sizeof($array)-1),$con<=(sizeof($array)-1),$llegada<=(sizeof($array)-1),$fuente<=(sizeof($array)-1); $tareaID=$tareaID+19,$claseID=$claseID+19,$tipoID=$tipoID+19,$subtipoID=$subtipoID+19,$tiempo=$tiempo+19,$carretera=$carretera+19,$clase=$clase+19,$tipo=$tipo+19,$subtipo=$subtipo+19,$desc=$desc+19,$pki=$pki+19,$pkf=$pkf+19,$fecha_c=$fecha_c+19,$fecha_l=$fecha_l+19,$sector=$sector+19,$tramo=$tramo+19,$con=$con+19,$llegada=$llegada+19,$fuente=$fuente+19) {
			foreach($consulta_clases as $Clase):
			if($array[$claseID]==$Clase["id"] and $array[$tiempo]<=$Clase["valor_tiempo"]):
				$tarea_notificada=consulta_tarea_notificada($array[$tareaID]);
				if(sizeof($tarea_notificada)==0){
					$inserta=mssql_query("INSERT INTO sgwc_tarea_pendiente_notificada (tareaID,idbase) VALUES (".$array[$tareaID].",1)",$connect2);
					$mail->Subject    = utf8_decode("Incidencia Próxima a vencer (Clase ").trim($array[$clase]).")";
$texto='<HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8"></HEAD><BODY>
<div style="padding:10px;border:solid 1px #ccc;
font-family:Arial,Helvetica,sans-serif;font-size:12px;">
<span style="color:#444444;font-size:16px;">
<strong>
SE LE INFORMA QUE LA SIGUIENTE INCIDENCIA ESTA PR&Oacute;XIMA A VENCER
</strong>
</span>
<br><br>
<div>
<table style="text-align:left;font-size:11px;color:#000" >
<thead>
<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
<th>REF</th>
<th>SECTOR</th>
<th>-CLASE <br>-TIPO <br>-SUBTIPO</th>
<th>DESCRIPCI&Oacute;N</th>
<th>CARRETERA</th>
<th>KM INICIAL</th>
<th>KM FINAL</th>
<th>FECHA CONOCIMIENTO</th>
<th>FECHA L&Iacute;MITE PARA FINALIZAR</th>
</tr>
</thead>
<tbody>';
$texto.=
	'
<tr style="background-color:#D8E4B9">
<td valign="top" align="center">
<a href="https://atm.hermesrm.mx/index.php?
type=public&zone=tareas&action=view&categoryID=-1&
num=0&orderBy=&orderList=&fase=alta&codeID='.$array[$tareaID].'">'.
$array[$tareaID].'
</a></td>
<td valign="top">'.
$array[$sector].'</td>
<td valign="top">-'.
$array[$clase].'
<br>-'.
$array[$tipo].'
<br>-'.
$array[$subtipo]
.'</td>
<td valign="top">'.
$array[$desc]
.'</td>
<td valign="top">'.
$array[$carretera].
"<br>(".
$array[$tramo]
.')</td>
<td valign="top">'.
$array[$pki].'</td>
<td valign="top">'.
$array[$pkf].'</td>
<td valign="top">'.
$array[$con]
.'</td>
<td valign="top">'.
$array[$llegada]
.'</td>
</tr>';
$texto.='</tbody>
</table>
</div>
<div style="clear:both;"></div>
<br>
<p style="color:#444444;font-size:12px;"><i>
Mensaje enviado autom&aacute;ticamente desde el
m&oacute;dulo de Agenda de Vialidad del
Sistema de Gesti&oacute;n Web Carretera.
</i></p>
<img src="http://intranet.grupohi.mx/ghi_mail.png">
</div>
</BODY></HTML>';

				$mail->ClearAllRecipients();
				$mail->Body		= $texto;
				$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
				//Quitar comentarios para enviar notificaciones a usuarios
				foreach($c_correos as $c_correo):
					$mail->AddAddress($c_correo["correo"]);
				endforeach;

				foreach($array_correos as $a_correo):
					$mail->AddBCC($a_correo);
				endforeach;
				if(!$mail->Send()) {echo "Error: " . $mail->ErrorInfo;}
				else {echo "tarea enviada Clase-".$array[$tareaID]."<br>";}
				}
			endif;
		endforeach;

		//CORREO TIPOS
		foreach($consulta_tipos as $Tipo):
			if($array[$tipoID]==$Tipo["id"] and $array[$tiempo]<=$Tipo["valor_tiempo"]):
				$tarea_notificada=consulta_tarea_notificada($array[$tareaID]);
				if(sizeof($tarea_notificada)==0){
					$inserta=mssql_query("INSERT INTO sgwc_tarea_pendiente_notificada (tareaID,idbase) VALUES (".$array[$tareaID].",1)",$connect2);
					$mail->Subject    = utf8_decode("Incidencia Próxima a vencer")." (".trim($array[$clase])." - Tipo ".trim($array[$tipo]).")";
$texto='<HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8"></HEAD><BODY>
<div style="padding:10px;border:solid 1px #ccc;
font-family:Arial,Helvetica,sans-serif;font-size:12px;">
<span style="color:#444444;font-size:16px;">
<strong>
SE LE INFORMA QUE LA SIGUIENTE INCIDENCIA ESTA PR&Oacute;XIMA A VENCER
</strong>
</span>
<br><br>
<div>
<table style="text-align:left;font-size:11px;color:#000" >
<thead>
<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
<th>REF</th>
<th>SECTOR</th>
<th>-CLASE <br>-TIPO <br>-SUBTIPO</th>
<th>DESCRIPCI&Oacute;N</th>
<th>CARRETERA</th>
<th>KM INICIAL</th>
<th>KM FINAL</th>
<th>FECHA CONOCIMIENTO</th>
<th>FECHA L&Iacute;MITE PARA FINALIZAR</th>
</tr>
</thead>
<tbody>';
$texto.=
	'
<tr style="background-color:#D8E4B9">
<td valign="top" align="center">
<a href="https://atm.hermesrm.mx/index.php?
type=public&zone=tareas&action=view&categoryID=-1&
num=0&orderBy=&orderList=&fase=alta&codeID='.$array[$tareaID].'">'.
$array[$tareaID].'
</a></td>
<td valign="top">'.
$array[$sector].'</td>
<td valign="top">-'.
$array[$clase].'
<br>-'.
$array[$tipo].'
<br>-'.
$array[$subtipo]
.'</td>
<td valign="top">'.
$array[$desc]
.'</td>
<td valign="top">'.
$array[$carretera].
"<br>(".
$array[$tramo]
.')</td>
<td valign="top">'.
$array[$pki].'</td>
<td valign="top">'.
$array[$pkf].'</td>
<td valign="top">'.
$array[$con]
.'</td>
<td valign="top">'.
$array[$llegada]
.'</td>
</tr>';
$texto.='</tbody>
</table>
</div>
<div style="clear:both;"></div>
<br>
<p style="color:#444444;font-size:12px;"><i>
Mensaje enviado autom&aacute;ticamente desde el
m&oacute;dulo de Agenda de Vialidad del
Sistema de Gesti&oacute;n Web Carretera.
</i></p>
<img src="http://intranet.grupohi.mx/ghi_mail.png">
</div>
</BODY></HTML>';
					$mail->ClearAllRecipients();
					$mail->Body		= $texto;
					$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
					//Quitar comentarios para enviar notificaciones a usuarios
					foreach($c_correos as $t_correo):
						$mail->AddAddress($t_correo["correo"]);
					endforeach;

					foreach($array_correos as $a_correo):
						$mail->AddBCC($a_correo);
					endforeach;
					if(!$mail->Send()) {echo "Error: " . $mail->ErrorInfo;}
					else {echo "tarea enviada Tipo-".$array[$tareaID]."<br>";}
				}
			endif;
		endforeach;

		//CORREO SUBTIPO
		foreach($consulta_subtipos as $Subtipo):
			if($array[$subtipoID]==$Subtipo["id"] and $array[$tiempo]<=$Subtipo["valor_tiempo"]):
				$tarea_notificada=consulta_tarea_notificada($array[$tareaID]);
				if(sizeof($tarea_notificada)==0){
					$inserta=mssql_query("INSERT INTO sgwc_tarea_pendiente_notificada (tareaID,idbase) VALUES (".$array[$tareaID].",1)",$connect2);
					$mail->Subject    = utf8_decode("Incidencia Próxima a vencer")." (".trim($array[$clase])." - Subtipo ".trim($array[$subtipo]).")";
$texto='<HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html;charset=UTF-8"></HEAD><BODY>
<div style="padding:10px;border:solid 1px #ccc;
font-family:Arial,Helvetica,sans-serif;font-size:12px;">
<span style="color:#444444;font-size:16px;">
<strong>
SE LE INFORMA QUE LA SIGUIENTE INCIDENCIA ESTA PR&Oacute;XIMA A VENCER
</strong>
</span>
<br><br>
<div>
<table style="text-align:left;font-size:11px;color:#000" >
<thead>
<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
<th>REF</th>
<th>SECTOR</th>
<th>-CLASE <br>-TIPO <br>-SUBTIPO</th>
<th>DESCRIPCI&Oacute;N</th>
<th>CARRETERA</th>
<th>KM INICIAL</th>
<th>KM FINAL</th>
<th>FECHA CONOCIMIENTO</th>
<th>FECHA L&Iacute;MITE PARA FINALIZAR</th>
</tr>
</thead>
<tbody>';
$texto.=
	'
<tr style="background-color:#D8E4B9">
<td valign="top" align="center">
<a href="https://atm.hermesrm.mx/index.php?
type=public&zone=tareas&action=view&categoryID=-1&
num=0&orderBy=&orderList=&fase=alta&codeID='.$array[$tareaID].'">'.
$array[$tareaID].'
</a></td>
<td valign="top">'.
$array[$sector].'</td>
<td valign="top">-'.
$array[$clase].'
<br>-'.
$array[$tipo].'
<br>-'.
$array[$subtipo]
.'</td>
<td valign="top">'.
$array[$desc]
.'</td>
<td valign="top">'.
$array[$carretera].
"<br>(".
$array[$tramo]
.')</td>
<td valign="top">'.
$array[$pki].'</td>
<td valign="top">'.
$array[$pkf].'</td>
<td valign="top">'.
$array[$con]
.'</td>
<td valign="top">'.
$array[$llegada]
.'</td>
</tr>';
$texto.='</tbody>
</table>
</div>

<div style="clear:both;"></div>
<br>
<p style="color:#444444;font-size:12px;"><i>
Mensaje enviado autom&aacute;ticamente desde el
m&oacute;dulo de Agenda de Vialidad del
Sistema de Gesti&oacute;n Web Carretera.
</i></p>
<img src="http://intranet.grupohi.mx/ghi_mail.png">
</div>
</BODY></HTML>';
					$mail->ClearAllRecipients();
					$mail->Body		= $texto;
					$mail->AltBody    = "Para ver el mensaje, por favor, utilice un visor de correo electronico HTML compatible";
					//Quitar comentarios para enviar notificaciones a usuarios
					foreach($c_correos as $s_correo):
						$mail->AddAddress($s_correo["correo"]);
					endforeach;

					foreach($array_correos as $a_correo):
						$mail->AddBCC($a_correo);
					endforeach;
					if(!$mail->Send()) {echo "Error: " . $mail->ErrorInfo;}
					else {echo "tarea enviada Subtipo-".$array[$tareaID]."<br>";}
				}
			endif;
		endforeach;
		}
	}

}

$ssh->disconnect();

?>
