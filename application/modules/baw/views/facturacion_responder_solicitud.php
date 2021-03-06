<script>
var postForm = function() {
	var content = $('textarea[name="respuesta"]').html($('#summernote').code());
} 
</script>
<div class="page-header">
	<h5><b>
    <img src="<?=base_url('assets/img/1399334087_time.png')?>"> <a class="bom-menu" href="<?=base_url('baw/home/index')?>">BIT&Aacute;CORA DE ATENCI&Oacute;N WEB / </a><a class="bom-menu" href="<?=base_url('baw/facturacion/index')?>"> SOLICITUD DE FACTURACI&Oacute;N /</a>
	<?php if($accion==0):?>
    <a class="bom-menu" href="<?=base_url('baw/facturacion/atendiendose')?>">SOLICITUDES ATENDIENDOSE / </a>RESPUESTA
    <?php else:?>
    <a class="bom-menu" href="<?=base_url('baw/facturacion/registrados')?>">SOLICITUDES REGISTRADAS / </a>RESPUESTA
    <?php endif;?>
    </b></h5>
</div>
<?=form_open_multipart('baw/facturacion/respuesta',array('class'=>'form-horizontal','role'=>'form','id'=>'respuesta'));?>
<?php foreach($solicitudes as $solicitud):?>          
<div id="mensaje_respuesta">
    <div class="row">
        <div class="col-md-12">
            <div class="square">
                <h5><strong>Por favor llene todos los campos</strong></h5>            
                <br>
              <div class="form-group">
                <label for="registro-plaza" class="col-sm-1 control-label">Folio: </label>
                <div class="col-sm-11">
                  <label class="text-danger"><h4><?=$solicitud->folio?></h4></label>
                </div>
              </div>
              <div class="form-group">
                <label for="respuesta" class="col-sm-1 control-label">Respuesta: </label>
                <div class="col-sm-11">
                	<textarea name="respuesta" id="summernote"><?=$respuesta[0]->respuesta_automatica_cuerpo?></textarea>
                    <!--<div class="required">Escribe el texto</div>-->               
                </div>
              </div>
              <div class="form-group">
              	<label for="respuesta" class="col-sm-1 control-label">Archivos: </label>
                <div class="col-sm-11">
                  <input type="file" name="userfile[]" id="userfile" class="fileUpload" multiple>              
                </div>
              </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="solicitud" name="solicitud" value="<?=$solicitud->idsolicitud?>" />
    <br>
    <div class="row" align="center">
        <div class="col-md-12">
            <a href="<?=base_url('baw/facturacion/solicitudes_atendidas')?>/<?=$solicitud->idsolicitud?>/0" class="btn btn-warning cancelar-state">Cancelar</a>
            <!--<button id="btn_respuesta" type="button" class="btn btn-success">Enviar Respuesta</button>-->
            <button type="submit" class="btn btn-success loading-state">Enviar Respuesta</button>
            <!--<input type="submit" class="btn btn-success" value="Enviar Respuesta">-->
        </div>
    </div>
</div>
<?=form_close();?>
<?php endforeach;?>
