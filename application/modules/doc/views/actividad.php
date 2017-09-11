<style>
	a.modificar-actividad, a.programar-actividad{
		cursor:pointer;
		text-decoration:underline;
	}
	i.info_area{
		cursor:pointer;
	}
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

</style>
<div class="page-header">
	<h4><a class="bom-menu" href="<?=base_url('doc/home/index')?>">
    <i class="fa fa-institution fa-2x"></i> DOCUMENTOS </a>/ <a class="bom-menu" href="<?=base_url('doc/contratos_concesion/index')?>">CONTRATOS DE CONCESI&Oacute;N </a> / ACTIVIDADES</h4>
</div>
<div class="row">
	<div class="col-md-4">
    	<b>CONTRATO</b>
    	<select name="act-contrato" class="form-control" id="act-contrato">
        	<option value="0">- SELECCIONE -</option>
            <?php foreach ($contratos as $contrato):?>
            <option value="<?=$contrato->idcontrato?>"><?=$contrato->clave.'-'.$contrato->numero_contrato?></option>
            <?php endforeach;?>
        </select>
    </div>
    <div class="col-md-4" id="act-categoria-contenedor">

    </div>
    <div class="col-md-4" id="act-subcategoria-contenedor">

    </div>
</div>
<br>
<div class="row" id="act-resultado" style="display:none;">
	<div class="col-md-12">
    	<a class="btn btn-success pull-right" id="btn-abrir-agregar-actividad"><i class="fa fa-plus"></i> AGREGAR ACTIVIDAD</a>
        <br><br>
        <table id="grid" style="font-size:10px;"></table>
    </div>
</div>
<!--<div class="row">
	<a class="btn btn-success pull-right" id="btn-abrir-agregar-contrato"><i class="fa fa-plus"></i> REGISTRAR CONTRATO</a>
    <br><br>
    <table id="grid" style="font-size:10px;"></table>
</div>-->
<!--INICIA MODAL ALTA DE CONTRATO-->
<div class="modal fade" id="modal-alta-actividad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Agregar actividad</h4>
      </div>
      <form class="form-horizontal" role="form" id="form-agregar-actividad">
      <div class="modal-body">
      	<fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos Generales</legend>
            	<div class="col-md-12">
                	<div class="form-group" style="margin-bottom:0px;">
                	<label for="">Contrato</label>
                	<span id="label-contrato"></span>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group" style="margin-bottom:0px;">
                	<label for="">Categoria</label>
                	<span id="label-categoria"></span>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group">
                	<label for="">Subcategoria</label>
                	<span id="label-subcategoria"></span>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group">
                	<label for="exampleInputEmail1">Nombre de actividad</label>
                	<textarea class="form-control required" name="nombre" id="nombre" maxlength="254"></textarea>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group">
                	<label for="exampleInputEmail1">Descripci&oacute;n de actividad</label>
                	<textarea class="form-control required" name="descripcion" id="descripcion" maxlength="254"></textarea>
              		</div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                      <label for="prioridad">Prioridad</label>
                      <select class="form-control" id="prioridad" name="prioridad">
                      </select>
                    </div>
                </div>

        </fieldset>

        <fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos Adicionales</legend>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label" style="text-align:left;">Documento Contractual</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="documento" name="documento" maxlength="30">
    			</div>
  			</div>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label"  style="text-align:left;">&Aacute;rea/Empresa Responsable</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="area" name="area" maxlength="30">
    			</div>
  			</div>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label"  style="text-align:left;">Persona Responsable</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="persona" name="persona" maxlength="30">
    			</div>
  			</div>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label"  style="text-align:left;">Referencia Documental</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="referencia" name="referencia" maxlength="30">
    			</div>
  			</div>

            <div class="col-md-12">
                	<div class="form-group">
                	<label for="exampleInputEmail1">Detalle Referencia Documental</label>
                	<textarea class="form-control required" name="detalle" id="detalle" maxlength="254"></textarea>
              		</div>
                </div>


        </fieldset>
        <fieldset class="well the-fieldset">
        	<legend class="the-legend">Observaciones/Acci&oacute;n</legend>
        	<div class="form-group">
    			<div class="col-sm-12">
            		<textarea class="form-control required" name="observaciones" id="observaciones" maxlength="254"></textarea>
                </div>
            </div>
    	</fieldset>


        <fieldset class="well the-fieldset">
      		<legend class="the-legend">Areas involucradas</legend>
            <div class="col-md-12">
            	<?php foreach ($areas as $area):?>
                	<div class="checkbox">
                        <label>
                        	<input type="checkbox" class="areas_i" name="areas[]" value="<?=$area["idarea_involucrada"]?>">
                        	<?=$area["nombre_area_involucrada"]?>
                        </label>
                    </div>
				<?php endforeach;?>
            </div>
      	</fieldset>

      </div>
      <input type="hidden" name="idcontrato" id="idcontrato" value="">
      <input type="hidden" name="idcategoria" id="idcategoria" value="">
      <input type="hidden" name="idsubcategoria" id="idsubcategoria" value="">
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-success" id="btn-agregar-actividad">Agregar</button>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL ALTA DE INCIDENCIA-->


<!--INICIA MODAL PARA AREAS-->
<div class="modal fade" id="modal-areas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">Agregar area involucrada</h4>
      </div>
      <div class="modal-body">
        	<fieldset class="well the-fieldset">
      			<legend class="the-legend">Datos de actividad</legend>
                <div class="col-md-12">
            		<table class="table table-striped table-condensed table-bordered">
                    	<tr><td colspan="2" style="background-color:#888; color:#fff;"><b>Datos Generales</b></td></tr>
                		<tr><td width="40%"><b>Contrato</b></td><td id="area-contrato"></td></tr>
                        <tr><td><b>Categor&iacute;a</b></td><td id="area-categoria"></td></tr>
                        <tr><td><b>Subcategor&iacute;a</b></td><td id="area-subcategoria"></td></tr>
                        <tr><td><b>Nombre Actividad</b></td><td id="area-actividad"></td></tr>
                        <tr><td><b>Descripci&oacute;n</b></td><td id="area-descripcion"></td></tr>
                        <tr><td colspan="2" style="background-color:#888; color:#fff;"><b>Datos Adicionales</b></td></tr>
                		<tr><td width="40%"><b>Documento Contractual</b></td><td id="area-documento"></td></tr>
                        <tr><td><b>&Aacute;rea/Empresa Responsable</b></td><td id="area-empresa"></td></tr>
                        <tr><td><b>Persona Responsable</b></td><td id="area-persona"></td></tr>
                        <tr><td><b>Referencia Documental</b></td><td id="area-referencia"></td></tr>
                        <tr><td><b>Detalle Referencia</b></td><td id="area-detalle"></td></tr>
                        <tr><td colspan="2" style="background-color:#888; color:#fff;"><b>Observaciones/Acci&oacute;n</b></td></tr>
                		<tr><td id="area-observacion" colspan="2"></td></tr>
                	</table>
                </div>
            </fieldset>
            <fieldset class="well the-fieldset">
      			<legend class="the-legend">Areas involucradas</legend>
                <div class="col-md-12" id="actividad-areas"></div>
            </fieldset>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
<!--TERMINA MODAL PARA AREAS-->



<!--INICIA MODAL MODIFICAR ACTIVIDAD-->
<div class="modal fade" id="modal-modificar-actividad" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Modificar actividad</h4>
      </div>
      <form class="form-horizontal" role="form" id="form-modificar-actividad">
      <div class="modal-body">
      	<fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos Generales</legend>
            	<!--<div class="col-md-12">
                	<div class="form-group" style="margin-bottom:0px;">
                	<label for="">Contrato</label>
                	<span id="label-contrato"></span>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group" style="margin-bottom:0px;">
                	<label for="">Categoria</label>
                	<span id="label-categoria"></span>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group">
                	<label for="">Subcategoria</label>
                	<span id="label-subcategoria"></span>
              		</div>
                </div>-->





                <div class="col-md-12">
                  <div class="form-group" style="margin-bottom:0px;">
                  <label for="">Contrato</label>
                    <select name="mod-contrato" class="form-control" id="mod-contrato">
                      <option value="0">- SELECCIONE -</option>
                        <?php foreach ($contratos as $contrato):?>
                      <option value="<?=$contrato->idcontrato?>"><?=$contrato->clave.'-'.$contrato->numero_contrato?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group" style="margin-bottom:0px;">
                  <label for="">Categoria</label>
                    <select name="mod-categoria" class="form-control" id="mod-categoria">
                      <option value="0">- SELECCIONE -</option>
                        <?php foreach ($categorias as $categoria):?>
                      <option value="<?=$categoria->idcat_categoria?>"><?=$categoria->cat_categoria?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>

                <div class="col-md-12">
                  <div class="form-group" style="margin-bottom:0px;">
                  <label for="">Subcategoria</label>
                    <select name="mod-subcategoria" class="form-control" id="mod-subcategoria">
                      <option value="0">- SELECCIONE -</option>
                        <?php foreach ($subcategorias as $subcategoria):?>
                      <option value="<?=$subcategoria->idcat_subcategoria?>"><?=$subcategoria->cat_subcategoria?></option>
                        <?php endforeach;?>
                    </select>
                  </div>
                </div>


                <div class="col-md-12">
                	<div class="form-group">
                	<label for="exampleInputEmail1">Nombre de actividad</label>
                	<textarea class="form-control required" name="modificar-nombre" id="modificar-nombre" maxlength="254"></textarea>
              		</div>
                </div>
                <div class="col-md-12">
                	<div class="form-group">
                	<label for="exampleInputEmail1">Descripci&oacute;n de actividad</label>
                	<textarea class="form-control required" name="modificar-descripcion" id="modificar-descripcion" maxlength="254"></textarea>
              		</div>
                </div>
                <div class="col-md-12">
                  <div class="form-group">
                      <label for="prioridad">Prioridad</label>
                      <select class="form-control" id="modificar-prioridad" name="modificar-prioridad">
                      <?php foreach ($prioridades as $prioridad):?>
                        <option value="<?=$prioridad['idprioridad']?>"><?=$prioridad['nombre']?></option>
                        <?php endforeach;?>
                      </select>
                    </div>
                </div>

        </fieldset>

        <fieldset class="well the-fieldset">
      	<legend class="the-legend">Datos Adicionales</legend>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label" style="text-align:left;">Documento Contractual</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="modificar-documento" name="modificar-documento" maxlength="30">
    			</div>
  			</div>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label"  style="text-align:left;">&Aacute;rea/Empresa Responsable</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="modificar-area" name="modificar-area" maxlength="30">
    			</div>
  			</div>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label"  style="text-align:left;">Persona Responsable</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="modificar-persona" name="modificar-persona" maxlength="30">
    			</div>
  			</div>
            <div class="form-group">
    			<label for="cuerpo" class="col-sm-5 control-label"  style="text-align:left;">Referencia Documental</label>
    			<div class="col-sm-7">
      			<input type="text" class="form-control required" id="modificar-referencia" name="modificar-referencia" maxlength="30">
    			</div>
  			</div>

            <div class="col-md-12">
                	<div class="form-group">
                	<label for="exampleInputEmail1">Detalle Referencia Documental</label>
                	<textarea class="form-control required" name="modificar-detalle" id="modificar-detalle" maxlength="254"></textarea>
              		</div>
                </div>


        </fieldset>
        <fieldset class="well the-fieldset">
        	<legend class="the-legend">Observaciones/Acci&oacute;n</legend>
        	<div class="form-group">
    			<div class="col-sm-12">
            		<textarea class="form-control required" name="modificar-observaciones" id="modificar-observaciones" maxlength="254"></textarea>
                </div>
            </div>
    	</fieldset>

        <fieldset class="well the-fieldset">
      		<legend class="the-legend">Areas involucradas</legend>
            <div class="col-md-12" id="modificar-areas"></div>
      	</fieldset>
      </div>
      <input type="hidden" name="modificar-idactividad" id="modificar-idactividad" value="">
      <!--<input type="hidden" name="idcontrato" id="idcontrato" value="">
      <input type="hidden" name="idcategoria" id="idcategoria" value="">
      <input type="hidden" name="idsubcategoria" id="idsubcategoria" value="">-->
      </form>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-warning" id="btn-modificar-actividad">Modificar</button>
      </div>
    </div>
  </div>
</div>
<!--FIN MODAL ALTA DE INCIDENCIA-->

<!------------------- INICIO MODAL PARA ALTA DE PRIOGRAMACIÓN  -------------->
<div class="modal fade" id="modal-programar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="myModalLabel">Programar Actividad</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <table>
              <tr><td valign="top"><b>Proyecto</b>:</td><td><span id="proyecto"></span></td></tr>
                <tr><td valign="top"><b>Numero Contrato</b>:</td><td><span class="detalle" id="numero"></span></td></tr>
                <tr><td valign="top"><b>Categor&iacute;a</b>:</td><td><span class="detalle" id="categoria"></span></td></tr>
                <tr><td valign="top"><b>Subcategor&iacute;a</b>:</td><td><span class="detalle" id="subcategoria"></span></td></tr>
                <tr><td valign="top"><b>Actividad</b>:</td><td><span class="detalle" id="actividad"></span></td></tr>
                <tr><td valign="top"><b>Descripci&oacute;n</b>:</td><td><span class="detalle" id="descripcion"></span></td></tr>
                <tr><td valign="top"><b>&Aacute;rea/Empresa Responsable</b>:</td><td><span class="detalle" id="area"></span></td></tr>
                <tr><td valign="top"><b>Persona Responsable</b>:</td><td><span class="detalle" id="persona"></span></td></tr>
                </table>
            </div>
        </div>
        <br>

        <!--En caso de existir previa programación, se muestra en este espacio-->
        <fieldset class="well the-fieldset">
        <legend class="the-legend">Programaci&oacute;n Existente</legend>
        <div id="programacion-existente">
        </div>
        </fieldset>

        <!--Areas para notificar -->
        <fieldset class="well the-fieldset">
        <legend class="the-legend">Notificaciones</legend>
        <div id="areas-notificacion">
        </div>
        </fieldset>

        <h4>Programar actividades</h4>
        <div class="row">
          <form class="form-horizontal" role="form" id="prog-guardar-programacion-form">
              <div class="col-md-4">Fecha L&iacute;mite Inicial</div>
                <div class="col-md-4">Veces a ejecutar</div>
                <div class="col-md-4">Periodo</div>

                <div class="col-md-4">
                  <div class="input-append input-group datepicker">
                        <input data-format="yyyy-MM-dd" value="<?=date('Y-m-d');?>" type="text" class="form-control required" readonly name="fecha" id="fecha">
                        <span class="input-group-addon add-on">
                          <i data-time-icon="fa fa-calendar" data-date-icon="fa fa-calendar"></i>
                        </span>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-control required" name="repeticion" id="repeticion">
                        <?php for($i=1;$i<=30;$i++):?>
                        <option value="<?=$i?>"><?=$i?></option>
                        <?php endfor?>
                    </select>
                </div>
                <div class="col-md-4">
                  <select class="form-control required" id="periodo" name="periodo">
                        <?php foreach ($periodos as $periodo): ?>
                        <option value="<?=$periodo["idcat_periodo"]?>"><?=$periodo["cat_periodo"]?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
          <!--<div class="col-md-12">
              <div class="form-group">
                  <label for="fecha" class="col-md-4 control-label">Fecha de inicio</label>
                    <div class="col-md-8">
                      <div class="input-append input-group datepicker">
                          <input data-format="yyyy-MM-dd" value="<?=date('Y-m-d');?>" type="text" class="form-control required" readonly name="fecha" id="fecha">
                          <span class="input-group-addon add-on">
                            <i data-time-icon="fa fa-calendar" data-date-icon="fa fa-calendar"></i>
                          </span>
                      </div>
                    </div>
                </div>
                <div class="form-group">
                  <label for="repeticion" class="col-md-4 control-label">Veces a ejecutar</label>
                    <div class="col-md-8">
                      <select class="form-control" name="repeticion" id="repeticion">
                          <?php for($i=1;$i<=15;$i++):?>
                              <option value="<?=$i?>"><?=$i?></option>
                            <?php endfor?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                  <label for="periodo" class="col-md-4 control-label">Periodo</label>
                    <div class="col-md-8">
                        <select class="form-control required" id="periodo" name="periodo">
                          <?php foreach ($periodos as $periodo): ?>
                            <option value="<?=$periodo["idcat_periodo"]?>"><?=$periodo["cat_periodo"]?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>-->
            <input type="hidden" id="idactividad" name="idactividad" value="0">
            </form>
        </div>
        <br><br><br><br><br><br>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-success" id="prog-guardar-programacion">Programar</button>
      </div>
    </div>
  </div>
</div>
<!-------------------   FIN MODAL PARA BAJA DE PROGRAMACIÓN    -------------->
