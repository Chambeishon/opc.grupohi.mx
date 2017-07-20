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
SELECT        COUNT(a.idcat_categoria) AS y, a.idcat_categoria, c.cat_categoria, a.idcontrato
FROM            [opi].[dbo].[doc_programacion_actividad] AS pa LEFT OUTER JOIN
                         [opi].[dbo].[doc_actividad] AS a ON pa.idactividad = a.idactividad LEFT OUTER JOIN
                         [opi].[dbo].[doc_cat_categoria] AS c ON a.idcat_categoria = c.idcat_categoria LEFT OUTER JOIN
                         [opi].[dbo].[doc_contrato] AS co ON a.idcontrato = co.idcontrato LEFT OUTER JOIN
                         [opi].[dbo].[grl_proyecto] AS p ON co.idproyecto = p.idproyecto LEFT OUTER JOIN
                         [opi].[dbo].[doc_cat_estado_actividad] ON pa.idestado_actividad = [opi].[dbo].[doc_cat_estado_actividad].[idestado_actividad]
WHERE co.idproyecto = ". $idproyecto ."
". $fecha ." GROUP BY a.idcat_categoria, cat_categoria, a.idcontrato");

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
SELECT fecha_ini, fecha_fin
FROM vw_doc_rangos_reportes_ejecutivos
WHERE  idreporte_ejecutivo = ". $idreporte_ejecutivo ."
AND tipo = ". $tipo ."
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_rango_negativo($idreporte_ejecutivo = 0, $tipo = 0)
	{
		$query = $this->db->query("
SELECT fecha_ini, fecha_fin
FROM vw_doc_rangos_reportes_ejecutivos_negativos
WHERE  idreporte_ejecutivo = ". $idreporte_ejecutivo ."
AND tipo = ". $tipo ."
");

		return $query ? $query->result_array() : array();
	}

	public function obtener_lista($fecha_inicio, $fecha_fin, $idproyecto = 0, $idcat_categoria = 0)
	{
		$query = $this->db->query("EXEC sp_doc_desplegar_dashboard 134,2,$idproyecto,0,0,0,'$fecha_inicio','$fecha_fin'");
        return $query->result_array();

		return $query ? $query->result_array() : array();
	}
}
