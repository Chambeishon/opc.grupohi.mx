$(document).ready(function(e) {

	var f = new Date();
	anio = f.getFullYear();
	mes = (f.getMonth() +1).toString();
	mes = (mes.length>1)?mes:'0'+mes;
	dia = f.getDate().toString();
	dia = (dia.length>1)?dia:'0'+dia;
	fecha=$("#fecha_fin").val();

	$(".date1").datetimepicker({pickTime:false}).on("changeDate",function(e){
		sel=$("#fecha_inicio").val();
		hoy =anio+'-'+mes+'-'+dia;
	});

	$(".date2").datetimepicker({pickTime:false}).on("changeDate",function(e){
		sel=$("#fecha_fin").val();
		hoy =anio+'-'+mes+'-'+dia;
	});

	var cargando='<div style="font-size:12px;" align="center"><i class="fa fa-spinner fa-spin"></i> Cargando</div>';

	$("button#actualizar").click(function(){
		$("div.proyecto-inicio").html(cargando);
		var fecha_inicio = $('input#fecha_inicio').val();
		var fecha_fin = $('input#fecha_fin').val();
		var historico='';
		datos = 'fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin;
		$.getJSON(base_url+'doc/dashboard_hist/proyectos',datos,function(json){
			$.each(json,function(x,y){
				historico = historico + '<li class="proyectos"><i class="fa fa-plus-square-o abrir-proyecto" id="'+y.idproyecto+'" idproyecto="'+y.idproyecto+'"></i><span style="color:'+y.color+'"> '+y.nombre_proyecto+'</span><i class="fa fa-circle pull-right" style="color:'+y.color+'"></i><div class="contratos proyecto'+y.idproyecto+'" style="display:none;"></div></li>';
			});
			$('div.proyecto-inicio').html(historico);
		});

	});

	$('div.proyecto-inicio').on('click','i.abrir-proyecto',function(){
		idproyecto = $(this).attr('idproyecto');
		var fecha_inicio = $('input#fecha_inicio').val();
		var fecha_fin = $('input#fecha_fin').val();
		datos = 'idproyecto='+idproyecto+'&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin;
		contratos = '';
		if($('li.proyectos i#'+idproyecto).hasClass('fa-plus-square-o')){
			$('.proyecto'+idproyecto).html(cargando).toggle();
			$.getJSON(base_url+'doc/dashboard_hist/contratos',datos,function(json){
				$.each(json,function(x,y){
					contratos = contratos + '<li style="margin-left:20px;"><i class="fa fa-plus-square-o abrir-contratos" idcontrato="'+y.idcontrato+'"></i> <span style="color:'+y.color+'">CONTRATO: '+y.numero_contrato+'</span> <i class="fa fa-circle pull-right" style="color:'+y.color+'"></i><div class="categorias contrato'+y.idcontrato+'" style="display:none"></div></li>';
				});
			$('.proyecto'+idproyecto).html(contratos);
			});
			$('li.proyectos i#'+idproyecto).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
		}else{
			$('.proyecto'+idproyecto).toggle();
			$('li.proyectos i#'+idproyecto).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
		}
	});

	$('div.proyecto-inicio').on('click','div.contratos i.abrir-contratos',function(){
		var idcontrato = $(this).attr('idcontrato');
		var fecha_inicio = $('input#fecha_inicio').val();
		var fecha_fin = $('input#fecha_fin').val();
		datos = 'idcontrato='+idcontrato+'&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin;
		categorias='';
		if($(this).hasClass('fa-plus-square-o')){
			$('.contrato'+idcontrato).html(cargando).toggle();
			$.getJSON(base_url+'doc/dashboard_hist/categorias',datos,function(json){
				$.each(json,function(x,y){
					categorias = categorias + '<li style="margin-left:40px;background:#DFDFD0"><i class="fa fa-plus-square-o abrir-categorias" idcategoria="'+y.idcat_categoria+'" idcontrato="'+idcontrato+'"></i> <span style="color:'+y.color+'">'+y.cat_categoria+'</span> <i class="fa fa-circle pull-right" style="color:'+y.color+'"></i><div class="subcategorias categoria'+idcontrato+y.idcat_categoria+'" style="display:none"></div></li>';
				});
			$('.contrato'+idcontrato).html(categorias);
			});
			$(this).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
		}else{
			$('.contrato'+idcontrato).toggle();
			$(this).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
		}
	});


	$('div.proyecto-inicio').on('click','div.contratos i.abrir-categorias',function(){
		var idcategoria = $(this).attr('idcategoria');
		var idcontrato = $(this).attr('idcontrato');
		var fecha_inicio = $('input#fecha_inicio').val();
		var fecha_fin = $('input#fecha_fin').val();
		datos = 'idcategoria='+idcategoria+'&idcontrato='+idcontrato+'&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin;
		subcategorias='';
		if($(this).hasClass('fa-plus-square-o')){
			$('.categoria'+idcontrato+idcategoria).html(cargando).toggle();
			$.getJSON(base_url+'doc/dashboard_hist/subcategorias',datos,function(json){
				$.each(json,function(x,y){
					subcategorias = subcategorias + '<li style="margin-left:40px;background:#F0F0E1"><i class="fa fa-plus-square-o abrir-subcategorias" idcategoria="'+idcategoria+'" idsubcategoria="'+y.idcat_subcategoria+'" idcontrato="'+idcontrato+'"></i> <span style="color:'+y.color+'">'+y.cat_subcategoria+'</span> <i class="fa fa-circle pull-right" style="color:'+y.color+'"></i><div class="actividades subcategoria'+idcontrato+idcategoria+y.idcat_subcategoria+'" style="display:none"></div></li>';
				});
			$('.categoria'+idcontrato+idcategoria).html(subcategorias);
			});
			$(this).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
		}else{
			$('.categoria'+idcontrato+idcategoria).toggle();
			$(this).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
		}
	});

	$('div.proyecto-inicio').on('click','div.contratos i.abrir-subcategorias',function(){
		var idsubcategoria = $(this).attr('idsubcategoria');
		var idcategoria = $(this).attr('idcategoria');
		var idcontrato = $(this).attr('idcontrato');
		var fecha_inicio = $('input#fecha_inicio').val();
		var fecha_fin = $('input#fecha_fin').val();
		datos = 'idcategoria='+idcategoria+'&idcontrato='+idcontrato+'&idsubcategoria='+idsubcategoria+'&fecha_inicio='+fecha_inicio+'&fecha_fin='+fecha_fin;
		actividades='';
		leyenda='';
		if($(this).hasClass('fa-plus-square-o')){
			$('.subcategoria'+idcontrato+idcategoria+idsubcategoria).html(cargando).toggle();

			$.getJSON(base_url+'doc/dashboard_hist/actividades',datos,function(json){
				actividades = actividades + '<table class="table table-condensed table-bordered table-striped" style="font-size:10px;margin-bottom: 5px;"><tr><th style="width:5%">Id</th><th style="width:20%">Actividad</th><th>Descripci&oacute;n</th><th style="width:10%;">Fecha L&iacute;mite</th><th style="width:10%">Prioridad</th><th style="width:10%">Estado</th><th style="width:5%">Acci&oacute;n</th><th style="width:5%">Evidencias</th><th style="width:5%">Notificar</th><th style="width:5%">Anotaciones</th></tr>';
				$.each(json,function(x,y){
					actividades = actividades + '<tr id="programacion'+y.idprogramacion+'" style="background:#F9F9F9;color:'+y.color+'"><td class="abrir-programacion" idprogramacion="'+y.idprogramacion+'">P-'+y.idprogramacion+'</td><td>'+y.nombre_actividad+'</td><td>'+y.descripcion_actividad+'</td><td>'+y.fecha.substring(8,10)+'-'+y.fecha.substring(5,7)+'-'+y.fecha.substring(0,4)+'</td><td>'+y.prioridad.prioridad_nombre+'</td><td id="estado">'+y.estado_actividad+'</td><td id="botones" align="center">'+y.botones+'</td><td id="evidencias" align="center" style="color:#555;">'+y.e_doc+' &nbsp; '+y.doc+'</td><td id="notificacion" align="center" style="color:#555;">'+y.mensaje+'</td><td id ="anotacion" align="center" style="color:#555;">'+y.a+' &nbsp; <i id="'+y.idprogramacion+'" class="notas fa fa-pencil-square fa-lg"></i></td></tr>';
				});
				actividades=actividades + '</table>';
			$('.subcategoria'+idcontrato+idcategoria+idsubcategoria).html(actividades);
			});
			$(this).removeClass('fa-plus-square-o').addClass('fa-minus-square-o');
		}else{
			$('.subcategoria'+idcontrato+idcategoria+idsubcategoria).toggle();
			$(this).removeClass('fa-minus-square-o').addClass('fa-plus-square-o');
		}
	});


/*
	$('div.proyecto-inicio').on('click','div.contratos .historico',function(){
		id=$(this).attr('id');
		var idcontrato=$(this).attr('idcontrato');
		var idcategoria=$(this).attr('idcategoria');
		var idsubcategoria=$(this).attr('idsubcategoria');
		var historico='';
		datos = 'idcategoria='+idcategoria+'&idcontrato='+idcontrato+'&idsubcategoria='+idsubcategoria;

		if($('span#'+id).attr('valor')==0){
			$('span#'+id).html('CERRAR HIST\u00d3RICO');
			$('span#'+id).attr('valor',1);
			$('div.historico-doc'+id).html(cargando);
			$.getJSON(base_url+'doc/dashboard/historicos',datos,function(json){
				historico = historico + '<table class="table table-condensed table-bordered table-striped" style="font-size:10px;"><tr><th style="width:5%">Id</th><th style="width:20%">Actividad</th><th>Descripci&oacute;n</th><th style="width:10%;">Fecha L&iacute;mite</th><th style="width:10%">Estado</th><th style="width:5%">Acci&oacute;n</th><th style="width:5%">Evidencias</th><th style="width:5%">Notificar</th><th style="width:5%">Anotaciones</th></tr>';
				$.each(json,function(x,y){
					historico = historico + '<tr style="background:#F9F9F9" id="programacion'+y.idprogramacion+'"><td class="abrir-programacion" idprogramacion="'+y.idprogramacion+'">P-'+y.idprogramacion+'</td><td>'+y.nombre_actividad+'</td><td>'+y.descripcion_actividad+'</td><td>'+y.fecha.substring(8,10)+'-'+y.fecha.substring(5,7)+'-'+y.fecha.substring(0,4)+'</td><td id="estado">'+y.estado_actividad+'</td><td id="botones" align="center">'+y.botones+'</td><td id="evidencias" align="center" style="color:#555;">'+y.e_doc+' &nbsp; '+y.doc+'</td><td id="notificacion" align="center" style="color:#555;">'+y.mensaje+'</td><td id="anotacion" align="center" style="color:#555;">'+y.a+' &nbsp; <i id="'+y.idprogramacion+'" class="notas fa fa-pencil-square fa-lg"></i></td></tr>';
				});
				historico=historico + '</table>';
				$('div.historico-doc'+id).html(historico);
			});
		}
		else{
			$('span#'+id).html('VER HIST\u00d3RICO');
			$('span#'+id).attr('valor',0);
			$('div.historico-doc'+id).html('');
		}

	});

*/

	$('div.proyecto-inicio').on('click','div.contratos .abrir-programacion',function(){
		idprogramacion=$(this).attr('idprogramacion');
		datos='idprogramacion='+idprogramacion;
		$.getJSON(base_url+'doc/dashboard_hist/areas',datos,function(json0){
			areas='';
			tabla_seg='';
			$.each(json0,function(u,v){
				areas = areas + '<span style="font-size:10px;">'+v.nombre_area_involucrada+'</span><br>';
			});
			$('#areas').html(areas);
		}).done(function(){
			$.getJSON(base_url+'doc/dashboard_hist/programacion',datos,function(json){
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
				$('#fin_actividad').text(json[0].fecha);
			}).done(function(){
				$.getJSON(base_url+'doc/dashboard_hist/desplegar_evidencias',datos,function(json){
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

					$.getJSON(base_url+'doc/dashboard_hist/desplegar_seguimiento',datos,function(json){
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


/*

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

			}else{
				alert('Ha ocurrido un error, intente nuevamente');
			}
		})
	});
*/
/*
	$('div#detalle_acciones').on('click','i.doc',function(){
		idprogramacion = $(this).attr('id');
		idestado = $(this).attr('idestado');
		idprgramacion = $('input#idprogramacion').val(idprogramacion);
		idestado = $('input#idestado').val(idestado);
		loadEvidencias(idprogramacion);
	});
*/

/*

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
*/
/*
	$('div.proyecto-inicio').on('click','div.contratos i.cancelar_programacion_actividad',function(){
		idprogramacion=$(this).attr('idprogramacion');
		if(confirm("\u00BF Est\u00e1 seguro que desea cancelar la actividad P-"+idprogramacion+"?")){
			datos = 'idprogramacion='+idprogramacion+'&estado=1&ruta=cancelar';
			$.getJSON(base_url+'doc/dashboard/cambiar_estado',datos,function(json){
				if(json.msg>0){
					alert('La actividad ha sido cancelada');
					$('#modal-detalle').modal('hide');
					$.getJSON(base_url+'doc/dashboard/programacion_actividad','idprogramacion='+idprogramacion,function(json){
						$('tr#programacion'+idprogramacion).remove();
					});
				}else{
					alert('Ha ocurrido un error, intente nuevamente');
				}
			});
			return true;
		}
		else
			return false;
	});
*/
/*
	$('#modal-detalle').on('click','i.cancelar_programacion_actividad',function(){
		idprogramacion=$(this).attr('idprogramacion');
		if(confirm("\u00BF Est\u00e1 seguro que desea cancelar la actividad P-"+idprogramacion+"?")){
			datos = 'idprogramacion='+idprogramacion+'&estado=1&ruta=cancelar';
			$.getJSON(base_url+'doc/dashboard/cambiar_estado',datos,function(json){
				if(json.msg>0){
					alert('La actividad ha sido cancelada');
					$('#modal-detalle').modal('hide');
					$.getJSON(base_url+'doc/dashboard/programacion_actividad','idprogramacion='+idprogramacion,function(json){
						$('tr#programacion'+idprogramacion).remove();
					});
				}else{
					alert('Ha ocurrido un error, intente nuevamente');
				}
			});
			return true;
		}
		else
			return false;
	});

*/
	$('div.proyecto-inicio').on('click','div.contratos i.doc',function(){
		idprogramacion = $(this).attr('id');
		idestado = $(this).attr('idestado');
		idprgramacion = $('input#idprogramacion').val(idprogramacion);
		idestado = $('input#idestado').val(idestado);
		loadEvidencias(idprogramacion);
	});


	$('div.proyecto-inicio').on('click','div.contratos i.not',function(){
		idprogramacion = $(this).attr('id');
		idestado = $(this).attr('idestado');
		datos='idprogramacion='+idprogramacion;
		$("#not-idprogramacion").val(idprogramacion);
		$.getJSON(base_url+'doc/dashboard_hist/areas',datos,function(json0){
			areas='';
			$.each(json0,function(u,v){
				areas = areas + '<span id="area'+v.idarea_involucrada+'" style="font-size:10px; font-weight:bold;">'+v.nombre_area_involucrada+'</span><br>';
				$.getJSON(base_url+'doc/dashboard_hist/areas_usuarios','idarea='+v.idarea_involucrada,function(json1){
					usuarios='';
					$.each(json1,function(x,y){
						usuarios = usuarios + '<div class="checkbox" style="font-size:10px; margin-left:50px;"><label><input type="checkbox" value="'+y.correo_usuario+'" name="correos[]">'+y.usuario+' <strong>&nbsp;&nbsp;NIVEL:'+y.idnivel+'</strong></label></div>';
					});
					$(usuarios).insertAfter('span#area'+v.idarea_involucrada);
				});
			});
			$('#enviar-areas').html(areas);
		}).done(function(){
			$.getJSON(base_url+'doc/dashboard_hist/programacion',datos,function(json){
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


	$('#btn-enviar-notificacion').click(function(){
		datos = $('form#form-enviar-notificaciones-usuarios').serialize();
		$.getJSON(base_url+'doc/dashboard_hist/notificacion',datos,function(json){
			if(json.msg>0){
				alert('Las notificaciones han sido enviadas');
				$('#modal-notificacion').modal('hide');
			}else{
				alert('Las notificaciones no han podido ser enviadas, intente nuevamente');
			}
		});
	});

	$('div.proyecto-inicio').on('click','div.contratos i.accion',function(){
		ruta = $(this).attr('ruta');
		idprogramacion = $(this).attr('id');
		estado = $(this).attr('idestado');
		datos = 'idprogramacion='+idprogramacion+'&estado='+estado+'&ruta='+ruta;
		idcontrato=$(this).attr('co');
		idcategoria=$(this).attr('ca');
		idsubcategoria=$(this).attr('su');
		datos = 'idprogramacion='+idprogramacion+'&estado='+estado+'&ruta='+ruta;
		$.getJSON(base_url+'doc/dashboard_hist/cambiar_estado',datos,function(json){
			if(json.msg>0){
				alert('La actividad ha sido '+json.text);
				if(estado!=1){
					loadEvidencias(idprogramacion,estado);
					$('form#form-evidencia-documental input#idprogramacion').val(idprogramacion);
					$('form#form-evidencia-documental input#idestado').val(estado);
					loadEvidencias(idprogramacion);
				}
				$.getJSON(base_url+'doc/dashboard_hist/programacion_actividad','idprogramacion='+idprogramacion,function(json){
					$('tr#programacion'+idprogramacion+' td#botones').html(json[0].botones);
					$('tr#programacion'+idprogramacion+' td#estado').html(json[0].estado_actividad);
				});

			}else{
				alert('Ha ocurrido un error, intente nuevamente');
			}
		})
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
		$.getJSON(base_url+'doc/dashboard_hist/eliminar_evidencia',datos,function(json){
			if(json.msg>0){
				alert('La evidencia ha sido eliminada');
				loadEvidencias(idprogramacion);
			}else{
				alert('Ha ocurrido un error, intente nuevamente');
			}
		});
	});

});

//anotaciones
	$('div').on('click','i.notas',function(){
		id=$(this).attr('id');
		muestra_anotaciones(id);
		$('#idprogramacion_v').val(id);
		$('#modal-anotacion').modal();
	});

	//guardar anotacion
	$('#modal-anotacion').on('click','button#guarda_anotacion',function(){
		errores = 0;
		if($('textarea#nota_v').val()==''){
			errores = errores + 1;
			$('textarea#nota_v').css('border','solid 1px red');
		}else{
			$('textarea#nota_v').css('border','solid 1px #ccc');
		}

		if(errores>0){
			alert('Llene los campos correctamente');
		}else{
			$(this).html('Procesando...').attr('disabled',true);
			fecha=$('input#fecha_anotacion').val();
			nota=$('textarea#nota_v').val();
			valoracion=$('select#val').val();
			usuario=$('input#usuario_v').val();
			idprogramacion=$('input#idprogramacion_v').val();
			datos='fecha='+fecha+'&nota='+nota+'&valoracion='+valoracion+'&usuario='+usuario+'&idprogramacion='+idprogramacion;
			$.getJSON(base_url+'doc/dashboard_hist/guarda_anotacion',datos,function(data){
				$('button#guarda_anotacion').html('Guardar').attr('disabled',false);
				if(data.msg>0){
					alert('La anotacion se guardo correctamente.');
					muestra_anotaciones(idprogramacion);
					actualiza_anotacions(idprogramacion);
				}else{
					alert('Ocurrio un error, intente nuevamente.');
				}
			});
		}
	});

	//bloquea anotacion
	$('#modal-anotacion').on('click','i.bloquear_v',function(){
		id=$(this).attr('id');
		idprogramacion=$(this).attr('idprogramacion');
		datos='id='+id;
		if(confirm("Esta seguro de bloquear la anotacion")){
			$.getJSON(base_url+'doc/dashboard_hist/bloquea_anotacion',datos,function(data){
				if(data.msg>0){
					alert('La anotacion ha sido bloqueada.');
					muestra_anotaciones(idprogramacion);
				}else{
					alert('Ocurrio un error, intente nuevamente.');
				}
			});
		return true;
		}else{
			return false;
		}
	});

	//elimina anotacion
	$('#modal-anotacion').on('click','i.borrar_v',function(){
		id=$(this).attr('id');
		idprogramacion=$(this).attr('idprogramacion');
		datos='id='+id;
		if(confirm("Esta seguro de eliminar la anotacion")){
			$.getJSON(base_url+'doc/dashboard_hist/eliminar_anotacion',datos,function(data){
				if(data.msg>0){
					alert('La anotacion ha sido eliminada.');
					muestra_anotaciones(idprogramacion);
					actualiza_anotacion(idprogramacion);
				}else{
					alert('Ocurrio un error, intente nuevamente.');
				}
			});
		return true;
		}else{
			return false;
		}
	});

function muestra_anotaciones(idprogramacion)
{
	$('#fecha_anotacion').val(anio+'-'+mes+'-'+dia);
	$('#nota_v').val('');
	$("select#val").val("1");

	datos = 'idprogramacion='+idprogramacion;
	$.getJSON(base_url+'doc/dashboard_hist/desplegar_anotaciones',datos,function(json){
		contenido='<table style="font-size:11px" class="table table-striped table-condensed table-bordered"><tr><td>#</td><td>Fecha</td><td>Anotacion</td><td>Valoracion</td><td>Uusuario</td><td>Estado</td><td>Accion</td><tr>';
		i=0;
		$.each(json,function(x,y){
			i++;
			contenido=contenido+'<tr><td>'+i+'</td><td>'+y.fecha_anotacion+' '+y.hora_anotacion+'</td><td>'+y.anotacion+'</td><td>'+y.valoracion+'</td><td>'+y.usuario_registra+'</td><td>'+y.cat_estado_anotacion+'</td><td>'+y.accion+'</td></tr>';
		});
		contenido=contenido+'</table>';
		if(i>0){
			$('#detalle-anotaciones').html(contenido);
		}else{
			$('#detalle-anotaciones').html('No existen anotaciones');
		}

	});

}

function loadEvidencias(idprogramacion)
{
	datos = 'idprogramacion='+idprogramacion;
	$.getJSON(base_url+'doc/dashboard_hist/desplegar_evidencias',datos,function(json){
		table='<style>td.td_eliminar{cursor:pointer;}</style><table class="table table-striped table-condensed table-bordered"><tr><td>#</td><td>Evidencia documental</td><td>Estado</td><td>Acci&oacute;n</td></tr>';
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
	}).done(function(){
		$.getJSON(base_url+'doc/dashboard_hist/numero_evidencia',datos,function(json1){
			$('tr#programacion'+idprogramacion+' td#evidencias').html(json1[0].e_doc+' &nbsp; '+json1[0].doc);
		});
	});
}


function actualiza_anotacions(idprogramacion)
{
	datos = 'idprogramacion='+idprogramacion;
	$.getJSON(base_url+'doc/dashboard_hist/numero_anotacion',datos,function(json){
		$('tr#programacion'+idprogramacion+' td#anotacion').html(json[0].numero+' &nbsp; '+json[0].anotacion);
	});
}
