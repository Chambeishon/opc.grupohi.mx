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
			$hoy = date("Y-m-d");
			$totales = array();
			$limite = 5; // 5 default

			$proyectosRaw = $this->dashboard_model->desplegar_proyectos($data['iduser']);

			foreach ($proyectosRaw as $p)
			{
				$max_rango = $this->reporte_model->obtener_max_rango($p['idproyecto']);

				$proyectos = $this->dashboard_model->desplegar_proyectos_fecha($data['iduser'], $max_rango[1]['fecha_ini'], $max_rango[0]['fecha_fin']);
			}

			foreach ($proyectos as $p)
			{
				$max_rango = $this->reporte_model->obtener_max_rango($p['idproyecto']);

				if(empty($max_rango[0]))
					continue;

				$vencer[$p['idproyecto']] = $this->reporte_model->obtener_datos($p['idproyecto'], "fecha BETWEEN '". $hoy ."' AND '". $max_rango[1]['fecha_fin'] ."'");
				$vencidas[$p['idproyecto']] = $this->reporte_model->obtener_datos($p['idproyecto'], "fecha BETWEEN '". $max_rango[0]['fecha_ini'] ."' AND '". $hoy ."'");

				if (empty($totales[$p['idproyecto']]))
					$totales[$p['idproyecto']] = array();
			}

			foreach ($vencidas as $k => $a)
				foreach ($a as $v)
					$totales[$k][$v['idcontrato']][$v['idcat_categoria']]['vencidas'][$v['idprogramacion']] = $v;

			foreach ($vencer as $k => $a)
				foreach ($a as $v)
					$totales[$k][$v['idcontrato']][$v['idcat_categoria']]['vencer'][$v['idprogramacion']] = $v;

			$grafica = $proyectoPag = '';
			$show = array();
			$n = array();

			foreach ($totales as $k  => $kata)
			{
				$n[$k] = array();
				foreach ($kata as $c => $contrato)
				{
					$sup = array();
					$n[$k][$c] = array();

					foreach ($contrato as $cat => $categoria)
					{
						if (!isset($show))
							$show = array();

						foreach ($categoria as $v => $a)
						{
							if (!isset($tVencidas))
							{
								$tVencidas[$k] = array();
								$tVencer[$k] = array();
							}

							foreach ($a as $id => $act)
							{
								if (!isset($tVencer[$k][$cat .'-'. $c]))
									$tVencer[$k][$cat .'-'. $c] = 0;

								if (!isset($tVencidas[$k][$cat .'-'. $c]))
									$tVencidas[$k][$cat .'-'. $c] = 0;

								if ($v == 'vencer')
									$tVencer[$k][$cat .'-'. $c]++;

								if ($v == 'vencidas')
									$tVencidas[$k][$cat .'-'. $c]++;

								$show[$k][$act['idcat_categoria'] .'-'. $act['idcontrato']] = array(
									'nombre' => $act['numero_contrato'] .' - '. $act['cat_categoria'],
									'nombre_contrato' => $act['numero_contrato'],
									'nombre_categoria' => $act['cat_categoria'],
									'idproyecto' => $k,
									'idcat_categoria' => $act['idcat_categoria'],
									'idcontrato' => $act['idcontrato'],
								);
								$nombre_proyecto[$k] = $act['nombre_proyecto'];
							}

							foreach ($a as $id => $act)
							{
								$show[$k][$act['idcat_categoria'] .'-'. $act['idcontrato']] += array(
									'y' => $tVencidas[$k][$cat .'-'. $c] + $tVencer[$k][$cat .'-'. $c],
									'customLegend' => 'Actividades vencidas: <strong>'. $tVencidas[$k][$cat .'-'. $c]  . '</strong><br>Actividades por vencer: <strong>'. $tVencer[$k][$cat .'-'. $c] .'</strong>',
									'customTooltip' => $act['cat_categoria'] .'<br>Número total de actividades: <strong>'. ($tVencidas[$k][$cat .'-'. $c] + $tVencer[$k][$cat .'-'. $c]) .'</strong>',
								);
							}
						}
					}

					$sup['actividades'] = $show;
					$n[$k][$c][] = $sup;

				}

				if (empty($nombre_proyecto[$k]))
				{
					$nodata = $this->reporte_model->obtener_nombre_proyecto($k);
					$nombre_proyecto[$k] = $nodata[0]["nombre_proyecto"];
				}

				$grafica .= '
noData = "No hay tareas pendientes ni vencidas en el periodo especificado";
generar_grafica($("#proyecto_'. $k .'"), {useHTML:true, text:"'. $nombre_proyecto[$k] .'", style:{ "color": "#333333", "fontSize": "20px", "text-decoration": "underline"}}, [], '. json_encode(array_values($show[$k])) .', function(){
	this.point.options.color = this.point.color;
	generar_subgraficas(this.point.options);
}, noData);
';
			}


			$items= count($totales);
			if ($items > $limite)
			{
				$paginas = ceil($items / $limite);
				$proyectoPag .= '
$(".qwerty").hide();
var cuerpo = $("#cuerpo");
for(i = 0; i < '. $paginas .'; i++){
	li = $("<li/>", {
		html: "<a href=\'#\'>" + (i + 1) + \'</a>\',
		\'class\': \'ppag_\' + i + \' proyectoPaginacion\' + (i === 0 ? " active" : "")
	});
	li.data("pag", i);
	li.data("limit", '. $limite .');
	li.data("total", '. $items .');
	li.insertBefore(cuerpo.find("li.proyectoNext"));
	$(".proyectoPrevious").data("limit", '. $limite .');
	$(".proyectoNext").data("limit", '. $limite .');
}
$(".qwerty").slice(0, '. $limite .').show();
';
			}

			else
				$proyectoPag .= '$(".proPag").hide();';

			$data['proyectos'] = $totales;
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts-more.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts-no-data-to-display.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/doc-reportes-ejecutivos.js').'"></script>';
			$data['js'] .= '
<script>
$(function() {
'. $grafica . '
'. $proyectoPag .'
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
		$datos = $this->input->get();
		$parametrosVencidas = $this->reportes_ejecutivos_model->obtener_parametros_todos(1, $idproyecto);
		$parametrosVencer = $this->reportes_ejecutivos_model->obtener_parametros_todos(2, $idproyecto);
		$hoy = date("Y-m-d");

		 $periodos = array(
			'3' => 'meses',
			'2' => 'semanas',
			'1' => 'días'
		);
		$send = $vencidas = $vencer = array();
		$coloresVencer = array('#789048', '#A8DBA8', '#3B8686', '#0B486B', '#519548', '#79BD9A', '#607848', '#E3EDC4', '#CFF09E', '#88C425', '#0A7B74');
		$coloresVencidas = array('#DFCCCC', '#FD9960', '#FFD3D3', '#D17878', '#965959', '#D83018', '#F07848', '#FDAB64', '#FFA4A4', '#9B0F2B', '#FE4E76');

		// '1' => 'Fecha vencida',
		// '2' => 'Fecha por vencer'
		end($parametrosVencidas);
		$lastVencidas = key($parametrosVencidas);
		$countVencidas = count($parametrosVencidas);
		foreach ($parametrosVencidas as $k => $p)
		{
			$rango_vencidas = $this->reporte_model->obtener_rango_negativo($p["idreporte_ejecutivo"], $p['tipo']);

			// Evita traslapar actividades de un mismo día
			if ($k != $lastVencidas && $countVencidas > 1)
				$rango_vencidas[0]['fecha_fin'] = date('Y-m-d', strtotime($rango_vencidas[0]['fecha_fin'] .' + 1 days'));

			$vencidas[$p["idreporte_ejecutivo"]]['actividades']  = $this->reporte_model->obtener_datos($p['idproyecto'], ""
			. "fecha BETWEEN '". $rango_vencidas[0]['fecha_fin'] ."' AND '". $rango_vencidas[0]['fecha_ini'] ."' AND idcat_categoria = ". $datos['idcat_categoria']);

			$vencidas[$p["idreporte_ejecutivo"]]['rango'] = $rango_vencidas[0];
			$vencidasContador++;
		}

		$countVencer = count($parametrosVencer);
		$contador = 0;
		foreach ($parametrosVencer as $k => $p)
		{
			$rango_vencer = $this->reporte_model->obtener_rango($p['idreporte_ejecutivo'], $p['tipo']);

			// Evita traslapar actividades de un mismo día
			if ($contador && $countVencer > 1)
				$rango_vencer[0]['fecha_ini'] = date('Y-m-d', strtotime($rango_vencer[0]['fecha_ini'] .' + 1 days'));

			$vencer[$p["idreporte_ejecutivo"]]['actividades'] =  $this->reporte_model->obtener_datos($idproyecto, ""
			. "fecha BETWEEN '". $rango_vencer[0]['fecha_ini'] ."' AND '". $rango_vencer[0]['fecha_fin'] ."' AND idcat_categoria = ". $datos['idcat_categoria']);
			$vencer[$p["idreporte_ejecutivo"]]['rango'] = $rango_vencer[0];
			$contador++;
		}

		foreach ($vencidas as $reporteId => $v)
		{
			if(empty($v['actividades']))
				continue;

			$rando = mt_rand(0, count($coloresVencer) - 1);
			$total = count($v['actividades']);
			$stringActividades = $total > 1 ? 'actividades' : 'actividad';

			$send['vencidas'][] = array(
				'nombre' => $datos['nombre'],
				'color' => array_pop($coloresVencidas),
				'y' => $total,
				'customLegend' => '<strong>'. $total .'</strong> '. $stringActividades .'<br>(de '. ($v['rango']['rango_inicial'] .' a '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']] .')',
				'customTooltip' => $datos['nombre'] ."<br>Vencidas <strong>". $total ."</strong> periodo:<br>". $v['rango']['fecha_fin'] ." al ". $v['rango']['fecha_ini'],
				'tableHeader' => '<strong>'. $datos['nombre'] .'</strong> - VENCIDAS (DE '. ($v['rango']['rango_inicial'] .' A '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']] .') TOTAL DE ACTIVIDADES: '. $total,
				'idproyecto' => $datos['idproyecto'],
				'idcat_categoria' => $datos['idcat_categoria'],
				'rango' => array(
					$v['rango']['fecha_ini'],
					$v['rango']['fecha_fin'],
				),
			);
		}

		foreach ($vencer as $reporteId => $v)
		{
			if(empty($v['actividades']))
				continue;

			$rando = mt_rand(0, count($coloresVencer) - 1);
			$total = count($v['actividades']);
			$stringActividades = $total > 1 ? 'actividades' : 'actividad';

			$send['vencer'][] = array(
				'nombre' => $datos['nombre'],
				'color' => array_pop($coloresVencer),
				'y' => $total,
				'customLegend' => '<strong>'. $total .'</strong> '. $stringActividades .'<br>(de '. ($v['rango']['rango_inicial'] .' a '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']] .')',
				'customTooltip' => $datos['nombre'] ."<br>Por vencer <strong>". $total ."</strong> periodo:<br>". $v['rango']['fecha_ini'] ." al ". $v['rango']['fecha_fin'],
				'tableHeader' => '<strong>'. $datos['nombre'] .'</strong> - POR VENCER (DE '. ($v['rango']['rango_inicial'] .' A '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']] .') TOTAL DE ACTIVIDADES: '. $total,
				'idproyecto' => $datos['idproyecto'],
				'idcat_categoria' => $datos['idcat_categoria'],
				'rango' => array(
					$v['rango']['fecha_ini'],
					$v['rango']['fecha_fin'],
				),
			);
		}

		$send['vencer'] = array_filter($send['vencer']);
		$send['vencidas'] = array_filter($send['vencidas']);
		echo json_encode($send);
	}

	public function sub_lista()
	{
		$data = array();
		$session_data = $this->session->userdata();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$datos = $this->input->post();
		usort($datos['rango'],"strcmp");
		$lista = $this->reporte_model->obtener_datos($datos['idproyecto'], ""
			. "fecha BETWEEN '". $datos['rango'][0] ."' AND '". $datos['rango'][1] ."' AND idcat_categoria = ". $datos['idcat_categoria']);

		$json = array();

		foreach ($lista as $l)
			$json[] = array(
				'link' => '<a class=\'abrir-programacion\' idprogramacion=\''. $l['idprogramacion'] .'\'>P-'. $l['idprogramacion'] .'</a>',
				'nombre' => $l['nombre_actividad'],
				'descripcion' => $l['descripcion_actividad'],
				'fecha' => $l['fecha'],
				'estado' => $l['estado_actividad']
			);

		echo json_encode($json);
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
				{
					$datos['categorias'][$c['idcat_categoria']]['subcategorias'][$s['idcat_subcategoria']] = array();

					foreach ($s as $ss)
					{
						$a = $this->dashboard_model->desplegar_actividades($iduser, $t['idcontrato'], $c['idcat_categoria'] , $s['idcat_subcategoria'] , $fecha_ini, $fecha_fin);
						$ac = array();
						$actividades = array();
						$total = 0;

						foreach ($a as $r)
						{
							if(!empty($actividades[$r['idprogramacion']]))
								continue;

							$ac[$r['idprogramacion']] = $r;
							$actividades[$r['idprogramacion']] = true;
						}

						$total = count($ac);
						$datos['categorias'][$c['idcat_categoria']]['subcategorias'][$s['idcat_subcategoria']]['actividades'] = !empty($ac) ? $ac : array();
						$datos['categorias'][$c['idcat_categoria']]['y'] = $datos[$t['idcontrato']]['categorias'][$c['idcat_categoria']]['y'] + $total;
					}
				}
			}
		}

		return $datos;
	}
}
