<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes_ejecutivos extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');  
		$this->load->library('menu'); 
		$this->load->model('reportes_ejecutivos_model');  
	}

	public function index()
	{
		if($this->session->userdata('id')):
			$session_data = $this->session->userdata();
			$data["mensaje"]='';
			$data['proyectos'] = array();
			$data['usuario'] = $session_data['username'];
			$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);
			$data['css'] = '';
			$data['js'] = '<script src="'.base_url('assets/js/jquery-ui.min.js').'"></script>
<script type="text/javascript">
$(".box").slice(1).hide();
$(function() {
	$(".proyectos").change(function() {
		$(".box").hide();
		id = $(this).val();
		$("#proyecto_"+ id).show();
	});
});
</script>';
			$data['campos'] = $this->session->flashdata('campos') ? $this->session->flashdata('campos') : array('tipo' => false, 'rango' => false, 'fecha' => false);
			$data["mensaje"].= $this->session->flashdata('mensaje') ? $this->session->flashdata('mensaje') : '';
			$proyectos = $this->reportes_ejecutivos_model->obtener_proyectos();

			foreach ($proyectos as $p)
			{
				$data['proyectos'][$p['idproyecto']] = array(
					'nombre' => $p['nombre_proyecto'],
					'params' => array(
						'1' => $this->reportes_ejecutivos_model->obtener_parametros_todos(1, $p['idproyecto']),
						'2' => $this->reportes_ejecutivos_model->obtener_parametros_todos(2, $p['idproyecto']),
					),
				);
			}

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
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function guardar()
	{
		if($this->session->userdata('id')):
			$session_data = $this->session->userdata();
			$data = $this->input->get();
// echo '<pre>';var_dump($data);die;
			if (empty($data['tipo'])):
				$this->session->set_flashdata('mensaje', "<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
						<br>El campo \"N&uacute;mero\" no puede quedar vac&iacute;o<br>
					</div>");
				$this->session->set_flashdata('campos', $data);
			else:
				$data['rangos']['de'] = array_filter($data['rangos']['de'], function($var) {
					return ($var==="0" || $var);
				});
				$data['rangos']['a'] = array_filter($data['rangos']['a'], function($var) {
					return ($var==="0" || $var);
				});

				$guardar = array();

				foreach ($data['rangos']['de'] as $key => $value):
					$guardar[] = array(
						'tipo' => $data['tipo'],
						'rango_inicial' => $data['rangos']['de'][$key],
						'rango_final' => $data['rangos']['a'][$key],
						'periodo' => $data['periodo'],
						'iduser' => $session_data['id'],
						'idproyecto' => $data['idproyecto']
					);
				endforeach;

				$this->reportes_ejecutivos_model->agregar_parametros($guardar);
				$this->session->set_flashdata('mensaje', "<div class='alert alert-success'>
								<h5>El par&aacute;metro fu&eacute; creado con &eacute;xito</h5></div>");
			endif;

			redirect('doc/reportes_ejecutivos/index', 'refresh');
		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function modificar($idparametro_reporte = 0)
	{
		if($this->session->userdata('id')):
			if (!$idparametro_reporte):
				$this->session->set_flashdata('mensaje', "<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
						<br>El ID del par&aacute;metro est&aacute; vac&iacute;o o no es v&aacute;lido<br>
					</div>");
				return redirect('doc/reportes_ejecutivos/index', 'refresh');
			else:
				if ($this->input->get('tipo')):
					$data = $this->input->get();
					$this->reportes_ejecutivos_model->modificar_parametro($idparametro_reporte, $data);
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
					$data['idparametro_reporte'] = $idparametro_reporte;
					$data['periodos'] = array(
						'1' => 'D&iacute;as',
						'2' => 'Semanas',
						'3' => 'Meses'
					);
					$data['tipos'] = array(
						'1' => 'Fecha vencida',
						'2' => 'Fecha por vencer'
					);
					$parametro = $this->reportes_ejecutivos_model->obtener_parametro($idparametro_reporte);
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

	public function eliminar($idparametro_reporte = 0)
	{
		if($this->session->userdata('id')):
			if (!$idparametro_reporte):
				$this->session->set_flashdata('mensaje', "<div class='alert alert-danger'>
						<h5>Ocurri&oacute; un error durante la acci&oacute;n</h5>
						<br>El ID del par&aacute;metro est&aacute; vac&iacute;o o no es v&aacute;lido<br>
					</div>");
				return redirect('doc/reportes_ejecutivos/index', 'refresh');
			else:
				$this->reportes_ejecutivos_model->eliminar_parametro($idparametro_reporte);
				$this->session->set_flashdata('mensaje', "<div class='alert alert-success'>
							<h5>El par&aacute;metro fu&eacute; eliminado con &eacute;xito</h5></div>");
				return redirect('doc/reportes_ejecutivos/index', 'refresh');
			endif;

		else:
			redirect('login/index', 'refresh');
		endif;
	}
 }