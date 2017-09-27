<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Prioridad_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function obtener_prioridades($idprioridad = 0)
	{
		$this->db->select("nombre, clave, idprioridad, color");
		$this->db->from("doc_actividades_prioridad");

		if ($idprioridad)
			$this->db->where('idprioridad', $idprioridad);

		$query = $this->db->get();

		return $query->result_array();
	}

	public function agregar_prioridad($data)
	{
		$this->db->insert("doc_actividades_prioridad", $data);
		$last_id = $this->db->insert_id();
		return $last_id;
	}

	public function modificar_prioridad($data, $idprioridad)
	{
		$this->db->where('idprioridad', $idprioridad);
		$this->db->update("doc_actividades_prioridad", $data);
	}

	public function eliminar_prioridad($idprioridad)
	{
		$this->db->delete("doc_actividades_prioridad", array('idprioridad' => $idprioridad));
	}

	public function revisar_uso_prioridad($idprioridad)
	{
		$query = $this->db->query("
SELECT idprioridad
FROM vw_doc_programacion
WHERE idprioridad = ". $idprioridad);
		return $query ? $query->result_array() : array();
	}

	// Boolean, regresa true si la clave está repetida.
	public function revisarClave($clave = '')
	{
		// El campo no puede estar vacio
		if (empty($clave))
			return true;

		$query = $this->db->query("
SELECT idprioridad
FROM doc_actividades_prioridad
WHERE clave LIKE '%". $clave ."%'");

		return $query ? $query->result_array() : array();
	}
}
