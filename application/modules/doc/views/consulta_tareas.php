<style>
	.the-legend {
		border-style: none;
		border-width: 0;
		font-size: 14px;
		line-height: 20px;
		margin-bottom: 0;
		font-weight:bold;
	}
	.the-fieldset {
		border: 2px groove threedface #444;
		-webkit-box-shadow:  0px 0px 0px 0px #000;
		box-shadow:  0px 0px 0px 0px #000;
	}
	.abrir-proyecto,
	.abrir-contratos,
	.abrir-categorias,
	.abrir-subcategorias{
		cursor:pointer;
	}
	li.proyectos{
		list-style:none;
		/*border-bottom:solid 1px #CCC;*/
	}
	li.proyectos{
		font-size:20px;
		}
	a.abrir-programacion
	{
		cursor:pointer;
		text-decoration:underline;
	}
	td.evi_doc{
		cursor:pointer;
	}
	td.msj{
		cursor:pointer;
	}
	td.td_eliminar{
		cursor:pointer;
	}
	i.accion{
		cursor:pointer;
	}
	i.cancelar_act{
		cursor:pointer;
	}
	i.green{
		color:#090;
	}
	i.red{
		color:#C00;
	}
	i.blue{
		color:#036;
	}
	i.yellow{
		color:#F90;
	}
	i.black{
		color:#000;
	}
</style>
<div class="page-header">
	<h4><a class="bom-menu" href="<?=base_url('doc/home/index')?>">
    <i class="fa fa-institution fa-2x"></i> DOCUMENTOS </a>/ <a class="bom-menu" href="<?=base_url('doc/contratos_concesion/index')?>">CONTRATOS DE CONCESI&Oacute;N </a> / CONSULTA TAREAS PROGRAMADAS</h4>
</div>
<b>Buscar por</b>
	<ul class="nav nav-tabs" role="tablist" id="filtro-ul">
        <li class="active" filtro="filtro_id">
        	<a data-toggle="tab" role="tab" >ID Tarea Programada</a>
        </li>
        <li proyecto="Museo Maya" filtro="filtro_filtros">
        	<a data-toggle="tab" role="tab" >Filtro de Datos</a>
        </li>
    </ul>
    <br/>
  <div id="filtro_id" class="row letra-11">
  	<div class="col-md-6">
    	<div class="form-group">
        	<b>ID</b>
            <div class="input-group">
            	<div class="input-group-addon">P - </div>
	            <input type="text" name="p_id" id="p_id" placeholder="ID Tarea Programada" class="form-control solo_numeros" />
            </div>
        </div>
    </div>
  </div>
  <div id="filtro_filtros" class="row letra11" style="display:none">
	<div class="col-md-6">
        <div class="form-group" id="act-contrato-contenedor">
        	<b>CONTRATO</b>
            <select class="form-control selectpicker letra11" multiple data-selected-text-format="count>0" id="act-contrato">
            </select>
        </div>
        <div class="form-group" id="act-categoria-contenedor">
        	<b>CATEGOR&Iacute;A</b>
            <select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-categoria-select">
            </select>
        </div>
        <div class="form-group" id="act-subcategoria-contenedor">
        	<b>SUBCATEGOR&Iacute;A</b>
            <select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-subcategoria-select">
            </select>
        </div>
        <div class="form-group" id="act-estado-contenedor">
        	<b>ESTADO</b>
            <select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-estado-select">
            </select>
        </div>
        <div class="form-group" id="act-prioridad-contenedor">
            <b>PRIORIDAD</b>
            <select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-prioridad-select">
            </select>
        </div>
    </div>

    <div class="col-md-4" id="f_fecha">
    	<input type="checkbox" name="c_fechas" id="c_fechas" />
        <b>B&Uacute;SQUEDA POR FECHA L&Iacute;MITE DE PROGRAMACI&Oacute;N</b><br/><br/>

        <div class="form-group" style="width:250px">
        	<label for="Inicio" class="col-sm-3 control-label">Inicio</label>
            <div class="input-append input-group datepicker">
        		<input data-format="yyyy-MM-dd" value="" type="text" class="form-control letra11" readonly name="fecha_inicio" id="fecha_inicio">
            	<span class="input-group-addon add-on">
            		<i data-time-icon="fa fa-calendar" data-date-icon="fa fa-calendar"></i>
            	</span>
        	</div>
        </div>
        <div class="form-group" style="width:250px">
        	<label for="Fin" class="col-sm-3 control-label">Fin</label>
        	<div class="input-append input-group datepicker">
        		<input data-format="yyyy-MM-dd" value="" type="text" class="form-control letra11" readonly name="fecha_fin" id="fecha_fin">
            	<span class="input-group-addon add-on" id="icono-fecha-fin">
            		<i data-date-icon="fa fa-calendar"></i>
				</span>
        	</div>
        </div>
    </div>
</div>
<div id="informacion">
</div>

<div class="modal fade" id="modal-detalle" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Detalle de actividad programada</h4>
      </div>
      <div class="modal-body">
      	<fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos de contrato</legend>
        	<div class="col-md-12">
            	<table style="width:100%; vertical-align:top; font-size:10px;">
                	<tr><td style="width:30%;">Proyecto</td><td id="proyecto"></td></tr>
                    <tr><td>Contrato</td><td id="contrato"></td></tr>
                    <tr><td>Categor&iacute;a</td><td id="categoria"></td></tr>
                    <tr><td>Subcategor&iacute;a</td><td id="subcategoria"></td></tr>
                    <tr><td>Descripci&oacute;n</td><td id="descripcion_contrato"></td></tr>
                    <tr><td>Fecha Incio</td><td id="inicio_contrato"></td></tr>
                    <tr><td>Fecha Fin</td><td id="fin_contrato"></td></tr>
                </table>
        	</div>
        </fieldset>

        <fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos de actividad</legend>
        	<div class="col-md-12">
            	<table style="width:100%; vertical-align:top; font-size:10px;">
                    <tr><td style="width:30%;">ID Tarea Programada</td><td id="idactividad_p"></td></tr>
                	<tr><td style="width:30%;">Actividad</td><td id="actividad"></td></tr>
                    <tr><td>Descripci&oacute;n</td><td id="descripcion"></td></tr>
                    <tr><td>Documento Cotnractual</td><td id="documento"></td></tr>
                    <tr><td>&Aacute;rea/Empresa Responsable</td><td id="empresa"></td></tr>
                    <tr><td>Persona Responsable</td><td id="persona"></td></tr>
                    <tr><td>Referencia Documental</td><td id="referencia"></td></tr>
                    <tr><td>Detalle Referencia Documental</td><td id="detalle"></td></tr>
                    <tr><td>Observaci&oacute;n/Acci&oacute;n</td><td id="observacion"></td></tr>
                </table>
        	</div>
        </fieldset>

        <fieldset class="well the-fieldset">
      	<legend class="the-legend">Seguimiento de la tarea</legend>
        	<div id="seguimiento" class="col-md-12">
        	</div>
        </fieldset>

        <div id="detalle_acciones"></div>
        <fieldset class="well the-fieldset">
      		<legend class="the-legend">Notificaciones</legend>
        	<div class="col-md-12" id="areas">

        	</div>
        </fieldset>

        <fieldset class="well the-fieldset">
        	<legend class="the-legend">Evidencias documentales</legend>
            <div class="col-md-12" id="consultar-evidencias"></div>
        </fieldset>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" id="cerrar_detalle">Cerrar</button>
      </div>
    </div>
  </div>
</div>


<!--INICIA MODAL PARA EVIDENCIAS DOCUMENTALES-->
<div class="modal fade" id="modal-evidencia-documental" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Agregar evidencia documental</h4>
      </div>
      <div class="modal-body">
      	<div class="row">
        	<form id="form-evidencia-documental" action="<?=base_url('doc/dashboard/agregar_evidencia_documental')?>" method="post" enctype="multipart/form-data">
        	<div class="col-md-12">
            	<div class="form-group">
                	<label for="exampleInputFile">Evidencia documental</label>
                	<input type="file" name="myfile" id="exampleInputFile">
              	</div>
            </div>
            <div class="col-md-3">
            	<input type="submit" value="Guardar" class="btn btn-success" id="guardar_evidencia">
                <input type="hidden" id="idprogramacion" name="idprogramacion" value="">
                <input type="hidden" id="idestado" name="idestado" value="">
            </div>
            </form>
        </div>
        <div class="row">
        	<br>
        	<div class="col-md-12" id="detalle-evidencia-documental"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--TERMINA MODAL PARA EVIDENCIAS DOCUMENTALES-->


<!--INICIA MODAL PARA EVIAR NOTIFICACION-->
<div class="modal fade" id="modal-notificacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Enviar notificaci&oacute;n</h4>
      </div>
      <div class="modal-body">
      <fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos de actividad</legend>
        	<div class="col-md-12">
            	<table style="width:100%; vertical-align:top; font-size:10px;">
                    <tr><td style="width:30%;">ID Tarea Programada</td><td id="enviar-idactividad_p"></td></tr>
                	<tr><td style="width:30%;">Actividad</td><td id="enviar-actividad"></td></tr>
                    <tr><td>Descripci&oacute;n</td><td id="enviar-descripcion"></td></tr>
                    <tr><td>Documento Contractual</td><td id="enviar-documento"></td></tr>
                    <tr><td>&Aacute;rea/Empresa Responsable</td><td id="enviar-empresa"></td></tr>
                    <tr><td>Persona Responsable</td><td id="enviar-persona"></td></tr>
                    <tr><td>Referencia Documental</td><td id="enviar-referencia"></td></tr>
                    <tr><td>Detalle Referencia Documental</td><td id="enviar-detalle"></td></tr>
                    <tr><td>Observaci&oacute;n/Acci&oacute;n</td><td id="enviar-observacion"></td></tr>
                </table>
        	</div>
        </fieldset>

        <fieldset class="well the-fieldset">
      		<legend class="the-legend">Notificaciones</legend>
            	<form id="form-enviar-notificaciones-usuarios">
                    <div class="col-md-12" id="enviar-areas">

                    </div>
                    <input type="hidden" name="not-idprogramacion" id="not-idprogramacion">
            	</form>
        </fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-enviar-notificacion">Enviar notificaci&oacute;n</button>
      </div>
    </div>
  </div>
</div>
<!--TERMINA MODAL PARA EVIDENCIAS DOCUMENTALES-->
