
var loading = $("<i />", {
	"class": "fa fa-spinner fa-spin fa-2x",
	text: "",
});

function generar_subgraficas(options)
{
	var sub_vencidas =  $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencidas").html(loading),
		sub_vencer = $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencer").html(loading);
		$(".mostrar_lista").empty().html(" ");

	$("html, body").animate({
		scrollTop: sub_vencidas.offset().top
	}, 1000);

	toServer = {
		"idcat_categoria" : options.idcat_categoria,
		"idproyecto" : options.idproyecto,
		"subcategorias": options.subcategorias
	};

	$.ajax({
		url: base_url + "doc/reporte/sub_categorias",
		data: toServer,
		cache: false,
		type: "GET",
		success: function(data) {
			data = JSON.parse(data),
			legend = [];
			$.each(data, function(key, item){
				mostrar = (key == "vencidas" ? sub_vencidas : sub_vencer);
				titulo = (key == "vencidas" ? " vencidas" : " por vencer");
				mostrar.html();
				generar_grafica(mostrar, options.nombre + titulo, false, item, function(){
					generar_lista(this.point.options);
				});
			});
		},
		error: function(xhr) {}
	});
}

function generar_lista(options)
{
	divTable = $("#proyecto_lista_"+ options.idproyecto).empty();
	tabla = $(".lista").clone();

	$("html, body").animate({
		scrollTop: divTable.offset().top
	}, 1000);

	$.ajax({
		url: base_url + 'doc/reporte/sub_lista',
		data: options,
		cache: false,
		type: "POST",
		success: function(data) {

			tabla.removeClass("hidden");
			tabla.children("tbody").prepend(data);
			divTable.html(tabla);
		},
		error: function(xhr) {}
	});
}

function generar_grafica(jObject, titulo, legend, data, callback)
{
	data.nombre = data.cat_categoria;
	jObject.highcharts({
		chart: {
			type: "pie"
		},
		title: {
			text: titulo
		},
		credits: {
			enabled: false
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: "pointer",
				showInLegend: true,
				dataLabels: {
					enabled: true,
					formatter: function(){
						return this.point.customLegend ? this.point.customLegend : this.point.nombre +":"+ this.point.y;
					}
				}
			}
		},
		tooltip: {
			formatter: function() {
				return (this.point.customLegend ? this.point.customLegend : "Número de actividades para <b>" + this.point.nombre + "</b> es <b>" + this.y + "</b>");
			}
		},
		legend: {
			align: "left",
			layout: "vertical",
			verticalAlign: "top",
			x: 0,
			y: 20,
			enabled: legend,
			labelFormatter: function() {
				return this.options.nombre;
			}
		},
		xAxis: {
			categories: legend,
		},
		series: [{
			data: data,
			type: "pie",
			point:{
				events:{
					click: function (event) {
						callback.call(event);
					}
				}
			}
		}]
	});
}
window.sr = ScrollReveal();
sr.reveal(document.querySelectorAll(".box"));

$('body').on('click','.abrir-programacion', function(){

	idprogramacion=$(this).attr('idprogramacion');
	datos='idprogramacion='+idprogramacion;
	$.getJSON(base_url+'doc/dashboard/areas',datos,function(json0){
		areas='';
		tabla_seg='';
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
			$('#fin_actividad').text(json[0].fecha);

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
						accion = '<style>td.evi_doc{cursor:pointer;}td.msj{cursor:pointer;}</style><table class="table table-bordered"><tr><td>Acci&oacute;n</td><td>Evidencias</td><td>Notificar</td><td>Anotaciones</td></tr><tr><td>'+json[0].botones+'</td><td class="evi_doc">'+json[0].e+' &nbsp; '+ json[0].doc+'</td><td class="msj">'+json[0].mensaje+'</td><td>'+json[0].a+' &nbsp; <i title="Anotaciones" id="'+idprogramacion+'" class="notas fa fa-pencil-square fa-lg"></i></td></tr></table>';
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
