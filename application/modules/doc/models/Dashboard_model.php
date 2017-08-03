<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Dashboard_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	public function desplegar_proyectos($iduser)
	{
		$query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,1,0,0,0,0,'0','0'");
        return $query->result_array();
	}

	public function desplegar_proyectos_fecha($iduser,$fecha_inicio,$fecha_fin)
	{
		$query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,11,0,0,0,0,'$fecha_inicio','$fecha_fin'");
        return $query->result_array();
	}

	public function desplegar_contratos($iduser,$idproyecto,$fecha_inicio,$fecha_fin)
	{
		$query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,2,$idproyecto,0,0,0,'$fecha_inicio','$fecha_fin'");
        return $query->result_array();
	}

	public function desplegar_categorias($iduser,$idcontrato,$fecha_inicio,$fecha_fin)
	{
		$query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,3,0,$idcontrato,0,0,'$fecha_inicio','$fecha_fin'");
        return $query->result_array();
	}

	public function desplegar_subcategorias($iduser,$idcontrato,$idcategoria,$fecha_inicio,$fecha_fin){
            $query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,4,0,$idcontrato,$idcategoria,0,'$fecha_inicio','$fecha_fin'");
            return $query->result_array();
	}

    public function desplegar_actividades($iduser,$idcontrato,$idcategoria,$idsubcategoria,$inicio,$fin){
        //echo 'EXEC sp_doc_desplegar_dashboard ',$iduser,', ','5',', ','0',', ',$idcontrato,', ',$idcategoria,', ',$idsubcategoria,', ',$inicio,', ',$fin;
        $query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,5,0,$idcontrato,$idcategoria,$idsubcategoria,'$inicio','$fin'");
        return $query->result_array();
    }

	public function desplegar_historicos($iduser,$idcontrato,$idcategoria,$idsubcategoria){
		$query = $this->db->query("EXEC sp_doc_desplegar_dashboard $iduser,6,0,$idcontrato,$idcategoria,$idsubcategoria,'0','0'");
        return $query->result_array();
	}

	public function desplegar_programacion($idprogramacion){
		$this->db->select("*");
		$this->db->from("vw_doc_programacion");
		$this->db->where("idprogramacion",$idprogramacion);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function desplegar_areas($idprogramacion){
		$query = $this->db->query("SELECT a.idarea_involucrada,nombre_area_involucrada from doc_area_involucrada a
LEFT JOIN doc_actividad_area_involucrada aa ON aa.idarea_involucrada = a.idarea_involucrada
WHERE idactividad = (SELECT idactividad FROM vw_doc_programacion WHERE idprogramacion=".$idprogramacion." GROUP BY idactividad)");
        return $query->result_array();
	}

	public function desplegar_areas_usuarios($idarea)
	{
		$query = $this->db->query("SELECT * FROM vw_doc_area_involucrada_nivel_usuario WHERE idarea_involucrada=".$idarea."");
        return $query->result_array();
	}

	public function cambiar_estado($idprogramacion,$estado,$usuario)
	{
		$query = $this->db->query("EXEC sp_doc_cambiar_estado_actividad_programada $idprogramacion,$estado,'$usuario'");
        return $query->result_array();
	}

	public function agregar_evidencia($idprogramacion,$file,$idestado,$usuario)
	{
		$query = $this->db->query("EXEC sp_doc_agregar_evidencia_documental $idprogramacion,'$file',$idestado,'$usuario'");
        return $query->result_array();
	}

	public function desplegar_evidencias($idprogramacion)
	{
		$this->db->select("*");
		$this->db->from("vw_doc_actividad_evidencia_documental");
		$this->db->where("idprogramacion",$idprogramacion);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function desplegar_programacion_actividad($idprogramacion)
	{
		$this->db->select("*");
		$this->db->from("vw_doc_programacion_actividad");
		$this->db->where("idprogramacion",$idprogramacion);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function eliminar_evidencia($iddocumento)
	{
		$query = $this->db->query("EXEC sp_doc_eliminar_evidencia_documental $iddocumento");
        return $query->result_array();
	}

	public function get_botones($idprogramacion)
	{
		$query = $this->db->query("SELECT * FROM vw_doc_programacion_actividad WHERE idprogramacion=".$idprogramacion."");
        return $query->result_array();
	}

	public function get_seguimiento($idprogramacion)
	{
		$this->db->select("p.fecha,p.hora,p.usuario_registra,e.estado_actividad");
		$this->db->from("vw_doc_actividad_programada_seguimiento AS p");
		$this->db->join("doc_cat_estado_actividad AS e", "p.idestado_actividad = e.idestado_actividad", "inner");
		$this->db->where("idprogramacion",$idprogramacion);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function get_numero_evidencia($idprogramacion)
	{
		$query = $this->db->query("SELECT * FROM vw_doc_numero_evidencias WHERE idprogramacion=".$idprogramacion);
        return $query->result_array();
	}

	function get_guarda_anotacion($idprogramacion,$hora,$fecha,$nota,$valoracion,$usuario)
	{
		$query=$this->db->query("EXEC sp_doc_guarda_anotacion '".$hora."','".$fecha."','".$nota."',".$valoracion.",'".$usuario."',".$idprogramacion);
		return $query->result_array();
	}

	public function desplegar_anotaciones($idprogramacion)
	{
		$this->db->select("*");
		$this->db->from("vw_doc_anotacion");
		$this->db->where("idprogramacion",$idprogramacion);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function get_bloquea_anotacion($id)
	{
		$query=$this->db->query("EXEC sp_doc_bloquea_anotacion ".$id);
		return $query->result_array();
	}

	public function get_eliminar_anotacion($id)
	{
		$query=$this->db->query("EXEC sp_doc_elimina_anotacion ".$id);
		return $query->result_array();
	}

	public function get_numero_anotacion($idprogramacion)
	{
		$query = $this->db->query("SELECT * FROM vw_doc_numero_anotacion WHERE idprogramacion=".$idprogramacion);
        return $query->result_array();
	}
}
