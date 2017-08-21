<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Facturacion extends MX_Controller
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

			$data['tipos'] = $this->administrar_model->desplega_resumen_facturacion($data['iduser']);
			$this->template->load('template','facturacion',$data);
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
			$resultados=$this->administrar_model->solicitudes_registradas_facturacion($data['iduser']);
			//$data["solicitudes"]=$this->administrar_model->resumen_informacion_facturacion();
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;
			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','facturacion_registrados',$data);

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
			$data["preguntas"]=$this->administrar_model->desplega_preguntas_facturacion($solicitud);
			$data["informacion"]=$this->administrar_model->desplega_solicitud_informacion($solicitud);
			$data["archivos"]=$this->administrar_model->desplega_archivos($solicitud);
			$data["facturados"]=$this->administrar_model->desplega_tickets_facturados($solicitud,$data['iduser']);
			$notificado=$this->administrar_model->valida_notificacion($solicitud);
			$data["accion"]=$accion;
			$notificado=$notificado[0]->total;
			if($accion==1 && $notificado==0):
				$data["tickets"]=$this->administrar_model->desplega_tickets($solicitud,$data['iduser']);
				$data["no_validados"]=$this->administrar_model->desplega_no_validados($solicitud);
				$data["accion"]=$accion;
				$data["titulo"]="SOLICITUDES REGISTRADAS";
				$data["solicitudes"]=$this->administrar_model->desplega_solicitud_facturacion($solicitud);
				$data["result"]=$this->administrar_model->atender_solicitud($solicitud,$data['usuario']);
				$etiquetas = $this->administrar_model->desplega_siguientes_registrados($solicitud,$data['iduser']);
				$data["anterior"]='';
				$data["siguiente"]='';
				foreach ($etiquetas as $etiqueta):
					if($etiqueta->etiqueta=="ANTERIOR"):
						$data["anterior"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/solicitudes_atendidas/'.$etiqueta->idsolicitud.'/1').'"><i class="fa fa-chevron-left"></i> Anterior</a>';
					elseif($etiqueta->etiqueta=="SIGUIENTE"):
						$data["siguiente"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/solicitudes_atendidas/'.$etiqueta->idsolicitud.'/1').'" style="margin-left:5px;">Siguiente <i class="fa fa-chevron-right"></i></a>';
					endif;
				endforeach;
			elseif($accion==0 && $notificado>0):
				$data["tickets"]=$this->administrar_model->desplega_tickets_validados($solicitud);
				$data["accion"]=$accion;
				$data["titulo"]="SOLICITUDES ATENDIENDOSE";
				$data["solicitudes"]=$this->administrar_model->desplega_solicitud_cancelar($solicitud);
				$etiquetas = $this->administrar_model->desplega_siguientes_atendiendose($solicitud,$data['iduser']);
				$data["anterior"]='';
				$data["siguiente"]='';
				foreach ($etiquetas as $etiqueta):
					if($etiqueta->etiqueta=="ANTERIOR"):
						$data["anterior"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/solicitudes_atendidas/'.$etiqueta->idsolicitud.'/0').'"><i class="fa fa-chevron-left"></i> Anterior</a>';
					elseif($etiqueta->etiqueta=="SIGUIENTE"):
						$data["siguiente"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/solicitudes_atendidas/'.$etiqueta->idsolicitud.'/0').'" style="margin-left:5px;">Siguiente <i class="fa fa-chevron-right"></i></a>';
					endif;
				endforeach;
			else:
				redirect('baw/facturacion/index');
			endif;

			$this->template->load('template','facturacion_solicitudes_atendidas',$data);
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
			$data["aceptar"]=base_url('baw/facturacion/registrados');
			$data["error"]=base_url('baw/facturacion/registrados');

			$this->template->load('template','facturacion_mensaje_accion',$data);
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
			$data["respuesta"]=$this->administrar_model->respuesta_automatica_solicitud();
			$this->template->load('template','facturacion_responder_solicitud',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

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
			$result=$this->administrar_model->agrega_respuesta($solicitud,$data['usuario'],$respuesta);
			$data["mensaje"]='';
			if($result[0]["mensaje"]==0):
				$data["mensaje"].="<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
						<a href='".base_url('baw/facturacion/solicitudes_atendidas/').$solicitud."/0' class='btn btn-danger'>Intentar nuevamente</a>
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
				$etiquetas = $this->administrar_model->desplega_siguientes_atendiendose($solicitud,$data['iduser']);
				foreach ($etiquetas as $etiqueta):
					if($etiqueta->etiqueta=="ANTERIOR"):
						$anterior=$etiqueta->idsolicitud;
					endif;
				endforeach;
				$data["mensaje"].="<div class='alert alert-success'>
						<h5>La acci&oacute;n se realiz&oacute; con &eacute;xito</h5>
						<a href='".base_url('baw/facturacion/solicitudes_atendidas/'.$anterior.'/0')."' class='btn btn-success'>Aceptar</a>
					</div>";

			$data["respuesta_correo"]=$this->administrar_model->respuesta_correo($solicitud);

				foreach($data["respuesta_correo"] as $respuesta_c):
					$mail->From = $respuesta_c->contacto;
					$mail->FromName = $respuesta_c->nombre;
					$mail->Subject    = "Respuesta de Solicitud de facturación (TICKET #".$respuesta_c->folio.")";
					$mail->AddAddress($respuesta_c->mail_solicitante);
					$mail->AddBCC("khernandezz@grupohi.mx");
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
<fieldset style="background-color:#ebebeb;">
  <legend><strong>DATOS FISCALES</strong></legend>
  <br>
  <div style="padding:10px">'.$respuesta_c->mensaje_solicitud.'</div>
</fieldset>
<br>
<fieldset style="background-color:#ebebeb;">
  <legend><strong>RESPUESTA</strong></legend>
  <br>
  <div style="padding:10px">'.$respuesta_c->respuesta.'</div>
</fieldset>
<br>
<br>
<span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada
para recibir mensajes.
<br><br>
Si requiere mayor informaci&oacute;n sobre el contenido visite
nuestra p&aacute;gina web.
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
			$this->template->load('template','facturacion_responder_solicitud_mensaje',$data);

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
			$resultados=$this->administrar_model->solicitudes_atendiendose_facturacion($data['iduser']);
			//$data["solicitudes"]=$this->administrar_model->resumen_informacion_atendidas(1,'6');
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;

			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','facturacion_atendiendose',$data);

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
			$resultados=$this->administrar_model->solicitudes_atendidos_facturacion($data['iduser']);
			//$data["solicitudes"]=$this->administrar_model->resumen_informacion_atendidas(2,'6');
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;

			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','facturacion_atendidos',$data);

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
			$connect = mysqli_connect('localhost', 'intranet_ghi','Int_GHi14','igh');
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
			$this->template->load('template','facturacion_solicitar_datos',$data);
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
			$tema = strtr(mb_strtoupper($tema,'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			$comentario = strtr(mb_strtoupper($comentario,'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
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
				$data["mensaje"].="<br><a href='".base_url('baw/facturacion/solicitudes_atendidas/'.$id)."/0' class='btn btn-success'>Aceptar</a>
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
<img src="http://fianzas.grupohi.mx/img/correo.png">
</div>
<br/>
'.$data["usuario"].'
le solicita lo siguiente:
<br><br>
<table cellpadding="0" cellspacing="0"
style="width:100%;">
<td class="valor" valign="top">
<div>'.
$comentario
.'</div></td>
</tr>
</table>
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
			$this->template->load('template','facturacion_administrar_enviar_datos',$data);
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
			$data["documentos"]=$this->administrar_model->desplega_documentos_facturacion($solicitud);
			$data["tickets"] = $this->administrar_model->consultar_solicitud_facturacion($solicitud);
			$etiquetas = $this->administrar_model->desplega_siguientes_atendidos($solicitud,$data['iduser']);
			$data["anterior"]='';
			$data["siguiente"]='';
			foreach ($etiquetas as $etiqueta):
				if($etiqueta->etiqueta=="ANTERIOR"):
					$data["anterior"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/consulta/'.$etiqueta->idcon_solicitud).'"><i class="fa fa-chevron-left"></i> Anterior</a>';
				elseif($etiqueta->etiqueta=="SIGUIENTE"):
					$data["siguiente"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/consulta/'.$etiqueta->idcon_solicitud).'" style="margin-left:5px;">Siguiente <i class="fa fa-chevron-right"></i></a>';
				endif;
			endforeach;


			$this->template->load('template','facturacion_consulta',$data);
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

	public function validar_ticket()
	{
		$idsolicitud_ticket=$this->input->get('idsolicitud_ticket');
		$estado=$this->input->get('estado');
		$result=$this->administrar_model->validar_ticket($idsolicitud_ticket,$estado);
		$msg = $result[0]["mensaje"];
		$val = $result[0]["valor"];
		echo '{"msg":"'.$msg.'","val":'.$val.'}';
	}

	public function eliminar_tickets()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$idsolicitud=$this->input->get('idsolicitud');
			$result=$this->administrar_model->eliminar_tickets($idsolicitud,$data['iduser']);
				echo '{"msg":"'.$result[0]["mensaje"].'"}';
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function notificar_tickets()
	{
		$solicitud = $this->input->get('idsolicitud');
		$result = $this->administrar_model->notifica_tickets($solicitud);
		if($result[0]["mensaje"]==1):
			$informacion=$this->administrar_model->desplega_solicitud_facturacion($solicitud);
			$tickets=$this->administrar_model->desplega_tickets_all($solicitud);
			foreach($informacion as $info):
				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;
				$mail->From = $info->correo;
				$mail->FromName = "Autopista ".mb_convert_case($info->nombre_proyecto, MB_CASE_TITLE, "utf8");
				$mail->Subject    = 'Atención de Solicitud de facturación (TICKET #'.$info->folio.')';
				$mail->AddAddress($info->mail_solicitante);
				$mail->AddBCC("khernandezz@grupohi.mx");
				$mail->AddBCC('oaguayo@grupohi.mx');
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
width:100%;
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
<img src="'.$info->logo.'"><br><br>
'.$info->folio.'
</div>
<br>
La solicitud de facturación <strong>'.$info->folio.'</strong> está siendo atendida.<br><br>';
$total=0;
	foreach($tickets as $ticket):
	if($ticket->idestado_ticket==2):
		$total = $total + $ticket->tarifa;
	endif;
	endforeach;
if($total>0):
	$cuerpo.='La factura será por un monto total de <b>$'.number_format($total,2).' pesos.</b><br><br>';
else:
	$cuerpo.='La solicitud no cuenta con ningún ticket válido para facturar, por lo tanto, no se puede elaborar la factura electrónica.<br>Se le recomienda que consulte los elementos que integran nuestro ticket, los cuales se encuentran publicados en nuestra página y le ayudarán a saber los datos a registrar.<br><br>';
endif;
$j=0;
foreach($tickets as $ticket):
	if($ticket->idestado_ticket==3):
	$j++;
	endif;
endforeach;
if($j>0):
	$cuerpo.='A continuación se enlistan los folios que han sido detectados como no válidos:
<br><br><table style="font-size:12px; border:solid 1px #ccc;" width="100%" cellpadding="2" cellspacing="0">
	<tr style="background-color:#444; color:#fff;">
		<td>#</td>
		<td>Folio Impreso</td>
		<td>Plaza de Cobro</td>
		<td>Carril</td>
		<td>Folio Evento</td>
		<td>Fecha</td>
		<td>Hora</td>
		<td>Tarifa</td>
	</tr>';
	$i=0;
	foreach($tickets as $ticket):
		if($ticket->idestado_ticket==3):
		$i++;
		$color=($i%2==0)?'#fff':'#eee';
			$cuerpo.='
		<tr style="background-color:'.$color.'">
			<td>'.$i.'</td>
			<td>'.$ticket->folio_impreso.'</td>
			<td>'.$ticket->nombre_plaza.'</td>
			<td>'.$ticket->carril.'</td>
			<td>'.$ticket->folio_evento.'</td>
			<td>'.$ticket->fecha.'</td>
			<td>'.$ticket->hora.'</td>
			<td>'.$ticket->tarifa.'</td>
		</tr>';
		endif;
	endforeach;
	$cuerpo.='</table>';
endif;
$cuerpo.='<br>
Si tiene alguna duda o comentario al respecto comuniquese con nosotros por este medio o pueder dar clic en el siguiente enlace:<br>
'.$info->ruta.'contacto.php
<br><br>
<i style="font-size:10px;">
			<strong>
			Este correo es informativo, favor de no
responder a esta dirección de correo,
			ya que no se encuentra habilitada para recibir
mensajes.<br><br>
			Mensaje enviado automáticamente desde la
página<br>
			<A HREF="'.$info->ruta.'">'.$info->ruta.'</A><strong></strong>.
			</i>
</div>
</BODY></HTML>';
					$mail->IsHTML(true);
					$mail->Body = $cuerpo;
					if($mail->Send()):
						echo '{"msg":"ok"}';
					else:
						echo '{"msg":"ko"}';
					endif;
		endforeach;
		else:
			echo '{"msg":"ko"}';
		endif;
	}

	public function reenviar()
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


				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;

			$solicitud = $this->input->post('idsolicitud');
			$destinatario = $this->input->post('destinatario');


			$respuestas=$this->administrar_model->respuesta_correo($solicitud);



				foreach($respuestas as $respuesta_c):
					$mail->From = $respuesta_c->contacto;
					$mail->FromName = $respuesta_c->nombre;
					$mail->Subject    = "Respuesta de Solicitud de facturación (TICKET #".$respuesta_c->folio.")";
					//$mail->AddAddress($respuesta_c->mail_solicitante);
					$mail->AddAddress($destinatario);
					$adicioneles = preg_replace('/\s+/', '', $this->input->post('adicionales'));
					$adicionales = explode(';',$adicioneles);
					foreach($adicionales as $adicional):
						$mail->AddAddress($adicional);
					endforeach;
					$mail->AddBCC("khernandezz@grupohi.mx");
					$mail->AddBCC('oaguayo@grupohi.mx');
					$documentos=$this->administrar_model->desplega_documentos_facturacion($solicitud);
					foreach ($documentos as $docto):
						$mail->AddAttachment('./documents/baw/'.$docto->nombre_documento);
					endforeach;
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
<fieldset style="background-color:#ebebeb;">
  <legend><strong>DATOS FISCALES</strong></legend>
  <br>
  <div style="padding:10px">'.$respuesta_c->mensaje_solicitud.'</div>
</fieldset>
<br>
<fieldset style="background-color:#ebebeb;">
  <legend><strong>RESPUESTA</strong></legend>
  <br>
  <div style="padding:10px">'.$respuesta_c->respuesta.'</div>
</fieldset>
<br>
<br>
<span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada
para recibir mensajes.
<br><br>
Si requiere mayor informaci&oacute;n sobre el contenido visite
nuestra p&aacute;gina web.
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

					if($mail->Send()):
						$data["mensaje"]='<div class="alert alert-success">
						<h5>La respuesta de la solicitud fue reenviada con &eacute;xito</h5><br><a href="'.base_url('baw/facturacion/consulta/'.$solicitud).'" class="btn btn-success">Aceptar</a></div>';
					else:
						$data["mensaje"]='<div class="alert alert-danger">
						<h5>La respuesta de la solicitud no pudo ser reenviada.</h5><br><a href="'.base_url('baw/facturacion/consulta/'.$solicitud).'" class="btn btn-success">Aceptar</a></div>';
					endif;
				endforeach;

			$this->template->load('template','facturacion_reenviar_solicitud_mensaje',$data);

		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function consultar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.btechco.excelexport.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.base64.js').'"></script>';
			$resultados=$this->administrar_model->consultar_solicitudes_facturacion($data['iduser']);
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;
			$data["datasource"]=json_encode($datasource);
			$this->template->load('template','facturacion_consultar',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function consultar_detalle($idsolicitud)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '';
			$data['js'] = '';
			$data["solicitudes"]=$this->administrar_model->consultar_solicitud_facturacion($idsolicitud);
			$data["respuestas"]=$this->administrar_model->consultar_respuesta_facturacion($idsolicitud);
			$data["documentos"]=$this->administrar_model->consultar_documentos_respuesta_facturacion($idsolicitud);
			$etiquetas = $this->administrar_model->desplega_siguientes_consulta($idsolicitud,$data['iduser']);
			$data["anterior"]='';
			$data["siguiente"]='';
			foreach ($etiquetas as $etiqueta):
				if($etiqueta->etiqueta=="ANTERIOR"):
					$data["anterior"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/consultar_detalle/'.$etiqueta->idsolicitud).'"><i class="fa fa-chevron-left"></i> Anterior</a>';
				elseif($etiqueta->etiqueta=="SIGUIENTE"):
					$data["siguiente"].='<a class="btn btn-danger btn-xs pull-right" href="'.base_url('baw/facturacion/consultar_detalle/'.$etiqueta->idsolicitud).'" style="margin-left:5px;">Siguiente <i class="fa fa-chevron-right"></i></a>';
				endif;
			endforeach;
			$this->template->load('template','facturacion_consultar_detalle',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function validacion_automatica()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '<link href="'.base_url('assets/css/bootstrap-datetimepicker.min.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker.min.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker-init.js').'"></script>';
			$this->template->load('template','facturacion_validacion_automatica',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function ejecutar_validacion()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$fecha = $this->input->get('fecha');
			$usuario = $data['usuario'];
			$resultados = $this->administrar_model->ejecutar_validacion($fecha,$usuario);
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;
			echo json_encode($datasource);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function ejecutar_validacion_pdf($idvalidacion)
	{

			$this->load->library('pdf_validacion');
			$this->pdf_validacion = new Pdf_validacion();
			$encabezado = $this->administrar_model->desplegar_validacion_encabezado($idvalidacion);
			$titulo = $encabezado[0]["fecha"];
			$this->pdf_validacion->titulo=$titulo;
			// Agregamos una página
			$this->pdf_validacion->AddPage();
			// Define el alias para el número de página que se imprimirá en el pie
			$this->pdf_validacion->AliasNbPages();
			/* Se define el titulo, márgenes izquierdo, derecho y
			 * el color de relleno predeterminado
			 */
			$this->pdf_validacion->SetTitle("VALIDACION AUTOMATICA");
			$this->pdf_validacion->SetLeftMargin(15);
			$this->pdf_validacion->SetRightMargin(15);
			$this->pdf_validacion->SetFillColor(235,235,235);

			// Se define el formato de fuente: Arial, negritas, tamaño 9
			$this->pdf_validacion->SetFont('Arial', '', 7);
			/*
			 * TITULOS DE COLUMNAS
			 *
			 * $this->pdf->Cell(Ancho, Alto,texto,borde,posición,alineación,relleno);
			 */

			$this->pdf_validacion->Cell(7,4,'#','TL',0,'C','1');
			$this->pdf_validacion->Cell(25,4,'FOLIO','TL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'TICKETS DE','TL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'TICKETS NO','TL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'TICKETS','TL',0,'C','1');
			$this->pdf_validacion->Cell(19,4,'VALIDADOS','TL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'TOTAL DE','TL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'TOTAL','TL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'TOTAL NO','TL',0,'C','1');
			$this->pdf_validacion->Cell(20,4,'INEFECTIVIDAD','TLR',0,'C','1');
			$this->pdf_validacion->Ln(4);

			$this->pdf_validacion->Cell(7,4,'','BL',0,'C','1');
			$this->pdf_validacion->Cell(25,4,'','BL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'SOLICITUD','BL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'VALIDADOS','BL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'VALIDADOS','BL',0,'C','1');
			$this->pdf_validacion->Cell(19,4,'PREVIAMENTE','BL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'SOLICITUD','BL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'VALIDADOS','BL',0,'C','1');
			$this->pdf_validacion->Cell(18,4,'VALIDADOS','BL',0,'C','1');
			$this->pdf_validacion->Cell(20,4,'','BLR',0,'C','1');
			$this->pdf_validacion->Ln(4);

        // La variable $x se utiliza para mostrar un número consecutivo
        $x = 1;
		$resultados = $this->administrar_model->desplegar_validacion_detalle($idvalidacion);
        foreach ($resultados as $row) {
			$color = ($x%2==0)?1:0;
            // se imprime el numero actual y despues se incrementa el valor de $x en uno
            $this->pdf_validacion->Cell(7,5,$x++,'BL',0,'C',$color);
            // Se imprimen los datos de cada alumno
            $this->pdf_validacion->Cell(25,5,$row["folio"],'BL',0,'C',$color);
            $this->pdf_validacion->Cell(18,5,$row["total_tickets"],'BL',0,'C',$color);
            $this->pdf_validacion->Cell(18,5,$row["tickets_no_validados"],'BL',0,'C',$color);
            $this->pdf_validacion->Cell(18,5,$row["tickets_validados"],'BL',0,'C',$color);
            $this->pdf_validacion->Cell(19,5,$row["tickets_validados_previamente"],'BL',0,'C',$color);
			$this->pdf_validacion->Cell(18,5,'$'.$row["total_tarifa"],'BL',0,'R',$color);
			$this->pdf_validacion->Cell(18,5,'$'.$row["total_tarifa_validados"],'BL',0,'R',$color);
			$this->pdf_validacion->Cell(18,5,'$'.$row["total_tarifa_no_validados"],'BL',0,'R',$color);
			$this->pdf_validacion->Cell(20,5,$row["inefectividad"].'%','BLR',0,'R',$color);
            /*$this->pdf->Cell(25,5,$alumno->grupo,'BR',0,'C',0);*/
            //Se agrega un salto de linea
            $this->pdf_validacion->Ln(5);
        }
        $this->pdf_validacion->Output("Validación ATA ".$titulo.".pdf", 'D');
	}

	public function cargar_tickets()
	{
		if($this->session->userdata('id')):
			$this->load->model('grl/general_model');
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/bootstrap.file-input.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/upload.js').'"></script>';
			$data["plazas"] = $this->general_model->desplega_lista_plazas_activas();
			$this->template->load('template','facturacion_cargar_tickets',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function procesaExcel()
	{
		if($this->session->userdata('id')):
			$this->load->model('grl/general_model');
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			try{
				$this->load->library('my_phpexcel');
				$idplaza = $this->input->post('plaza');
				$tipo_fecha = $this->input->post('tipo_fecha');
				$nombre_temp_archivo = $_FILES["archivo"]["tmp_name"];
				$nombre_archivo = $_FILES["archivo"]["name"];
				$tipo_archivo = $_FILES["archivo"]["type"];
				$tamano_archivo = ($_FILES["archivo"]["size"] / 1024);
				$timestamp = date('d-m-Y_H-i-s');
				$dirfilename = './documents/tickets/'.DIRECTORY_SEPARATOR.$timestamp.'-'.$nombre_archivo;
				//move_uploaded_file($nombre_temp_archivo,$dirfilename);
				$objPHPExcel = PHPExcel_IOFactory::load($nombre_temp_archivo);
				$objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
				$highestRow = $objWorksheet->getHighestRow();
				$highestColumn = $objWorksheet->getHighestColumn();
				$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
				$headingsArray = $objWorksheet->rangeToArray('A10:'.'M10',null, true, true, true);
				$headingsArray = $headingsArray[10];
				$r = -1;
				$datos = array();
				$cabecera_2 = array(
					"A"=>"Carril",
					"B"=>"Fecha y Hora",
					"C"=>"Sesion",
					"D"=>"Evento",
					"E"=>"Folio",
					"F"=>"Clase",
					"G"=>"E.E.",
					"H"=>"Clase1",
					"I"=>"E.E1.",
					"J"=>"Forma de",
					"K"=>"Tarifa",
					"L"=>"Tarifa1",
					"M"=>"Faltante/",
				);
				for ($row = 12; $row <= $highestRow; ++$row):
					$dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
					if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')):
						++$r;
						foreach($cabecera_2 as $columnKey => $columnHeading):
							$datos[$r][$columnHeading] = $dataRow[$row][$columnKey];
						endforeach;
					endif;
				endfor;
				$cont=0;
				foreach($datos as $key => $value):
					$fecha_hora = preg_replace('/\s+/', '', $value["Fecha y Hora"]);
					$fecha = substr($fecha_hora, 0, 10);
					$time = substr($fecha_hora, -8);
					$fecha = explode('/',$fecha);
					if($tipo_fecha==1):
						$fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
						//$fecha = 'CONVERT(DATE,"'.$fecha.'",101)';
					else:
						$fecha = $fecha[2].'-'.$fecha[0].'-'.$fecha[1];
						//$fecha = 'CONVERT(DATE,"'.$fecha.'",103)';
					endif;
					$arreglo = array(
						"idplaza" => $idplaza,
						"carril" => $value["Carril"],
						"fecha_hora" => $fecha.' '.$time,
						"fecha" => $fecha,
						"hora" => $time,
						"sesion" => $value["Sesion"],
						"folio" => $value["Evento"],
						"evento" => $value["Folio"],
						"clase_marcada" => $value["Clase"],
						"ee_marcada" => $value["E.E."],
						"clase_detectada" => $value["Clase1"],
						"ee_detectada" => $value["E.E1."],
						"forma_pago" => $value["Forma de"],
						"tarifa_marcada" => $value["Tarifa"],
						"tarifa_detectada" => $value["Tarifa1"],
						"faltante_sobrante" => $value["Faltante/"],
						"usuario_registra" => $data['usuario']
					);
					$result = $this->administrar_model->insertar_ticket($arreglo);
					if($result>0):
						$cont++;
					endif;
				endforeach;
				if($cont>0):
					move_uploaded_file($nombre_temp_archivo,$dirfilename);
					echo json_encode(array('msj'=>'<i class="fa fa-check"></i> El archivo ha sido subido con &eacute;xito. Se han insertado '.$cont.' tickets'));
				else:
					echo json_encode(array('error'=>'<i class="fa fa-warning"></i> El archivo esta vacio o no tiene los datos coresponientes a insertar....'));
				endif;
				//echo json_encode(array('msj'=>'Muy bien'));

			}
			catch (Exception $ex){
				echo json_encode(array('error'=>$ex->getMessage()));
			}
		else:
			echo '<script>alert("Reinicie sesión");</script>';
		endif;

	}

	function validar_archivos()
	{
		$fecha = $this->input->get('fecha');
		$result = $this->administrar_model->validar_archivos($fecha);
		echo '{"msg":'.$result.'}';
	}

	function busqueda()
	{
		if($this->session->userdata('id')):
			$this->load->model('grl/general_model');
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '<link href="'.base_url('assets/css/infragistics.theme.css').'" rel="stylesheet" />';
			$data['css'].='<link href="'.base_url('assets/css/infragistics.css').'" rel="stylesheet" />';
			$data['js'] = '<script src="'.base_url('assets/js/baw.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';
			$this->template->load('template','facturacion_busqueda',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	function buscar()
	{
		$valor = $this->input->get('valor');
		$resultados = $this->administrar_model->buscar_solicitudes($valor);
		$datasource = array();
		foreach ($resultados as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}




}
/*
*end modules/login/controllers/index.php
*/
