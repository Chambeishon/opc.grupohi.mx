<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Reporte_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function obtener_categorias()
	{
		$query = $this->db->query("SELECT * FROM doc_cat_categoria");
		return $query ? $query->result_array() : array();
	}

	public function obtener_proyectos()
	{
		$query = $this->db->query("SELECT * FROM grl_proyecto WHERE idestado=1");
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
}