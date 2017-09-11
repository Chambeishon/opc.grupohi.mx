$(document).ready(function(e) {
	//Iniciar calendario
    $('.datepicker').datetimepicker({
      pickTime: false
    });

	$('.selectpicker').selectpicker();

	$('select#act-contrato').click(function(){
	});

	$(".solo_numeros").keyup(function(){
		if ($(this).val())
		{
			$(this).val($(this).val().replace(/[^0-9]/g, ""));
		}
	});

	$('#filtro-ul').on('click','li',function(){
		filtro = $(this).attr('filtro');
		if(filtro=='filtro_id'){
			$("div#filtro_id").css("display","block");
			$("div#filtro_filtros").css("display","none");
			$('div#act-contrato-contenedor').html('<b>CONTRATO</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-contrato"></select>');
			$('div#act-categoria-contenedor').html('<b>CATEGOR&Iacute;A</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-categoria-select"></select>');
		$('div#act-subcategoria-contenedor').html('<b>SUBCATEGOR&Iacute;A</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-subcategoria-select">');
		$('div#act-estado-contenedor').html('<b>ESTADO</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-estado-select"></select>');
		$('div#act-prioridad-contenedor').html('<b>PRIORIDAD</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-prioridad-select"></select>');
			$("input#fecha_inicio").val('');
			$("input#fecha_fin").val('');
			$("#c_fechas").prop("checked", false);
			$('div#informacion').html('');
		}
		else{
			//MUESTRA CONTRATOS
			opciones = '<b>CONTRATO</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-contrato">';
			$.getJSON(base_url+'doc/consulta/contratos',function(json){
				$.each(json,function(x,y){
						opciones = opciones + '<option value="'+y.idcontrato+'">'+y.clave+'-'+y.numero_contrato+'</option>';
				});
				opciones = opciones + '</select>';
				$('#act-contrato-contenedor').html(opciones);
			}).done(function(){
					$('.selectpicker').selectpicker();
				});
			$("div#filtro_id").css("display","none");
			$("div#filtro_filtros").css("display","block");
			$("input#p_id").val('');
			$('div#informacion').html('');
		}
	});

	$("div#f_fecha").on('click','#c_fechas',function(){
		if($("#c_fechas").is(':checked'))
		{
			$("#c_fechas").prop("checked", true);
			var f = new Date();
			anio = f.getFullYear();
			mes = (f.getMonth() +1).toString();
			mes = (mes.length>1)?mes:'0'+mes;
			dia = f.getDate().toString();
			dia = (dia.length>1)?dia:'0'+dia;
			$("input#fecha_inicio").val(anio+'-'+mes+'-'+dia);
			$("input#fecha_fin").val(anio+'-'+mes+'-'+dia);
		}
		else
		{
			$("#c_fechas").prop("checked", false);
			$("input#fecha_inicio").val('');
			$("input#fecha_fin").val('');
		}
	});

	$('input#p_id').keyup(function(){
		texto=$(this).val();
		if(texto==""){
			$('div#informacion').html('');
		}
		else{
			$('div#informacion').html('<button type="button" class="btn btn-success" id="busca-tarea">Buscar</button><br/><table id="grid" style="font-size:10px;"></table>');
		}
	});

	$("div#act-contrato-contenedor").on("change","select#act-contrato",function(){
		idcontrato = $(this).val();
		$('div#informacion').html('<button type="button" class="btn btn-success" id="busca-tarea">Buscar</button><br/><table id="grid" style="font-size:10px;"></table>');
		$('div#act-categoria-contenedor').html('<b>CATEGOR&Iacute;A</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-categoria-select"></select>');
		$('div#act-subcategoria-contenedor').html('<b>SUBCATEGOR&Iacute;A</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-subcategoria-select">');
		$('div#act-estado-contenedor').html('<b>ESTADO</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-estado-select"></select>');
		$('div#act-prioridad-contenedor').html('<b>PRIORIDAD</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-prioridad-select"></select>');
		if(idcontrato==0){
		}else{
			//MUESTRA CATEGORIAS
			datos = 'idcontrato='+idcontrato;
			opciones = '<b>CATEGOR&Iacute;A</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-categoria-select">';
			$.getJSON(base_url+'doc/consulta/categorias',datos,function(json){
				$.each(json,function(x,y){
						opciones = opciones + '<option value="'+y.idcat_categoria+'">'+y.cat_categoria+'</option>';
				});
				opciones = opciones + '</select>';
				$('#act-categoria-contenedor').html(opciones);
			}).done(function(){
					$('.selectpicker').selectpicker();
				});

			//MUESTRA SUNCATEGORIAS
			opciones2 = '<b>SUBCATEGOR&Iacute;A</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-subcategoria-select">';
			$.getJSON(base_url+'doc/consulta/subcategorias',datos,function(json1){
				$.each(json1,function(x,y){
						opciones2 = opciones2 + '<option value="'+y.idcat_subcategoria+'">'+y.cat_subcategoria+'</option>';
				});
				opciones2 = opciones2 + '</select>';
				$('#act-subcategoria-contenedor').html(opciones2);
			}).done(function(){
					$('.selectpicker').selectpicker();
				});

			//MUESTRA ESTADOS
			opciones3 = '<b>ESTADO</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-estado-select">';
			$.getJSON(base_url+'doc/consulta/estados',datos,function(json2){
				$.each(json2,function(x,y){
						opciones3 = opciones3 + '<option value="'+y.idestado_actividad+'">'+y.estado_actividad+'</option>';
				});
				opciones3 = opciones3 + '</select>';
				$('#act-estado-contenedor').html(opciones3);
			}).done(function(){
					$('.selectpicker').selectpicker();
				});

			// Prioridades
			opciones4 = '<b>PRIORIDAD</b><select class="form-control letra11 selectpicker" multiple data-selected-text-format="count>0" id="act-prioridad-select">';
			$.getJSON(base_url+'doc/consulta/prioridades',datos,function(json2){
				$.each(json2,function(x,y){
						opciones4 = opciones4 + '<option value="'+y.idprioridad+'">'+y.nombre+'</option>';
				});
				opciones4 = opciones4 + '</select>';
				$('#act-prioridad-contenedor').html(opciones4);
			}).done(function(){
					$('.selectpicker').selectpicker();
				});
		}
	});

	$('div#informacion').on('click','#busca-tarea',function(){
		id = $('input#p_id').val();
		idcontrato = $('select#act-contrato').val();
		idcategoria = $('select#act-categoria-select').val();
		idsubcategoria = $('select#act-subcategoria-select').val();
		idestado = $('select#act-estado-select').val();
		idprioridad = $('select#act-prioridad-select').val();
		fecha_inicio = $('input#fecha_inicio').val();
		fecha_fin = $('input#fecha_fin').val();

		loadTable(id,idcontrato,idcategoria,idsubcategoria,idestado,fecha_inicio,fecha_fin, idprioridad);

	});

	$('div#informacion').on('click','.abrir-programacion',function(){
		idprogramacion=$(this).attr('idprogramacion');
		datos='idprogramacion='+idprogramacion;
		$.getJSON(base_url+'doc/dashboard/areas',datos,function(json0){
			areas='';
			$.each(json0,function(u,v){
				areas = areas + '<span style="font-size:10px;">'+v.nombre_area_involucrada+'</span><br>';
			});
			$('#areas').html(areas);
		}).done(function(){
			$.getJSON(base_url+'doc/dashboard/programacion',datos,function(json){
				$('#proyecto').text(json[0].nombre_proyecto);
				$('#contrato').text(json[0].numero_contrato);
				$('#categoria').text(json[0].cat_categoria);
				$('#subcategoria').text(json[0].cat_subcategoria);
				$('#descripcion_contrato').text(json[0].descripcion_contrato);
				$('#inicio_contrato').text(json[0].fecha_inicio);
				$('#fin_contrato').text(json[0].fecha_fin);
				$('#idactividad_p').text('P-'+json[0].idprogramacion);
				$('#actividad').text(json[0].nombre_actividad);
				$('#descripcion').text(json[0].descripcion_actividad);
				$('#documento').text(json[0].documento_contractual);
				$('#empresa').text(json[0].empresa_responsable);
				$('#persona').text(json[0].persona_responsable);
				$('#referencia').text(json[0].referencia_documental);
				$('#detalle').text(json[0].detalle_referencia);
				$('#observacion').text(json[0].observacion);
				/*$('.cancelar_programacion_actividad').remove();
				if(json[0].idestado_actividad!=1){
					boton = '<button idprogramacion="'+idprogramacion+'" type="button" class="btn btn-danger cancelar_programacion_actividad">Cancelar</button>';
					$(boton).insertAfter('button#cerrar_detalle');
				}*/
			}).done(function(){
				$.getJSON(base_url+'doc/dashboard/desplegar_evidencias',datos,function(json){
					$('div#detalle_acciones').html('');
					table='<table class="table table-striped table-condensed table-bordered" style="font-size:10px;"><tr><td>#</td><td>Evidencia documental</td><td>Estado</td></tr>';
					i=0;
					$.each(json,function(x,y){
						i++;
						table = table+'<tr><td>'+i+'</td><td><a target="_blank" href="'+base_url+'documents/doc/evidencias/'+y.link+'">'+y.documento+'</a></td><td>'+y.doc_estado_actividad+'</td></tr>';
					});
					table = table + '</table>';
					if(i>0){
						$('div#consultar-evidencias').html(table);
					}else{
						$('div#consultar-evidencias').html('No existen evidencias documentales');
					}
				}).done(function(){
					$('button.detalle_accion_actividad').remove();
					$.getJSON(base_url+'doc/dashboard/desplegar_botones',datos,function(json){
						if(json[0].idestado_actividad!=6){
							accion = '<table class="table table-bordered"><tr><td>Acci&oacute;n</td><td>Evidencias</td><td>Notificar</td></tr><tr><td>'+json[0].botones+'</td><td class="evi_doc">'+json[0].doc+'</td><td class="msj">'+json[0].mensaje+'</td></tr></table>';
							$('div#detalle_acciones').html(accion);
						}
					});

					$.getJSON(base_url+'doc/dashboard/desplegar_seguimiento',datos,function(json){
						tabla_seg = '<table class="table table-bordered" style="width:100%; vertical-align:top; font-size:10px;"><th>DESCRIPCI\u00d3N</th><th>FECHA</th><th>HORA</th><th>USUARIO</th>';
						$.each(json,function(u,v){
							tabla_seg = tabla_seg +'<tr><td>'+v.estado_actividad+'</td><td>'+v.fecha+'</td><td>'+v.hora+'</td><td>'+v.usuario_registra+'</td></tr>';
						});
						$('div#seguimiento').html(tabla_seg);
					});

					$('#modal-detalle').modal();
				});
				//
			});
		});
	});

	$('#modal-detalle').on('click','i.cancelar_programacion_actividad',function(){
		idprogramacion=$(this).attr('idprogramacion');
		if(confirm("\u00BF Est\u00e1 seguro que desea cancelar la actividad P-"+idprogramacion+"?")){
			datos = 'idprogramacion='+idprogramacion+'&estado=1&ruta=cancelar';
			$.getJSON(base_url+'doc/dashboard/cambiar_estado',datos,function(json){
				if(json.msg>0){
					alert('La actividad ha sido cancelada');
					$('#modal-detalle').modal('hide');
					$.getJSON(base_url+'doc/dashboard/programacion_actividad','idprogramacion='+idprogramacion,function(json){
						id = $('input#p_id').val();
						idcontrato = $('select#act-contrato').val();
						idcategoria = $('select#act-categoria-select').val();
						idsubcategoria = $('select#act-subcategoria-select').val();
						idestado = $('select#act-estado-select').val();
						idprioridad = $('select#act-prioridad-select').val();
						fecha_inicio = $('input#fecha_inicio').val();
						fecha_fin = $('input#fecha_fin').val();
						loadTable(id,idcontrato,idcategoria,idsubcategoria,idestado,fecha_inicio,fecha_fin, idprioridad);
					});
				}else{
					alert('Ha ocurrido un error, intente nuevamente');
				}
			/*$.getJSON(base_url+'doc/dashboard/programacion_actividad',datos,function(json){
						$('tr#programacion'+idprogramacion+' td#botones').html(json[0].botones);
					});*/
			});
			return true;
		}
		else
			return false;
	});

	$('#btn-enviar-notificacion').click(function(){
		datos = $('form#form-enviar-notificaciones-usuarios').serialize();
		$.getJSON(base_url+'doc/dashboard/notificacion',datos,function(json){
			if(json.msg>0){
				alert('Las notificaciones han sido enviadas');
				$('#modal-notificacion').modal('hide');
			}else{
				alert('Las notificaciones no han podido ser enviadas, intente nuevamente');
			}
		});
	});

	$('div#detalle_acciones').on('click','i.accion',function(){
		ruta = $(this).attr('ruta');
		idprogramacion = $(this).attr('id');
		estado = $(this).attr('idestado');
		datos = 'idprogramacion='+idprogramacion+'&estado='+estado+'&ruta='+ruta;
		$.getJSON(base_url+'doc/dashboard/cambiar_estado',datos,function(json){
			if(json.msg>0){
				alert('La actividad ha sido '+json.text);
				$('#modal-detalle').modal('hide');
				if(estado!=1){
					loadEvidencias(idprogramacion,estado);
					$('form#form-evidencia-documental input#idprogramacion').val(idprogramacion);
					$('form#form-evidencia-documental input#idestado').val(estado);
					loadEvidencias(idprogramacion);
				}
				$.getJSON(base_url+'doc/dashboard/programacion_actividad','idprogramacion='+idprogramacion,function(json){
					$('tr#programacion'+idprogramacion+' td#botones').html(json[0].botones);
					$('tr#programacion'+idprogramacion+' td#estado').html(json[0].estado_actividad);
				});
				id = $('input#p_id').val();
				idcontrato = $('select#act-contrato').val();
				idcategoria = $('select#act-categoria-select').val();
				idsubcategoria = $('select#act-subcategoria-select').val();
				idestado = $('select#act-estado-select').val();
				idprioridad = $('select#act-prioridad-select').val();
				fecha_inicio = $('input#fecha_inicio').val();
				fecha_fin = $('input#fecha_fin').val();
				loadTable(id,idcontrato,idcategoria,idsubcategoria,idestado,fecha_inicio,fecha_fin, idprioridad);
			}else{
				alert('Ha ocurrido un error, intente nuevamente');
			}
		})
	});

	$('div#detalle_acciones').on('click','i.doc',function(){
		idprogramacion = $(this).attr('id');
		idestado = $(this).attr('idestado');
		idprgramacion = $('input#idprogramacion').val(idprogramacion);
		idestado = $('input#idestado').val(idestado);
		loadEvidencias(idprogramacion);
	});

	$('div#detalle_acciones').on('click','i.not',function(){
		idprogramacion = $(this).attr('id');
		idestado = $(this).attr('idestado');
		datos='idprogramacion='+idprogramacion;
		$("#not-idprogramacion").val(idprogramacion);
		$.getJSON(base_url+'doc/dashboard/areas',datos,function(json0){
			areas='';
			$.each(json0,function(u,v){
				areas = areas + '<span id="area'+v.idarea_involucrada+'" style="font-size:10px; font-weight:bold;">'+v.nombre_area_involucrada+'</span><br>';
				$.getJSON(base_url+'doc/dashboard/areas_usuarios','idarea='+v.idarea_involucrada,function(json1){
					usuarios='';
					$.each(json1,function(x,y){
						usuarios = usuarios + '<div class="checkbox" style="font-size:10px; margin-left:50px;"><label><input type="checkbox" value="'+y.correo_usuario+'" name="correos[]">'+y.usuario+' <strong>&nbsp;&nbsp;NIVEL:'+y.idnivel+'</strong></label></div>';

					});
					$(usuarios).insertAfter('span#area'+v.idarea_involucrada);
				});
			});
			$('#enviar-areas').html(areas);
		}).done(function(){
			$.getJSON(base_url+'doc/dashboard/programacion',datos,function(json){
				$('#enviar-idactividad_p').text('P-'+json[0].idprogramacion);
				$('#enviar-actividad').text(json[0].nombre_actividad);
				$('#enviar-descripcion').text(json[0].descripcion_actividad);
				$('#enviar-documento').text(json[0].documento_contractual);
				$('#enviar-empresa').text(json[0].empresa_responsable);
				$('#enviar-persona').text(json[0].persona_responsable);
				$('#enviar-referencia').text(json[0].referencia_documental);
				$('#enviar-detalle').text(json[0].detalle_referencia);
				$('#enviar-observacion').text(json[0].observacion);
			});
		});
		$('#modal-notificacion').modal();
	});

	$('input#guardar_evidencia').click(function(){
		contenedor='<input type="submit" value="Procesando..." class="btn btn-success" disabled id="btn-copia" >';
		$(contenedor).insertAfter('#guardar_evidencia');
		$('#guardar_evidencia').hide();
	});

	var options = {
    	beforeSend: function(){
        	$("#progress").hide();
        	//clear everything
        	$("#bar").width('0%');
        	$("#message").html("");
        	$("#percent").html("0%");
    	},
    	uploadProgress: function(event, position, total, percentComplete){
        	$("#bar").width(percentComplete+'%');
        	$("#percent").html(percentComplete+'%');
 		},
    	success: function(){
        	$("#bar").width('100%');
        	$("#percent").html('100%');
    	},
		complete: function(response){
			//$("#message").html("<font color='green'>"+response.responseText+"</font>");
			$('#btn-copia').val('Guardar').hide();
			$('#guardar_evidencia').val('Guardar').show();
			alert(response.responseText);
			idprgramacion = $('input#idprogramacion').val();
			idestado = $('input#idestado').val();
			loadEvidencias(idprogramacion);
			$fileupload=$("#form-evidencia-documental #exampleInputFile");
			$fileupload.replaceWith($fileupload.clone(true));
		},
		error: function(){
			//$("#message").html("<font color='red'> ERROR: Intente nuevamente</font>");
			alert('Ocurrio un error, intente nuevamente');
		}
	};
	$("#form-evidencia-documental").ajaxForm(options);

	$('div#detalle-evidencia-documental').on('click','a.eliminar-documento',function(){
		iddocumento = $(this).attr('id');
		idprogramacion = $('input#idprogramacion').val();
		datos = 'iddocumento='+iddocumento;
		$.getJSON(base_url+'doc/dashboard/eliminar_evidencia',datos,function(json){
			if(json.msg>0){
				alert('La evidencia ha sido eliminada');
				loadEvidencias(idprogramacion);
			}else{
				alert('Ha ocurrido un error, intente nuevamente');
			}
		});
	});

});

function loadTable(id,idcontrato,idcategoria,idsubcategoria,idestado,fecha_inicio,fecha_fin, idprioridad)
{
	datos='id='+id+'&'+'idcontrato='+idcontrato+'&idcategoria='+idcategoria+'&idsubcategoria='+idsubcategoria+'&idestado='+idestado+'&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin+'&idprioridad='+idprioridad;
	$.getJSON(base_url+'doc/consulta/desplegar',datos,function(json){console.log(json);
		$(function () {
            $("#grid").igGrid({
                width: '100%',
                columns: [
					{ headerText: "ID", key: "idprogramacion", dataType: "string", width: "5%" },
					{ headerText: "CONTRATO", key: "contrato", dataType: "string", width: "10%" },
					{ headerText: "CATEGORIA", key: "cat_categoria", dataType: "string", width: "20%" },
					{ headerText: "SUBCATEGORIA", key: "cat_subcategoria", dataType: "string", width: "20%" },
					{ headerText: "ACTIVIDAD", key: "nombre_actividad", dataType: "string", width: "20%" },
					{ headerText: "DESCRIPCION", key: "descripcion_actividad", dataType:"string", width: "30%" },
					{ headerText: "FECHA LIMITE", key: "fecha", dataType:"string", width: "10%" },
					{ headerText: "ESTADO", key: "estado_actividad", dataType: "string", width: "15%"},
					{ headerText: "PRIORIDAD", key: "prioridad_clave", dataType: "string", width: "10%"}
                ],

				autofitLastColumn: false,
    			autoGenerateColumns: false,
    			dataSource: json,
				dataRendered: function (evt, ui) {
					//ui.owner.element.find("tr td:nth-child(8)").css("text-align", "center");
					ui.owner.element.find("tr td").css("vertical-align", "top");
				},
    			features: [
					{
                        name: "Resizing"
                    },
				    {
                        name: "Sorting",
                        type: "local",
                        mode: "multi"
                    },
                    {
                        name: "Filtering",
                        type: "local",
                        mode: "advanced"
                    },
                    {
                        name: "Hiding"
                    },
					{
                        name: "Paging",
                        type: "local",
                        pageSize: 10
                    },
                    {
                        name: "ColumnMoving"
                    },
					{
                        name: "Selection"
                    },
					{
                        name: "Resizing"
                    }
                ]
            });
        });
	});
}


function loadEvidencias(idprogramacion)
{
	datos = 'idprogramacion='+idprogramacion;
	$.getJSON(base_url+'doc/dashboard/desplegar_evidencias',datos,function(json){
		table='<table class="table table-striped table-condensed table-bordered"><tr><td>#</td><td>Evidencia documental</td><td>Estado</td><td>Acci&oacute;n</td></tr>';
			i=0;
			$.each(json,function(x,y){
				i++;
				table = table+'<tr><td>'+i+'</td><td><a target="_blank" href="'+base_url+'documents/doc/evidencias/'+y.link+'">'+y.documento+'</a></td><td>'+y.doc_estado_actividad+'</td><td class="td_eliminar" align="center">'+y.eliminar+'</td></tr>';
			});
		table = table + '</table>';
		if(i>0){
			$('div#detalle-evidencia-documental').html(table);
		}else{
			$('div#detalle-evidencia-documental').html('No existen evidencias documentales');
		}
	}).done(function(){
		$('div#modal-evidencia-documental').modal();
		$fileupload=$("#form-evidencia-documental #exampleInputFile");
		$fileupload.replaceWith($fileupload.clone(true));
	});
}
