<script>
var postForm = function() {
	var content = $('textarea[name="respuesta"]').html($('#summernote').code());
}
</script>
<div class="page-header">
	<h5><b>
    <img src="<?=base_url('assets/img/1399334070_user.png')?>"> <a class="bom-menu" href="<?=base_url('baw/home/index')?>">BIT&Aacute;CORA DE ATENCI&Oacute;N WEB / </a><a class="bom-menu" href="<?=base_url('baw/administrar/index')?>">ADMINISTRAR /</a>
	<?php if($accion==0):?>
    <a class="bom-menu" href="<?=base_url('baw/administrar/atendiendose')?>">SOLICITUDES ATENDIENDOSE / </a>RESPUESTA
	<?php elseif($action==1):?>
    <a class="bom-menu" href="<?=base_url('baw/administrar/registrados')?>">SOLICITUDES REGISTRADAS / </a>RESPUESTA
    <?php endif;?>
    </b></h5>
</div>
<?=form_open_multipart('baw/administrar/respuesta',array('class'=>'form-horizontal','role'=>'form','id'=>'respuesta'));?>
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
                	<textarea name="respuesta" id="summernote"></textarea>
                    <!--<div class="required">Escribe el texto</div>-->
                    <?php
                            //Checar si venimos de una respuesta vacia, update
                            echo '<input type="hidden" name="accion" value="'. ($accion == 2 ? $accion : 0) .'"/>';
                    ?>
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
            <a href="<?=base_url('baw/administrar/'. ($accion == 2 ? 'consulta' : 'solicitudes_atendidas'))?>/<?=$solicitud->idsolicitud?>/0" class="btn btn-warning cancelar-state">Cancelar</a>
            <!--<button id="btn_respuesta" type="button" class="btn btn-success">Enviar Respuesta</button>-->
            <button type="submit" class="btn btn-success loading-state">Enviar Respuesta</button>
            <!--<input type="submit" class="btn btn-success" value="Enviar Respuesta">-->
        </div>
    </div>
</div>
<?=form_close();?>
<?php endforeach;?>
