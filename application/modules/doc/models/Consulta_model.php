<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Consulta_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	public function desplegar_categorias($idcontrato)
	{
		$query = $this->db->query("SELECT idcat_categoria,cat_categoria FROM vw_doc_programacion WHERE idcontrato IN (".$idcontrato.") GROUP BY idcat_categoria,cat_categoria ORDER BY cat_categoria ASC");
        return $query->result_array();
	}

	public function desplegar_subcategorias($idcontrato)
	{
		$query = $this->db->query("SELECT idcat_subcategoria,cat_subcategoria FROM vw_doc_programacion WHERE idcontrato IN (".$idcontrato.") GROUP BY idcat_subcategoria,cat_subcategoria ORDER BY cat_subcategoria ASC");
        return $query->result_array();
	}

	public function desplegar_estados($idcontrato)
	{
		$query = $this->db->query("SELECT idestado_actividad,estado_actividad FROM vw_doc_programacion WHERE idcontrato IN (".$idcontrato.") GROUP BY idestado_actividad,estado_actividad ORDER BY idestado_actividad ASC");
        return $query->result_array();
	}

	public function desplegar_actividades($consulta,$iduser)
	{
		$query = $this->db->query("SELECT DISTINCT('<a idprogramacion='''+CAST(p.idprogramacion AS varchar(4000))+''' class=''abrir-programacion'' >P-'+CAST(p.idprogramacion AS varchar(4000))+'</a>')AS idprogramacion,
p.clave+'-'+p.numero_contrato AS contrato, p.cat_categoria, p.cat_subcategoria, p.nombre_actividad, p.descripcion_actividad, p.fecha, p.estado_actividad, CONVERT( VARCHAR(MAX),pri.clave) AS prioridad_clave FROM vw_doc_programacion p inner join grl_proyecto pr on p.idproyecto=pr.idproyecto inner join grl_plaza pl on p.idproyecto=pl.idproyecto inner join grl_usuario_plaza up on pl.idplaza=up.idplaza inner join doc_actividades_prioridad AS pri on p.idprioridad=pri.idprioridad and up.idusuario=".$iduser." ".$consulta);
        return $query->result_array();
	}
}
?>
