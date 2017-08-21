<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Asignacion extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('grl/general_model');
		$this->load->model('bom_general_model');
		$this->load->model('asignacion_model');
    }

    public function index()
    {
		if($this->session->userdata('id')):
			$this->load->helper('text');
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '<link href="'.base_url('assets/css/bootstrap-table.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/jquery-table.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-table.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bom.js').'"></script>';
			$data['reportes'] = $this->bom_general_model->desplega_reportes(1,$data["iduser"]);
			$this->template->load('template','asignacion',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
    }

	public function generales($id)
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '<link href="'.base_url('assets/css/bootstrap-datetimepicker.min.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/bom.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker.min.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker-init.js').'"></script>';
			$data["result"] = $this->asignacion_model->desplega_reporte($id);
			$data["usuarios"] = $this->bom_general_model->desplega_lista_tecnicos_plaza($data["result"][0]["idplaza"]);
			$data["idreporte"]=$id;
			//$result[0]["folio"];
			$this->template->load('template','asignacion_generales',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function asignar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$idreporte=$this->input->post('idreporte');
			$tipo=$this->input->post('idtipo');
			$clasificacion=$this->input->post('idclasificacion');
			$fecha=$this->input->post('registro-fecha');
			$hora=$this->input->post('registro-hora');
			$tecnico=$this->input->post('registro-tecnico');
			$result = $this->asignacion_model->asigna_tecnico($idreporte,$tecnico,$data['iduser'],$fecha,$hora);
			$reporte = $this->asignacion_model->desplega_reporte($idreporte);
			$usuario = $this->general_model->desplega_usuario($tecnico);
			$data["css"]='';
			$data["js"]='';
			$data["tecnico"]=$usuario[0]->nombre.' '.$usuario[0]->apaterno.' '.$usuario[0]->amaterno;
			$data['mensaje'] = $result[0]["mensaje"];
			if($data['mensaje']!='Error'):
				$this->load->model('notificacion_model');
				//$this->load->library('email');
				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;
				$this->load->model('diagnostico_model');
				$data["result"] = $this->diagnostico_model->desplega_reporte($idreporte);
				$correos = $this->notificacion_model->select_correos($tipo,$clasificacion,$data["result"][0]["idplaza"],21);
				$copiaoculta = $this->notificacion_model->select_copiaoculta();
				$html = $this->load->view('asignacion_notificacion', $data, true);
				$mail->SetFrom('sao@grupohi.mx', utf8_decode('Bitácora Operación y Mantto.'));
				$mail->Subject = utf8_decode('Técnico Asignado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
				$mail->Body      = $html;
				$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!";
				foreach ($correos as $correo):
					$mail->AddAddress($correo["correo"]);
				endforeach;
				foreach($copiaoculta as $co):
					$mail->AddBcc($co["correo"]);
				endforeach;
				$mail->Send();
				/*foreach($correos as $correo):
					$this->email->clear();
					$this->email->to($correo["correo"]);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Técnico Asignado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;
				foreach($copiaoculta as $co):
					$this->email->clear();
					$this->email->bcc($co['correo']);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Técnico Asignado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;*/
			endif;
			$this->template->load('template','asignacion_mensaje',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function carga_informacion()
	{
		$id=$this->input->post('id');
		$data["result"]=$this->asignacion_model->desplega_reporte($id);
		$mensaje = '<div class="square">
    		<h5><strong>GENERALES DEL REPORTE</strong></h5>
            <div class="col-md-12">
            	<div class="pull-right">'.
					$data["result"][0]["folio"].'
                	<br>
                	<span class="label label-'.$data["result"][0]["color"].'">'.$data["result"][0]["nombre_clasificacion"].'</span>
            	</div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="row">
                <div class="col-md-4">
                    <strong>
                        Plaza de Cobro<br>
                        Nombre de quien reporta<br>
                        Puesto<br>
                    </strong>
                </div>
                <div class="col-md-8">'.
                    $data["result"][0]["nombre_plaza"].'<br>'.
                    $data["result"][0]["nombre_reporta"].'<br>'.
                    $data["result"][0]["puesto_reporta"].'
                </div>
            </div>
            <div class="row">
            	<div class="col-md-12"><hr></div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <strong>
                        Tipo de Reporte<br>
                        Fecha Detecci&oacute;n Falla<br>
                        Hora Detecci&oacute;n Falla<br>
                    </strong>
                </div>
                <div class="col-md-8">'.
                    $data["result"][0]["nombre_tiporeporte"].'<br>'.
                    $data["result"][0]["fecha"].'<br>'.
                    $data["result"][0]["hora"].'
                </div>
            </div>
            <div class="row">
            	<div class="col-md-12"><hr></div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <strong>
                        &Aacute;rea de Afectaci&oacute;n<br>';
			if($data["result"][0]["nombre_area"]=="CARRIL"):
				$mensaje .='Ubicaci&oacute;n<br>';
			endif;
                        $mensaje .= 'Tipo de Falla<br>
                        Observaciones<br>
                    </strong>
                </div>
                <div class="col-md-8">'.
                    $data["result"][0]["nombre_area"].'<br>';
					if($data["result"][0]["nombre_area"]=="CARRIL"):
						$mensaje .=$data["result"][0]["nombre_carril"].'<br>';
					endif;
                    $mensaje .= $data["result"][0]["nombre_tipofalla"].'<br>'.
                    $data["result"][0]["observacion_reporte"].'
                </div>
            </div>';
			echo $mensaje;
	}
}
/*
*end modules/login/controllers/index.php
*/
