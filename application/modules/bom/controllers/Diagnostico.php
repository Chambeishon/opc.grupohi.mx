<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Diagnostico extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('grl/general_model');
		$this->load->model('bom_general_model');
		$this->load->model('diagnostico_model');
		$this->load->model('solucion_model');
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
			$data['reportes'] = $this->bom_general_model->desplega_reportes(2,$data["iduser"]);
			$this->template->load('template','diagnostico',$data);
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
			$data["result"] = $this->diagnostico_model->desplega_reporte($id);
			//$data["usuarios"] = $this->general_model->desplega_lista_usuarios_activos();
			$data["idreporte"]=$id;
			//$result[0]["folio"];
			$this->template->load('template','diagnostico_generales',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function registrar()
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
			$diagnostico=$this->input->post('registro-diagnostico');
			$n_reparar = $this->input->post('n_reparar');
			$n_reemplazar = $this->input->post('n_reemplazar');
			$reparar = $this->input->post('reparar');
			$reemplazar = $this->input->post('reemplazar');
			$Reparar = '';
			$Reemplazar = '';
			$Equipos = '';
			$Marcas = '';
			$Modelos = '';
			$Motivos = '';
			$Destinos = '';
			$Fechas = '';
			$VEquipos = '';
			$VMarcas = '';
			$VModelos = '';
			$VMotivos = '';
			$VUbicaciones = '';
			$NMarcas = '';
			$NModelos = '';
			$NCapacidades = '';
			$NSeries = '';
			$NMotivos = '';
			$NUbicaciones = '';
			for($k=1;$k<=$n_reparar;$k++):
				if($this->input->post('motivo'.$k)!=''):
					$equipo = ($this->input->post('equipo'.$k)=='')?0:$this->input->post('equipo'.$k);
					$marca = ($this->input->post('marca'.$k)=='')?0:$this->input->post('marca'.$k);
					$modelo = ($this->input->post('modelo'.$k)=='')?0:$this->input->post('modelo'.$k);
					$motivo = ($this->input->post('motivo'.$k)=='')?0:$this->input->post('motivo'.$k);
					$destino = ($this->input->post('destino'.$k)=='')?0:$this->input->post('destino'.$k);
					$fecha = ($this->input->post('fecha'.$k)=='')?0:$this->input->post('fecha'.$k);

					//$Reparar.= $marca.','.$modelo.','.$capacidad.','.$serie.','.$motivo.','.$destino.','.$fecha.';';

					if($k==$n_reparar):$coma=',';else:$coma=',';endif;
					$Equipos .= $equipo.$coma;
					$Marcas .= $marca.$coma;
					$Modelos .= $modelo.$coma;
					$Motivos .= $motivo.$coma;
					$Destinos .= $destino.$coma;
					$Fechas .= $fecha.$coma;
				endif;
			endfor;
			for($k=1;$k<=$n_reemplazar;$k++):
				if($this->input->post('v_motivo'.$k)!=''):
					$v_equipo = ($this->input->post('v_equipo'.$k)=='')?0:$this->input->post('v_equipo'.$k);
					$v_marca = ($this->input->post('v_marca'.$k)=='')?0:$this->input->post('v_marca'.$k);
					$v_modelo = ($this->input->post('v_modelo'.$k)=='')?0:$this->input->post('v_modelo'.$k);
					$v_motivo = ($this->input->post('v_motivo'.$k)=='')?0:$this->input->post('v_motivo'.$k);
					$v_ubicacion = ($this->input->post('v_ubicacion'.$k)=='')?0:$this->input->post('v_ubicacion'.$k);

					//$Reemplazar.= $v_marca.','.$v_modelo.','.$v_capacidad.','.$v_serie.','.$v_motivo.','.$v_ubicacion.';';

					if($k==$n_reemplazar):$coma=',';else:$coma=',';endif;
					$VEquipos .= $v_equipo.$coma;
					$VMarcas .= $v_marca.$coma;
					$VModelos .= $v_modelo.$coma;
					$VMotivos .= $v_motivo.$coma;
					$VUbicaciones .= $v_ubicacion.$coma;
				endif;
			endfor;
			/*echo "<br>";
			echo $idreporte."<br>";
			echo $diagnostico."<br>";
			echo $data['iduser']."<br>";
			echo $reparar."<br>";
			echo $reemplazar."<br>";
			echo $Equipos."<br>";
			echo $Marcas."<br>";
			echo $Modelos."<br>";
			echo $Motivos."<br>";
			echo $Destinos."<br>";
			echo $Fechas."<br>";
			echo $VEquipos."<br>";
			echo $VMarcas."<br>";
			echo $VModelos."<br>";
			echo $VMotivos."<br>";
			echo $VUbicaciones."<br>";*/
			$result = $this->diagnostico_model->registra_diagnostico($idreporte,$diagnostico,$data['iduser'],
																$reparar,
																$reemplazar,
																$Equipos,
																$Marcas,
																$Modelos,
																$Motivos,
																$Destinos,
																$Fechas,
																$VEquipos,
																$VMarcas,
																$VModelos,
																$VMotivos,
																$VUbicaciones);

			$data["css"]='';
			$data["js"]='';
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
				$this->load->model('solucion_model');
				$data["result"] = $this->solucion_model->desplega_reporte($idreporte);
				$data["reparos"]=$this->solucion_model->desplega_reparo($idreporte);
				$data["reemplazos"]=$this->solucion_model->desplega_reemplazo($idreporte);
				$correos = $this->notificacion_model->select_correos($tipo,$clasificacion,$data["result"][0]["idplaza"],22);
				$copiaoculta = $this->notificacion_model->select_copiaoculta();
				$html = $this->load->view('diagnostico_notificacion', $data, true);
				$mail->SetFrom('sao@grupohi.mx', utf8_decode('Bitácora Operación y Mantto.'));
				$mail->Subject = utf8_decode('Diagnóstico Registrado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
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
					$this->email->subject('Diagnóstico Registrado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;
				foreach($copiaoculta as $co):
					$this->email->clear();
					$this->email->bcc($co['correo']);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Diagnóstico Registrado '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;*/
			endif;
			$this->template->load('template','diagnostico_mensaje',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function carga_informacion()
	{
		$id=$this->input->post('id');
		$data["result"]=$this->diagnostico_model->desplega_reporte($id);
		$html =  '<div class="square">
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
				$html .='Ubicaci&oacute;n<br>';
			endif;
            $html .='Tipo de Falla<br>
                        Observaciones<br>
                    </strong>
                </div>
                <div class="col-md-8">'.
                    $data["result"][0]["nombre_area"].'<br>';
             if($data["result"][0]["nombre_area"]=="CARRIL"):
				$html .=$data["result"][0]["nombre_carril"].'<br>';
			endif;
			 $html .=  $data["result"][0]["nombre_tipofalla"].'<br>'.
                    $data["result"][0]["observacion_reporte"].'
                </div>
            </div>';
			$html .= '<div class="row">
            	<div class="col-md-12"><hr></div>
            </div>
			<div class="row">
				<div class="col-md-4">
					<strong>
						Fecha de Asignaci&oacute;n<br>
						Hora de Asignaci&oacute;n<br>
						T&eacute;cnico<br>
					</strong>
				</div>
				<div class="col-md-8">'.
					$data["result"][0]["fecha_asignacion"].'<br>'.
					$data["result"][0]["hora_asignacion"].'<br>'.
					$data["result"][0]["tecnico"].'
				</div>
			</div>
            </div>';

			echo $html;
	}

	public function carga_tabla_reemplazar()
	{
		$idreporte = $this->input->post('idreporte');
		$data["equipos"] = $this->solucion_model->desplega_equipos($idreporte);
		$this->load->view('diagnostico_tabla_reemplazar',$data);
	}
}
/*
*end modules/login/controllers/index.php
*/
