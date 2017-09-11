<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start();
class Actividad extends MX_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('template');
		$this->load->library('menu');
		$this->load->model('actividad_model');
		$this->load->model('categoria_contrato_model');
		$this->load->model('subcategoria_contrato_model');
		$this->load->model('programacion_model');
		$this->load->model('area_model');
		$this->load->model('prioridad_model');
    }

	public function index()
    {
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			//$data["menu_doc"] = $this->menu->crea_menu_doc($data['idperfil'],$data['iduser']);
			$data['css'] = '<link href="'.base_url('assets/css/infragistics.theme.css').'" rel="stylesheet" />';
			$data['css'] .= '<link href="'.base_url('assets/css/infragistics.css').'" rel="stylesheet" />';
			$data['css'] .= '<link href="'.base_url('assets/css/bootstrap-datetimepicker.min.css').'" rel="stylesheet">';
			$data['js'] = '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script> ';
			$data['js'] .= '<script src="'.base_url('assets/js/modernizr.min.js').'"></script> ';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.core.js').'"></script>';
			$data['js'] .='<script src="'.base_url('assets/js/infragistics.lob.js').'"></script>';

			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker.min.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/bootstrap-datetimepicker-init.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/doc-act.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/jquery-form.js').'"></script>';
			$data["contratos"] = $this->programacion_model->desplegar_contratos_activos($data["iduser"]);
			$data["categorias"] = $this->programacion_model->desplegar_categorias_activas($data["iduser"]);
			$data["subcategorias"] = $this->programacion_model->desplegar_subcategorias_activas($data["iduser"]);
			$data["areas"] = $this->area_model->desplegar_areas();
			$data['prioridades'] = $this->prioridad_model->obtener_prioridades();
			$this->template->load('template','actividad',$data);
		else:
			redirect('login/index', 'refresh');
		endif;
    }

	public function categorias()
	{
		$idcontrato = $this->input->get('idcontrato');
		$categorias = $this->categoria_contrato_model->desplegar_categorias($idcontrato);
		$datasource = array();
		foreach ($categorias as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function subcategorias()
	{
		$idcontrato = $this->input->get('idcontrato');
		$idcategoria = $this->input->get('idcategoria');
		$subcategorias = $this->subcategoria_contrato_model->desplegar_subcategorias($idcontrato,$idcategoria);
		$datasource = array();
		foreach ($subcategorias as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function agregar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$idcontrato = $this->input->get('idcontrato');
			$idcategoria = $this->input->get('idcategoria');
			$idsubcategoria = $this->input->get('idsubcategoria');
			$nombre = ($this->input->get('nombre'));
			$descripcion = ($this->input->get('descripcion'));
			$documento = ($this->input->get('documento'));
			$referencia = ($this->input->get('referencia'));
			$empresa = ($this->input->get('area'));
			$persona = ($this->input->get('persona'));
			$detalle = ($this->input->get('detalle'));
			$prioridad = ($this->input->get('prioridad'));
			$observaciones = ($this->input->get('observaciones'));
			$areas='';
			foreach($this->input->get('areas') as $area):
				$areas = $areas.$area.';';
			endforeach;
			$result = $this->actividad_model->agregar_actividad($idcontrato,$idcategoria,$idsubcategoria,$nombre,$descripcion,$documento,$referencia,$empresa,$persona,$detalle,$observaciones,$areas,$data['usuario'], $prioridad);

			echo '{"msg":'.$result[0]["mensaje"].'}';
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function buscar()
	{
		$idactividad = $this->input->get('idactividad');
		$actividades = $this->actividad_model->desplegar_actividad($idactividad);
		$datasource = array();
		foreach ($actividades as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function desplegar()
	{
		$idcategoria = $this->input->get('idcategoria');
		$idsubcategoria = $this->input->get('idsubcategoria');
		$idcontrato = $this->input->get('idcontrato');
		$actividades = $this->actividad_model->desplegar_actividades($idcontrato,$idcategoria,$idsubcategoria);
		$datasource = array();
		foreach ($actividades as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}


	public function desplegar_actividad()
	{
		$idactividad = $this->input->get('idactividad');
		$actividades = $this->actividad_model->desplegar_actividad($idactividad);
		$datasource = array();
		foreach ($actividades as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function agregar_atividad_area()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$idactividad = $this->input->get('idactividad');
			$idarea = $this->input->get('idarea');
			$estado = $this->input->get('estado');
			$result = $this->actividad_model->agregar_actividad_area($idactividad,$idarea,$estado,$data['usuario']);
			echo '{"msg":'.$result[0]["mensaje"].'}';
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function desplegar_actividad_area()
	{
		$idactividad = $this->input->get('idactividad');
		$areas = $this->actividad_model->desplegar_actividad_area($idactividad);
		$datasource = array();
		foreach ($areas as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function usuarios_area()
	{
		$idarea = $this->input->get('idarea');
		$usuarios = $this->actividad_model->desplegar_usuarios_area($idarea);
		$datasource = array();
		foreach ($usuarios as $resultado):
			//$datasource[]=array_map('utf8_encode', $resultado);
			$datasource[]=($resultado);
		endforeach;
		echo json_encode($datasource);
	}

	public function modificar()
	{
		if($this->session->userdata('id')):
     		$session_data = $this->session->userdata();
     		$data['usuario'] = $session_data['username'];
	 		$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$idcontrato = $this->input->get('idcontrato');
			$idcategoria = $this->input->get('idcategoria');
			$idsubcategoria = $this->input->get('idsubcategoria');
			$idactividad = $this->input->get('modificar-idactividad');
			$nombre = $this->input->get('modificar-nombre');
			$descripcion = $this->input->get('modificar-descripcion');
			$documento = $this->input->get('modificar-documento');
			$referencia = $this->input->get('modificar-referencia');
			$empresa = $this->input->get('modificar-area');
			$persona = $this->input->get('modificar-persona');
			$detalle = $this->input->get('modificar-detalle');
			$idprioridad = $this->input->get('modificar-prioridad');
			$observaciones = $this->input->get('modificar-observaciones');
			$areas='';
			foreach($this->input->get('areas') as $area):
				$areas = $areas.$area.';';
			endforeach;
			$result = $this->actividad_model->modificar_actividad($idactividad,$idcontrato,$idcategoria,$idsubcategoria,$nombre,$descripcion,$documento,$referencia,$empresa,$persona,$detalle,$observaciones,$areas,$data['usuario'], $idprioridad);
			echo '{"msg":'.$result[0]["mensaje"].'}';
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function prioridades()
	{
		$data = $this->prioridad_model->obtener_prioridades();

		echo json_encode($data);
	}

}
/*
*end modules/login/controllers/index.php
*/
