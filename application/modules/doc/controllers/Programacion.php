<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Programacion extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('programacion_model');
    }

	public function index()
    {
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			//$data["menu_doc"] = $this->menu->crea_menu_doc($data['idperfil'],$data['iduser']);
			//$data['proyectos'] = $this->dashboard_model->desplegar_proyectos($data['iduser']);
			$data['css'] = '<link href="'.base_url('assets/css/infragistics.theme.css').'" rel="stylesheet" />';
			$data['css'] .= '<link href="'.base_url('assets/css/infragistics.css').'" rel="stylesheet" />';
			$data['css'] .= '<link href="'.base_url('assets/css/bootstrap-datetimepicker.min.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';

			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker.min.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/doc-prog.js').'"></script>';
			$data["contratos"] = $this->programacion_model->desplegar_contratos_activos($data["iduser"]);
			$data["periodos"] = $this->programacion_model->desplegar_periodos();
			$this->template->load('template','programacion',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
    }

	public function agregar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$idactividad = $this->input->get('idactividad');
			$repeticion = $this->input->get('repeticion');
			$periodo = $this->input->get('periodo');
			$fecha = $this->input->get('fecha');
			$correos = $this->input->get('correos');
			$result = $this->programacion_model->agregar_programacion($idactividad,$repeticion,$periodo,$fecha,$data['usuario']);
			echo '{"msg":'.$result[0]["mensaje"].'}';
			if($result[0]["mensaje"]>0 and $correos<>''):
				$datos = $this->programacion_model->desplegar_programacion($idactividad);
				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;
				$mail->From       = "ContratosDeConcesiones@grupohi.mx";
				$mail->FromName   = utf8_decode("Módulo de Contratos de Concesiones");
				$mail->Subject    = utf8_decode($datos[0]["clave"])."-".utf8_decode($datos[0]["numero_contrato"])." ".utf8_decode($datos[0]["nombre_actividad"])." REGISTRADA";
				foreach($correos as $correo):
					$mail->AddAddress($correo);
				endforeach;
				$mail->AddBCC('lahernandezg@grupohi.mx');
				$mail->AddBCC('oaguayo@grupohi.mx');
				$html='
<html>
		<div style="padding:10px;border:solid 1px #ccc; font-family:Arial,Helvetica,sans-serif;font-size:12px;">
		<span style="color:#444444;font-size:16px;">
<strong>SE LE NOTIFICA QUE LA(S) SIGUIENTE(S) ACTIVIDAD(ES) HA(N) SIDO PROGRAMADA(S)</strong>
</span><br><br><table style="text-align:left;font-size:11px;color:#000" width="100%">
	<thead>
	<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
		<th>Id</th>
		<th>Actividad</th>
		<th>Descripci&oacute;n</th>
		<th>Persona Responsable</th>
		<th>Fecha Limite</th>
		<th>Estado</th>
	</tr>
	</thead>
	<tbody>';
	foreach($datos as $dato):
		$html .= '<tr style="background-color:#D8E4B9"><td valign="top" align="center"><a href="http://opc.grupohi.mx/doc/dashboard/index">P-'.$dato["idprogramacion"].'</a></td><td valign="top">'.utf8_decode($dato["nombre_actividad"]).'</td><td valign="top">'.utf8_decode($dato["descripcion_actividad"]).'</td><td valign="top">'.utf8_decode($dato["persona_responsable"]).'</td><td valign="top">'.$dato["fecha"].'</td><td valign="top">'.$dato["estado_actividad"].'</td></tr>';
	endforeach;
	$html .= '</tbody></table><br><p style="color:#444444;font-size:12px;"><span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada para recibir mensajes.<br><br></span><i>Mensaje enviado autom&aacute;ticamente desde el M&oacute;dulo de Operaci&oacute;n e Infraestructura.</i></p><img src="http://intranet.grupohi.mx/ghi_mail.png"></div>
';
		$mail->IsHTML(true);
		$mail->Body = $html;
		$mail->Send();
			endif;
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function categorias()
	{
		$this->load->model('categoria_contrato_model');
		$idcontrato = $this->input->get('idcontrato');
		$categorias = $this->categoria_contrato_model->desplegar_categorias($idcontrato);
		$datasource = array();
		foreach ($categorias as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function subcategorias()
	{
		$this->load->model('subcategoria_contrato_model');
		$idcontrato = $this->input->get('idcontrato');
		$idcategoria = $this->input->get('idcategoria');
		$subcategorias = $this->subcategoria_contrato_model->desplegar_subcategorias($idcontrato,$idcategoria);
		$datasource = array();
		foreach ($subcategorias as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function desplegar()
	{
		$this->load->model('actividad_model');
		$idcategoria = $this->input->get('idcategoria');
		$idsubcategoria = $this->input->get('idsubcategoria');
		$idcontrato = $this->input->get('idcontrato');
		$actividades = $this->actividad_model->desplegar_actividades($idcontrato,$idcategoria,$idsubcategoria);
		$datasource = array();
		foreach ($actividades as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function buscar()
	{
		$idactividad = $this->input->get('idactividad');
		$programaciones = $this->programacion_model->desplegar_programaciones($idactividad);
		$datasource = array();
		foreach ($programaciones as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function editar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$idprogramacion = $this->input->get('idprogramacion');
			$fecha = $this->input->get('fecha');
			$result = $this->programacion_model->editar_programacion($idprogramacion,$fecha);
			echo '{"msg":'.$result[0]["mensaje"].'}';
			if($result[0]["mensaje"]>0):
				$datos = $this->programacion_model->desplegar_programacion_editar($idprogramacion);
				$correos = $this->programacion_model->correo_notificacion_programacion_editar($datos[0]["idactividad"]);
				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;
				$mail->From       = "ContratosDeConcesiones@grupohi.mx";
				$mail->FromName   = utf8_decode("Módulo de Contratos de Concesiones");
				$mail->Subject    = utf8_decode($datos[0]["clave"])."-".utf8_decode($datos[0]["numero_contrato"])." ".utf8_decode($datos[0]["nombre_actividad"])." (P-".$datos[0]["idprogramacion"].") MODIFICADA";
				foreach ($correos as $correo):
					//$mail->AddAddress($correo["correo_usuario"]);
				endforeach;
				$mail->AddBCC('lahernandezg@grupohi.mx');
				$mail->AddBCC('oaguayo@grupohi.mx');
				$html='
<html>
		<div style="padding:10px;border:solid 1px #ccc; font-family:Arial,Helvetica,sans-serif;font-size:12px;">
		<span style="color:#444444;font-size:16px;">
<strong>SE LE NOTIFICA QUE LA ACTIVIDAD PROGRAMADA P-'.$datos[0]["idprogramacion"].' HA SIDO MODIFICADA</strong>
</span><br><br><table style="text-align:left;font-size:11px;color:#000" width="100%">
	<thead>
	<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
		<th>Id</th>
		<th>Actividad</th>
		<th>Descripci&oacute;n</th>
		<th>Persona Responsable</th>
		<th>Fecha Limite</th>
		<th>Estado</th>
	</tr>
	</thead>
	<tbody>';
		$html .= '<tr style="background-color:#D8E4B9"><td valign="top" align="center">P-'.$datos[0]["idprogramacion"].'</td><td valign="top">'.utf8_decode($datos[0]["nombre_actividad"]).'</td><td valign="top">'.utf8_decode($datos[0]["descripcion_actividad"]).'</td><td valign="top">'.utf8_decode($datos[0]["persona_responsable"]).'</td><td valign="top">'.$datos[0]["fecha"].'</td><td valign="top">'.$datos[0]["estado_actividad"].'</td></tr>';
	$html .= '</tbody></table><br><p style="color:#444444;font-size:12px;"><span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada para recibir mensajes.<br><br></span><i>Mensaje enviado autom&aacute;ticamente desde el M&oacute;dulo de Operaci&oacute;n e Infraestructura.</i></p><img src="http://intranet.grupohi.mx/ghi_mail.png"></div>
';
		$mail->IsHTML(true);
		$mail->Body = $html;
		$mail->Send();
			endif;
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function cancelar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$idprogramacion = $this->input->get('idprogramacion');
			$datos = $this->programacion_model->desplegar_programacion_editar($idprogramacion);
			$clave=$datos[0]["clave"];
			$numero_contrato=$datos[0]["numero_contrato"];
			$nombre_actividad=$datos[0]["nombre_actividad"];
			$descripcion_actividad=$datos[0]["descripcion_actividad"];
			$persona_responsable=$datos[0]["persona_responsable"];
			$fecha=$datos[0]["fecha"];
			$result = $this->programacion_model->cancelar_programacion($idprogramacion);
			echo '{"msg":'.$result[0]["mensaje"].'}';
			if($result[0]["mensaje"]>0):
				$correos = $this->programacion_model->correo_notificacion_programacion_editar($datos[0]["idactividad"]);
				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;
				$mail->From       = "ContratosDeConcesiones@grupohi.mx";
				$mail->FromName   = utf8_decode("Módulo de Contratos de Concesiones");
				$mail->Subject    = utf8_decode($clave)."-".utf8_decode($numero_contrato)." ".utf8_decode($nombre_actividad)." (P-".$idprogramacion.") ELIMINADA";
				foreach ($correos as $correo):
					//$mail->AddAddress($correo["correo_usuario"]);
				endforeach;
				$mail->AddBCC('lahernandezg@grupohi.mx');
				$mail->AddBCC('oaguayo@grupohi.mx');
				$html='
<html>
		<div style="padding:10px;border:solid 1px #ccc; font-family:Arial,Helvetica,sans-serif;font-size:12px;">
		<span style="color:#444444;font-size:16px;">
<strong>SE LE NOTIFICA QUE LA ACTIVIDAD PROGRAMADA P-'.$idprogramacion.' HA SIDO ELIMINADA</strong>
</span><br><br><table style="text-align:left;font-size:11px;color:#000" width="100%">
	<thead>
	<tr valign="bottom" style="background-color:#9ABC55;color:#fff;">
		<th>Id</th>
		<th>Actividad</th>
		<th>Descripci&oacute;n</th>
		<th>Persona Responsable</th>
		<th>Fecha Limite</th>
		<th>Estado</th>
	</tr>
	</thead>
	<tbody>';
		$html .= '<tr style="background-color:#D8E4B9"><td valign="top" align="center">P-'.$idprogramacion.'</td><td valign="top">'.utf8_decode($nombre_actividad).'</td><td valign="top">'.utf8_decode($descripcion_actividad).'</td><td valign="top">'.utf8_decode($persona_responsable).'</td><td valign="top">'.$fecha.'</td><td valign="top">ELIMINADA</td></tr>';
	$html .= '</tbody></table><br><p style="color:#444444;font-size:12px;"><span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada para recibir mensajes.<br><br></span><i>Mensaje enviado autom&aacute;ticamente desde el M&oacute;dulo de Operaci&oacute;n e Infraestructura.</i></p><img src="http://intranet.grupohi.mx/ghi_mail.png"></div>
';
		$mail->IsHTML(true);
		$mail->Body = $html;
		$mail->Send();
			endif;
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function notificacion()
	{
		$idactividad = $this->input->get('idactividad');
		$areas = $this->programacion_model->desplegar_area_notificacion($idactividad);
		$datasource = array();
		foreach ($areas as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function notificacion_usuarios()
	{
		$idarea = $this->input->get('idarea');
		$usuarios = $this->programacion_model->desplegar_usuario_notificacion($idarea);
		$datasource = array();
		foreach ($usuarios as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}




}
/*
*end modules/login/controllers/index.php
*/
