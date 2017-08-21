<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Administrar extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('administrar_model');
    }

	public function index()
    {
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';

			$data['tipos'] = $this->administrar_model->desplega_resumen($data['iduser']);
			$this->template->load('template','administrar',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
    }

	public function registrados()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.btechco.excelexport.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.base64.js').'"></script>';
			$resultados=$this->administrar_model->solicitudes_registradas($data['iduser'],'1,2,3,4,5');
			$data["solicitudes"]=$this->administrar_model->resumen_informacion('1,2,3,4,5');
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;

			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','registrados',$data);

		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function solicitudes_atendidas($solicitud,$accion)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';

			$data["tipos"]=$this->administrar_model->desplega_tipos_solicitud();
			$data["preguntas"]=$this->administrar_model->desplega_preguntas($solicitud);
			$data["informacion"]=$this->administrar_model->desplega_solicitud_informacion($solicitud);
			$data["archivos"]=$this->administrar_model->desplega_archivos($solicitud);
			$data["accion"]=$accion;
			$data["anterior"]='';
			$data["siguiente"]='';

			if($accion==1):
				$data["titulo"]="SOLICITUDES REGISTRADAS";
				$data["solicitudes"]=$this->administrar_model->desplega_solicitud($solicitud);
				$data["result"]=$this->administrar_model->atender_solicitud($solicitud,$data['usuario']);
			else:
				//Inicio de etiquetas
				$etiquetas = $this->administrar_model->desplega_etiquetas_registrados($solicitud,$data['iduser']);
				foreach ($etiquetas as $etiqueta):
					if($etiqueta->etiqueta=="ANTERIOR"):
						$data["anterior"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/administrar/solicitudes_atendidas/'.$etiqueta->idcon_solicitud.'/0').'"><i class="fa fa-chevron-left"></i> Anterior</a>';
					elseif($etiqueta->etiqueta=="SIGUIENTE"):
						$data["siguiente"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/administrar/solicitudes_atendidas/'.$etiqueta->idcon_solicitud.'/0').'" style="margin-left:5px;">Siguiente <i class="fa fa-chevron-right"></i></a>';
					endif;
				endforeach;
				//Fin de etiquetas
				$data["titulo"]="SOLICITUDES ATENDIENDOSE";
				$data["solicitudes"]=$this->administrar_model->desplega_solicitud_cancelar($solicitud);
			endif;

			$this->template->load('template','solicitudes_atendidas',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function solicitudes_descartadas($solicitud)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';

			$result=$this->administrar_model->descartar_solicitud($solicitud);
			$data["result"]=$result[0]["mensaje"];
			$data["aceptar"]=base_url('baw/administrar/registrados');
			$data["error"]=base_url('baw/administrar/registrados');

			$this->template->load('template','mensaje_accion',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function responder_solicitud()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['css'] .= '<link href="'.base_url('assets/css/summernote.css').'" rel="stylesheet">';
			$data['css'] .= '<link href="'.base_url('assets/css/summernote-bs3.css').'" rel="stylesheet">';

			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/baw_editor.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/summernote.min.js').'"></script> ';

			$idsolicitud=$this->input->post('idsolicitud');
			$data["accion"]=$this->input->post('accion');
			$data["solicitudes"]=$this->administrar_model->informacion_solicitud($idsolicitud);
			$this->template->load('template','responder_solicitud',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	/*public function respuesta()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '';

			$solicitud=$this->input->post('solicitud');
			$respuesta=$this->input->post('respuesta');
			$respuesta=strtr(mb_strtoupper($respuesta,'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			$result=$this->administrar_model->agrega_respuesta($solicitud,$data['usuario'],$respuesta);

			if($result[0]["mensaje"]==0):
				echo "<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
						<a href='".base_url('baw/administrar/solicitudes_atendidas/').$solicitud."' class='btn btn-danger'>Intentar nuevamente</a>
					</div>";
			else:
				echo "<div class='alert alert-success'>
						<h5>La acci&oacute;n se realiz&oacute; con &eacute;xito</h5>
						<a href='".base_url('baw/administrar/index')."' class='btn btn-success'>Aceptar</a>
					</div>";

			$data["respuesta_correo"]=$this->administrar_model->respuesta_correo($solicitud);

				foreach($data["respuesta_correo"] as $respuesta_c):
					$this->load->library('My_PHPMailer');
					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPAuth   = true;
					$mail->Host       = "172.20.74.2";
					$mail->Port       = 25;
					$mail->Username   = "scaf";
					$mail->Password   = "GpoHermesInfra";
					$mail->From = $respuesta_c->contacto;
					$mail->FromName = $respuesta_c->nombre;
					$mail->Subject    = "Respuesta de Solicitud de Informacion(TICKET #".$respuesta_c->folio.")";
					$mail->AddAddress($respuesta_c->mail_solicitante);
					$mail->AddBCC("khernandezz@grupohi.mx");
					$mail->AddBCC('jccarrillo@grupohi.mx');
					$mail->AddBCC('oaguayo@grupohi.mx');

					 //Cuerpo del mensaje
					$cuerpo='
<HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1"></HEAD><BODY>
<style>
#cuerpo{
margin:0;
padding:10px;
font-family:Arial, Helvetica, sans-serif;
font-size:12px;
border:solid 1px #ccc;
width:400px;
max-width:400px;
}
#header{
background-color:#efefef;
padding:10px;
}
.campo{
background-color:#888888;
color:#ffffff;
font-weight:300;
padding:5px;
font-size:12px;
text-align:right;
}
.valor{
background-color:#efefef;
padding:5px;
font-size:12px;
}
.leyenda{
font-size:10px;
}
</style>
<div id="cuerpo">
<div id="header" align="center">
<img
src="'.$respuesta_c->logo.'">
<h4>Ticket #'.$respuesta_c->folio
.'</h4>
</div>
<br><br>
<table cellpadding="0" cellspacing="0"
style="width:100%;">
<tr>
<td class="campo" valign="top"
width="150px;">COMENTARIO</td>
<td class="valor" valign="top">'.
utf8_decode($respuesta_c->mensaje_solicitud).'
</td>
</tr>
<td class="campo" valign="top"
width="150px;">RESPUESTA</td>
<td class="valor" valign="top">'.
$respuesta_c->respuesta
.'</td>
</tr>
</table>
<br>
<span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada
para recibir mensajes.
<br>
Si requiere mayor informaci&oacute;n sobre el contenido visite
nuestra p&aacute;gina web '.$respuesta_c->pagina.'
</span>
<br>
<i style="font-size:10px;">
<strong>Mensaje enviado autom&aacute;ticamente desde la
p&aacute;gina<br>
<A HREF="'.$respuesta_c->pagina.'">
'.$respuesta_c->pagina.'</A><strong></strong>.
</i>
</div>
</BODY></HTML>';
					$mail->IsHTML(true);
					$mail->Body = utf8_decode($cuerpo);
					$mail->Send();
				endforeach;

            endif;


		else:
			redirect('login/index', 'refresh');
		endif;
	}*/

	private function set_upload_options()
	{
		$config = array();
		$config['upload_path'] = './documents/baw/';
		$config['allowed_types'] = 'txt|pdf|xls|ppt|zip|rar|jpeg|jpg|xml|xsl|doc|docx|xlsx|word|xl';
		//$config['max_size']      = '0';
		$config['overwrite']     = FALSE;
		return $config;
	}

	public function respuesta()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '';

			$solicitud=$this->input->post('solicitud');
			$respuesta=$this->input->post('respuesta');
			$respuesta=strtr(mb_strtoupper($respuesta,'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			$accion =$this->input->post('accion');

			if(empty($respuesta) || empty(trim($respuesta)) || empty(strip_tags($respuesta))):
					$data["mensaje"].="<div class='alert alert-danger'>
							<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
							<br>El mensaje contiene caracteres no v&aacute;lidos<br>
							<a href='".base_url('baw/administrar/solicitudes_atendidas/').'/'. $solicitud."' class='btn btn-danger'>Intentar nuevamente</a>
						</div>";
			else:
				if(!empty($accion) && $accion == 2):
					$result[0]["mensaje"]= 1;
					$this->administrar_model->reenviar_respuesta($solicitud,$data['usuario'],$respuesta);
				else:
					$result=$this->administrar_model->agrega_respuesta($solicitud,$data['usuario'],$respuesta);
				endif;

				$data["mensaje"]='';
				if($result[0]["mensaje"]==0):
					$data["mensaje"].="<div class='alert alert-danger'>
							<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
							<a href='".base_url('baw/administrar/solicitudes_atendidas/').'/'. $solicitud."' class='btn btn-danger'>Intentar nuevamente</a>
						</div>";
				else:
					$this->load->library('My_PHPMailer');
					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPAuth   = true;
					$mail->Host       = "mail.hermesconstruccion.com.mx";
					$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
					$mail->Password   = "hz9dzt";
					$mail->Port       = 25;

					$idrespuesta = $result[0]["mensaje"];
					$this->load->library('upload');
					$files = $_FILES;
					$cpt = count($_FILES['userfile']['name']);
					echo 'NUMERO DE ARCHIVOS ='.$cpt;
					if(isset($_FILES['userfile']) && count($_FILES['userfile']['error']) == 1 && $_FILES['userfile']['error'][0] > 0):
						$data["mensaje"].='';
					else:
						for($i=0; $i<$cpt; $i++):
							$_FILES['userfile']['name']= $files['userfile']['name'][$i];
							$_FILES['userfile']['type']= $files['userfile']['type'][$i];
							$_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
							$_FILES['userfile']['error']= $files['userfile']['error'][$i];
							$_FILES['userfile']['size']= $files['userfile']['size'][$i];
							$this->upload->initialize($this->set_upload_options());
							if($this->upload->do_upload()):
								$datos = array('upload_data' => $this->upload->data());
								$file = $datos["upload_data"]["file_name"];
								$upload = $this->administrar_model->agrega_respuesta_documento($idrespuesta,$file);
								$data["mensaje"].="<div class='alert alert-success'>
								<h5>El archivo ".$file." fue subido con &eacute;xito</h5></div>";
								$mail->AddAttachment('./documents/baw/'.$file);
							else:
								$data["mensaje"].="<div class='alert alert-danger'>
								<h5>El archivo ".$file." no pudo ser subido</h5></div>";
							endif;
						endfor;
					endif;
					$data["mensaje"].="<div class='alert alert-success'>
							<h5>La acci&oacute;n se realiz&oacute; con &eacute;xito</h5>
							<a href='".base_url('baw/administrar/index')."' class='btn btn-success'>Aceptar</a>
						</div>";

				$data["respuesta_correo"]=$this->administrar_model->respuesta_correo($solicitud);

					foreach($data["respuesta_correo"] as $respuesta_c):
						$mail->From = $respuesta_c->contacto;
						$mail->FromName = $respuesta_c->nombre;
						$mail->Subject    = "Respuesta de Solicitud de Informacion(TICKET #".$respuesta_c->folio.")";
						$mail->AddAddress($respuesta_c->mail_solicitante);
						$mail->AddBCC("khernandezz@grupohi.mx");
						$mail->AddBCC('oaguayo@grupohi.mx');
						$mail->AddBCC('lahernandezg@grupohi.mx');



						 //Cuerpo del mensaje
						$cuerpo='
<HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1"></HEAD><BODY>
<style>
#cuerpo{
margin:0;
padding:10px;
font-family:Arial, Helvetica, sans-serif;
font-size:12px;
border:solid 1px #ccc;
/*width:400px;
max-width:400px;*/
}
#header{
background-color:#ebebeb;
padding:10px;
}
.campo{
background-color:#888888;
color:#ffffff;
font-weight:300;
padding:5px;
font-size:12px;
text-align:right;
}
.valor{
background-color:#ebebeb;
padding:5px;
font-size:12px;
}
.leyenda{
font-size:10px;
}
</style>
<div id="cuerpo">
<div id="header" align="center">
<img
src="'.$respuesta_c->logo.'">
<h4>Ticket #'.$respuesta_c->folio
.'</h4>
</div>
<br><br>
<!--<table cellpadding="0" cellspacing="0"
style="width:100%;">
<tr>
<td class="campo" valign="top"
width="150px;">COMENTARIO</td>
<td class="valor" valign="top">'.
utf8_decode($respuesta_c->mensaje_solicitud).'
</td>
</tr>
<td class="campo" valign="top"
width="150px;">RESPUESTA</td>
<td class="valor" valign="top">'.
utf8_decode($respuesta_c->respuesta)
.'</td>
</tr>
</table>-->
<fieldset style="background-color:#ebebeb;">
  <legend><strong>COMENTARIO</strong></legend>
  <br>
  <div style="padding:10px">'.utf8_decode($respuesta_c->mensaje_solicitud).'</div>
<br>
</fieldset>
<br>
<fieldset style="background-color:#ebebeb;">
  <legend><strong>RESPUESTA</strong></legend>
  <br>
  <div style="padding:10px">'.utf8_decode($respuesta_c->respuesta).'</div>
<br>
</fieldset>
<br>
<span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada
para recibir mensajes.
<br>
Si requiere mayor informaci&oacute;n sobre el contenido visite
nuestra p&aacute;gina web '.$respuesta_c->pagina.'
</span>
<br>
<i style="font-size:10px;">
<strong>Mensaje enviado autom&aacute;ticamente desde la
p&aacute;gina<br>
<A HREF="'.$respuesta_c->pagina.'">
'.$respuesta_c->pagina.'</A><strong></strong>.
</i>
</div>
</BODY></HTML>';
						$mail->IsHTML(true);
						$mail->Body = $cuerpo;
						$mail->Send();
					endforeach;
				endif;

            endif;
			$this->template->load('template','responder_solicitud_mensaje',$data);

		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function atendiendose()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.btechco.excelexport.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.base64.js').'"></script>';
			$resultados=$this->administrar_model->solicitudes_atendiendose($data['iduser'],'1,2,3,4,5');
			$data["solicitudes"]=$this->administrar_model->resumen_informacion_atendidas(1,'1,2,3,4,5');
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;

			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','atendiendose',$data);

		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function atendidos()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.btechco.excelexport.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.base64.js').'"></script>';
			$resultados=$this->administrar_model->solicitudes_atendidos($data['iduser'],'1,2,3,4,5');
			$data["solicitudes"]=$this->administrar_model->resumen_informacion_atendidas(2,'1,2,3,4,5');
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;

			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','atendidos',$data);

		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function modificar_solicitud()
	{
		$solicitud=$this->input->get('solicitud');
		$correo=$this->input->get('correo');
		$tipo=$this->input->get('tipo');
		$result=$this->administrar_model->modificar_solicitud($correo,$tipo,$solicitud);
		$new = $result[0]["mensaje"];
		if($new==0):
			echo '{"msg":"ko"}';
		else:
			echo '{"msg":"'.$new.'"}';
		endif;
	}

	public function solicitudes_atendidas_descartadas($solicitud)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';

			$result=$this->administrar_model->descartar_solicitud_atendida($solicitud);
			$data["result"]=$result[0]["mensaje"];
			$data["aceptar"]=base_url('baw/administrar/atendiendose');
			$data["error"]=base_url('baw/administrar/atendiendose');

			$this->template->load('template','mensaje_accion',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function solicitar_datos($solicitud,$accion)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '<link href="'.base_url('assets/css/summernote.css').'" rel="stylesheet">';
			$data['css'] .= '<link href="'.base_url('assets/css/summernote-bs3.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/baw_editor.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/summernote.min.js').'"></script> ';

			//CONEXION A INTRANET
			$connect = mysqli_connect('172.20.74.92', 'intranet_ghi','Int_GHi14','igh');
			if ($connect)
			{
				$result=mysqli_query($connect,"SELECT idusuario,concat_ws(' ',nombre,apaterno,amaterno)as nombre,correo FROM usuario where usuario_estado = 2 and correo<>'' order by nombre,apaterno,amaterno ASC");
				$row=mysqli_num_rows($result);
				$datasource = array();
				for($j=0;$j<$row;$j++)
				{
					$datasource[]=mysqli_fetch_array($result);

				}
				$data["datasource"]=$datasource;
			}
			else{exit;}
			$data["id"]=$solicitud;
			$data["accion"]=$accion;
			$data["solicitudes"]=$this->administrar_model->informacion_solicitud_atendida($solicitud);
			$this->template->load('template','solicitar_datos',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function enviar_datos()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';

			$conteo = $this->input->post('conteo');
			$tema=$this->input->post('tema');
			$comentario=$this->input->post('comentario');
			$solicitud=$this->input->post('solicitud');
			$id=$this->input->post('id');
			$Usuarios='';
			$usuario_enviar='';

			for($i=1;$i<=$conteo;$i++):
				if($this->input->post('usuario'.$i)!=''):
					$usuario = ($this->input->post('usuario'.$i)=='')?0:$this->input->post('usuario'.$i);

					$Usuarios.= $usuario.';';
					if($i==$conteo):$coma=',';else:$coma=',';endif;
					$usuario_enviar .= $usuario.$coma;
				endif;
			endfor;

			$data["mensaje"]='';
			$tema = strtr(mb_strtoupper($tema, 'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			$comentario = strtr(mb_strtoupper($comentario, 'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			$result=$this->administrar_model->enviar_datos($solicitud,$data["usuario"],$tema,$comentario,$usuario_enviar);
			$new = $result[0]["mensaje"];
			if($new=="0"):
				//echo '{"msg":"ko"}';
				$data["mensaje"].="<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante el envio de la respuesta</h5>
						<a href='".base_url('baw/informacion/informacion_responder/'.$idrespuesta)."' class='btn btn-danger'>Intentar nuevamente</a>
					</div>";
			else:
				//echo '{"msg":"ok"}';
				//ENVIO DE CORREO
				$informacion=$this->administrar_model->consulta_solicitud_datos($solicitud);
				foreach($informacion as $info):
					$this->load->library('My_PHPMailer');
					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPAuth   = true;
					$mail->Host       = "mail.hermesconstruccion.com.mx";
					$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
					$mail->Password   = "hz9dzt";
					$mail->Port       = 25;
					$mail->From = 'sao@grupohi.mx';
					$mail->FromName = "Bitácora de Atención Web";
					$mail->Subject    = $tema;

					//DOCUMENTOS DE SOLICITUD
				$idrespuesta_datos = $new;
				$this->load->library('upload');
				$files = $_FILES;
				$cpt = count($_FILES['userfile']['name']);
				if(isset($_FILES['userfile']) && count($_FILES['userfile']['error']) == 1 && $_FILES['userfile']['error'][0] > 0):
					$data["mensaje"].="<div class='alert alert-success'>No se adjuntaron documentos en la solicitud de informaci&oacute;n</div>";
				else:
					$data["mensaje"].="<div class='alert alert-success'>Se han adjuntado los siguientes documentos:</div>";
					for($i=0; $i<$cpt; $i++):
						echo $i;
						$_FILES['userfile']['name']= $files['userfile']['name'][$i];
						$_FILES['userfile']['type']= $files['userfile']['type'][$i];
						$_FILES['userfile']['tmp_name']= $files['userfile']['tmp_name'][$i];
						$_FILES['userfile']['error']= $files['userfile']['error'][$i];
						$_FILES['userfile']['size']= $files['userfile']['size'][$i];
						$this->upload->initialize($this->set_upload_options());
						if($this->upload->do_upload()):
							$datos = array('upload_data' => $this->upload->data());
							$file = $datos["upload_data"]["file_name"];
							$upload = $this->administrar_model->solicitud_comentario_documento($idrespuesta_datos,$file);
							$data["mensaje"].="<div class='alert alert-success'>
							<h5>El archivo ".$file." fue adjuntado con &eacute;xito</h5></div>";
							$mail->AddAttachment('./documents/baw/'.$file);
						else:
							$data["mensaje"].="<div class='alert alert-danger'>
							<h5>El archivo ".$file." no pudo ser adjuntado</h5></div>";
						endif;
					endfor;
				endif;

				$data["mensaje"].="<div class='alert alert-success'>
						<h5>La solicitud de informaci&oacute;n fue enviada con &eacute;xito a los siguientes destinatarios:</h5>";
				for($k=1;$k<=$conteo;$k++):
						if($this->input->post('correo'.$k)!=''):
							$data["mensaje"].=$this->input->post('correo'.$k).'<br>';
						endif;
					endfor;
				$data["mensaje"].="<br><a href='".base_url('baw/administrar/solicitudes_atendidas/'.$id)."/0' class='btn btn-success'>Aceptar</a>
					</div>";


					for($k=1;$k<=$conteo;$k++):
						if($this->input->post('correo'.$k)!=''):
							$mail->AddAddress($this->input->post('correo'.$k));
						endif;
					endfor;
					$mail->AddBCC('khernandezz@grupohi.mx');
					$mail->AddBCC('oaguayo@grupohi.mx');

					 //Cuerpo del mensaje
					$cuerpo='
<HTML><HEAD><META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1"></HEAD><BODY>
<style>
#cuerpo{
margin:0;
padding:10px;
font-family:Arial, Helvetica, sans-serif;
font-size:12px;
border:solid 1px #ccc;
/*width:400px;
max-width:400px;*/
}
#header{
background-color:#efefef;
padding:10px;
}
.campo{
background-color:#888888;
color:#ffffff;
font-weight:300;
padding:5px;
font-size:12px;
text-align:right;
}
.valor{
background-color:#efefef;
padding:5px;
font-size:12px;
}
.leyenda{
font-size:10px;
}
</style>
<div id="cuerpo">
<div id="header" align="center">
<img src="http://fianzas.grupohi.mx/img/correo.png">
</div>
<br/>
'.$data["usuario"].'
le solicita lo siguiente:
<br><br>
<!--<table cellpadding="0" cellspacing="0"
style="width:100%;">
<td class="valor" valign="top">
<div>'.
$comentario
.'</div></td>
</tr>
</table>-->
<fieldset style="background-color:#ebebeb;">
  <legend><strong>COMENTARIO</strong></legend>
  <br>
  <div style="padding:10px">'.$comentario.'</div>
<br>
</fieldset>
<br>
Se le recuerda que usted debe responder esta solicitud
desde el m&oacute;dulo de Atenci&oacute;n a
usuarios ubicado en la siguiente direcci&oacute;n:
<a href="http://opc.grupohi.mx/baw/informacion/index">
Responder</a><br><br>
<br>
<i style="font-size:10px;">
<strong>Mensaje enviado autom&aacute;ticamente desde
el m&oacute;dulo de proyectos carreteros.<br>
SAO.- Grupo Hermes Infraestructura. </strong>.
</i>
</div>
</BODY></HTML>';
					$mail->IsHTML(true);
					$mail->Body = $cuerpo;
					$mail->Send();
				endforeach;
			endif;
			$this->template->load('template','administrar_enviar_datos',$data);;
		else:
			redirect('login/index', 'refresh');
		endif;


	}

	public function consulta($solicitud)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data["menu_baw"] = $this->menu->crea_menu_baw($data['idperfil'],$data['iduser']);
			$data['css'] = '';
			$data['js'] = '';

			$data["solicitudes"] = $this->administrar_model->desplega_consulta_solicitud($solicitud);
			$data["preguntas"]=$this->administrar_model->desplega_preguntas($solicitud);
			$data["respuesta"]=$this->administrar_model->desplega_respuesta($solicitud);

                        if(!empty($data['solicitudes']))
                        {
                            $dom = new DOMDocument();
                            foreach ($data['solicitudes'] as $k => $v)
                            {
                                $dom->loadHTML(mb_convert_encoding($v->respuesta, 'HTML-ENTITIES', 'UTF-8'));
                                $v->respuesta = strip_tags($dom->saveHTML(), '<br><a><p><span><ul><li><h1><h2><h3><b><img><ol><i><hr>');
                                $data['solicitudes'][$k]->respuesta = $v->respuesta;
                            }
                        }

			$data["anterior"]='';
			$data["siguiente"]='';
			//Inicio de etiquetas
				$etiquetas = $this->administrar_model->desplega_etiquetas_atendidos($solicitud,$data['iduser']);
				foreach ($etiquetas as $etiqueta):
					if($etiqueta->etiqueta=="ANTERIOR"):
						$data["anterior"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/administrar/consulta/'.$etiqueta->idcon_solicitud).'"><i class="fa fa-chevron-left"></i> Anterior</a>';
					elseif($etiqueta->etiqueta=="SIGUIENTE"):
						$data["siguiente"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/administrar/consulta/'.$etiqueta->idcon_solicitud).'" style="margin-left:5px;">Siguiente <i class="fa fa-chevron-right"></i></a>';
					endif;
				endforeach;
				//Fin de etiquetas

			$this->template->load('template','consulta',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function cerrar_tema ()
	{
		$solicitud=$this->input->get('solicitud');
		$result=$this->administrar_model->cerrar_tema($solicitud);

		$new = $result[0]["mensaje"];
		if($new=="Error"):
			echo '{"msg":"ko"}';
		else:
			echo '{"msg":"ok"}';
		endif;
	}
}
/*
*end modules/login/controllers/index.php
*/
