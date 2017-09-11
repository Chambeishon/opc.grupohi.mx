<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Actividad_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	public function desplegar_actividades($idcontrato,$idcategoria,$idsubcategoria)
	{
		$this->db->select("idactividad,nombre_actividad,descripcion_actividad,documento_contractual,persona_responsable,empresa_responsable,estado_programada,accion,modificar, prioridad_clave, idprioridad, prioridad_nombre");
		$this->db->from("vw_doc_actividad");
		$this->db->where("idcat_categoria",$idcategoria);
		$this->db->where("idcat_subcategoria",$idsubcategoria);
		$this->db->where("idcontrato",$idcontrato);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function desplegar_actividad($idactividad)
	{
		$this->db->select("idactividad,numero_contrato,nombre_proyecto,cat_categoria,cat_subcategoria,nombre_actividad,descripcion_actividad,documento_contractual,referencia_documental,empresa_responsable,persona_responsable,detalle_referencia,observacion, idprioridad");
		$this->db->from("vw_doc_actividad");
		$this->db->where("idactividad",$idactividad);
		$query = $this->db->get();
        return $query->result_array();
	}

	public function agregar_actividad($idcontrato,$idcategoria,$idsubcategoria,$nombre,$descripcion,$documento,$referencia,$empresa,$persona,$detalle,$observaciones,$areas,$usuario, $prioridad)
	{
		$query = $this->db->query("EXEC sp_doc_agregar_actividad $idcontrato,$idcategoria,$idsubcategoria,'$nombre','$descripcion','$documento','$referencia','$empresa','$persona','$detalle','$observaciones','$areas','$usuario', $prioridad");
        return $query->result_array();
	}

	public function agregar_actividad_area($idactividad,$idarea,$estado,$usuario)
	{
		$query = $this->db->query("EXEC sp_doc_actividad_area_involucrada $idactividad,$idarea,$estado,'$usuario'");
        return $query->result_array();
	}

	public function desplegar_actividad_area($idactividad)
	{
		$query = $this->db->query("EXEC sp_doc_desplegar_area_involucrada '$idactividad'");
        return $query->result_array();
	}

	public function modificar_actividad($idactividad,$idcontrato,$idcategoria,$idsubcategoria,$nombre,$descripcion,$documento,$referencia,$empresa,$persona,$detalle,$observaciones,$areas,$usuario, $prioridad)
	{
		$query = $this->db->query("EXEC sp_doc_modificar_actividad $idactividad,$idcontrato,$idcategoria,$idsubcategoria,'$nombre','$descripcion','$documento','$referencia','$empresa','$persona','$detalle','$observaciones','$areas','$usuario', $prioridad");
        return $query->result_array();
	}

	public function desplegar_usuarios_area($idarea)
	{
		$query = $this->db->query("SELECT usuario,idnivel FROM vw_doc_area_involucrada_nivel_usuario WHERE idarea_involucrada=".$idarea." ORDER BY idnivel");
        return $query->result_array();
	}

}
