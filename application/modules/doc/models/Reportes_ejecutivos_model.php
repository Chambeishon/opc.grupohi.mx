<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes_ejecutivos_model extends CI_Model
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

	public function obtener_parametros_todos($tipo = 0, $idproyecto = 0)
	{
		$this->db->select("*");
		$this->db->from("doc_reportes_ejecutivos");

		if ($tipo)
			$this->db->where("tipo",$tipo);

		if ($idproyecto)
			$this->db->where("idproyecto",$idproyecto);

		$query = $this->db->get();
	
		return $query->result_array();
	}

	public function obtener_parametro($idparametro_reporte)
	{
		$this->db->select("*");
		$this->db->from("doc_reportes_ejecutivos");
		$this->db->where("idparametro_reporte", $idparametro_reporte);

		$query = $this->db->get(); 	
		return $query->result_array();
	}
 
	public function agregar_parametro($tipo, $rango, $fecha)
	{
		$query = $this->db->query("EXEC sp_doc_agregar_reporte_parametro $tipo,$rango,$fecha");
		return $query->result_array();
	}

	public function agregar_parametros($data)
	{
		$this->db->insert_batch("doc_reportes_ejecutivos", $data); 
	}

	public function modificar_parametro($idparametro_reporte, $data)
	{
		$this->db->where("idparametro_reporte", $idparametro_reporte);
		$this->db->update("doc_reportes_ejecutivos", $data);
		return true;
	}

	public function eliminar_parametro($idparametro_reporte)
	{
		$this->db->delete("doc_reportes_ejecutivos", array('idparametro_reporte' => $idparametro_reporte)); 
	}
}