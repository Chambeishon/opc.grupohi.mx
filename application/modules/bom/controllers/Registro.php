<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Registro extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('grl/general_model');
		$this->load->model('area_model');
		$this->load->model('registro_model');
    }

    public function index()
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
			$data['perfiles'] = $this->general_model->desplega_perfil_usuario($session_data['idperfil']);
			$data['plazas'] = $this->general_model->desplega_plazas_usuario($session_data['id']);
			$data['areas'] = $this->area_model->desplega_areas_activas();
			$data['clasificaciones'] = $this->area_model->desplega_clasificaciones();
			//$data["menu_mp"] = $this->menu->crea_menu_mp($data['idperfil']);
			$this->template->load('template','registro',$data);
		else:
			redirect('login/index', 'refresh');
		endif;

    }

	public function carga_carriles()
	{
		$plaza = $this->input->post('plaza');
		$data['carriles'] = $this->general_model->desplega_carriles_plaza($plaza);
		$this->load->view('registro_linea',$data);
	}

	public function carga_fallas()
	{
		$area = $this->input->post('area');
		$data['fallas'] = $this->area_model->desplega_fallas_area($area);
		$this->load->view('registro_falla',$data);
	}

	public function carga_clasificacion()
	{
		$falla = $this->input->post('falla');
		$data['clasificaciones'] = $this->area_model->desplega_clasificaciones_falla($falla);
		$data['s_clasificaciones']=$this->area_model->desplega_clasificaciones();
		$data['fondo']=$data['clasificaciones'][0]['fondo'];
		$this->load->view('registro_clasificacion',$data);
	}

	public function registrar()
	{
		if($this->session->userdata('id')):
			$this->load->model('asignacion_model');
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '';
			$data['js'] = '';
			$plaza = $this->input->post('registro-plaza');
			$area = $this->input->post('registro-area');
			$carril = $this->input->post('registro-carril');
			$reporta = $this->input->post('registro-reporta');
			$puesto = $this->input->post('registro-puesto');
			$tipo = $this->input->post('registro-tipo');
			$fecha = $this->input->post('registro-fecha');
			$hora = $this->input->post('registro-hora');
			$falla = $this->input->post('registro-falla');
			$observaciones = $this->input->post('registro-observaciones');
			$clasificacion = $this->input->post('registro-clasificacion');
			$result = $this->registro_model->agrega_registro_reporte($plaza,$area,$carril,$reporta,$puesto,$tipo,$fecha,$hora,$falla,$observaciones,$clasificacion,$data['iduser']);
			$data['mensaje'] = $result[0]["mensaje"];
			$data['idreporte'] = $result[0]["idreporte"];
			$data["result"] = $this->asignacion_model->desplega_reporte($data["idreporte"]);
			$this->load->model('notificacion_model');
			$data['usuarios'] = $this->notificacion_model->select_usuarios($tipo,$clasificacion,$data["result"][0]["idplaza"],20);
			$this->template->load('template','registro_mensaje',$data);
			if($data['mensaje']!='Error'):
				//$this->load->library('email');
				$this->load->library('My_PHPMailer');
				$mail = new PHPMailer();
				$mail->IsSMTP();
				$mail->SMTPAuth   = true;
				$mail->Host       = "mail.hermesconstruccion.com.mx";
				$mail->Username   = 'sgwc@hermesconstruccion.com.mx';
				$mail->Password   = "hz9dzt";
				$mail->Port       = 25;
				$correos = $this->notificacion_model->select_correos($tipo,$clasificacion,$data["result"][0]["idplaza"],20);
				$copiaoculta = $this->notificacion_model->select_copiaoculta();
				$html = $this->load->view('registro_notificacion', $data, true);
				$mail->SetFrom('sao@grupohi.mx', utf8_decode('Bitácora Operación y Mantto.'));
				$mail->Subject = utf8_decode('Reporte Registrado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
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
					$this->email->subject('Reporte Registrado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;
				foreach($copiaoculta as $co):
					$this->email->clear();
					$this->email->bcc($co['correo']);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Reporte Registrado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;*/
			endif;
		else:
			redirect('login/index', 'refresh');
		endif;
	}


	public function pdf_registro($id)
	{
		$data["result"] = $this->registro_model->pdf_registro($id);
		//print_r($data["result"]);
		echo $data["result"][0]->idreporte;
		//$this->load->view('pdf_registro');
	}
}
/*
*end modules/login/controllers/index.php
*/
