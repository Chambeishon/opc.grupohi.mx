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
				{
					$totales[$p['idproyecto']] = array(
						'categorias' => array(),
					);
				}
            }

			foreach ($vencidas as $k => $a)
			{
				foreach ($a as $v)
				{
					$totales[$k]['categorias'][$v['idcat_categoria']]['vencidas'][$v['idprogramacion']] = $v;
				}
			}

			foreach ($vencer as $k => $a)
			{
				foreach ($a as $v)
				{
					$totales[$k]['categorias'][$v['idcat_categoria']]['vencer'][$v['idprogramacion']] = $v;
				}
			}

            $grafica = '';

            foreach ($totales as $k => $t)
            {
				$show = array();
				foreach ($t['categorias'] as $cat_id => $cat)
				{
					$total = count($cat['vencidas']) + count($cat['vencer']);

					foreach ($cat['vencidas'] as $v)
					{
						$t['nombre'] = $v['nombre_proyecto'];
						$cat_categoria[$cat_id] = $v['cat_categoria'];
					}

					$show[$cat_id] = array(
						'nombre' => $cat_categoria[$cat_id],
						'y' => $total,
						'customLegend' => 'Actividades vencidas: <b>'. count($cat['vencidas'])  . '</b><br>Actividades por vencer: <b>'. count($cat['vencer']) .'</b>',
						'customTooltip' => $cat_categoria[$cat_id] .'<br>Número total de actividades: <b>'. $total .'</b>',
						'idproyecto' => $k,
						'idcat_categoria' => $cat_id,
					);
				}

                $grafica .= '
generar_grafica($("#proyecto_'. $k .'"), "'. $t['nombre'] .'", [], '. json_encode(array_values($show)) .', function(){
	this.point.options.color = this.point.color;
	generar_subgraficas(this.point.options);
});
';
            }

            $data['proyectos'] = $totales;
            $data['js'] .= '<script src="'.base_url('assets/js/highcharts.js').'"></script>';
            $data['js'] .= '<script src="'.base_url('assets/js/highcharts-more.js').'"></script>';
			$data['js'] .= '<script src="'.base_url('assets/js/highcharts-no-data-to-display.js').'"></script>';
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
        $coloresVencer = array('#CFF09E', '#A8DBA8', '#79BD9A', '#3B8686', '#0B486B', '#88C425', '#519548', '#607848', '#789048');
		$coloresVencidas = array('#DFCCCC', '#FFD3D3', '#FFA4A4', '#D17878', '#965959', '#D83018', '#F07848', '#FDAB64', '#FD9960');

		// '1' => 'Fecha vencida',
		// '2' => 'Fecha por vencer'
		foreach ($parametrosVencidas as $p)
		{
			$rango_vencidas = $this->reporte_model->obtener_rango_negativo($p["idreporte_ejecutivo"], $p['tipo']);

			$vencidas[$p["idreporte_ejecutivo"]]['actividades']  = $this->reporte_model->obtener_datos($p['idproyecto'], ""
			. "fecha BETWEEN '". $rango_vencidas[0]['fecha_fin'] ."' AND '". $rango_vencidas[0]['fecha_ini'] ."' AND idcat_categoria = ". $datos['idcat_categoria']);

			$vencidas[$p["idreporte_ejecutivo"]]['rango'] = $rango_vencidas[0];
		}

		foreach ($parametrosVencer as $p)
		{
			$rango_vencer = $this->reporte_model->obtener_rango($p['idreporte_ejecutivo'], $p['tipo']);

			$vencer[$p["idreporte_ejecutivo"]]['actividades'] =  $this->reporte_model->obtener_datos($idproyecto, ""
			. "fecha BETWEEN '". $rango_vencer[0]['fecha_ini'] ."' AND '". $rango_vencer[0]['fecha_fin'] ."' AND idcat_categoria = ". $datos['idcat_categoria']);
			$vencer[$p["idreporte_ejecutivo"]]['rango'] = $rango_vencer[0];
		}

		foreach ($vencidas as $reporteId => $v)
		{
			if(empty($v['actividades']))
			{
				continue;
			}

			$rando = mt_rand(0, count($coloresVencer) - 1);
			$total = count($v['actividades']);

			$send['vencidas'][] = array(
				'nombre' => $datos['nombre'],
				'color' => $coloresVencidas[$rando],
				'y' => $total,
				'customLegend' => $datos['nombre'] .'<br><b>'. $total .'</b> '. ($v['rango']['rango_inicial'] .' a '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']],
				'customTooltip' => $datos['nombre'] ." Vencidas <b>". $total ."</b> periodo:<br>". $v['rango']['fecha_fin'] ." al ". $v['rango']['fecha_ini'],
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
			{
				continue;
			}

			$rando = mt_rand(0, count($coloresVencer) - 1);
			$total = count($v['actividades']);

			$send['vencer'][] = array(
				'nombre' => $datos['nombre'],
				'color' => $coloresVencer[$rando],
				'y' => $total,
				'customLegend' => $datos['nombre'] .'<br><b>'. $total .'</b>'. ($v['rango']['rango_inicial'] .' a '. $v['rango']['rango_final']) .' '. $periodos[$v['rango']['periodo_raw']],
				'customTooltip' => $datos['nombre'] ." Vencidas <b>". $total ."</b> periodo:<br>". $v['rango']['fecha_ini'] ." al ". $v['rango']['fecha_fin'],
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

		foreach ($lista as $l)
		{
			echo '
<tr>
<th scope="row"><a class="abrir-programacion" idprogramacion="'. $l['idprogramacion'] .'">P-'. $l['idprogramacion'] .'</a></th>
<td>'. $l['nombre_actividad'] .'</td>
<td>'. $l['descripcion_actividad'] .' </td>
<td>'. $l['fecha'] .'</td>
<td>'. $l['estado_actividad']  .'</td>
</tr>
';
		}
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
