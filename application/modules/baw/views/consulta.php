<div class="page-header">
	<h5><b>
    <img src="<?=base_url('assets/img/1399336774_View_Details.png')?>"> <a class="bom-menu" href="<?=base_url('baw/home/index')?>">BIT&Aacute;CORA DE ATENCI&Oacute;N WEB / </a> <a class="bom-menu" href="<?=base_url('baw/administrar/index')?>">ADMINISTRAR / </a><a class="bom-menu" href="<?=base_url('baw/administrar/atendidos')?>">SOLICITUDES ATENDIDAS / CONSULTA</a></b></h5>
</div>
<?php
	foreach($solicitudes as $solicitud):
		if (empty(trim($solicitud->respuesta)) || empty(trim(strip_tags($solicitud->respuesta)))):
			echo '
		<form action="'. base_url('baw/administrar/responder_solicitud') .'" class="form-horizontal" role="form" method="post" accept-charset="utf-8">
		<div class="row" align="center">
			<div class="alert alert-danger">
				<h4>La respuesta se encuentra vacia</h4>
				<input type="hidden" id="idsolicitud" name="idsolicitud" value="'. $solicitud->idsolicitud .'"/>
        <input type="hidden" id="accion" name="accion" value="2"/>
				<input type="submit" value="Volver a responder solicitud" class="btn btn-danger">
			</div>
		</div>
	</form>';
		endif;
	endforeach; ?>
<?=form_open('',array('class'=>'form-horizontal','role'=>'form'));?>
<?php foreach($solicitudes as $solicitud):?>
<div class="row">
	<div class="col-md-6">
    	<div class="square letra10">
        	<h5><strong>DATOS DE LA SOLICITUD</strong></h5>
            <br>
        	<div class="form-group">
            	<label for="ticket" class="col-sm-3 control-label">Folio: </label>
            		<div class="col-sm-9">
              			<label class="text-danger"><h5><?=$solicitud->folio?></h5></label>
            		</div>
          	</div>
            <div class="form-group">
            	<label for="ticket" class="col-sm-3 control-label">Fecha / Hora: </label>
            		<div class="col-sm-9">
                                <input type="text" class="form-control letra10" value="<?=$solicitud->fecha .' '. $solicitud->hora?>" readonly />
            		</div>
          	</div>
          	<div class="form-group">
            	<label for="solicitante" class="col-sm-3 control-label">Escrito por: </label>
            	<div class="col-sm-9">
              		<input type="text" class="form-control letra10" value="<?=$solicitud->nombre_solicitante?>" readonly />
              		<br>
              		<input type="text" name="correo" id="correo" class="form-control letra10" value="<?=$solicitud->mail_solicitante?>"  disabled />
            	</div>
          	</div>
          	<div class="form-group">
            	<label for="tipo-solicitud" class="col-sm-3 control-label">Tipo:</label>
            	<div class="col-sm-9" >
              		<input type="text" class="form-control letra10" value="<?=$solicitud->tipo_solicitud?>" readonly />
            	</div>
          	</div>
          	<div class="form-group">
            	<label for="tema" class="col-sm-3 control-label">Tema:</label>
            	<div class="col-sm-9">
              		<input type="text" class="form-control letra10" value="<?=$solicitud->tema_solicitud?>" readonly />
            	</div>
          	</div>
          	<div class="form-group">
            	<label for="descripcion" class="col-sm-3 control-label">Descripci&oacute;n:</label>
            	<div class="col-sm-9">
              		<textarea class="form-control letra10" rows="10" readonly><?=$solicitud->mensaje_solicitud?></textarea>
            	</div>
          	</div>
        </div>
    </div>
    <div class="col-md-6">
    	<div class="square letra10">
        	<h5><strong>RESPUESTA</strong></h5>
            <br>
        	<div class="form-group">
            	<label for="respondio" class="col-sm-3 control-label">Respondio:</label>
            	<div class="col-sm-9">
              		<input type="text" class="form-control" value="<?=$solicitud->usuario_respuesta?>" readonly />
            	</div>
          	</div>
            <div class="form-group">
            	<label for="respondio" class="col-sm-3 control-label">Respuesta:</label>
            	<div class="col-sm-9">
              		<?=$solicitud->respuesta?>
            	</div>
          	</div>
            <hr>
            <h5><strong>SOLICITUD DE INFORMACI&Oacute;N</strong></h5>
            <div class="form-group">
            	<?php if(sizeof($preguntas)==0):?>
                	<div class="col-sm-12"><label class="control-label">No se ha hecho solicitud de informaci&oacute;n</label></div>
                <?php endif;?>
            	<?php foreach($preguntas as $pregunta):?>
                	<div class="col-sm-12"><label class="control-label">Tema: </label> <?=$pregunta->tema?> <?=$pregunta->fecha_solicitud?> | <?=$pregunta->hora_s?>
                    <!--<?=$pregunta->boton?>-->
                    </div>
                    <div class="col-sm-12"><label class="control-label">Solicitud: </label> <?=$pregunta->comentario?></div>
                    <div class="col-sm-12"><label class="control-label">Respuesta: </label></div>
                    <div class="col-sm-12"><?=$pregunta->link?></div>
                    <?php $respuestas=$this->administrar_model->desplega_respuesta($pregunta->idsolicitud_datos);?>
                    <hr>
                    <?php foreach($respuestas as $respuesta):?>
                        <!-- Modal -->
                        <div class="modal fade" id="myModal<?=$respuesta["idsolicitud_datos"]?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel">TEMA <?=$respuesta["tema"]?></h4>
                              </div>
                              <div class="modal-body">
                                <?php foreach($respuestas as $respuesta2):?>
									<?=$respuesta2["titulo"]?>
                                    <br>
									<?=$respuesta2["respuesta"]?>
                                    <hr>
                                <?php endforeach;?>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                              </div>
                            </div>
                          </div>
                        </div>
                    <?php endforeach;?>
                <?php endforeach?>
          	</div>
        </div>
    </div>
</div>
<?php endforeach;?>
<div class="row">
	<div class="col-md-10"></div>
    <div class="col-md-2"><?=$siguiente?><?=$anterior?></div>
</div>
<br>
<div class="row" align="center">
<a href="<?=base_url('baw/administrar/atendidos')?>" class="btn btn-success">Aceptar</a>
</div>
<?=form_close();?>
