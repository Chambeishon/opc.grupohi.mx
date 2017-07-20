<div class="page-header">
	<i class="fa fa-building fa-2x"></i> <span style="font-size:18px;">BREADCRUMB AQUÍ</span>
</div><div class="row">
		<div class="col-md-6" ></div>
		<div class="col-md-6" >
			<select class="form-control" id="dropdown">
				<?php foreach ($proyectos as $proyecto):?>
				<option value="<?=$proyecto['idproyecto']?>"><?=$proyecto['nombre_proyecto']?></option>
				<?php endforeach;?>
			</select>
		</div>
	</div>
<div class="row cuerpo">

	<?php foreach ($proyectos as $proyecto):?>
		<div class="row box" id="show_proyecto_<?=$proyecto['idproyecto']?>">
			<div class="col-md-6" id="proyecto_<?=$proyecto['idproyecto']?>"></div>
			<div class="col-md-6 mostrar_categoria"></div>
			<hr>
		</div>
	<?php endforeach;?>
</div>