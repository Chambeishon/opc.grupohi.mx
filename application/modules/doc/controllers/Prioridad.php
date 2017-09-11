<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Prioridad extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');
		$this->load->library('menu');
		$this->load->model('prioridad_model');
	}
	public function index()
	{
		if(!$this->session->userdata('id'))
			redirect('login/index', 'refresh');

		$session_data = $this->session->userdata();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$data['idperfil'] = $session_data['idperfil'];
		$data["menu"] = $this->menu->crea_menu($data['idperfil']);
		$data['css'] = '<link href="'. base_url('assets/css/infragistics.theme.css') .'" rel="stylesheet" />';
		$data['css'] .= '<link href="'. base_url('assets/css/infragistics.css') .'" rel="stylesheet" />';
		$data['js'] = '<script src="'. base_url('assets/js/jquery-ui.min.js') .'"></script> ';
		$data['js'] .= '<script src="'. base_url('assets/js/doc-prioridades.js') .'"></script> ';

		$data['prioridades'] = $this->prioridad_model->obtener_prioridades();

		$this->template->load('template','prioridad',$data);
	}

	public function obtener($idprioridad = 0)
	{
		$send = array(
			'error' => false,
			'msg' => "",
			'data' => false,
		);
		$data = $this->prioridad_model->obtener_prioridades($idprioridad);
		$send['data'] = $data[0];

		if (empty($send['data']))
			$send = array(
				'error' => true,
				'msg' => "No existe esa prioridad o ya fué eliminada",
				'data' => false,
			);

		echo json_encode($send);
	}

	public function guardar()
	{
		if(!$this->session->userdata('id'))
			redirect('login/index', 'refresh');

		$session_data = $this->session->userdata();
		$data = $this->input->post();
		$send = array(
			'error' => false,
			'msg' => "",
			'data' => "",
		);
		$idprioridad = 0;

		// Revisa si los rangos se encuentran vacios
		if (empty($data['nombre']) || empty($data['clave']))
		{
			$send['error'] = true;
			$send['msg'] = "Los campos no pueden estar vacios.";
		}

		else
		{
			$data['idusuario'] = $session_data['id'];

			// Estamos editando o creando?
			if (!empty($data['idprioridad']))
			{
				$idprioridad = $data['idprioridad'];
				unset($data['idprioridad']);

				$this->prioridad_model->modificar_prioridad($data, $idprioridad);
			}

			// Guarda el rango en la db
			else
				$id = $this->prioridad_model->agregar_prioridad($data);

			// Regresa una nueva fila
			$send['msg'] = "El parámetro fué ".(!empty($idprioridad) ? "modificado" : "agregado")." con éxito";
			$send['data'] = '<tr id="tr_'. $id .'">
								<th scope="row" class="td_nombre">'. $data['nombre'] .'</th>
								<td class="td_clave">'. $data['clave'] .'</td>
								<td><a class="modificar_prioridad">Modificar</a></td>
								<td><a href="'. base_url('doc/prioridad/eliminar/'. $id) .'" class="eliminar_prioridad">Eliminar</a></td>
							</tr>';
		}

		echo json_encode($send);
	}

	public function eliminar($idprioridad = 0)
	{
		$send = array();

		if(!$this->session->userdata('id'))
			$send = array(
				'error' => false,
				'msg' => ""
			);

		else
		{
			$idprioridad = (int) $idprioridad;

			if (!$idprioridad || !is_int($idprioridad))
			{
				$send['error'] = true;
				$send['msg'] = "El ID de la prioridad está vacío o no es válido";
			}

			// REvisa si es la actividad por default (ID 1)
			else if ($idprioridad == 1)
				$send = array(
					'error' => true,
					'msg' => "No se puede eliminar la prioridad por defecto"
				);

			// O si la prioridad ya se está usando
			else if ($this->prioridad_model->revisar_uso_prioridad($idprioridad))
				$send = array(
					'error' => true,
					'msg' => "La prioridad no puede ser eliminada porque está siendo usada por una o más actividades"
				);

			else
			{
				$this->prioridad_model->eliminar_prioridad($idprioridad);
				$send = array(
					'error' => false,
					'msg' => "La prioridad fué eliminada con éxito"
				);
			}
		}

		echo json_encode($send);
	}
}
