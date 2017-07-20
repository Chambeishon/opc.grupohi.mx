<style>
	.sr .fooReveal { visibility: hidden; }
</style>
<div class="page-header">
	<h4><a class="bom-menu" href="<?=base_url('doc/home/index')?>">
	<i class="fa fa-institution fa-2x"></i> DOCUMENTOS </a>/ <a class="bom-menu" href="<?=base_url('doc/contratos_concesion/index')?>">CONTRATOS DE CONCESI&Oacute;N </a> / REPORTE</h4>
</div>

<div class="row" id="cuerpo">
<?php foreach ($proyectos as $k => $p):?>
	<div class="row box" id="show_proyecto_<?=$k?>">
		<div class="col-md-12" id="proyecto_<?=$k?>"></div>
		<div class="col-md-6 mostrar_vencidas"></div>
		<div class="col-md-6 mostrar_vencer"></div>
	</div>
	<div class="row mostrar_lista" id="proyecto_lista_<?=$k?>"></div>
	<hr>
<?php endforeach;?>
</div>
<table class="table table-striped lista hidden">
	<thead>
		<tr>
			<th>#</th>
			<th>Actividad</th>
			<th>Descripci&oacute;n</th>
			<th>Fecha L&iacute;mite</th>
			<th>Estado</th>
		</tr>
	</thead>
	<tbody>
	</tbody>
</table>
