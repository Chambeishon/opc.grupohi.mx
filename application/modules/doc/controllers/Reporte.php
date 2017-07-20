<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reporte extends MX_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('reportes_ejecutivos_model');
		$this->load->model('reporte_model');
		$this->load->library('template');
		$this->load->library('menu');
	}

	public function index()
	{
		if($this->session->userdata('id')):
			$session_data = $this->session->userdata();
			$data['js'] = '';
			$data['usuario'] = $session_data['username'];
			$data['iduser'] = $session_data['id'];
			$data['idperfil'] = $session_data['idperfil'];
			$data["menu"] = $this->menu->crea_menu($data['idperfil']);

			$fecha = new DateTime();
			$hoy = $fecha->format('Y-m-d');
			$proyectos = $this->reporte_model->obtener_proyectos();
			$categorias = array();

			foreach ($proyectos as $p)
			{
				$max_rango = $this->reporte_model->obtener_max_rango($p['idproyecto']);

				$total = $this->reporte_model->obtener_cantidad_categorias("AND fecha BETWEEN CONVERT(datetime,'". $max_rango[0]['fecha_ini'] ."')
AND CONVERT(datetime,'". $max_rango[1]['fecha_fin'] ."')", $p['idproyecto']);

				if (empty($total))
					continue;

				$proyectos_data[$p['idproyecto']] = array(
					'nombre' => $p['nombre_proyecto'],
					'data' => array()
				);

				if (!empty($total))
					foreach ($total as $tot)
					{
						$proyectos_data[$p['idproyecto']]['data'][$tot['cat_categoria']] = array(
							'nombre' => $tot['cat_categoria'],
							'idproyecto' => $p['idproyecto'],
							'idcontrato' => $p['idcontrato'],
							'idcat_categoria' => $tot['idcat_categoria'],
							'y' => $tot['y'],
							'customLegend' => '<h6>'. $tot['cat_categoria'] .'</h6><br>Total de actividades: <b>'. $tot['y'] .'</b><br> <b>0</b> vencidas',
							'enableLegend' => false
						);

						if (!in_array($tot['cat_categoria']))
							$categorias[] = $tot['cat_categoria'];
					}

				$vencidas = $this->reporte_model->obtener_cantidad_categorias("AND fecha BETWEEN CONVERT(datetime,'". $max_rango[0]['fecha_ini'] ."')
AND CONVERT(datetime,'". $max_rango[0]['fecha_fin'] ."')", $p['idproyecto']);

				if (!empty($vencidas))
					foreach ($vencidas as $v)
					{
						$proyectos_data[$p['idproyecto']]['data'][$v['cat_categoria']]['vencidas'] = $v['y'];
						$proyectos_data[$p['idproyecto']]['data'][$v['cat_categoria']]['customLegend'] = str_replace('<b>0</b> vencidas', '<br>Actividades vencidas:'. $v['y'], $proyectos_data[$p['idproyecto']]['data'][$v['cat_categoria']]['customLegend']);
					}

				$proyectos_data[$p['idproyecto']]['data'] = array_values($proyectos_data[$p['idproyecto']]['data']);
			}

			$grafica = '';
			foreach ($proyectos_data as $k => $p)
				$grafica .= '
	generar_grafica($("#proyecto_'. $k .'"), "'. $p['nombre'] .'", '. json_encode($categorias) .', '. json_encode($p['data']) .', function(){
		generar_subgraficas(this.point.options);
	});
';

			$data['proyectos'] = $proyectos_data;
			$data['mes'] = $mes;
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts-more.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/scrollreveal.min.js').'"></script>';
			$data['js'] .= '
<script>
$(function() {
'. $grafica;

			$data['js'] .= '

	var loading = $("<i />", {
		"class": "fa fa-spinner fa-spin fa-2x",
		text: "",
	});

	function generar_subgraficas(options)
	{
		var sub_vencidas =  $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencidas").html(loading),
			sub_vencer = $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencer").html(loading);
			$(".mostrar_lista").empty().html(" ");

		$("html, body").animate({
			scrollTop: sub_vencidas.offset().top
		}, 1000);

		$.ajax({
			url: "'. base_url('doc/reporte/sub_categorias') .'",
			data: options,
			cache: false,
			type: "GET",
			success: function(data) {
				data = JSON.parse(data),
				legend = [];
				$.each(data, function(key, item){
					mostrar = (key == 1 ? sub_vencidas : sub_vencer);
					titulo = (key == 1 ? " vencidas" : " por vencer");
					mostrar.html();
					generar_grafica(mostrar, options.nombre + titulo, false, item, function(){
						generar_lista(this.point.options);
					});
				});
			},
			error: function(xhr) {}
		});
	}
	function generar_lista(options)
	{
		divTable = $("#proyecto_lista_"+ options.idproyecto).empty();
		tabla = $(".lista").clone();

		// $("html, body").animate({
		// 	scrollTop: divTable.offset().top
		// }, 1000);

		$.ajax({
			url: "'. base_url('doc/reporte/sub_lista') .'",
			data: options,
			cache: false,
			type: "GET",
			success: function(data) {
				console.log(data);
				tabla.removeClass("hidden");
				tabla.children("tbody").prepend(data);
				divTable.html(tabla);
			},
			error: function(xhr) {}
		});
	}

	function generar_grafica(jObject, titulo, legend, data, callback)
	{
		jObject.highcharts({
			chart: {
				type: "pie"
			},
			title: {
				text: titulo
			},
			credits: {
				enabled: false
			},
			plotOptions: {
				pie: {
					allowPointSelect: true,
					cursor: "pointer",
					showInLegend: true,
					dataLabels: {
						enabled: true,
						formatter: function(){
							return this.point.customLegend ? this.point.customLegend : this.point.nombre +":"+ this.point.y;
						}
					}
				}
			},
			tooltip: {
				formatter: function() {
					return (this.point.customLegend ? this.point.customLegend : "Número de actividades para <b>" + this.point.nombre + "</b> es <b>" + this.y + "</b>");
				}
			},
			legend: {
				align: "left",
				layout: "vertical",
				verticalAlign: "top",
				x: 0,
				y: 20,
				enabled: legend,
				labelFormatter: function() {
					return this.options.nombre;
				}
			},
			xAxis: {
				categories: legend,
			},
			series: [{
				data: data,
				type: "pie",
				point:{
					events:{
						click: function (event) {
							callback.call(event);
						}
					}
				}
			}]
		});
	}
	window.sr = ScrollReveal();
	sr.reveal(document.querySelectorAll(".box"));

});
</script>';

			$this->template->load('template','reporte',$data);

		else:
			redirect('login/index', 'refresh');
		endif;
	}

	public function sub_categorias()
	{
		$idproyecto = $this->input->get('idproyecto');
		$idcat_categoria = $this->input->get('idcat_categoria');
		$parametros = $this->reportes_ejecutivos_model->obtener_parametros_todos(0, $idproyecto);
		$fecha = new DateTime();
		$hoy = $fecha->format('Y-m-d');
		$periodos = array(
			'3' => 'month',
			'2' => 'week',
			'1' => 'day'
		);
		$data = array();

		foreach ($parametros as $k => $p)
		{
			// '1' => 'Fecha vencida',
			// '2' => 'Fecha por vencer'
			if ($p['tipo'] == 1)
			{
				$rango_vencidas = $this->reporte_model->obtener_rango_negativo($p['idreporte_ejecutivo'], $p['tipo']);

				$vencidas = $this->reporte_model->obtener_cantidad_categorias("AND fecha BETWEEN CONVERT(datetime,'". $rango_vencidas[0]['fecha_fin'] ."')
AND CONVERT(datetime,'". $rango_vencidas[0]['fecha_ini'] ."') AND c.idcat_categoria = ". $idcat_categoria, $idproyecto);

				if (!empty($vencidas))
					$data[$p['tipo']][] = array(
						'customLegend' =>  "Actividades vencidas: " . $vencidas[0]['y'] .'<br> Fecha: '. $rango_vencidas[0]['fecha_fin'] .' al '. $rango_vencidas[0]['fecha_ini'],
						'idproyecto' => $idproyecto,
						'idcat_categoria' => $idcat_categoria,
						'y' => $vencidas[0]['y'],
						'fecha_ini' => $rango_vencidas[0]['fecha_ini'],
						'fecha_fin' => $rango_vencidas[0]['fecha_fin'],
						'tipo' => $p['tipo'],
					);
			}

			elseif ($p['tipo'] == 2)
			{
				$rango_vencer = $this->reporte_model->obtener_rango($p['idreporte_ejecutivo'], $p['tipo']);

				$vencer = $this->reporte_model->obtener_cantidad_categorias("AND fecha BETWEEN CONVERT(datetime,'". $rango_vencer[0]['fecha_ini'] ."')
AND CONVERT(datetime,'". $rango_vencer[0]['fecha_fin'] ."') AND c.idcat_categoria = ". $idcat_categoria, $idproyecto);

				if (!empty($vencer))
					$data[$p['tipo']][] = array(
						'customLegend' => "Actividades por vencer: ". $vencer[0]['y'] .'<br>Fecha: '. $rango_vencer[0]['fecha_ini'] .' al '. $rango_vencer[0]['fecha_fin'],
						'idproyecto' => $idproyecto,
						'idcat_categoria' => $idcat_categoria,
						'y' => $vencer[0]['y'],
						'fecha_ini' => $rango_vencer[0]['fecha_ini'],
						'fecha_fin' => $rango_vencer[0]['fecha_fin'],
						'tipo' => $p['tipo'],
					);
			}

		}
		echo json_encode($data);
	}

	public function sub_lista()
	{
		$data =  $this->input->get();
// echo '<pre>';var_dump($data);
// 		$string = $data['tipo'] == 1 ? "AND fecha BETWEEN CONVERT(datetime,'". $data['fecha_fin'] ."')
// AND CONVERT(datetime,'". $data['fecha_ini'] ."')" : "AND fecha BETWEEN CONVERT(datetime,'". $data['fecha_ini'] ."')
// AND CONVERT(datetime,'". $data['fecha_fin'] ."')";

		$contrato = $this->reporte_model->obtener_contrato($data['fecha_ini'], $data['fecha_fin'], $data['idproyecto'], $data['idcat_categoria']);
echo '<pre>';var_dump($lista);
		foreach ($lista as $l)
		 {
			echo '<tr>
<th scope="row">'. $l['idactividad'] .'</th>
<td>'. $l['nombre_actividad'] .'</td>
<td>'. $l['descripcion_actividad'] .' </td>
<td>'. $l['fecha'] .'</td>
<td>'. $l['estado_actividad']  .'</td>
</tr>';
		}
	}
}
