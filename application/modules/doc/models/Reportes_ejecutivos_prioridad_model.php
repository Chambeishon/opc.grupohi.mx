<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportes_ejecutivos_prioridad_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function obtener_parametro($idprioridad = 0)
	{
		$this->db->select("*");
		$this->db->from("doc_reportes_prioridad");

		if ($idprioridad)
			$this->db->where("idprioridad", $idprioridad);

		$query = $this->db->get();
		return $query->result_array();
	}

	public function agregar_parametro($data)
	{
		$this->db->insert("doc_reportes_prioridad", $data);
		$last_id = $this->db->insert_id();
		return $last_id;
	}

	public function modificar_parametro($idreporte_prioridad, $data)
	{
		$this->db->where("idreporte_prioridad", $idprioridad);
		$this->db->update("doc_reportes_prioridad", $data);
		return true;
	}

	public function eliminar_parametro($idreporte_prioridad)
	{
		$this->db->delete("doc_reportes_prioridad", array('idreporte_prioridad' => $idreporte_prioridad));
	}
}
