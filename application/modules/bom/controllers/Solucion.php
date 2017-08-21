<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Solucion extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('grl/general_model');
		$this->load->model('bom_general_model');
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
			$data['reportes'] = $this->bom_general_model->desplega_reportes(3,$data["iduser"]);
			$this->template->load('template','solucion',$data);
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
			$data["result"] = $this->solucion_model->desplega_reporte($id);

			$data["idreporte"]=$id;
			$this->template->load('template','solucion_generales',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function carga_tabla_reparar()
	{
		$idreporte = $this->input->post('idreporte');
		$data["equipos"] = $this->solucion_model->desplega_equipos($idreporte);
		$this->load->view('solucion_tabla_reparar',$data);
	}

	public function carga_datos_activo()
	{
		$idactivo = $this->input->get('idactivo');
		$resultados = $this->solucion_model->desplega_activo($idactivo);
		$datasource = array();
		foreach ($resultados as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function agregar_carga_tabla_reparar()
	{
		$id=$this->input->post('reporte');
		$data["reparos"]=$this->solucion_model->desplega_reparo($id);
		$data["equipos"] = $this->solucion_model->desplega_equipos($id);
		$this->load->view('solucion_tabla_reparar_agregar',$data);
	}

	public function carga_tabla_reemplazar()
	{
		$this->load->view('solucion_tabla_reemplazar');
	}

	public function agregar_carga_tabla_reemplazar()
	{
		$id=$this->input->post('reporte');
		$data["reemplazos"]=$this->solucion_model->desplega_reemplazo($id);
		$data["equipos"] = $this->solucion_model->desplega_equipos($id);
		$this->load->view('solucion_tabla_reemplazar_agregar',$data);
	}


	public function registrar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css']='';
			$data['js']='';
			$idreporte = $this->input->post('idreporte');
			$tipo = $this->input->post('idtipo');
			$clasificacion = $this->input->post('idclasificacion');
			$n_reparar = $this->input->post('n_reparar');
			$n_reemplazar = $this->input->post('n_reemplazar');
			$reparar = $this->input->post('reparar');
			$reemplazar = $this->input->post('reemplazar');
			$solucion = $this->input->post('registro-solucion');
			$Reparar = '';
			$Reemplazar = '';
			$Equipos = '';
			$Marcas = '';
			$Modelos = '';
			/*$Capacidades = '';
			$Series = '';*/
			$Motivos = '';
			$Destinos = '';
			$Fechas = '';
			$VEquipos = '';
			$VMarcas = '';
			$VModelos = '';
			//$VCapacidades = '';
			//$VSeries = '';
			$VMotivos = '';
			$VUbicaciones = '';
			$NMarcas = '';
			$NModelos = '';
			//$NCapacidades = '';
			$NSeries = '';
			$NMotivos = '';
			//$NUbicaciones = '';
			$IDreparo = '';
			$IDreemplazo = '';

			for($k=1;$k<=$n_reparar;$k++):
				if($this->input->post('motivo'.$k)!=''):
					$equipo = ($this->input->post('equipo'.$k)=='')?0:$this->input->post('equipo'.$k);
					$marca = ($this->input->post('marca'.$k)=='')?0:$this->input->post('marca'.$k);
					$modelo = ($this->input->post('modelo'.$k)=='')?0:$this->input->post('modelo'.$k);
					//$capacidad = ($this->input->post('capacidad'.$k)=='')?0:$this->input->post('capacidad'.$k);
					//$serie = ($this->input->post('serie'.$k)=='')?0:$this->input->post('serie'.$k);
					$motivo = ($this->input->post('motivo'.$k)=='')?0:$this->input->post('motivo'.$k);
					$destino = ($this->input->post('destino'.$k)=='')?0:$this->input->post('destino'.$k);
					$fecha = ($this->input->post('fecha'.$k)=='')?0:$this->input->post('fecha'.$k);
					$idreparo=($this->input->post('idreparo'.$k)=='')?0:$this->input->post('idreparo'.$k);

					//$Reparar.= $marca.','.$modelo.','.$capacidad.','.$serie.','.$motivo.','.$destino.','.$fecha.';';

					if($k==$n_reparar):$coma=',';else:$coma=',';endif;
					$Equipos .= $equipo.$coma;
					$Marcas .= $marca.$coma;
					$Modelos .= $modelo.$coma;
					//$Capacidades .= $capacidad.$coma;
					//$Series .= $serie.$coma;
					$Motivos .= $motivo.$coma;
					$Destinos .= $destino.$coma;
					$Fechas .= $fecha.$coma;
					$IDreparo .=$idreparo.$coma;
				endif;
			endfor;
			//echo '<br><br><br><br><br><br><br>';
			/*echo $Equipos.'=Equipos<br>';
			echo $Marcas.'=Marcas<br>';
			echo $Modelos.'=Modelos<br>';
			//echo $Capacidades.'<br>';
			//echo $Series.'<br>';
			echo $Motivos.'=Motivos<br>';
			echo $Destinos.'=Destinos<br>';
			echo $Fechas.'=Fechas<br>';
			echo $IDreparo.'=IDs reparo<br>';*/
			for($k=1;$k<=$n_reemplazar;$k++):
				if($this->input->post('v_motivo'.$k)!=''):
					$v_equipo = ($this->input->post('hidden-v_equipo'.$k)=='')?0:$this->input->post('hidden-v_equipo'.$k);
					$v_marca = ($this->input->post('v_marca'.$k)=='')?0:$this->input->post('v_marca'.$k);
					$v_modelo = ($this->input->post('v_modelo'.$k)=='')?0:$this->input->post('v_modelo'.$k);
					//$v_capacidad = ($this->input->post('v_capacidad'.$k)=='')?0:$this->input->post('v_capacidad'.$k);
					//$v_serie = ($this->input->post('v_serie'.$k)=='')?0:$this->input->post('v_serie'.$k);
					$v_motivo = ($this->input->post('v_motivo'.$k)=='')?0:$this->input->post('v_motivo'.$k);
					$v_ubicacion = ($this->input->post('v_ubicacion'.$k)=='')?0:$this->input->post('v_ubicacion'.$k);

					$n_marca = ($this->input->post('n_marca'.$k)=='')?0:$this->input->post('n_marca'.$k);
					$n_modelo = ($this->input->post('n_modelo'.$k)=='')?0:$this->input->post('n_modelo'.$k);
					//$n_capacidad = ($this->input->post('n_capacidad'.$k)=='')?0:$this->input->post('n_capacidad'.$k);
					$n_serie = ($this->input->post('n_serie'.$k)=='')?0:$this->input->post('n_serie'.$k);
					$n_motivo = ($this->input->post('n_motivo'.$k)=='')?0:$this->input->post('n_motivo'.$k);
					//$n_ubicacion = ($this->input->post('n_ubicacion'.$k)=='')?0:$this->input->post('n_ubicacion'.$k);

					$idreemplazo =($this->input->post('idreemplazo'.$k)=='')?0:$this->input->post('idreemplazo'.$k);

					//$Reemplazar.= $n_marca.','.$n_modelo.','.$n_capacidad.','.$n_serie.','.$n_motivo.','.$n_ubicacion.','.$v_marca.','.$v_modelo.','.$v_capacidad.','.$v_serie.','.$v_motivo.','.$v_ubicacion.','.$idreemplazo.';';

					if($k==$n_reemplazar):$coma=',';else:$coma=',';endif;
					$VEquipos .= $v_equipo.$coma;
					$VMarcas .= $v_marca.$coma;
					$VModelos .= $v_modelo.$coma;
					//$VCapacidades .= $v_capacidad.$coma;
					//$VSeries .= $v_serie.$coma;
					$VMotivos .= $v_motivo.$coma;
					$VUbicaciones .= $v_ubicacion.$coma;

					$NMarcas .= $n_marca.$coma;
					$NModelos .= $n_modelo.$coma;
					//$NCapacidades .= $n_capacidad.$coma;
					$NSeries .= $n_serie.$coma;
					$NMotivos .= $n_motivo.$coma;
					//$NUbicaciones .= $n_ubicacion.$coma;

					$IDreemplazo .=$idreemplazo.$coma;
				endif;
			endfor;
			/*echo $VEquipos.'=VEquipos<br>';
			echo $VMarcas.'=VMarcas<br>';
			echo $VModelos.'=VModelos<br>';
			//echo $VCapacidades.'<br>';
			//echo $VSeries.'<br>';
			echo $VMotivos.'=VMotivos<br>';
			echo $VUbicaciones.'=VUbicaciones<br>';
			echo $NMarcas.'=NMarcas<br>';
			echo $NModelos.'=NModelos<br>';
			//echo $NCapacidades.'<br>';
			echo $NSeries.'<br>';
			echo $NMotivos.'=NMotivos<br>';
			//echo $NUbicaciones.'<br>';
			echo $IDreemplazo.'=ID remplazo<br>';*/
			$result = $this->solucion_model->registra_solucion(
																$idreporte,
																$data['iduser'],
																$solucion,
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
																$VUbicaciones,
																$NMarcas,
																$NModelos,
																$NSeries,
																$NMotivos,
																$IDreparo,$IDreemplazo,
																$data['usuario']
																);
			$data["mensaje"] = $result[0]["mensaje"];
			//echo $data["mensaje"];
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
				$this->load->model('emitir_model');
				//echo $idreporte;
				$data["result"] = $this->emitir_model->desplega_encabezado_reporte($idreporte);
				$correos = $this->notificacion_model->select_correos($tipo,$clasificacion,$data["result"][0]["idplaza"],23);
				$copiaoculta = $this->notificacion_model->select_copiaoculta();
				$data["reparar"] = $this->emitir_model->desplega_equipo_reparar($idreporte,$data["result"][0]["idregistro_solucion"]);
				$data["reemplazar"] = $this->emitir_model->desplega_equipo_reemplazar($idreporte,$data["result"][0]["idregistro_solucion"]);
				$html = $this->load->view('solucion_notificacion', $data, true);
				$mail->SetFrom('sao@grupohi.mx', utf8_decode('Bitácora Operación y Mantto.'));
				$mail->Subject = utf8_decode('Solución Registrada '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
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
					$this->email->subject('Solución Registrada '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;
				foreach($copiaoculta as $co):
					$this->email->clear();
					$this->email->bcc($co['correo']);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Solución Registrada '.$data["result"][0]["folio"].' Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;*/
			endif;
			$this->template->load('template','solucion_mensaje',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}
	/*public function registrar()
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
			$result = $this->diagnostico_model->registra_diagnostico($idreporte,$diagnostico,$data['iduser']);
			$data["css"]='';
			$data["js"]='';
			$data['mensaje'] = $result[0]["mensaje"];
			if($data['mensaje']!='Error'):
				$this->load->model('notificacion_model');
				$this->load->library('email');
				$this->load->model('solucion_model');
				$correos = $this->notificacion_model->select_correos($tipo,$clasificacion);
				$copiaoculta = $this->notificacion_model->select_copiaoculta();
				$data["result"] = $this->solucion_model->desplega_reporte($idreporte);
				$html = $this->load->view('diagnostico_notificacion', $data, true);
				foreach($correos as $correo):
					$this->email->clear();
					$this->email->to($correo["correo"]);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Diagnóstico Registrado Falla Equipo Peaje Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;
				foreach($copiaoculta as $co):
					$this->email->clear();
					$this->email->bcc($co['correo']);
					$this->email->from('sao@grupohi.mx','Bitácora Operación y Mantto.');
					$this->email->subject('Diagnóstico Registrado Falla Equipo Peaje Plaza "'.$data["result"][0]["nombre_plaza"].'"');
					$this->email->message($html);
					$this->email->send();
				endforeach;
			endif;
			$this->template->load('template','diagnostico_mensaje',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
	}*/

	public function carga_informacion()
	{
		$id=$this->input->post('id');
		$data["result"]=$this->solucion_model->desplega_reporte($id);
		$data["reparar"]=$this->solucion_model->desplega_reparo($data["result"][0]["idreporte"]);
		$data["reemplazar"]=$this->solucion_model->desplega_reemplazo($data["result"][0]["idreporte"]);
		$tabla= '<div class="square">
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
				$tabla .='Ubicaci&oacute;n<br>';
			endif;
            $tabla .='Tipo de Falla<br>
                        Tipo de Falla<br>
                        Observaciones<br>
                    </strong>
                </div>
                <div class="col-md-8">'.
                    $data["result"][0]["nombre_area"].'<br>';
				if($data["result"][0]["nombre_area"]=="CARRIL"):
				$tabla .=$data["result"][0]["nombre_carril"].'<br>';
			endif;
			 $tabla .=  $data["result"][0]["nombre_tipofalla"].'<br>'.
                    $data["result"][0]["observacion_reporte"].'
                </div>
            </div>';
			$tabla .= '<div class="row">
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
			<div class="row">
            	<div class="col-md-12"><hr></div>
            </div>
			<div class="row">
				<div class="col-md-4">
					<strong>
						Diagn&oacute;stico
					</strong>
				</div>
                <div class="col-md-8">'.
                    $data["result"][0]["diagnostico"].'<br>'.
                '</div>
            </div>';
			if($data["result"][0]["reparar"]==1):
            $tabla .='<table style="width:100%"><tr><td colspan="4"><strong><i>EQUIPO QUE REQUIERE REPARACI&Oacute;N</i></strong></td></tr>
            <tr>
                <td colspan="4">
                    <table style="font-size:8px;width:100%;" cellpadding="0" cellspacing="0" border="1">
                        <tr style="background-color:#CCC;" align="center">
                            <td><strong>EQUIPO</strong></td>
							<td><strong>MARCA</strong></td>
                            <td><strong>MODELO</strong></td>
                            <td><strong>MOTIVO</strong></td>
							<td><strong>UBICACI&Oacute;N</strong></td>
							<td><strong>FECHA DE REGRESO</strong></td>
                        </tr>';
                        $n=0; foreach($data["reparar"] as $elemento): $n++;
                        $tabla.='<tr align="center">
                            <td>'.$elemento["nombre_equipo"].'</td>
							<td>'.$elemento["marca"].'</td>
                            <td>'.$elemento["modelo"].'</td>
                            <td>'.$elemento["motivo"].'</td>
                            <td>'.$elemento["destino"].'</td>
                            <td>'.$elemento["fecha"].'</td>
                        </tr>';
                        endforeach;
                    $tabla.='</table>
                </td>
            </tr>';
            endif;
			if($data["result"][0]["reemplazo"]==1):
            $tabla.='<tr><td colspan="4"><strong><i>MATERIALES REQUERIDOS POR DA&Ntilde;O O REEMPLAZO</i></strong></td></tr>
            <tr>
                <td colspan="2">
                    <table style="font-size:8px;width:100%;" cellpadding="0" cellspacing="0" border="1">
                        <tr style="background-color:#CCC;" align="center">
                            <td><strong>EQUIPO</strong></td>
							<td><strong>MARCA</strong></td>
                            <td><strong>MODELO</strong></td>
                            <td><strong>MOTIVO</strong></td>
                            <td><strong>UBICACI&Oacute;N</strong></td>
                        </tr>';
                        $n=0; foreach($data["reemplazar"] as $elemento): $n++;
                        $tabla.='<tr align="center">
                            <td>'.$elemento["r_nombre_equipo"].'</td>
							<td>'.$elemento["r_marca"].'</td>
                            <td>'.$elemento["r_modelo"].'</td>
                            <td>'.$elemento["r_motivo"].'</td>
                            <td>'.$elemento["r_ubicacion"].'</td>
                        </tr>';
                        endforeach;
                    $tabla.='</table>
                </td>
			</tr>
			<tr>
                <td colspan="2" align="center" style="font-size:8px;"><i>(MATERIALES A SUSTITUIR)</i></td>
            </tr>
            </tr>
			</table>';
            endif;
			$tabla .='</div>';
			echo $tabla;
	}
}
/*
*end modules/login/controllers/index.php
*/
