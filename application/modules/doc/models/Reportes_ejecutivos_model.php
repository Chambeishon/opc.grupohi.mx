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

	public function obtener_parametro($idreporte_ejecutivo)
	{
		$this->db->select("*");
		$this->db->from("doc_reportes_ejecutivos");
		$this->db->where("idreporte_ejecutivo", $idreporte_ejecutivo);

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
		$this->db->insert("doc_reportes_ejecutivos", $data);
		$last_id = $this->db->insert_id();
		return $last_id;
	}

	public function modificar_parametro($idreporte_ejecutivo, $data)
	{
		$this->db->where("idreporte_ejecutivo", $idreporte_ejecutivo);
		$this->db->update("doc_reportes_ejecutivos", $data);
		return true;
	}

	public function eliminar_parametro($idreporte_ejecutivo)
	{
		$this->db->delete("doc_reportes_ejecutivos", array('idreporte_ejecutivo' => $idreporte_ejecutivo));
	}
}
