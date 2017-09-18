<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes_ejecutivos_prioridad extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');
		$this->load->library('menu');
		$this->load->model('reportes_ejecutivos_model');
		$this->load->model('reportes_ejecutivos_prioridad_model');
		$this->load->model('programacion_model');
		$this->load->model('prioridad_model');
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
		$data['prioridades'] = array();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$data['idperfil'] = $session_data['idperfil'];
		$data["menu"] = $this->menu->crea_menu($data['idperfil']);
		$data['css'] = '';
		$data['js'] = '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script>';
		$data['js'] .= '<script src="'.base_url('assets/js/doc-reportes-ejecutivos-prioridad-config.js').'"></script>';

		$proyectos = $this->programacion_model->desplegar_contratos_activos($data['iduser']);

		foreach ($proyectos as $p)
			$data['proyectos'][$p->idproyecto] = array(
				'nombre' => $p->nombre_proyecto,
				'params' => array(
					1 => array(),
					2 => array()
				),
			);

		$prioridades = $this->prioridad_model->obtener_prioridades();

		foreach ($prioridades as $p)
			$data['prioridades'][$p['idprioridad']] = $p;

		$rangos = $this->reportes_ejecutivos_prioridad_model->obtener_parametro();

		foreach ($rangos as $r)
			$data['proyectos'][$r['idproyecto']]['params'][$r['tipo']][] = $r;

		$data['periodos'] = array(
			'1' => 'D&iacute;as',
			'2' => 'Semanas',
			'3' => 'Meses'
		);
		$data['tipos'] = array(
			'1' => 'Fecha vencida',
			'2' => 'Fecha por vencer'
		);

		$this->template->load('template','reportes_ejecutivos_prioridad',$data);
	}

	public function guardar()
	{
		if(!$this->session->userdata('id'))
			return redirect('login/index', 'refresh');

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
		if (empty($data['rangos']))
		{
			$send['error'] = true;
			$send['msg'] = "Los campos de rango no pueden estar vacios.";
		}

		else
		{
			$prioridades = $this->prioridad_model->obtener_prioridades();

			foreach ($prioridades as $p)
				$prioridades[$p['idprioridad']] = $p;

			$guardar = array(
				'tipo' => $data['tipo'],
				'rango_inicial' => $data['rangos']['de'],
				'rango_final' => $data['rangos']['a'],
				'periodo' => $data['periodo'],
				'iduser' => $session_data['id'],
				'idproyecto' => $data['idproyecto'],
				'idprioridad' => $data['idprioridad'],
			);

			// Guarda el rango en la db
			$id = $this->reportes_ejecutivos_prioridad_model->agregar_parametro($guardar);

			// Regresa una nueva fila
			$send['msg'] = "El parámetro fué agregado con éxito";
			$send['data'] = '
	<tr>
		<th scope="row">'. $data['rangos']['de'] .' - '. $data['rangos']['a'] .'</th>
		<td class="text-center">'. $prioridades[$data['idprioridad']]['nombre'] .'</td>
		<td class="text-center">'. $periodos[$data['periodo']] .'</td>
		<td class="text-center"><a href="'. base_url('doc/reportes_ejecutivos_prioridad/eliminar/'. $id) .'" class="eliminar_rango">Eliminar</a></td>
	</tr>';
		}

		echo json_encode($send);
	}

	public function eliminar($idreporte_prioridad = 0)
	{
		if(!$this->session->userdata('id'))
			return redirect('login/index', 'refresh');

		$send = array(
			'error' => false,
			'msg' => ""
		);

		if (!$idreporte_prioridad)
		{
			$send['error'] = true;
			$send['msg'] = "El ID del parámetro está vacío o no es válido";
		}

		else
		{
			$this->reportes_ejecutivos_prioridad_model->eliminar_parametro($idreporte_prioridad);
			$send['msg'] = "El parámetro fué eliminado con éxito";
		}

		echo json_encode($send);
	}
}
