<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reporte_prioridad_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function obtener_datos($idproyecto = 0, $string = '')
	{
		$query = $this->db->query("
SELECT idactividad, nombre_actividad, descripcion_actividad, documento_contractual, empresa_responsable, persona_responsable, referencia_documental, detalle_referencia,
		observacion, idprogramacion, fecha, idestado_actividad, idcat_categoria, cat_categoria, idproyecto, idcat_subcategoria, nombre_proyecto, estado_actividad, numero_contrato, idcontrato, idprioridad, prioridad_nombre, prioridad_clave
FROM vw_doc_programacion
WHERE idproyecto = ". $idproyecto ."
AND idestado_actividad NOT IN(1,6)
AND ". $string);

		return $query ? $query->result_array() : array();
	}

	public function obtener_rango($idreporte_prioridad = 0, $tipo = 0)
	{
		$query = $this->db->query("
SELECT rango_inicial, rango_final, periodo AS periodo_raw,
CONVERT (char(10),(case periodo
when 1 then DATEADD(day, rango_inicial, GETDATE())
when 2 then DATEADD(WEEK, rango_inicial, GETDATE())
when 3 then DATEADD(MONTH, rango_inicial, GETDATE())
end), 126) as fecha_ini,
CONVERT (char(10),(case periodo
when 1 then DATEADD(day, rango_final, GETDATE())
when 2 then DATEADD(WEEK, rango_final, GETDATE())
when 3 then DATEADD(MONTH, rango_final, GETDATE())
end), 126) as fecha_fin
FROM doc_reportes_prioridad
WHERE  idreporte_prioridad = ". $idreporte_prioridad ."
AND  tipo = ". $tipo ."
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_rango_negativo($idreporte_prioridad = 0, $tipo = 0)
	{
		$query = $this->db->query("
SELECT rango_inicial, rango_final, periodo AS periodo_raw,
CONVERT (char(10),(case periodo
when 1 then DATEADD(day, -rango_inicial, GETDATE())
when 2 then DATEADD(WEEK, -rango_inicial, GETDATE())
when 3 then DATEADD(MONTH, -rango_inicial, GETDATE())
end), 126) as fecha_ini,
CONVERT (char(10),(case periodo
when 1 then DATEADD(day, -rango_final, GETDATE())
when 2 then DATEADD(WEEK, -rango_final, GETDATE())
when 3 then DATEADD(MONTH, -rango_final, GETDATE())
end), 126) as fecha_fin
FROM doc_reportes_prioridad
WHERE idreporte_prioridad = ". $idreporte_prioridad ."
AND  tipo = ". $tipo ."
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_max_rango($idproyecto = 0)
	{
		$query = $this->db->query("
SELECT CONVERT (char(10),max(
case  periodo
when 3 then DATEADD (month , rango_final , getdate() )
when 2 then DATEADD (week , rango_final , getdate() )
when 1 then DATEADD (day , rango_final , getdate() )
end ), 126) as fecha_fin,
CONVERT (char(10),min(
case  periodo
when 3 then DATEADD (month , -rango_final , getdate() )
when 2 then DATEADD (week , -rango_final , getdate() )
when 1 then DATEADD (day , -rango_final , getdate() )
end ), 126) as fecha_ini
FROM doc_reportes_prioridad
where idproyecto = ". $idproyecto ."
group by tipo
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_parametros_todos($tipo = 0, $idproyecto = 0)
	{
		$this->db->select("*");
		$this->db->from("doc_reportes_prioridad");

		if ($tipo)
			$this->db->where("tipo", $tipo);

		if ($idproyecto)
			$this->db->where("idproyecto", $idproyecto);

		$query = $this->db->get();

		$return = $query->result_array();
		usort($return, function($a, $b) {
			return $a['rango_inicial'] - $b['rango_inicial'];
		});

		return $return;
	}
}
