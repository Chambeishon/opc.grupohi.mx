<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporte_Prioridad extends MX_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('reporte_prioridad_model');
		$this->load->model('prioridad_model');
		$this->load->model('programacion_model');
		$this->load->model('dashboard_model');
		$this->load->library('template');
		$this->load->library('menu');
	}

	public function index()
	{
		if(!$this->session->userdata('id'))
			return redirect('login/index', 'refresh');

		$session_data = $this->session->userdata();
		$data['js'] = '';
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$data['idperfil'] = $session_data['idperfil'];
		$data["menu"] = $this->menu->crea_menu($data['idperfil']);
		$hoy = date("Y-m-d");
		$totales = array();
		$proyectos = array();
		$limite = 5; // 5 default
		$prioridadesRaw = $this->prioridad_model->obtener_prioridades();
		$grafica = $proyectoPag = '';
		$show = array();

		foreach ($prioridadesRaw as $p)
			$prioridades[$p['idprioridad']] = $p;

		$proyectosRaw = $this->dashboard_model->desplegar_proyectos($data['iduser']);

		foreach ($proyectosRaw as $p)
		{
			$max_rango = $this->reporte_prioridad_model->obtener_max_rango($p['idproyecto']);

			$proyectos = $this->dashboard_model->desplegar_proyectos_fecha($data['iduser'], $max_rango[1]['fecha_ini'], $max_rango[0]['fecha_fin']);
		}

		foreach ($proyectos as $p)
		{
			$max_rango = $this->reporte_prioridad_model->obtener_max_rango($p['idproyecto']);

			if(empty($max_rango[0]))
				continue;

			$vencer[$p['idproyecto']] = $this->reporte_prioridad_model->obtener_datos($p['idproyecto'], "fecha BETWEEN '". $hoy ."' AND '". $max_rango[1]['fecha_fin'] ."'");

			$vencidas[$p['idproyecto']] = $this->reporte_prioridad_model->obtener_datos($p['idproyecto'], "fecha BETWEEN '". $max_rango[0]['fecha_ini'] ."' AND '". $hoy ."'");

			if (empty($totales[$p['idproyecto']]))
				$totales[$p['idproyecto']] = array();

			$nombre_proyecto[$p['idproyecto']] = $p['nombre_proyecto'];
		}

		foreach ($vencidas as $k => $a)
			foreach ($a as $v)
			{
				$totales[$k][$v['idprioridad']]['vencidas'][$v['idprogramacion']] = $v;
				$nombre_proyecto[$k] = $v["nombre_proyecto"];
			}

		foreach ($vencer as $k => $a)
			foreach ($a as $v)
			{
				$totales[$k][$v['idprioridad']]['vencer'][$v['idprogramacion']] = $v;
				$nombre_proyecto[$k] = $v["nombre_proyecto"];
			}

		foreach ($totales as $k => $prioridad)
		{
			foreach ($prioridad as $idprioridad => $tipo)
			{
				$totalVencidas = count($tipo['vencidas']);
				$totalVencer = count($tipo['vencer']);

				$show[$k][$idprioridad] = array(
					'nombre' => $prioridades[$idprioridad]['nombre'],
					'idproyecto' => $k,
					'y' => $totalVencer + $totalVencidas,
					'customLegend' => 'Actividades vencidas: <strong>'. $totalVencidas  . '</strong><br>Actividades por vencer: <strong>'. $totalVencer .'</strong>',
					'customTooltip' => $prioridades[$idprioridad]['nombre'] .'<br>Número total de actividades: <strong>'. ($totalVencer + $totalVencidas) .'</strong>',
					'idprioridad' => $idprioridad
				);
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
			$data['js'] .= '<script src="'.base_url('assets/js/doc-reportes-ejecutivos-prioridad.js').'"></script>';
			$data['js'] .= '
<script>
$(function() {
'. $grafica . '
'. $proyectoPag .'
});
</script>';

			$this->template->load('template','reporte_prioridad',$data);
	}

	public function sub_categorias()
	{
		$data = array();
		$session_data = $this->session->userdata();
		$data['usuario'] = $session_data['username'];
		$data['iduser'] = $session_data['id'];
		$idproyecto = $this->input->get('idproyecto');
		$datos = $this->input->get();
		$parametrosVencidas = $this->reporte_prioridad_model->obtener_parametros_todos(1, $idproyecto);
		$parametrosVencer = $this->reporte_prioridad_model->obtener_parametros_todos(2, $idproyecto);
		$hoy = date("Y-m-d");
		$prioridadesRaw = $this->prioridad_model->obtener_prioridades();

		foreach ($prioridadesRaw as $p)
			$prioridades[$p['idprioridad']] = $p;

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
			$rango_vencidas = $this->reporte_prioridad_model->obtener_rango_negativo($p["idreporte_prioridad"], $p['tipo']);

			if ($datos['idprioridad'] != $p['idprioridad'])
				continue;

			// Evita traslapar actividades de un mismo día
			if ($k != $lastVencidas && $countVencidas > 1)
				$rango_vencidas[0]['fecha_fin'] = date('Y-m-d', strtotime($rango_vencidas[0]['fecha_fin'] .' + 1 days'));

			$id = $p['idreporte_prioridad'] .'-'. $p['tipo'];
			$vencidas[$id]['actividades']  = $this->reporte_prioridad_model->obtener_datos($p['idproyecto'], ""
			. "fecha BETWEEN '". $rango_vencidas[0]['fecha_fin'] ."' AND '". $rango_vencidas[0]['fecha_ini'] ."' AND idprioridad = ". $datos['idprioridad']);

			$vencidas[$id]['rango'] = $rango_vencidas[0];
			$vencidas[$id]['prioridad'] = $p;
			$vencidasContador++;
		}

		$countVencer = count($parametrosVencer);
		$contador = 0;
		foreach ($parametrosVencer as $k => $p)
		{
			$rango_vencer = $this->reporte_prioridad_model->obtener_rango($p['idreporte_prioridad'], $p['tipo']);

			if ($datos['idprioridad'] != $p['idprioridad'])
				continue;

			// Evita traslapar actividades de un mismo día
			if ($contador && $countVencer > 1)
				$rango_vencer[0]['fecha_ini'] = date('Y-m-d', strtotime($rango_vencer[0]['fecha_ini'] .' + 1 days'));

			$id = $p['idreporte_prioridad'] .'-'. $p['tipo'];
			$vencer[$id]['actividades'] =  $this->reporte_prioridad_model->obtener_datos($idproyecto, ""
			. "fecha BETWEEN '". $rango_vencer[0]['fecha_ini'] ."' AND '". $rango_vencer[0]['fecha_fin'] ."' AND idprioridad = ". $datos['idprioridad']);
			$vencer[$id]['rango'] = $rango_vencer[0];
			$vencer[$id]['prioridad'] = $p;
			$contador++;
		}

		foreach ($vencidas as $reporteId => $v)
		{
			$totalAct = 0;
			$totalAct = count($v['actividades']);
			$stringActividades = $totalAct > 1 ? 'actividades' : 'actividad';

			$send['vencidas'][] = array(
				'nombre' => $datos['nombre'],
				'color' => array_pop($coloresVencidas),
				'y' => count($v['actividades']),
				'customLegend' => '<strong>'. $totalAct .'</strong> '. (count($v['actividades']) > 1 ? 'actividades' : 'actividad') .'<br>(de '. ($v['rango']['rango_inicial'] .' a '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']] .')',
				'customTooltip' => $datos['nombre'] ."<br>Vencidas <strong>". count($v['actividades']) ."</strong> periodo:<br>". $v['rango']['fecha_fin'] ." al ". $v['rango']['fecha_ini'],
				'idproyecto' => $datos['idproyecto'],
				'idcat_categoria' => $datos['idcat_categoria'],
				'idprioridad' => $datos['idprioridad'],
				'rango' => array(
					$v['rango']['fecha_ini'],
					$v['rango']['fecha_fin'],
				),
			);
		}

		foreach ($vencer as $reporteId => $v)
		{
			if(empty($v['actividades']) || $v['prioridad']['idprioridad'] != $datos['idprioridad'])
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
				'idprioridad' => $datos['idprioridad'],
				'rango' => array(
					$v['rango']['fecha_ini'],
					$v['rango']['fecha_fin'],
				),
			);
		}

		$send['vencer'] = array_values($send['vencer']);
		$send['vencidas'] = array_values($send['vencidas']);

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
		$lista = $this->reporte_prioridad_model->obtener_datos($datos['idproyecto'], ""
			. "fecha BETWEEN '". $datos['rango'][0] ."' AND '". $datos['rango'][1] ."' AND idprioridad = ". $datos['idprioridad']);

		$json = array();
		$total = count($lista);

		foreach ($lista as $l)
		{
			$json[$l['idcat_categoria'] .'-'. $l['idcontrato']]['data'][] = array(
				'link' => '<a class=\'abrir-programacion\' idprogramacion=\''. $l['idprogramacion'] .'\'>P-'. $l['idprogramacion'] .'</a>',
				'nombre' => $l['nombre_actividad'],
				'descripcion' => $l['descripcion_actividad'],
				'fecha' => $l['fecha'],
				'estado' => $l['estado_actividad'],
			);

		}

		foreach ($lista as $l)
		{
			$json[$l['idcat_categoria'] .'-'. $l['idcontrato']]['header'] = '<strong>'. $l['numero_contrato'] .' - '. $l['cat_categoria'] .' - PRIORIDAD '. strtoupper($datos['nombre']) .'</strong> - TOTAL DE ACTIVIDADES: '. count($json[$l['idcat_categoria'] .'-'. $l['idcontrato']]['data']);

		}

		echo json_encode($json);
	}
}
