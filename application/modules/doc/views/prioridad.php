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
	<table class="table table-striped table-sm" id="tabla_prioridades">
		<thead class="thead-inverse">
			<tr>
				<th>Nombre</th>
				<th>Clave</th>
				<th>Modificar</th>
				<th>Eliminar</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($prioridades as $p):?>
			<tr id="tr_<?= $p['idprioridad'] ?>">
				<th scope="row" class="td_nombre"><?= $p['nombre'] ?></th>
				<td class="td_clave"><?= $p['clave'] ?></td>
				<td ><a class="modificar_prioridad" data-idprioridad="<?= $p['idprioridad'] ?>">Modificar</a></td>
				<td><a href="<?=base_url('doc/prioridad/eliminar/'. $p['idprioridad'])?>" class="eliminar_prioridad">Eliminar</a></td>
			</tr>
		<?php endforeach;?>
		</tbody>
	</table>
</div>
<div class="row">
	<div class="row msg_view"></div>
</div>
