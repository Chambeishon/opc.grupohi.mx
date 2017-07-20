<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Administrar_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

	public function desplega_resumen($iduser)
	{
		$query = $this->db->query("EXEC sp_baw_resumen '$iduser'");
        return $query->result();
	}

	public function desplega_resumen_facturacion($iduser)
	{
		$query = $this->db->query("EXEC sp_baw_resumen_facturacion '$iduser'");
        return $query->result();
	}

	public function solicitudes_registradas($iduser,$tipo)
	{
		$query = $this->db->query("
                    SELECT grl_usuario_plaza.idusuario,
       vw_baw_solicitudes_registradas.nombre_proyecto,
       vw_baw_solicitudes_registradas.tema_solicitud,
       vw_baw_solicitudes_registradas.tipo_solicitud,
       vw_baw_solicitudes_registradas.idsolicitud,
       CAST (folio AS VARCHAR (4000)) AS folio,
       CONVERT (DATE, vw_baw_solicitudes_registradas.[timestamp], 101)
          AS fecha,
       CONVERT (VARCHAR (8), vw_baw_solicitudes_registradas.[timestamp], 108)
          AS hora
  FROM (opi.dbo.grl_plaza grl_plaza
        INNER JOIN
        opi.dbo.vw_baw_solicitudes_registradas vw_baw_solicitudes_registradas
           ON (grl_plaza.idproyecto =
                  vw_baw_solicitudes_registradas.idproyecto))
       INNER JOIN opi.dbo.grl_usuario_plaza grl_usuario_plaza
          ON (grl_usuario_plaza.idplaza = grl_plaza.idplaza)

                AND vw_baw_solicitudes_registradas.idproyecto != 2))
GROUP BY grl_usuario_plaza.idusuario,
         vw_baw_solicitudes_registradas.nombre_proyecto,
         vw_baw_solicitudes_registradas.tema_solicitud,
         vw_baw_solicitudes_registradas.tipo_solicitud,
         vw_baw_solicitudes_registradas.idsolicitud,
         CAST (folio AS VARCHAR (4000)),
         CONVERT (DATE, vw_baw_solicitudes_registradas.[timestamp], 101),
         CONVERT (VARCHAR (8),
                  vw_baw_solicitudes_registradas.[timestamp],
                  108),
         vw_baw_solicitudes_registradas.idtipo_solicitud,
         vw_baw_solicitudes_registradas.[timestamp]
ORDER BY vw_baw_solicitudes_registradas.[timestamp] DESC

                    ");
		return $query->result_array();
	}

	public function atender_solicitud($solicitud,$usuario)
	{
		$query=$this->db->query("EXEC sp_baw_atender_solicitud '$solicitud','$usuario'");
		return $query->result_array();
	}

	public function descartar_solicitud($solicitud)
	{
		$query=$this->db->query("EXEC sp_con_solicitud_descartada '$solicitud'");
		return $query->result_array();
	}

	public function desplega_solicitud($solicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_registradas WHERE idsolicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_solicitud_cancelar($solicitud)
	{
		$query=$this->db->query("SELECT *,CONVERT(VARCHAR(10),timestamp,105) as fecha,CONVERT(VARCHAR(8),timestamp,108)as hora FROM vw_baw_solicitudes_registradas_canceladas WHERE idsolicitud=$solicitud");
		return $query->result();
	}

	public function desplega_tipos_solicitud()
	{
		$query=$this->db->query("SELECT * FROM con_tipo_solicitud WHERE idestado=1");
		return $query->result();
	}

	public function desplega_archivos($solicitud)
	{
		$query=$this->db->query("SELECT nombre_documento FROM con_solicitud_documento  where idsolicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_tickets($solicitud,$user)
	{
		$query=$this->db->query("SELECT
nombre_plaza,
idsolicitud_ticket,
idsolicitud,
folio_impreso,
plaza_cobro,
carril,
folio_evento,
fecha,
hora,
importe,
iva,
tarifa,
idestado_ticket,
notificado,
idusuario
 FROM vw_con_solicitud_ticket st
LEFT OUTER JOIN grl_usuario_plaza up ON up.idplaza = st.plaza_cobro AND idusuario=".$user."  where idsolicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_tickets_all($solicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_con_solicitud_ticket WHERE idsolicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_tickets_facturados($solicitud,$user)
	{
		$query=$this->db->query("SELECT count(*) as facturados FROM con_solicitud_ticket  where idsolicitud=".$solicitud." AND idestado_ticket in (2,3) AND plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.")");
		return $query->result();
	}

	public function agrega_respuesta($solicitud,$usuario,$respuesta)
	{
		$query=$this->db->query("EXEC sp_baw_respuesta_solictitud '$solicitud','$usuario','$respuesta'");
		return $query->result_array();
	}

	public function reenviar_respuesta($solicitud,$usuario,$respuesta)
	{
		$query=$this->db->query("EXEC sp_baw_respuesta_reenvia_solictitud '$solicitud','$usuario','$respuesta'");
		return $query->result_array();
	}

	public function agrega_respuesta_documento($idrespuesta,$file)
	{
		$query=$this->db->query("EXEC sp_baw_respuesta_solictitud_documento '$idrespuesta','$file'");
		return $query->result_array();
	}

	public function informacion_solicitud($solicitud)
	{
		$query=$this->db->query("SELECT * FROM con_solicitud WHERE idsolicitud=".$solicitud);
		return $query->result();
	}

	public  function solicitudes_atendiendose($iduser,$tipo)
	{
		$query=$this->db->query("

SELECT grl_usuario_plaza.idusuario,
       CAST (folio AS VARCHAR (4000)) AS folio,
       CONVERT (DATE, vw_baw_solicitudes_atendidas.[timestamp], 101) AS fecha,
       CONVERT (VARCHAR (8), vw_baw_solicitudes_atendidas.[timestamp], 108)
          AS hora,
       vw_baw_solicitudes_atendidas.idsolicitud,
       vw_baw_solicitudes_atendidas.nombre_proyecto,
       vw_baw_solicitudes_atendidas.idtipo_solicitud,
       vw_baw_solicitudes_atendidas.tipo_solicitud,
       vw_baw_solicitudes_atendidas.tema_solicitud,
       vw_baw_solicitudes_atendidas.idcon_solicitud
  FROM (opi.dbo.grl_plaza grl_plaza
        INNER JOIN
        opi.dbo.vw_baw_solicitudes_atendidas vw_baw_solicitudes_atendidas
           ON (grl_plaza.idproyecto = vw_baw_solicitudes_atendidas.idproyecto))
       INNER JOIN opi.dbo.grl_usuario_plaza grl_usuario_plaza
          ON (grl_usuario_plaza.idplaza = grl_plaza.idplaza)
 WHERE     (grl_usuario_plaza.idusuario = ".$iduser.")
       AND (vw_baw_solicitudes_atendidas.idestado_solicitud = 1)
       AND (   idtipo_solicitud IN (".$tipo.")
            OR (    idtipo_solicitud = 6
                AND vw_baw_solicitudes_atendidas.idproyecto != 2))
GROUP BY grl_usuario_plaza.idusuario,
         CONVERT (DATE, vw_baw_solicitudes_atendidas.[timestamp], 101),
         CAST (folio AS VARCHAR (4000)),
         CONVERT (VARCHAR (8), vw_baw_solicitudes_atendidas.[timestamp], 108),
         vw_baw_solicitudes_atendidas.idsolicitud,
         vw_baw_solicitudes_atendidas.nombre_proyecto,
         vw_baw_solicitudes_atendidas.idtipo_solicitud,
         vw_baw_solicitudes_atendidas.tipo_solicitud,
         vw_baw_solicitudes_atendidas.tema_solicitud,
         vw_baw_solicitudes_atendidas.idcon_solicitud
ORDER BY 3 DESC, 2 DESC

                ");
		return $query->result_array();
	}

	public  function solicitudes_atendidos($iduser,$tipo)
	{
		$query=$this->db->query("

SELECT grl_usuario_plaza.idusuario,
			 CAST (folio AS VARCHAR (4000)) AS folio,
			 CONVERT (DATE, vw_baw_solicitudes_atendidas.[timestamp], 101) AS fecha,
			 CONVERT (VARCHAR (8), vw_baw_solicitudes_atendidas.[timestamp], 108)
					AS hora,
			 vw_baw_solicitudes_atendidas.idsolicitud,
			 vw_baw_solicitudes_atendidas.nombre_proyecto,
			 vw_baw_solicitudes_atendidas.idtipo_solicitud,
			 vw_baw_solicitudes_atendidas.tipo_solicitud,
			 vw_baw_solicitudes_atendidas.tema_solicitud,
			 vw_baw_solicitudes_atendidas.idcon_solicitud
	FROM (opi.dbo.grl_plaza grl_plaza
				INNER JOIN
				opi.dbo.vw_baw_solicitudes_atendidas vw_baw_solicitudes_atendidas
					 ON (grl_plaza.idproyecto = vw_baw_solicitudes_atendidas.idproyecto))
			 INNER JOIN opi.dbo.grl_usuario_plaza grl_usuario_plaza
					ON (grl_usuario_plaza.idplaza = grl_plaza.idplaza)
 WHERE  (grl_usuario_plaza.idusuario = ".$iduser.")
			 AND (vw_baw_solicitudes_atendidas.idestado_solicitud = 2)
			 AND (   idtipo_solicitud IN (".$tipo.")
						OR (    idtipo_solicitud = 6
								AND vw_baw_solicitudes_atendidas.idproyecto != 2))
GROUP BY grl_usuario_plaza.idusuario,
				 CONVERT (DATE, vw_baw_solicitudes_atendidas.[timestamp], 101),
				 CAST (folio AS VARCHAR (4000)),
				 CONVERT (VARCHAR (8), vw_baw_solicitudes_atendidas.[timestamp], 108),
				 vw_baw_solicitudes_atendidas.idsolicitud,
				 vw_baw_solicitudes_atendidas.nombre_proyecto,
				 vw_baw_solicitudes_atendidas.idtipo_solicitud,
				 vw_baw_solicitudes_atendidas.tipo_solicitud,
				 vw_baw_solicitudes_atendidas.tema_solicitud,
				 vw_baw_solicitudes_atendidas.idcon_solicitud
ORDER BY 3 DESC, 2 DESC");
		return $query->result_array();
	}

	public function modificar_solicitud($correo,$tipo,$solicitud)
	{
		$query =$this->db->query("EXEC sp_con_modificar_solicitud '$correo','$tipo','$solicitud'");
		return $query->result_array();
	}

	public function descartar_solicitud_atendida($solicitud)
	{
		$query=$this->db->query("EXEC sp_baw_descartar_solicitud_atendida '$solicitud'");
		return $query->result_array();
	}

	public function respuesta_correo($solicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_baw_correo_respuesta WHERE idcon_solicitud=".$solicitud);
		return $query->result();
	}

	public function obtener_correos($idproyecto)
	{
		$query=$this->db->query("SELECT * FROM baw_correos WHERE idproyecto=".$idproyecto);
		return $query->result_array();	
	}

	public function informacion_solicitud_atendida($solicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_atendidas WHERE idcon_solicitud=".$solicitud);
		return $query->result_array();
	}

	public function enviar_datos($solicitud,$usuario,$tema,$comentario,$usuario_enviar)
	{
		$query=$this->db->query("EXEC sp_baw_agregar_solicitud_datos '$solicitud','$usuario','$comentario','$tema','$usuario_enviar'");
		return $query->result_array();
	}

	public function consulta_solicitud_datos($solicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_atendidas WHERE idsolicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_solicitud_informacion($solicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_baw_solicitar_datos WHERE idcon_solicitud=".$solicitud);
		return $query->result_array();
	}

	public function desplega_preguntas($solicitud)
	{
		$query=$this->db->query("SELECT  s.idcon_solicitud, sd.tema, sd.comentario,sd.idsolicitud_datos , sd.fecha_solicitud, CONVERT(VARCHAR(8),sd.hora_solicitud,108)as hora_s,sd.estado_datos ,
CASE WHEN sd.estado_datos=1 THEN '<button id=\"'+ CAST(sd.idsolicitud_datos AS varchar)+'\" style=\"float:right\" type=\"button\"
class=\"btn btn-danger btn-xs btn-cerrar\" >Cerrar Tema</button>' END AS boton,
'<a href=\"\" data-toggle=\"modal\" data-target=\"#myModal' + CAST(sd.idsolicitud_datos AS varchar) + '\">VER RESPUESTA</a>'  AS link
FROM baw_solicitud s INNER JOIN baw_solicitar_datos sd ON s.idsolicitud = sd.idsolicitud
WHERE s.idcon_solicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_preguntas_facturacion($solicitud)
	{
		$query=$this->db->query("SELECT  s.idcon_solicitud, sd.tema, sd.comentario,sd.idsolicitud_datos , sd.fecha_solicitud, CONVERT(VARCHAR(8),sd.hora_solicitud,108)as hora_s,sd.estado_datos ,
CASE WHEN sd.estado_datos=1 THEN '<button id=\"'+ CAST(sd.idsolicitud_datos AS varchar)+'\" style=\"float:right\" type=\"button\"
class=\"btn btn-danger btn-xs fact-btn-cerrar\" >Cerrar Tema</button>' END AS boton,
'<a href=\"\" data-toggle=\"modal\" data-target=\"#myModal' + CAST(sd.idsolicitud_datos AS varchar) + '\">VER RESPUESTA</a>'  AS link
FROM baw_solicitud s INNER JOIN baw_solicitar_datos sd ON s.idsolicitud = sd.idsolicitud
WHERE s.idcon_solicitud=".$solicitud);
		return $query->result();
	}

	public function desplega_respuesta($solicitud)
	{
		$query=$this->db->query("SELECT s.idcon_solicitud,sd.tema,CASE WHEN rd.fecha_respuesta IS NULL THEN '' ELSE '<b>CONTESTO: </b>'+CAST(rd.usuario_respuesta AS varchar)+'<b> A LAS: </b>'+CAST(rd.fecha_respuesta AS varchar)+' | '+CONVERT(VARCHAR(8),rd.hora_respuesta,108) END AS titulo , CASE WHEN rd.respuesta IS NULL THEN '<b>NO SE TIENE RESPUESTA</b>' ELSE rd.respuesta END respuesta,sd.idsolicitud_datos, sd.usuario_pregunta FROM baw_solicitud AS s INNER JOIN baw_solicitar_datos AS sd ON s.idsolicitud = sd.idsolicitud LEFT OUTER JOIN baw_respuesta_datos AS rd ON sd.idsolicitud_datos = rd.idsolicitud_datos WHERE sd.idsolicitud_datos =".$solicitud);
		return $query->result_array();
	}

	public function desplega_consulta_solicitud($solicitud)
	{
		$query=$this->db->query("SELECT *,CONVERT (DATE, vw_baw_solicitud_respuesta.[timestamp], 101) AS fecha,
       CONVERT (VARCHAR (8), vw_baw_solicitud_respuesta.[timestamp], 108)
          AS hora FROM vw_baw_solicitud_respuesta WHERE idsolicitud=".$solicitud);
		return $query->result();
	}

	public function cerrar_tema($solicitud)
	{
		$query=$this->db->query("EXEC sp_baw_cerrar_tema '$solicitud'");
		return $query->result_array();
	}

	public function resumen_informacion($tipo)
	{
		$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_registradas WHERE
		(idtipo_solicitud in (".$tipo.") OR (idtipo_solicitud=6 AND idproyecto!=2))");
		return $query->result();
	}

	public function resumen_informacion_atendidas($estado,$tipo)
	{

		if($tipo=='6'):
			$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_atendidas WHERE idtipo_solicitud in (".$tipo.") AND idestado_solicitud=".$estado);
		else:
			$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_atendidas WHERE
			(idtipo_solicitud in (".$tipo.") OR (idtipo_solicitud=6 AND idproyecto!=2))
			AND idestado_solicitud=".$estado);
		endif;
		return $query->result();
	}

	public function solicitud_comentario_documento($idrespuesta_datos,$file)
	{
		$query=$this->db->query("EXEC sp_baw_agregar_solicitud_datos_documento '$idrespuesta_datos','$file'");
		return $query->result_array();
	}

	public function validar_ticket($solicitud_ticket,$estado)
	{
		$query=$this->db->query("EXEC sp_con_validar_ticket '$solicitud_ticket','$estado'");
		return $query->result_array();
	}

	public function eliminar_tickets($idsolicitud,$user)
	{
		$query=$this->db->query("EXEC sp_con_eliminar_tickets $idsolicitud,$user");
		return $query->result_array();
	}

	public function solicitudes_registradas_facturacion($iduser)
	{
		$query = $this->db->query("select folio AS link,nombre_proyecto,idsolicitud,CONVERT(DATE,timestamp,101)as fecha,CONVERT(VARCHAR(8),timestamp,108)as hora,
SUBSTRING
(
-- column
CAST(mensaje_solicitud as varchar(4000))
-- start position
,CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)) , 1) + 13
-- length
,CASE
WHEN (CHARINDEX('DOMICILIO FISCAL', CAST(mensaje_solicitud as varchar(4000)) , 0) - CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)), 0)) > 0
THEN CHARINDEX('DOMICILIO FISCAL', CAST(mensaje_solicitud as varchar(4000)), 0) - CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)), 0) - 13
ELSE 0
END
) as razon_social,
SUBSTRING(mensaje_solicitud,5,13) as rfc
from vw_baw_solicitudes_registradas_facturacion
WHERE idtipo_solicitud=6
AND idestado_solicitud=1
AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$iduser.") GROUP BY idsolicitud)
AND idsolicitud NOT IN (SELECT idsolicitud FROM  [con_solicitud_ticket] WHERE notificado=1 GROUP BY idsolicitud )
ORDER BY fecha ASC,hora ASC");
		return $query->result_array();
	}

	public function resumen_informacion_facturacion()
	{
		$query=$this->db->query("SELECT * FROM vw_baw_solicitudes_registradas_facturacion");
		return $query->result();
	}

	public function desplega_solicitud_facturacion($solicitud)
	{
		$query=$this->db->query("SELECT *,CASE idproyecto
	WHEN 1 THEN 'http://autopista-lerma-3marias.com.mx/img/logo_l3m.jpg'
	WHEN 2 THEN 'http://autopista-toluca-atlacomulco.com.mx/img/logo_ata.jpg'
	WHEN 3 THEN 'http://libramientoirapuato.mx/img/logo_ldi.jpg'
	WHEN 4 THEN 'http://pipnii.hoatsa.com.mx/img/logo_pipnii.jpg'
END as logo,
CASE idproyecto
	WHEN 1 THEN 'contacto@autopista-lerma-3marias.com.mx'
	WHEN 2 THEN 'contacto@autopista-toluca-atlacomulco.com.mx'
	WHEN 3 THEN 'contacto@libramientoirapuato.com.mx'
	WHEN 4 THEN 'contacto@pipnii.hoatsa.com.mx'
END as correo,
CASE idproyecto
	WHEN 1 THEN 'http://autopista-lerma-3marias.com.mx/'
	WHEN 2 THEN 'http://autopista-toluca-atlacomulco.com.mx/'
	WHEN 3 THEN 'http://libramientoirapuato.mx/'
	WHEN 4 THEN 'http://pipnii.hoatsa.com.mx/'
END as ruta,CONVERT(VARCHAR(10),timestamp,105) as fecha,CONVERT(VARCHAR(8),timestamp,108)as hora FROM vw_baw_solicitudes_registradas_facturacion WHERE idsolicitud=".$solicitud);
		return $query->result();
	}

	public  function solicitudes_atendiendose_facturacion($iduser)
	{
		$query=$this->db->query("select folio AS link,nombre_proyecto,idsolicitud,CONVERT(DATE,timestamp,101)as fecha,CONVERT(VARCHAR(8),timestamp,108)as hora,
SUBSTRING
(
-- column
CAST(mensaje_solicitud as varchar(4000))
-- start position
,CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)) , 1) + 13
-- length
,CASE
WHEN (CHARINDEX('DOMICILIO FISCAL', CAST(mensaje_solicitud as varchar(4000)) , 0) - CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)), 0)) > 0
THEN CHARINDEX('DOMICILIO FISCAL', CAST(mensaje_solicitud as varchar(4000)), 0) - CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)), 0) - 13
ELSE 0
END
) as razon_social,
SUBSTRING(mensaje_solicitud,5,13) as rfc
from vw_baw_solicitudes_registradas_facturacion
WHERE idtipo_solicitud=6
AND idsolicitud IN (SELECT idsolicitud FROM  [con_solicitud_ticket] WHERE notificado=1 GROUP BY idsolicitud )
AND idestado_solicitud=1
AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$iduser.") GROUP BY idsolicitud)
ORDER BY fecha ASC,hora ASC");
		return $query->result_array();
	}

	public function solicitudes_atendidos_facturacion($iduser)
	{
		$query=$this->db->query("SELECT folio AS link,nombre_proyecto,idcon_solicitud,CONVERT(DATE,timestamp,101)as fecha,CONVERT(VARCHAR(8),timestamp,108)as hora,
SUBSTRING
(
CAST(mensaje_solicitud as varchar(4000))
,CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)) , 1) + 13
,CASE
WHEN (CHARINDEX('DOMICILIO FISCAL', CAST(mensaje_solicitud as varchar(4000)) , 0) - CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)), 0)) > 0
THEN CHARINDEX('DOMICILIO FISCAL', CAST(mensaje_solicitud as varchar(4000)), 0) - CHARINDEX('RAZON SOCIAL:', CAST(mensaje_solicitud as varchar(4000)), 0) - 13
ELSE 0
END
) as razon_social,
SUBSTRING(mensaje_solicitud,5,13) as rfc
FROM vw_baw_solicitudes_atendidas
WHERE
idcon_solicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$iduser.") GROUP BY idsolicitud)
AND idestado_solicitud=2
AND idtipo_solicitud=6 ORDER BY fecha ASC,hora ASC");
		return $query->result_array();
	}

	public function desplega_tickets_validados($solicitud)
	{
		$query=$this->db->query("SELECT *,1 as idusuario FROM vw_con_solicitud_ticket  where idsolicitud=".$solicitud." AND idestado_ticket=2");
		return $query->result();
	}

	public function notifica_tickets($solicitud)
	{
		$query=$this->db->query("EXEC sp_con_notificar_tickets '$solicitud'");
		return $query->result_array();
	}

	public function valida_notificacion($solicitud)
	{
		$query=$this->db->query("SELECT COUNT(*) as total FROM [con_solicitud_ticket] WHERE idsolicitud=".$solicitud." AND notificado=1");
		return $query->result();
	}

	public function desplega_documentos_facturacion($solicitud)
	{
		$query=$this->db->query("SELECT * FROM [baw_solicitud_respuesta_documento]
WHERE idsolicitud_respuesta IN (SELECT idsolicitud_respuesta FROM [baw_solicitud_respuesta] WHERE idcon_solicitud=".$solicitud.")");
		return $query->result();
	}

	public function desplega_siguientes_registrados($solicitud,$user)
	{
		$query=$this->db->query("(SELECT idsolicitud,'SIGUIENTE' as etiqueta FROM vw_baw_solicitudes_registradas_facturacion
      WHERE idsolicitud =
      (select min(idsolicitud) FROM vw_baw_solicitudes_registradas_facturacion where idsolicitud > ".$solicitud."
         and idtipo_solicitud=6
         and idestado_solicitud=1
         AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)
AND idsolicitud NOT IN (SELECT idsolicitud FROM  [con_solicitud_ticket] WHERE notificado=1 GROUP BY idsolicitud )))
         UNION
(SELECT idsolicitud,'ANTERIOR' as etiqueta FROM vw_baw_solicitudes_registradas_facturacion
      WHERE idsolicitud =
      (select max(idsolicitud) FROM vw_baw_solicitudes_registradas_facturacion where idsolicitud < ".$solicitud."
         and idtipo_solicitud=6
         and idestado_solicitud=1
         AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)
AND idsolicitud NOT IN (SELECT idsolicitud FROM  [con_solicitud_ticket] WHERE notificado=1 GROUP BY idsolicitud )))");
		return $query->result();
	}

	public function desplega_siguientes_atendiendose($solicitud,$user)
	{
		$query=$this->db->query("(SELECT idsolicitud,'SIGUIENTE' as etiqueta FROM vw_baw_solicitudes_registradas_facturacion_botones
      WHERE idsolicitud =
      (select min(idsolicitud) FROM vw_baw_solicitudes_registradas_facturacion_botones where idsolicitud > ".$solicitud."
         and idestado_solicitud=1
         AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)
))
         UNION
(SELECT idsolicitud,'ANTERIOR' as etiqueta FROM vw_baw_solicitudes_registradas_facturacion_botones
      WHERE idsolicitud =
      (select max(idsolicitud) FROM vw_baw_solicitudes_registradas_facturacion_botones where idsolicitud < ".$solicitud."
         and idestado_solicitud=1
         AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)
))");
		return $query->result();
	}

	public function desplega_siguientes_atendidos($solicitud,$user)
	{
		$query=$this->db->query("(SELECT idcon_solicitud,'SIGUIENTE' as etiqueta FROM vw_baw_solicitudes_atendidas
      WHERE idcon_solicitud =
      (select min(idcon_solicitud) FROM vw_baw_solicitudes_atendidas where idcon_solicitud > ".$solicitud."
         and idtipo_solicitud=6 and idestado_solicitud=2
		 AND idcon_solicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)))
         UNION
(SELECT idcon_solicitud,'ANTERIOR' as etiqueta FROM vw_baw_solicitudes_atendidas
      WHERE idcon_solicitud =
      (select max(idcon_solicitud) FROM vw_baw_solicitudes_atendidas where idcon_solicitud < ".$solicitud."
         and idtipo_solicitud=6 and idestado_solicitud=2
		 AND idcon_solicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)))");
		return $query->result();
	}

	public function desplega_no_validados($solicitud)
	{
		$query=$this->db->query("SELECT COUNT(*) as no_validados FROM con_solicitud_ticket WHERE idsolicitud=".$solicitud." AND idestado_ticket=1");
		return $query->result();
	}

	public function consultar_solicitudes_facturacion($iduser)
	{
		/*$query=$this->db->query("SELECT *
		,CONVERT(DATE,timestamp,101)as fecha
		,CONVERT(VARCHAR(8),timestamp,108)as hora FROM vw_con_solicitudes WHERE idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$iduser.") GROUP BY idsolicitud) ORDER BY fecha ASC, hora ASC");*/
		$query=$this->db->query("SELECT
s.idsolicitud,s.folio,s.nombre_proyecto,s.razon_social,s.idestado_solicitud,s.estado
,CONVERT(DATE,s.timestamp,101)as fecha
,CONVERT(VARCHAR(8),s.timestamp,108)as hora
,fecha_respuesta
,s.rfc
FROM vw_con_solicitudes s
LEFT JOIN  baw_solicitud_respuesta sr ON sr.idcon_solicitud = s.idsolicitud
WHERE idsolicitud IN (
	SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (
		SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$iduser.")
	GROUP BY idsolicitud)
ORDER BY fecha ASC, hora ASC");

		return $query->result_array();
	}

	public function consultar_solicitud_facturacion($idsolicitud)
	{
		$query=$this->db->query("SELECT * FROM vw_con_solicitudes_tickets WHERE idsolicitud=".$idsolicitud." ORDER BY estado");
		return $query->result();
	}

	public function consultar_respuesta_facturacion($idsolicitud)
	{
		$query=$this->db->query("SELECT * FROM baw_solicitud_respuesta rs
LEFT JOIN baw_solicitud s ON rs.idcon_solicitud = s.idcon_solicitud
WHERE rs.idcon_solicitud = (select idcon_solicitud from baw_solicitud where idcon_solicitud=".$idsolicitud.");");
		return $query->result();
	}

	public function consultar_documentos_respuesta_facturacion($idsolicitud)
	{
		$query=$this->db->query("select * from baw_solicitud_respuesta_documento
where idsolicitud_respuesta in (select idsolicitud_respuesta from baw_solicitud_respuesta where idcon_solicitud=".$idsolicitud.")");
		return $query->result();
	}

	public function desplega_siguientes_consulta($solicitud,$user)
	{
		$query=$this->db->query("(SELECT idsolicitud,'SIGUIENTE' as etiqueta FROM vw_con_solicitudes
      WHERE idsolicitud =
      (select min(idsolicitud) FROM vw_con_solicitudes where idsolicitud > ".$solicitud."
		 AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)))
         UNION
(SELECT idsolicitud,'ANTERIOR' as etiqueta FROM vw_con_solicitudes
      WHERE idsolicitud =
      (select max(idsolicitud) FROM vw_con_solicitudes where idsolicitud < ".$solicitud."
		 AND idsolicitud IN (SELECT idsolicitud FROM [con_solicitud_ticket] WHERE plaza_cobro IN (SELECT idplaza FROM grl_usuario_plaza WHERE idusuario=".$user.") GROUP BY idsolicitud)))");
		return $query->result();
	}

	public function desplega_etiquetas_registrados($solicitud,$user)
	{
		$query=$this->db->query("(
SELECT idcon_solicitud,'SIGUIENTE' as etiqueta
FROM vw_baw_solicitudes_atendidas
WHERE idcon_solicitud =
(SELECT min(idcon_solicitud) FROM vw_baw_solicitudes_atendidas where idcon_solicitud > ".$solicitud."
         AND (idtipo_solicitud in (1,2,3,4,5) OR (idtipo_solicitud=6 AND idproyecto!=2))
         and idestado_solicitud=1
         AND idproyecto in (select idproyecto from vw_grl_usuario_plaza where idusuario=".$user." group by idproyecto))
 )UNION(
SELECT idcon_solicitud,'ANTERIOR' as etiqueta
FROM vw_baw_solicitudes_atendidas
WHERE idcon_solicitud =
(SELECT max(idcon_solicitud) FROM vw_baw_solicitudes_atendidas where idcon_solicitud < ".$solicitud."
         AND (idtipo_solicitud in (1,2,3,4,5) OR (idtipo_solicitud=6 AND idproyecto!=2))
         and idestado_solicitud=1
         AND idproyecto in (select idproyecto from vw_grl_usuario_plaza where idusuario=".$user." group by idproyecto))
)");
		return $query->result();
	}

	public function desplega_etiquetas_atendidos($solicitud,$user)
	{
		$query=$this->db->query("(
SELECT idcon_solicitud,'SIGUIENTE' as etiqueta
FROM vw_baw_solicitudes_atendidas
WHERE idcon_solicitud =
(SELECT min(idcon_solicitud) FROM vw_baw_solicitudes_atendidas where idcon_solicitud > ".$solicitud."
         AND (idtipo_solicitud in (1,2,3,4,5) OR (idtipo_solicitud=6 AND idproyecto!=2))
         and idestado_solicitud=2
         AND idproyecto in (select idproyecto from vw_grl_usuario_plaza where idusuario=".$user." group by idproyecto))
 )UNION(
SELECT idcon_solicitud,'ANTERIOR' as etiqueta
FROM vw_baw_solicitudes_atendidas
WHERE idcon_solicitud =
(SELECT max(idcon_solicitud) FROM vw_baw_solicitudes_atendidas where idcon_solicitud < ".$solicitud."
         AND (idtipo_solicitud in (1,2,3,4,5) OR (idtipo_solicitud=6 AND idproyecto!=2))
         and idestado_solicitud=2
         AND idproyecto in (select idproyecto from vw_grl_usuario_plaza where idusuario=".$user." group by idproyecto))
)");
		return $query->result();
	}

	public function respuesta_automatica_solicitud()
	{
		$query=$this->db->query("SELECT * FROM baw_respuesta_automatica
WHERE idproyecto=2 AND idtipo_solicitud=7");
		return $query->result();
	}

	//Ejecutar validacion de tickets
	public function ejecutar_validacion($fecha,$usuario)
	{
		$query = $this->db->query("EXEC sp_con_validacion_automatica '$fecha','$usuario'");
        return $query->result_array();
	}

	//Ejecutar carga de tickets
	public function insertar_ticket($datos)
	{
		$this->db->insert('con_ticket_cargado', $datos);
		return $this->db->affected_rows();
	}

	public function desplegar_validacion_encabezado($idvalidacion)
	{
		$query=$this->db->query("SELECT * FROM con_validacion_automatica_reporte_encabezado WHERE idvalidacion=".$idvalidacion."");
		return $query->result_array();
	}

	//Desplegar reporte de validacion detalle
	public function desplegar_validacion_detalle($idvalidacion)
	{
		$query=$this->db->query("SELECT idvalidacion,idsolicitud,folio,
ISNULL(SUM(CASE coincidencia WHEN 1 THEN 1 END),0) AS tickets_validados,
ISNULL(SUM(CASE coincidencia WHEN 0 THEN 1 END),0) AS tickets_no_validados,
ISNULL(SUM(CASE coincidencia WHEN 2 THEN 1 END),0) AS tickets_validados_previamente,
COUNT(*) AS total_tickets,
ISNULL(SUM(CASE coincidencia WHEN 1 THEN tarifa END),0) AS total_tarifa_validados,
ISNULL(SUM(CASE coincidencia WHEN 0 THEN tarifa END),0) AS total_tarifa_no_validados,
SUM(tarifa) AS total_tarifa,
CONVERT(INT,((ISNULL(SUM(CASE coincidencia WHEN 0 THEN tarifa END),0) * 100)/SUM(tarifa))) AS inefectividad
FROM con_validacion_automatica_reporte_detalle
WHERE idvalidacion=".$idvalidacion."
GROUP BY idvalidacion,idsolicitud,folio ORDER BY inefectividad DESC,folio ASC");
		return $query->result_array();
	}

	public function validar_archivos($fecha)
	{
		$query = $this->db->query("select idplaza,fecha from con_ticket_cargado where fecha='".$fecha."' group by idplaza,fecha");
		return $query->num_rows();
	}

	public function buscar_solicitudes($valor)
	{
		$query=$this->db->query("SELECT
s.nombre_plaza,
s.folio,
s.fecha,
s.hora,
s.folio_evento,
s.tarifa,
s.estado,
v.valida,
CONVERT(DATE,v.timestamp) as fecha_valida,
r.usuario_respuesta,
r.fecha_respuesta
FROM vw_con_solicitudes_tickets s
LEFT JOIN vw_baw_solicitud_respuesta r ON r.idsolicitud = s.idsolicitud
LEFT JOIN con_validacion_automatica_reporte_detalle v ON v.idsolicitud_ticket = s.idsolicitud_ticket
  WHERE folio_evento like '%".$valor."%'");
		return $query->result_array();
	}

}
/*
*end modules/login/models/index_model.php
*/
