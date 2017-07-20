<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Proyecto_model extends CI_Model
{
    
    public function __construct()
    {
        parent::__construct();   
    }
    
	 
    public function desplega()
    {
		$query = $this->db->query("SELECT * FROM vw_grl_proyecto ORDER BY nombre_proyecto");
        return $query->result();
    }
	
	
	public function agrega($proyecto,$clave,$direccion)
	{
		$query = $this->db->query("EXEC sp_grl_agregar_proyecto '$proyecto','$clave','$direccion'");
		return $query->result();
	}
	
	public function estado($id,$estado)
	{
		$data = array(
               'idestado' => $estado
            );

		$this->db->where('idproyecto', $id);
		$this->db->update('grl_proyecto', $data);
		return $this->db->affected_rows();
	}
	
	public function cambia($id,$proyecto,$clave,$direccion)
	{
		$data = array(
               'nombre_proyecto' => $proyecto,
			   'clave' => $clave,
			   'direccion' => $direccion
            );

		$this->db->where('idproyecto', $id);
		$this->db->update('grl_proyecto', $data);
		return $this->db->affected_rows();
	}
	
    
	
}
/*
*end modules/login/models/index_model.php
*/