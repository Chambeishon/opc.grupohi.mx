<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Dashboard_hist extends MX_Controller
{

    public function __construct(){
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('dashboard_hist_model');
    }

    public function info(){
            phpinfo();
    }

    public function index(){
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			//$data["menu_doc"] = $this->menu->crea_menu_doc($data['idperfil'],$data['iduser']);
			$data['proyectos'] = $this->dashboard_hist_model->desplegar_proyectos_hist($data['iduser']);
			//print_r($data["proyectos"]);
			$data['css'] = '<link href="'.base_url('assets/css/bootstrap-datetimepicker.min.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/doc-dash-hist.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker.min.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker-init.js').'"></script>';
            $data['js'] .= '<script src="'.base_url('assets/js/jquery-form.js').'"></script>';
			$this->template->load('template','dashboard_hist',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
    }

    public function proyectos(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $data['idperfil'] = $session_data['idperfil'];

                    $fecha_inicio = $this->input->get('fecha_inicio');
                    $fecha_fin = $this->input->get('fecha_fin');
                    $proyectos = $this->dashboard_hist_model->desplegar_proyectos_fecha_hist($data['iduser'],$fecha_inicio,$fecha_fin);
                    $datasource = array();
                    foreach ($proyectos as $resultado):
                            //$datasource[]=array_map('utf8_encode', $resultado);
                            $datasource[]=($resultado);
                    endforeach;
                    echo json_encode($datasource);
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

    public function contratos(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $data['idperfil'] = $session_data['idperfil'];

                    $idproyecto = $this->input->get('idproyecto');
                    $fecha_inicio = $this->input->get('fecha_inicio');
                    $fecha_fin = $this->input->get('fecha_fin');
                    $contratos = $this->dashboard_hist_model->desplegar_contratos_hist($data['iduser'],$idproyecto,$fecha_inicio,$fecha_fin);
                    $datasource = array();
                    foreach ($contratos as $resultado):
                            //$datasource[]=array_map('utf8_encode', $resultado);
                            $datasource[]=($resultado);
                    endforeach;
                    echo json_encode($datasource);
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

    public function categorias(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $data['idperfil'] = $session_data['idperfil'];

                    $idcontrato = $this->input->get('idcontrato');
                    $fecha_inicio = $this->input->get('fecha_inicio');
                    $fecha_fin = $this->input->get('fecha_fin');
                    $categorias = $this->dashboard_hist_model->desplegar_categorias_hist($data['iduser'],$idcontrato,$fecha_inicio,$fecha_fin);
                    $datasource = array();
                    foreach ($categorias as $resultado):
                            //$datasource[]=array_map('utf8_encode', $resultado);
                            $datasource[]=($resultado);
                    endforeach;
                    echo json_encode($datasource);
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

    public function subcategorias(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $data['idperfil'] = $session_data['idperfil'];

                    $idcontrato = $this->input->get('idcontrato');
                    $idcategoria = $this->input->get('idcategoria');
                    $fecha_inicio = $this->input->get('fecha_inicio');
                    $fecha_fin = $this->input->get('fecha_fin');
                    $subcategorias = $this->dashboard_hist_model->desplegar_subcategorias_hist($data['iduser'],$idcontrato,$idcategoria,$fecha_inicio,$fecha_fin);
                    $datasource = array();
                    foreach ($subcategorias as $resultado):
                            //$datasource[]=array_map('utf8_encode', $resultado);
                            $datasource[]=($resultado);
                    endforeach;
                    echo json_encode($datasource);
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

    public function actividades(){
        if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
            $data['iduser'] = $session_data['id'];
            $data['idperfil'] = $session_data['idperfil'];

            $idcontrato = $this->input->get('idcontrato');
            $idcategoria = $this->input->get('idcategoria');
            $idsubcategoria = $this->input->get('idsubcategoria');
            $fecha_inicio = $this->input->get('fecha_inicio');
            $fecha_fin = $this->input->get('fecha_fin');
            $actividades = $this->dashboard_hist_model->desplegar_actividades_hist($data['iduser'],$idcontrato,$idcategoria,$idsubcategoria,$fecha_inicio,$fecha_fin);
            $datasource = array();

            foreach ($actividades as $resultado):
                //$datasource[]=array_map('utf8_encode', $resultado);
                $datasource[]=($resultado);
            endforeach;

            echo json_encode($datasource);
        else:
            redirect('login/index', 'refresh');
        endif;
    }


    public function programacion(){
            $idprogramacion = $this->input->get('idprogramacion');
            $programaciones = $this->dashboard_hist_model->desplegar_programacion_hist($idprogramacion);
            $datasource = array();
            foreach ($programaciones as $resultado):
                    //$datasource[]=array_map('utf8_encode', $resultado);
                    $datasource[]=($resultado);
            endforeach;
            echo json_encode($datasource);
    }

    public function programacion_actividad(){
            $idprogramacion = $this->input->get('idprogramacion');
            $programaciones = $this->dashboard_hist_model->desplegar_programacion_actividad($idprogramacion);
            $datasource = array();
            foreach ($programaciones as $resultado):
                    //$datasource[]=array_map('utf8_encode', $resultado);
                    $datasource[]=($resultado);
            endforeach;
            echo json_encode($datasource);
    }

    public function areas(){
            $idprogramacion = $this->input->get('idprogramacion');
            $areas = $this->dashboard_hist_model->desplegar_areas($idprogramacion);
            $datasource = array();
            foreach ($areas as $resultado):
                    //$datasource[]=array_map('utf8_encode', $resultado);
                    $datasource[]=($resultado);
            endforeach;
            echo json_encode($datasource);
    }

    public function areas_usuarios(){
            $idarea = $this->input->get('idarea');
            $usuarios = $this->dashboard_hist_model->desplegar_areas_usuarios($idarea);
            $datasource = array();
            foreach ($usuarios as $resultado):
                    //$datasource[]=array_map('utf8_encode', $resultado);
                    $datasource[]=($resultado);
            endforeach;
            echo json_encode($datasource);
    }

    public function cambiar_estado(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $idprogramacion = $this->input->get('idprogramacion');
                    $estado = $this->input->get('estado');
                    $ruta = $this->input->get('ruta');
                    if($ruta=='iniciar'):
                            $texto='iniciada';
                    elseif($ruta=='finalizar'):
                            $texto='finalizada';
                    elseif($ruta=='cancelar'):
                            $texto='cancelada';
                    elseif($ruta=='entregar'):
                            $texto='entregada';
                    elseif($ruta=='cerrar'):
                            $texto='cerrada';
                    endif;
                    $result = $this->dashboard_hist_model->cambiar_estado($idprogramacion,$estado,$data['usuario']);
                    echo '{"msg":'.$result[0]["mensaje"].',"text":"'.$texto.'"}';
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

    public function agregar_evidencia_documental(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $idprogramacion = $this->input->post('idprogramacion');
                    $idestado = $this->input->post('idestado');
                    $output_dir = "./documents/doc/evidencias/";
                    if(isset($_FILES["myfile"])):
                            $typeAccepted = array(
                            "application/pdf",
                            "application/excel",
                            "application/msexcel",
                            "application/vnd.ms-excel",
                            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                            "application/powerpoint",
                            "application/vnd.ms-powerpoint",
                            "application/vnd.openxmlformats-officedocument.presentationml.presentation",
                            "application/msword",
                            "text/plain",
                            "image/jpeg",
                            "image/pjpeg",
                            "application/x-compressed",
                            "application/x-zip-compressed",
                            "application/zip",
                            "application/octet-stream",
                            "multipart/x-zip",
                            "application/vnd.openxmlformats-officedocument.wordprocessingml.document");
                            if(in_array($_FILES["myfile"]["type"],$typeAccepted)):
                                    $file = date('Ymd_His').utf8_decode($_FILES["myfile"]["name"]);
                                    move_uploaded_file($_FILES["myfile"]["tmp_name"],$output_dir.$file);
                                    echo "La evidencia documental ha sido registrada";
                                    //$file = utf8_decode($file);
                                    $result = $this->dashboard_hist_model->agregar_evidencia($idprogramacion,utf8_encode($file),$idestado,$data['usuario']);

                            else:
                                    echo "Ha ocurrido un error, el soporte debe ser PDF, WORD, EXCEL, POWER POINT, ZIP, JPG, TXT";
                            endif;
                    else:
                            echo "Ha ocurrido un error, seleccione un archivo";
                    endif;
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

    public function desplegar_evidencias(){
            $idprogramacion = $this->input->get('idprogramacion');
            $evidencias = $this->dashboard_hist_model->desplegar_evidencias($idprogramacion);
            $datasource = array();
            foreach ($evidencias as $resultado):
                    //$datasource[]=array_map('utf8_encode', $resultado);
                    $datasource[]=($resultado);
            endforeach;
            echo json_encode($datasource);
    }

    public function eliminar_evidencia(){
            if($this->session->userdata('id')):
            $session_data = $this->session->userdata();
            $data['usuario'] = $session_data['username'];
                    $data['iduser'] = $session_data['id'];
                    $iddocumento = $this->input->get('iddocumento');
                    $result = $this->dashboard_hist_model->eliminar_evidencia($iddocumento);
                    echo '{"msg":'.$result[0]["mensaje"].'}';
            else:
                    redirect('login/index', 'refresh');
            endif;
    }

	public function notificacion(){
		$idprogramacion = $this->input->get('not-idprogramacion');
		$correos = $this->input->get('correos');
		$datos = $this->dashboard_hist_model->desplegar_programacion($idprogramacion);
		$this->load->library('My_PHPMailer');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->SMTPAuth   = true;
		$mail->Host       = "mail.hermesconstruccion.com.mx";
		$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
		$mail->Password   = "hz9dzt";
		$mail->Port       = 25;
		$mail->From       = "ContratosDeConcesiones@grupohi.mx";
		$mail->FromName   = "Módulo de Contratos de Concesiones";
		$mail->Subject    = $datos[0]["clave"]."-".$datos[0]["numero_contrato"]." ".utf8_decode($datos[0]["nombre_actividad"])." (P-".$datos[0]["idprogramacion"].") ".$datos[0]["estado_actividad"];

                foreach ($correos as $correo):
			$mail->AddAddress($correo);
		endforeach;

		$mail->AddBCC('oaguayo@grupohi.mx');
		$html='
<HTML><HEAD>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html;"></HEAD>
<BODY>
<style>
#cuerpo{
margin:0;
padding:10px;
font-family:Arial, Helvetica, sans-serif;
font-size:12px;
border:solid 1px #ccc;
width:1000px;
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
src="'.base_url('assets/img/logo_.png').'">
<h4>Actividad Programada'
.'</h4>
</div>
<br><br>
<fieldset style="background-color:#ebebeb;">
  <legend><strong>DATOS DE ACTIVDAD</strong></legend>
  <br>
  <table style="font-size:10px;">
  	<tr>
		<td valign="top"><b>ID TAREA PROGRAMA</b></td><td valign="top"><a href="opc.grupohi.mx/doc/dashboard_hist/index">P-'.$datos[0]["idprogramacion"].'</a></td>
	</tr>
	<tr>
		<td valign="top"><b>ACTIVIDAD</b></td><td valign="top">'.utf8_decode($datos[0]["nombre_actividad"]).'</td>
	</tr>
	<tr>
		<td valign="top"><b>DESCRIPCI&Oacute;N</b></td><td valign="top">'.utf8_decode($datos[0]["descripcion_actividad"]).'</td>
	</tr>
	<tr>
		<td valign="top"><b>FECHA L&Iacute;MITE</b></td><td valign="top">'.$datos[0]["fecha"].'</td>
	</tr>
	<tr>
		<td valign="top"><b>ESTADO</b></td><td valign="top">'.$datos[0]["estado_actividad"].'</td>
	</tr>
	<tr>
		<td valign="top"><b>DOCUMENTO REFERENCIAL</b></td><td valign="top">'.utf8_decode($datos[0]["documento_contractual"]).'</td>
	</tr>
	<tr>
		<td valign="top"><b>&Aacute;REA/EMPRESA RESPONSABLE</b></td><td valign="top">'.utf8_decode($datos[0]["empresa_responsable"]).'</td>
	</tr>
	<tr>
		<td valign="top"><b>REFERENCIA DOCUMENTAL</b></td><td valign="top">'.utf8_decode($datos[0]["referencia_documental"]).'</td>
	</tr>
	<tr>
		<td valign="top"><b>DETALLE REFERENCIA</b></td><td valign="top">'.utf8_decode($datos[0]["detalle_referencia"]).'</td>
	</tr>
	<tr>
		<td valign="top"><b>OBSERVACI&Oacute;N/ACCI&Oacute;N</b></td><td valign="top">'.utf8_decode($datos[0]["observacion"]).'</td>
	</tr>
  </table>
  <br><br>
</fieldset>
<br>
<br>
<span>Este correo es informativo, favor de no responder a
esta direcci&oacute;n de correo, ya que no se encuentra habilitada
para recibir mensajes.
<br><br>
</span>
<i style="font-size:10px;">
<strong>Mensaje enviado autom&aacute;ticamente desde el m&oacute;dulo de operaci&oacute;n.
</i>
</div>
</BODY></HTML>';
		$mail->IsHTML(true);
		$mail->Body = $html;
		if($mail->Send()):
			echo '{"msg":1}';
		else:
			echo '{"msg":0}';
		endif;
	}

	public function desplegar_botones(){
		$idprogramacion = $this->input->get('idprogramacion');
		$botones  = $this->dashboard_hist_model->get_botones($idprogramacion);
		$datasource = array();
		foreach ($botones as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function desplegar_seguimiento(){
		$idprogramacion = $this->input->get('idprogramacion');
		$seguimiento  = $this->dashboard_hist_model->get_seguimiento($idprogramacion);
		$datasource = array();
		foreach ($seguimiento as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function numero_evidencia(){
		$idprogramacion = $this->input->get('idprogramacion');
		$seguimiento  = $this->dashboard_hist_model->get_numero_evidencia($idprogramacion);
		$datasource = array();
		foreach ($seguimiento as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function guarda_anotacion(){
		$idprogramacion = $this->input->get('idprogramacion');
		$fecha = $this->input->get('fecha');
		$hora=date('H:i:s');
		$nota = $this->input->get('nota');
		$valoracion = $this->input->get('valoracion');
		$usuario = $this->input->get('usuario');

		$insert=$this->dashboard_hist_model->get_guarda_anotacion($idprogramacion,$hora,$fecha,($nota),$valoracion,$usuario);
		echo '{"msg":'.$insert[0]["mensaje"].'}';
	}

	public function desplegar_anotaciones()
	{
		$idprogramacion = $this->input->get('idprogramacion');
		$anotaciones = $this->dashboard_hist_model->desplegar_anotaciones($idprogramacion);
		$datasource = array();
		foreach ($anotaciones as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function bloquea_anotacion()
	{
		$id = $this->input->get('id');
		$cambio=$this->dashboard_hist_model->get_bloquea_anotacion($id);
		echo '{"msg":'.$cambio[0]["mensaje"].'}';
	}

	public function eliminar_anotacion()
	{
		$id = $this->input->get('id');
		$delete=$this->dashboard_hist_model->get_eliminar_anotacion($id);
		echo '{"msg":'.$delete[0]["mensaje"].'}';
	}

	public function numero_anotacion()
	{
		$idprogramacion = $this->input->get('idprogramacion');
		$seguimiento  = $this->dashboard_hist_model->get_numero_anotacion($idprogramacion);
		$datasource = array();
		foreach ($seguimiento as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}


}
/*
*end modules/login/controllers/index.php
*/
