<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Informacion extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('informacion_model');
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
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.btechco.excelexport.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/jquery.base64.js').'"></script>';

			$resultados=$this->informacion_model->desplega_solicitud_datos($data['iduser']);
			//$data["solicitudes"]=$this->informacion_model->resumen_informacion();
			$datasource = array();
			foreach ($resultados as $resultado):
				//$datasource[]=array_map('utf8_encode', $resultado);
				$datasource[]=($resultado);
			endforeach;

			$data["datasource"]=json_encode($datasource);


			$this->template->load('template','informacion',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
    }

	public function modal_solicitud_info()
	{
		$idsolicitud=$this->input->get('idsolicitud');
		$result=$this->informacion_model->resumen_informacion($idsolicitud);
		$datasource = array();
		foreach ($result as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		json_encode($datasource);
		$data=trim(json_encode($datasource),']');
		$data=trim($data,'[');
		echo $data;
	}

	public function informacion_responder($solicitud)
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
			$this->load->model('administrar_model');
			$data["resultados"]=$this->informacion_model->informacion_datos($solicitud);
			$data["preguntas"]=$this->informacion_model->desplega_preguntas($solicitud);
			$data["respuestas"]=$this->informacion_model->desplega_respuesta($solicitud);
			$data["sol"]=$solicitud	;

			$this->template->load('template','informacion_responder',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	/*public function responder_comentario()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];

			$solicitud=$this->input->get('solicitud');
			$respuesta=$this->input->get('respuesta');
			$usuario=$this->input->get('usuario');

			$result=$this->informacion_model->responder_comentario($solicitud,$data['usuario'],$respuesta);
			$new = $result[0]["mensaje"];
			if($new=="Error"):
				echo '{"msg":"ko"}';
			else:
				echo '{"msg":"ok"}';
				//**CONEXION A INTRANET
				$connect = mysqli_connect('172.20.74.7', 'intranet_ghi','Int_GHi14','igh');
				if ($connect)
				{
					$result=mysqli_query($connect,"SELECT correo FROM usuario where usuario_estado = 2 and concat_ws(' ',nombre,apaterno,amaterno)='".$usuario."'");
					$row=mysqli_num_rows($result);
					for($j=0;$j<$row;$j++)
					{
						$fila=mysqli_fetch_array($result);
						$correo_usuario=$fila["correo"];
					}
				}
				else{exit;}
				$informacion=$this->informacion_model->consulta_solicitud_datos($solicitud);
				foreach($informacion as $info):
					$this->load->library('My_PHPMailer');
					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPAuth   = true;
					$mail->Host       = "172.20.74.2";
					$mail->Port       = 25;
					$mail->Username   = "scaf";
					$mail->Password   = 'GpoHermesInfra';
					$mail->From = 'sao@grupohi.mx';
					$mail->FromName = "Bitácora de Atención Web";
					$mail->Subject    = ($info->tema);
					$mail->AddAddress($correo_usuario);
					$mail->AddBCC('khernandezz@grupohi.mx');
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
<img src="http://fianzas.grupohi.mx:1026/img/correo.png">
</div>
<br/>
El usuario '.$data["usuario"].'
responde la solicitud:
<br><br>
<table cellpadding="0" cellspacing="0"
style="width:100%;">
<tr>
<td class="campo" valign="top"
width="150px;">SOLICITUD:</td>
<td class="valor" valign="top">
<div>'.
$info->comentario
.'</div></td>
</tr>
<tr>
<td class="campo" valign="top"
width="150px;">RESPUESTA:</td>
<td class="valor" valign="top">
<div>'.
$respuesta
.'</div></td>
</tr>
</table>
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


	public function responder_comentario()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '';
			$data['js'] = '';
			$idrespuesta=$this->input->post('idrespuesta');
			$solicitud=$this->input->post('solicitud');
			$respuesta=$this->input->post('respuesta');
			$usuario=$this->input->post('usuario_solicita');

			$data["mensaje"]='';
			$respuesta = strtr(mb_strtoupper($respuesta'ISO-8859-1'),"àèìòùáéíóúçñäëïöü","ÀÈÌÒÙÁÉÍÓÚÇÑÄËÏÖÜ");
			$result=$this->informacion_model->responder_comentario($solicitud,$data['usuario'],$respuesta);
			$new = $result[0]["mensaje"];
			if($new=="Error"):
				//echo '{"msg":"ko"}';
				$data["mensaje"].="<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante el envio de la respuesta</h5>
						<a href='".base_url('baw/informacion/informacion_responder/'.$idrespuesta)."' class='btn btn-danger'>Intentar nuevamente</a>
					</div>";
			else:
				//echo '{"msg":"ok"}';
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
				$idrespuesta_datos = $new;
				$this->load->library('upload');
				$files = $_FILES;
				$cpt = count($_FILES['userfile']['name']);
				if(isset($_FILES['userfile']) && count($_FILES['userfile']['error']) == 1 && $_FILES['userfile']['error'][0] > 0):
					$data["mensaje"].="<div class='alert alert-success'>No se adjuntaron documentos en la respuesta</div>";
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
							$upload = $this->informacion_model->responder_comentario_documento($idrespuesta_datos,$file);
							$data["mensaje"].="<div class='alert alert-success'>
							<h5>El archivo ".$file." fue enviado con &eacute;xito</h5></div>";
							$mail->AddAttachment('./documents/baw/'.$file);
						else:
							$data["mensaje"].="<div class='alert alert-danger'>
							<h5>El archivo ".$file." no pudo ser enviado</h5></div>";
						endif;
					endfor;
				endif;

				$data["mensaje"].="<div class='alert alert-success'>
						<h5>La respuesta fue enviada con &eacute;xito</h5>
						<a href='".base_url('baw/informacion/informacion_responder/'.$idrespuesta)."' class='btn btn-success'>Aceptar</a>
					</div>";
				/**CONEXION A INTRANET*/
				$connect = mysqli_connect('localhost', 'intranet_ghi','Int_GHi14','igh');
				if ($connect)
				{
					$result=mysqli_query($connect,"SELECT correo FROM usuario where usuario_estado = 2 and concat_ws(' ',nombre,apaterno,amaterno)='".$usuario."'");
					$row=mysqli_num_rows($result);
					for($j=0;$j<$row;$j++)
					{
						$fila=mysqli_fetch_array($result);
						$correo_usuario=$fila["correo"];
					}
				}
				else{exit;}
				$informacion=$this->informacion_model->consulta_solicitud_datos($solicitud);
				foreach($informacion as $info):
					$mail->Subject    = ($info->tema);
					$mail->AddAddress($correo_usuario);
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
El usuario '.$data["usuario"].'
responde la solicitud:
<br><br>
<!--<table cellpadding="0" cellspacing="0"
style="width:100%;">
<tr>
<td class="campo" valign="top"
width="150px;">SOLICITUD:</td>
<td class="valor" valign="top">
<div>'.
$info->comentario
.'</div></td>
</tr>
<tr>
<td class="campo" valign="top"
width="150px;">RESPUESTA:</td>
<td class="valor" valign="top">
<div>'.
$respuesta
.'</div></td>
</tr>
</table>-->
<fieldset style="background-color:#ebebeb;">
  <legend><strong>COMENTARIO</strong></legend>
  <br>
  <div style="padding:10px">'.$info->comentario.'</div>
<br>
</fieldset>
<br>
<fieldset style="background-color:#ebebeb;">
  <legend><strong>RESPUESTA</strong></legend>
  <br>
  <div style="padding:10px">'.$respuesta.'</div>
<br>
</fieldset>
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
			$this->template->load('template','informacion_responder_mensaje',$data);
		else:
			redirect('login/index', 'refresh');
		endif;

	}


}
/*
*end modules/login/controllers/index.php
*/
