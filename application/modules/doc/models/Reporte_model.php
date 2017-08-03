<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reporte_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function obtener_proyectos()
	{
		$query = $this->db->query("SELECT idproyecto, nombre_proyecto FROM grl_proyecto");
		return $query ? $query->result_array() : array();
	}

	public function obtener_categorias()
	{
		$query = $this->db->query("SELECT idcat_categoria, cat_categoria FROM doc_cat_Categoria");
		return $query ? $query->result_array() : array();
	}

	public function obtener_parametros()
	{
		$query = $this->db->query("SELECT * FROM [opi].[dbo].[doc_reportes_ejecutivos]");
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

  FROM doc_reportes_ejecutivos
  where idproyecto = ". $idproyecto ."
group by tipo
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_cantidad_categorias($fecha = '', $idproyecto = 0)
	{
		$query = $this->db->query("
SELECT        COUNT(a.idcat_categoria) AS y, a.idcat_categoria, c.cat_categoria
FROM            [opi].[dbo].[doc_programacion_actividad] AS pa LEFT OUTER JOIN
                         [opi].[dbo].[doc_actividad] AS a ON pa.idactividad = a.idactividad LEFT OUTER JOIN
                         [opi].[dbo].[doc_cat_categoria] AS c ON a.idcat_categoria = c.idcat_categoria LEFT OUTER JOIN
                         [opi].[dbo].[doc_contrato] AS co ON a.idcontrato = co.idcontrato LEFT OUTER JOIN
                         [opi].[dbo].[grl_proyecto] AS p ON co.idproyecto = p.idproyecto LEFT OUTER JOIN
                         [opi].[dbo].[doc_cat_estado_actividad] ON pa.idestado_actividad = [opi].[dbo].[doc_cat_estado_actividad].[idestado_actividad]
WHERE co.idproyecto = ". $idproyecto ."
AND pa.idestado_actividad IN (2,3,4,5)
". $fecha ." GROUP BY a.idcat_categoria, cat_categoria");

		return $query ? $query->result_array() : array();
	}

	public function obtener_datos($idproyecto = 0, $string = '')
	{
		$query = $this->db->query("
SELECT idactividad, nombre_actividad, descripcion_actividad, documento_contractual, empresa_responsable, persona_responsable, referencia_documental, detalle_referencia,
		observacion, idprogramacion, fecha, idestado_actividad, idcat_categoria, cat_categoria, idproyecto, idcat_subcategoria, nombre_proyecto, estado_actividad
FROM vw_doc_programacion
WHERE idproyecto = ". $idproyecto ."
AND idestado_actividad NOT IN(1,6)
AND ". $string);
		return $query ? $query->result_array() : array();
	}

	public function obtener_reporte($idproyecto, $fecha_inicio, $fecha_fin)
	{
		$query = $this->db->query("SELECT * FROM vw_doc_programacion WHERE idproyecto=". $idproyecto);
		return $query ? $query->result_array() : array();
	}

	public function obtener_numero_categorias($idproyecto, $idcat_categoria)
	{
		$query = $this->db->query("SELECT COUNT(idestado_actividad) AS y, idestado_actividad, estado_actividad AS nombre FROM vw_doc_programacion WHERE idproyecto=". $idproyecto ."AND idcat_categoria =" . $idcat_categoria  ."GROUP BY idestado_actividad, estado_actividad");
		return $query ? $query->result_array() : array();
	}

	public function obtener_rango($idreporte_ejecutivo = 0, $tipo = 0)
	{
		$query = $this->db->query("
SELECT fecha_ini, fecha_fin, rango_inicial, rango_final, periodo_raw
FROM vw_doc_rangos_reportes_ejecutivos
WHERE  idreporte_ejecutivo = ". $idreporte_ejecutivo ."
AND tipo = ". $tipo ."
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_rango_negativo($idreporte_ejecutivo = 0, $tipo = 0)
	{
		$query = $this->db->query("
SELECT fecha_ini, fecha_fin, rango_inicial, rango_final, periodo_raw
FROM vw_doc_rangos_reportes_ejecutivos_negativos
WHERE  idreporte_ejecutivo = ". $idreporte_ejecutivo ."
AND tipo = ". $tipo ."
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_lista($fecha_ini, $fecha_fin, $idproyecto = 0, $idcat_categoria = 0, $tipo = 0)
	{
		$string = $tipo == 1 ? "BETWEEN CONVERT(datetime, '". $fecha_fin ."') AND CONVERT(datetime,'". $fecha_ini ."')" : "BETWEEN CONVERT(datetime, '". $fecha_ini ."') AND CONVERT(datetime,'". $fecha_fin ."')";

		$query = $this->db->query("
SELECT co.idproyecto, p.nombre_proyecto, co.idcontrato, co.numero_contrato, a.idcat_categoria, c.cat_categoria, a.idcat_subcategoria, s.cat_subcategoria, a.idactividad,
	a.nombre_actividad, a.descripcion_actividad, a.documento_contractual, a.empresa_responsable, a.persona_responsable, a.referencia_documental,
	a.detalle_referencia, a.observacion, pa.idprogramacion, pa.fecha, pa.idestado_actividad, co.idcat_estado,
	dbo.doc_cat_estado_actividad.descripcion_dashboard AS estado_actividad, co.descripcion_contrato, co.fecha_inicio, co.fecha_fin, p.clave, CONVERT(char(10),
	pa.timestamp, 126) AS timestamp
FROM dbo.doc_programacion_actividad AS pa
LEFT OUTER JOIN dbo.doc_actividad AS a ON pa.idactividad = a.idactividad
LEFT OUTER JOIN dbo.doc_cat_categoria AS c ON a.idcat_categoria = c.idcat_categoria
LEFT OUTER JOIN dbo.doc_cat_subcategoria AS s ON a.idcat_subcategoria = s.idcat_subcategoria
LEFT OUTER JOIN dbo.doc_contrato AS co ON a.idcontrato = co.idcontrato
LEFT OUTER JOIN dbo.grl_proyecto AS p ON co.idproyecto = p.idproyecto
LEFT OUTER JOIN dbo.doc_cat_estado_actividad ON pa.idestado_actividad = dbo.doc_cat_estado_actividad.idestado_actividad
WHERE co.idproyecto = ". $idproyecto ."
AND pa.idestado_actividad NOT IN (1,6)
AND a.idcat_categoria = ". $idcat_categoria ."
AND pa.fecha ". $string);

		return $query ? $query->result_array() : array();
	}
}
