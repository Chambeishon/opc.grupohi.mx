<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes_ejecutivos extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');
		$this->load->library('menu');
		$this->load->model('reportes_ejecutivos_model');
		$this->load->model('programacion_model');
	}

	public function index()
	{
		if(!$this->session->userdata('id'))
			redirect('login/index', 'refresh');

		$session_data = $this->session->userdata();

		// Codigo duro, usuarios con permiso de configurar el reporte
		$accesoUsuarios = array(58, 100, 130, 136);
		if (!in_array($session_data['id'], $accesoUsuarios))
			redirect('login/index', 'refresh');

		$session_data = $this->session->userdata();
		$data["mensaje"]='';
		$data['proyectos'] = array();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$data['idperfil'] = $session_data['idperfil'];
		$data["menu"] = $this->menu->crea_menu($data['idperfil']);
		$data['css'] = '';
		$data['js'] = '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script>';
		$data['js'] .= '<script src="'.base_url('assets/js/doc-reportes-ejecutivos-config.js').'"></script>';

		$data['campos'] = $this->session->flashdata('campos') ? $this->session->flashdata('campos') : array('tipo' => false, 'rango' => false, 'fecha' => false);
		$data["mensaje"].= $this->session->flashdata('mensaje') ? $this->session->flashdata('mensaje') : '';
		$proyectos = $this->programacion_model->desplegar_contratos_activos($data['iduser']);

		foreach ($proyectos as $p)
			$data['proyectos'][$p->idproyecto] = array(
				'nombre' => $p->nombre_proyecto,
				'params' => array(
					'1' => $this->reportes_ejecutivos_model->obtener_parametros_todos(1, $p->idproyecto),
					'2' => $this->reportes_ejecutivos_model->obtener_parametros_todos(2, $p->idproyecto),
				),
			);

		$data['periodos'] = array(
			'1' => 'D&iacute;as',
			'2' => 'Semanas',
			'3' => 'Meses'
		);
		$data['tipos'] = array(
			'1' => 'Fecha vencida',
			'2' => 'Fecha por vencer'
		);

		$this->template->load('template','reportes_ejecutivos',$data);
	}

	public function guardar()
	{
		if($this->session->userdata('id')):
			$session_data = $this->session->userdata();
			$data = $this->input->post();
			$send = array(
				'error' => false,
				'msg' => "",
				'data' => "",
			);
			$periodos = array(
				'1' => 'D&iacute;as',
				'2' => 'Semanas',
				'3' => 'Meses'
			);

			// Filtra el array de rangos, aceptando "0" como respuesta válida
			$data['rangos'] = array_filter($data['rangos'], 'strlen');

			// Revisa si los rangos se encuentran vacios
			if (empty($data['rangos'])):
				$send['error'] = true;
				$send['msg'] = "Los campos de rango no pueden estar vacios.";

			else:

				$guardar = array(
					'tipo' => $data['tipo'],
					'rango_inicial' => $data['rangos']['de'],
					'rango_final' => $data['rangos']['a'],
					'periodo' => $data['periodo'],
					'iduser' => $session_data['id'],
					'idproyecto' => $data['idproyecto']
				);

				// Guarda el rango en la db
				$id = $this->reportes_ejecutivos_model->agregar_parametros($guardar);

				// Regresa una nueva fila
				$send['msg'] = "El parámetro fué agregado con éxito";
				$send['data'] = '<tr>
									<th scope="row">'. $data['rangos']['de'] .' - '. $data['rangos']['a'] .'</th>
									<td>'. $periodos[$data['periodo']] .'</td>
									<td><a href="'. base_url('doc/reportes_ejecutivos/eliminar/'. $id) .'" class="eliminar_rango">Eliminar</a></td>
								</tr>';
			endif;

			echo json_encode($send);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function eliminar($idreporte_ejecutivo = 0)
	{
		if($this->session->userdata('id')):
			$send = array(
				'error' => false,
				'msg' => ""
			);

			if (!$idreporte_ejecutivo):
				$send['error'] = true;

				$send['msg'] = "El ID del parámetro está vacío o no es válido";

			else:
				$this->reportes_ejecutivos_model->eliminar_parametro($idreporte_ejecutivo);
				$send['msg'] = "El parámetro fué eliminado con éxito";
			endif;

			echo json_encode($send);
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function modificar($idreporte_ejecutivo = 0)
	{
		if($this->session->userdata('id')):
			if (!$idreporte_ejecutivo):
				$this->session->set_flashdata('mensaje', "<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
						<br>El ID del par&aacute;metro est&aacute; vac&iacute;o o no es v&aacute;lido<br>
					</div>");
				return redirect('doc/reportes_ejecutivos/index', 'refresh');
			else:
				if ($this->input->get('tipo')):
					$data = $this->input->get();
					$this->reportes_ejecutivos_model->modificar_parametro($idreporte_ejecutivo, $data);
					$this->session->set_flashdata('mensaje', "<div class='alert alert-success'>
								<h5>El par&aacute;metro fu&eacute; modificado con &eacute;xito</h5></div>");
					return redirect('doc/reportes_ejecutivos/index', 'refresh');
				else:
					$session_data = $this->session->userdata();
					$data["mensaje"]='';
					$data['usuario'] = $session_data['username'];
					$data['iduser'] = $session_data['id'];
					$data['idperfil'] = $session_data['idperfil'];
					$data["menu"] = $this->menu->crea_menu($data['idperfil']);
					$data['idreporte_ejecutivo'] = $idreporte_ejecutivo;
					$data['periodos'] = array(
						'1' => 'D&iacute;as',
						'2' => 'Semanas',
						'3' => 'Meses'
					);
					$data['tipos'] = array(
						'1' => 'Fecha vencida',
						'2' => 'Fecha por vencer'
					);
					$parametro = $this->reportes_ejecutivos_model->obtener_parametro($idreporte_ejecutivo);
					$data['tipo'] = $parametro[0]['tipo'];
					$data['rango'] = $parametro[0]['rango'];
					$data['fecha'] = $parametro[0]['fecha'];

					$this->template->load('template','reportes_ejecutivos_modificar',$data);
				endif;
			endif;
		else:
			redirect('login/index', 'refresh');
		endif;
	}
 }
