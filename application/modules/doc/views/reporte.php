<style>
	.cerrar_subgraficas { float:right;display:inline-block; cursor: pointer; font-size:x-small;}
	.abrir-programacion {text-decoration:underline; cursor: pointer;}
	div.mostrar_lista > div.panel {margin-bottom:0px;}
	a.lista_titulo {color:#000; cursor: pointer;}
	div.titulo_subgrafica {background-color: #f5f5f5; border-color: #ddd;}
</style>
<div class="page-header">
	<h4><a class="bom-menu" href="<?=base_url('doc/home/index')?>">
	<i class="fa fa-institution fa-2x"></i> DOCUMENTOS </a>/ <a class="bom-menu" href="<?=base_url('doc/contratos_concesion/index')?>">CONTRATOS DE CONCESI&Oacute;N </a> / REPORTE EJECUTIVO</h4>
</div>

<div class="row" id="cuerpo">
	<div class="row">
		<nav aria-label="..." class="pull-right">
			<ul class="pagination proPag">
			<li class="proyectoPrevious" data-pag="0"><a href="#"><span aria-hidden="true">&larr;</span> Anterior</a></li>
			<li class="proyectoNext" data-pag="1"><a href="#">Siguiente <span aria-hidden="true">&rarr;</span></a></li>
			</ul>
		</nav>
	</div>
<?php foreach ($proyectos as $k => $p):?>
	<div class="qwerty">
	<div class="row box panel panel-default" id="show_proyecto_<?=$k?>">
		<div class="col-md-12" id="proyecto_<?=$k?>"></div>
		<div class="col-md-12 titulo_subgrafica text-center" style="color: #333333; font-size: 20px;"></div>
		<div class="col-md-6 mostrar_vencidas"></div>
		<div class="col-md-6 mostrar_vencer"></div>
	</div>
	<div class="row mostrar_lista" id="proyecto_lista_<?=$k?>"></div>
	</div>
	<br>
<?php endforeach;?>
	<div class="row">
		<nav aria-label="..." class="pull-right">
			<ul class="pagination proPag">
			<li class="proyectoPrevious" data-pag="0"><a href="#"><span aria-hidden="true">&larr;</span> Anterior</a></li>
			<li class="proyectoNext" data-pag="1"><a href="#">Siguiente <span aria-hidden="true">&rarr;</span></a></li>
			</ul>
		</nav>
	</div>
</div>
<div class="col-md-12 panel panel-default lista hidden">
	<div class="panel-heading text-center"></div>
	<div class="panel-body">
	<nav aria-label="..." class="pull-right">
		<ul class="pagination">
		<li class="previous" data-pag="0"><a href="#"><span aria-hidden="true">&larr;</span> Anterior</a></li>
		<li class="next" data-pag="0"><a href="#">Siguiente <span aria-hidden="true">&rarr;</span></a></li>
		</ul>
	</nav>
	<table class="table table-striped tabla">
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
	<nav aria-label="..." class="pull-right">
		<ul class="pagination">
		<li class="previous" data-pag="0"><a href="#"><span aria-hidden="true">&larr;</span> Anterior</a></li>
		<li class="next" data-pag="0"><a href="#">Siguiente <span aria-hidden="true">&rarr;</span></a></li>
		</ul>
	</nav>
	</div>
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
					<tr><td>Categor&iacute;a</td><td id="categoria"></td></tr>
					<tr><td>Subcategor&iacute;a</td><td id="subcategoria"></td></tr>
					<tr><td style="width:30%;">Actividad</td><td id="actividad"></td></tr>
					<tr><td>Descripci&oacute;n</td><td id="descripcion"></td></tr>
					<tr><td>Documento Cotnractual</td><td id="documento"></td></tr>
					<tr><td>&Aacute;rea/Empresa Responsable</td><td id="empresa"></td></tr>
					<tr><td>Persona Responsable</td><td id="persona"></td></tr>
					<tr><td>Referencia Documental</td><td id="referencia"></td></tr>
					<tr><td>Detalle Referencia Documental</td><td id="detalle"></td></tr>
					<tr><td>Observaci&oacute;n/Acci&oacute;n</td><td id="observacion"></td></tr>
					<tr><td>Fecha Fin</td><td id="fin_actividad"></td></tr>
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
