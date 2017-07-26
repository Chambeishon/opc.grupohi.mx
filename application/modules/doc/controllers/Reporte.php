<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporte extends MX_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('reportes_ejecutivos_model');
		$this->load->model('reporte_model');
		$this->load->model('programacion_model');
		$this->load->model('dashboard_model');
		$this->load->library('template');
		$this->load->library('menu');
	}

	public function index()
	{
		if($this->session->userdata('id'))
		{
			$session_data = $this->session->userdata();
			$data['js'] = '';
			$data['usuario'] = $session_data['username'];
			$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);

			$proyectosRaw = $this->dashboard_model->desplegar_proyectos($data['iduser']);
			$datos = $categorias = array();

			foreach ($proyectosRaw as $p)
			{
				$max_rango = $this->reporte_model->obtener_max_rango($p['idproyecto']);

				$proyectos = $this->dashboard_model->desplegar_proyectos_fecha($data['iduser'], $max_rango[0]['fecha_fin'], $max_rango[1]['fecha_fin']);
			}

			foreach ($proyectos as $p)
			{
				$max_rango = $this->reporte_model->obtener_max_rango($p['idproyecto']);

				if (!$max_rango[0]['fecha_fin'])
					continue;

				$vencer = $this->dashboard_model->desplegar_contratos($data['iduser'], $p['idproyecto'], $max_rango[1]['fecha_ini'], $max_rango[1]['fecha_fin']);
				$vencidas = $this->dashboard_model->desplegar_contratos($data['iduser'], $p['idproyecto'], $max_rango[0]['fecha_fin'], $max_rango[0]['fecha_ini']);
				$datos_vencidas[$p['idproyecto']] = $this->_generarDatos($p['idproyecto'], $data['iduser'], $vencidas, $max_rango[0]['fecha_fin'], $max_rango[0]['fecha_ini']);
				$datos_vencer[$p['idproyecto']] = $this->_generarDatos($p['idproyecto'], $data['iduser'], $vencer, $max_rango[1]['fecha_ini'], $max_rango[1]['fecha_fin']);

				$datos_vencidas[$p['idproyecto']]['nombre'] = $datos_vencer[$p['idproyecto']]['nombre'] = $p['nombre_proyecto'];
			}

			$grafica = '';
			foreach ($datos_vencer as $k => $p)
			{
				foreach ($p['categorias'] as $c)
				{
					$sub = array();
					foreach ($c['subcategorias'] as $sub_c)
						$sub[] = $sub_c['idcat_subcategoria'];

					$d[] = array(
						'nombre' => $c['cat_categoria'],
						'y' => $c['y'],
						'customLegend' => 'Actividades vencidas: <b>'. $datos_vencidas[$k]['categorias'][$c['idcat_categoria']]['y']  . '</b><br>Actividades por vencer: <b>'. $c['y'] .'</b>',
						'idproyecto' => $k,
						'idcat_categoria' => $c['idcat_categoria'],
						'subcategorias' => $sub,
					);
				}

				$grafica .= '

	generar_grafica($("#proyecto_'. $k .'"), "'. $p['nombre'] .'", [], '. json_encode($d) .', function(){
		generar_subgraficas(this.point.options);
	});
';
			}

			$data['proyectos'] = $datos_vencer;
			$data['mes'] = $mes;
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts-more.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/scrollreveal.min.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/doc-reportes-ejecutivos.js').'"></script>';
			$data['js'] .= '
<script>
$(function() {
'. $grafica . '
});
</script>';

			$this->template->load('template','reporte',$data);

		}
		else{
			redirect('login/index', 'refresh');
		}
	}

	public function sub_categorias()
	{
		$data = array();
		$session_data = $this->session->userdata();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$idproyecto = $this->input->get('idproyecto');
		$idcat_categoria = $this->input->get('idcat_categoria');
		$subcategorias = $this->input->get('subcategorias');

		$parametros = $this->reportes_ejecutivos_model->obtener_parametros_todos(0, $idproyecto);

		$periodos = array(
			'3' => 'month',
			'2' => 'week',
			'1' => 'day'
		);
		$send = $vencidas = $vencer = array();
		$colores = array('#514F78', '#42A07B', '#9B5E4A', '#72727F', '#1F949A', '#82914E', '#86777F', '#42A07B', '#FDD089', '#FF7F79', '#A0446E', '#251535', '#F3E796', '#95C471', '#35729E', '#251735', '#DDDF0D', '#55BF3B', '#DF5353', '#7798BF', '#aaeeee', '#ff0066', '#eeaaee',
		'#55BF3B', '#DF5353', '#7798BF', '#aaeeee');

		foreach ($parametros as $k => $p)
		{
			// '1' => 'Fecha vencida',
			// '2' => 'Fecha por vencer'
			if ($p['tipo'] == 1)
			{
				$rango_vencidas = $this->reporte_model->obtener_rango_negativo($p["idreporte_ejecutivo"], $p['tipo']);

				$total = $this->dashboard_model->desplegar_contratos($data['iduser'], $idproyecto, $rango_vencidas[0]['fecha_fin'], $rango_vencidas[0]['fecha_ini']);

				$vencidas[$p["idreporte_ejecutivo"]] =  $this->_generarDatos($idproyecto, $data['iduser'], $total, $rango_vencidas[0]['fecha_fin'], $rango_vencidas[0]['fecha_ini']);
			}

			elseif ($p['tipo'] == 2)
			{
				$rango_vencer = $this->reporte_model->obtener_rango($p['idreporte_ejecutivo'], $p['tipo']);

				$total = $this->dashboard_model->desplegar_contratos($data['iduser'], $idproyecto, $rango_vencer[0]['fecha_ini'], $rango_vencer[0]['fecha_fin']);

				$vencer[$p["idreporte_ejecutivo"]] =  $this->_generarDatos($idproyecto, $data['iduser'], $total, $rango_vencer[0]['fecha_ini'], $rango_vencer[0]['fecha_fin']);

			}

		}

		foreach ($vencidas as $rangoid => $v)
		{
			$rango = $this->reporte_model->obtener_rango_negativo($rangoid, 1);
			$rando = mt_rand(0, count($colores) - 1);

			$v['categorias'][$idcat_categoria]['customLegend'] = "Vencidas <b>". $v['categorias'][$idcat_categoria]['y'] ."</b><br>del ". $rango[0]['fecha_fin'] ." al ". $rango[0]['fecha_ini'];
			$v['categorias'][$idcat_categoria]['nombre'] = $v['categorias'][$idcat_categoria]['cat_categoria'];
			$v['categorias'][$idcat_categoria]['color'] = $colores[$rando];
			$v['categorias'][$idcat_categoria]['fecha_ini'] = $rango[0]['fecha_fin'];
			$v['categorias'][$idcat_categoria]['fecha_fin'] = $rango[0]['fecha_ini'];
			$v['categorias'][$idcat_categoria]['idcat_categoria'] = $idcat_categoria;
			$v['categorias'][$idcat_categoria]['idproyecto'] = $idproyecto;
			$send['vencidas'][] = $v['categorias'][$idcat_categoria];
			unset($colores[$rando]);
		}

		foreach ($vencer as $rangoid => $v)
		{
			$rango = $this->reporte_model->obtener_rango($rangoid, 2);
			$rando = mt_rand(0, count($colores) - 1);

			$v['categorias'][$idcat_categoria]['customLegend'] = "Por vencer <b>". $v['categorias'][$idcat_categoria]['y'] ."</b><br>del <br>". $rango[0]['fecha_ini'] ." al ". $rango[0]['fecha_fin'];
			$v['categorias'][$idcat_categoria]['color'] = $colores[$rando];
			$v['categorias'][$idcat_categoria]['nombre'] = $v['categorias'][$idcat_categoria]['cat_categoria'];
			$v['categorias'][$idcat_categoria]['fecha_ini'] = $rango[0]['fecha_ini'];
			$v['categorias'][$idcat_categoria]['fecha_fin'] = $rango[0]['fecha_fin'];
			$v['categorias'][$idcat_categoria]['idcat_categoria'] = $idcat_categoria;
			$v['categorias'][$idcat_categoria]['idproyecto'] = $idproyecto;
			$send['vencer'][] = $v['categorias'][$idcat_categoria];
			unset($colores[$rando]);
		}

		echo json_encode($send);
	}

	public function sub_lista()
	{
		$data = array();
		$session_data = $this->session->userdata();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$datos = $this->input->post();
		$lista = array();

		foreach ($datos['subcategorias'] as $d)
					if (!empty($d['actividades']))
						foreach ($d['actividades'] as $a)
							echo '<tr style="color:'. $a['color'] .'">
<th scope="row"><a class="abrir-programacion" idprogramacion="'. $a['idprogramacion'] .'">P-'. $a['idprogramacion'] .'</a></th>
<td>'. $a['nombre_actividad'] .'</td>
<td>'. $a['descripcion_actividad'] .' </td>
<td>'. $a['fecha'] .'</td>
<td>'. $a['estado_actividad']  .'</td>
</tr>';
	}

	protected function _generarDatos($idproyecto = 0, $iduser = 0, $total = array(), $fecha_ini = '', $fecha_fin = '')
	{
		$datos = array();

		foreach ($total as $t)
		{
			foreach ($this->dashboard_model->desplegar_categorias($iduser, $t['idcontrato'], $fecha_ini, $fecha_fin) as $c)
			{
				$datos['categorias'][$c['idcat_categoria']] = $c;
				$datos['categorias'][$c['idcat_categoria']]['y'] = 0;

				$sub = $this->dashboard_model->desplegar_subcategorias($iduser, $c['idcontrato'], $c['idcat_categoria'], $fecha_ini, $fecha_fin);

				foreach ($sub as $s)
					$datos['categorias'][$c['idcat_categoria']]['subcategorias'][$s['idcat_subcategoria']] = $s;

					foreach ($s as $ss)
					{
						$a = $this->dashboard_model->desplegar_actividades($iduser, $t['idcontrato'], $c['idcat_categoria'] , $s['idcat_subcategoria'] , $fecha_ini, $fecha_fin);

						$datos['categorias'][$c['idcat_categoria']]['subcategorias'][$s['idcat_subcategoria']]['actividades'] = !empty($a) ? $a : array();
						$datos['categorias'][$c['idcat_categoria']]['subcategorias'][$s['idcat_subcategoria']]['y']  = count($a);
						$datos['categorias'][$c['idcat_categoria']]['y']  = count($a);
					}
			}
		}
		return $datos;
	}
}
