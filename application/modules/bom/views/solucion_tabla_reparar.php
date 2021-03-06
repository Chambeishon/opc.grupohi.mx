<script src="<?=base_url('assets/js/bootstrap-datetimepicker.min.js')?>"></script>
<script>
    $('.datetimepicker1').datetimepicker({
      pickTime: false
    });
</script>
<br>
Indique el equipo a reparar:
<a class="btn btn-success btn-sm pull-right agregar_equipo"><i class="fa fa-plus"></i></a>
<table class="table table-striped table-bordered table-condensed" style="font-size:10px;">
	<tr>
    	<th>Equipo</th>
    	<th>Marca</th>
        <th>Modelo</th>
        <!--<th>Capacidad</th>
        <th>Serie</th>-->
        <th>Motivo</th>
        <th>Destino</th>
        <th style="width:150px;">Fecha de Regreso</th>
        <th>&nbsp;</th>
    </tr>
    <tr class="contenedor_equipo" n="1" id="contenedor_equipo1">
    	<td>
        	<select n="1" name="equipo1" class="form-control input-sm required combo-equipo">
            	<option value="0">-SELECCIONE-</option>
            <?php foreach ($equipos as $equipo):?>
            	<option value="<?=$equipo["idactivo"]?>">
					<?=$equipo["nombre_equipo"]?>(<?=$equipo["serie"]?>)
                </option>
            <?php endforeach;?>
            </select>
        </td>
        <td><input type="text" name="marca1" class="form-control input-sm required"></td>
        <td><input type="text" name="modelo1" class="form-control input-sm required"></td>
        <!--<td><input type="text" name="capacidad1" class="form-control input-sm"></td>
        <td><input type="text" name="serie1" class="form-control input-sm required"></td>-->
        <!--<td><input type="text" name="motivo1" class="form-control input-sm"></td>-->
        <td><textarea name="motivo1" class="form-control required" style="height:30px;"></textarea></td>
        <td><input type="text" name="destino1" class="form-control input-sm required"></td>
        <td>
        	<div class="input-append input-group datetimepicker1">
                        <input data-format="yyyy-MM-dd" value="<?=date('Y-m-d');?>" type="text" class="form-control" readonly name="fecha1">
                        <span class="input-group-addon add-on">
                        <i data-time-icon="fa fa-calendar" data-date-icon="fa fa-calendar">
                          </i>
                          </span>
                    </div>
        </td>
        <td><a class="btn btn-danger btn-sm eliminar_reparar" n="1"><i class="fa fa-minus"></i></a></td>
    </tr>
</table>