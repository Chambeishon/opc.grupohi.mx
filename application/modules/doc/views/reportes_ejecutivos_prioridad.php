<div class="page-header">
	<h4><a class="bom-menu" href="<?=base_url('doc/home/index')?>">
	<i class="fa fa-institution fa-2x"></i> DOCUMENTOS </a>/ <a class="bom-menu" href="<?=base_url('doc/contratos_concesion/index')?>">CONTRATOS DE CONCESI&Oacute;N </a>/ <a class="bom-menu" href="<?=base_url('doc/administracion/index')?>">ADMINISTRACI&Oacute;N </a> / REPORTES EJECUTIVOS PRIORIDAD</h4>
</div>
<?=$mensaje?>
<div class="row">
	<div class="col-md-6">
		<h4>Seleccionar proyecto:</h4>
	</div>
	<div class="col-md-6">
		<select class="form-control proyectos" id="fecha" name="periodo">
			<?php foreach ($proyectos as $id => $proyecto):?>
			<option value="<?=$id?>" ><?=$proyecto['nombre']?></option>
			<?php endforeach;?>
		</select>
	</div>
</div>
<?php foreach ($proyectos as $id => $proyecto):?>
<div class="row box" id="proyecto_<?=$id?>">
	<br>
	<div class="panel panel-default">
		<div class="panel-heading"><?= $proyecto['nombre']?></div>
		<div class="panel-body">
			<div class="row">
			<?php foreach ($proyecto['params'] as $tipo => $params):?>
				<div class="col-md-6">
					<h4>Agregar nuevo par&aacute;metro para actividades <?= $tipo == 1 ? 'vencidas' : 'por vencer' ?></h4>
					<form class="form-horizontal guardar_rango" action="<?=base_url('doc/reportes_ejecutivos_prioridad/guardar/')?>">
						<div class="col-md-8">
							<!-- Periodo -->
							<div class="form-group">
								<label for="periodo">Periodo</label>
								<select class="form-control" id="fecha" name="periodo">
									<?php foreach ($periodos as $k => $v):?>
									<option value="<?=$k?>" ><?=$v?></option>
									<?php endforeach;?>
								</select>
							</div>
							<!-- Prioridad -->
							<div class="form-group">
								<label for="idprioridad">Prioridad</label>
								<select class="form-control" name="idprioridad">
									<?php foreach ($prioridades as $v):?>
									<option value="<?=$v['idprioridad']?>" ><?=$v['nombre']?></option>
									<?php endforeach;?>
								</select>
							</div>
							<!-- Campos -->
							<div class="form-group row">
								<label for="rangos[de]" class="col-md-1 control-label">De</label>
								<div class="col-md-3">
									<input type="text" class="form-control" value="" name="rangos[de]">
								</div>
								<label for="rangos[a]" class="col-md-1 control-label">A</label>
								<div class="col-md-3">
									<input type="text" class="form-control" name="rangos[a]" value="">
								</div>
								<input type="hidden" name="tipo" value="<?= $tipo?>">
								<input type="hidden" name="idproyecto" value="<?= $id?>">
								<button type="submit" class="btn btn-default" >Agregar</button>
							</div>
						</div>
					</form>
					<div class="col-md-10">
						<h4>Fechas <?= $tipo == 1 ? 'vencidas' : 'por vencer' ?></h4>
						<table class="table table-striped table-sm" id="tabla_<?=$id . "_". $tipo ?>">
							<thead class="thead-inverse">
								<tr>
									<th>Rango</th>
									<th>Prioridad</th>
									<th>Periodo</th>
									<th>Acción</th>
								</tr>
							</thead>
							<tbody>
							<?php foreach ($params as $pa):?>
								<tr>
									<th scope="row" class="rango"><?= $pa['rango_inicial'] ?> - <?= $pa['rango_final'] ?></th>
									<td class="text-center"><?= $prioridades[$pa['idprioridad']]['nombre'] ?></td>
									<td class="text-center"> <?= $periodos[$pa['periodo']] ?></td>
									<td class="text-center">
										<a href="<?=base_url('doc/reportes_ejecutivos_prioridad/eliminar/'. $pa['idreporte_prioridad'])?>" class="btn btn-danger btn-xs eliminar_rango"><i class="fa fa-trash-o" aria-hidden="true" title="Eliminar"></i></a>
									</td>
								</tr>
							<?php endforeach;?>
							</tbody>
						</table>
					</div>
				</div>
			<?php endforeach;?>
			</div>
		</div>
	</div>
	<br>
</div>
<?php endforeach;?>
<div class="row">
	<div class="row msg_view"></div>
	<div class="panel panel-warning">
		<div class="panel-body">Favor de llenar los rangos de manera consecutiva. 0 indica la fecha a partir de Hoy</div>
	</div>
</div>
<div class="row"><a class="btn btn-primary" href="<?=base_url('doc/reporte_prioridad/index')?>" role="button">Ver reporte</a></div>
<br>
