<style>
	.modificar_prioridad {text-decoration:underline; cursor: pointer;}
</style>
<div class="page-header">
	<h4><a class="bom-menu" href="<?=base_url('doc/home/index')?>">
    <i class="fa fa-institution fa-2x"></i> DOCUMENTOS </a>/ <a class="bom-menu" href="<?=base_url('doc/prioridad/index')?>">CONTRATOS DE CONCESI&Oacute;N </a> / ADMINISTRAR PRIORIDADES</h4>
</div>
<div class="row">
	<div class="panel panel-default">
		<div class="panel-heading titulo"><h4>Agregar prioridad</h4></div>
		<div class="panel-body">
			<form class="form-horizontal guardar_prioridad" action="<?=base_url('doc/prioridad/guardar/')?>">
				<div class="form-group row">
					<label for="nombre" class="col-md-3 control-label">Nombre</label>
					<div class="col-md-3">
						<input type="text" class="form-control prioridad_nombre" value="" name="nombre">
					</div>
					<label for="clave" class="col-md-1 control-label">Clave</label>
					<div class="col-md-2">
						<input type="text" class="form-control prioridad_clave" name="clave" value="">
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-default boton_form" >Agregar</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<h4>Prioridades agregadas</h4>
	<table class="table table-striped table-condensed table-sm" id="tabla_prioridades">
		<thead class="thead-inverse">
			<tr>
				<th>Nombre</th>
				<th>Clave</th>
				<th>Acción</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($prioridades as $p):?>
			<tr id="tr_<?= $p['idprioridad'] ?>">
				<th scope="row" class="td_nombre"><?= $p['nombre'] ?></th>
				<td class="text-center td_clave"><?= $p['clave'] ?></td>
				<td class="text-center">
					<a class="btn btn-warning btn-xs modificar_prioridad" data-idprioridad="<?= $p['idprioridad'] ?>" data-toggle="modal" data-target="#modal-modificar"><i class="fa fa-edit" aria-hidden="true" title="Modificar"></i></a>
					<a href="<?=base_url('doc/prioridad/eliminar/'. $p['idprioridad'])?>" class="btn btn-danger btn-xs eliminar_prioridad"><i class="fa fa-trash-o" aria-hidden="true" title="Eliminar"></i></a>
				</td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</div>
<div class="row">
	<div class="row msg_view"></div>
</div>
<div class="modal fade" id="modal-modificar" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title">Modificar prioridad</h4>
	      </div>
	      <div class="modal-body">
	      	<form class="form-horizontal form_modificar_prioridad" action="<?=base_url('doc/prioridad/guardar/')?>">
				<div class="form-group row">
					<label for="nombre" class="col-md-3 control-label">Nombre</label>
					<div class="col-md-3">
						<input type="text" class="form-control prioridad_nombre" value="" name="nombre">
					</div>
					<label for="clave" class="col-md-1 control-label">Clave</label>
					<div class="col-md-2">
						<input type="text" class="form-control prioridad_clave" name="clave" value="">
					</div>
				</div>
			</form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	        <button type="button" class="btn btn-primary guardar_modificacion">Modificar</button>
	      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
