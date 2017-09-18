
var loading = $("<i />", {
	"class": "fa fa-spinner fa-spin fa-2x",
	text: ""
});

//Paginación proyecto
$(document).on('click','.proyectoPaginacion',function(e){
	e.preventDefault();
	_this = $(this);

	if (_this.hasClass("active"))
		return false;

	pag = _this.data("pag");
	limit = _this.data("limit");
	ini = (limit * pag);
	fin = ini + limit;

	$(".qwerty").hide();
	$(".qwerty").slice(ini, fin).show();
	$(".proyectoPaginacion").removeClass("active");
	_this.addClass("active");

	$(".proyectoPrevious").data("pag", pag - 1);
	$(".proyectoNext").data("pag", pag + 1);

	$(".proyectoPaginacion").each(function(i){
		__this = $(this);
		if(__this.data("pag") === _this.data("pag"))
			__this.addClass("active");
	});

	if (fin < _this.data("total")){
		$(".proyectoNext").removeClass("disabled");
	}

	else{
		$(".proyectoNext").addClass("disabled");
	}

	if (ini > 0){
		$(".proyectoPrevious").removeClass("disabled");
	}

	else{
		$(".proyectoPrevious").addClass("disabled");
	}

	return false;
});

$(document).on('click','.proyectoPrevious',function(e){
	e.preventDefault();
	_this = $(this);

	if (_this.hasClass("disabled"))
		return false;

	pag = _this.data("pag");

	$(".proyectoPaginacion").each(function( index ) {
		active = $(this);

		if (active.data("pag") == pag)
		{
			active.addClass("active");
			active.next().removeClass("active");
			_this.data("pag", active.prev().data("pag"));
		}
	});

	$(".proyectoPrevious").data("pag", pag - 1);
	$(".proyectoNext").data("pag", pag + 1);

	limit = _this.data("limit");
	ini = (limit * pag);
	fin = ini + limit;

	$(".qwerty").hide();
	$(".qwerty").slice(ini, fin).show();

	if (fin < active.data("total")){
		$(".proyectoNext").removeClass("disabled");
	}

	else{
		$(".proyectoNext").addClass("disabled");
	}

	if (ini > 0){
		$(".proyectoPrevious").removeClass("disabled");
	}

	else{
		$(".proyectoPrevious").addClass("disabled");
	}
});

$(document).on('click','.proyectoNext',function(e){
	e.preventDefault();
	_this = $(this);

	if (_this.hasClass("disabled"))
		return false;

	pag = _this.data("pag");
	$(".proyectoPaginacion").removeClass("active");
	$(".ppag_"+ pag).addClass("active");

	$(".proyectoPaginacion").each(function( index ) {
		active = $(this);

		if (active.data("pag") == _this.data("pag")){

			if (active.next().data("pag")){
				_this.data("pag", active.next().data("pag"));
			}
		}
	});

	$(".proyectoPrevious").data("pag", pag - 1);
	$(".proyectoNext").data("pag", pag + 1);

	limit = _this.data("limit");
	ini = (limit * pag);
	fin = ini + limit;

	$(".qwerty").hide();
	$(".qwerty").slice(ini, fin).show();

	if (fin < active.data("total")){
		$(".proyectoNext").removeClass("disabled");
	}

	else{
		$(".proyectoNext").addClass("disabled");
	}

	if (ini > 0){
		$(".proyectoPrevious").removeClass("disabled");
	}

	else{
		$(".proyectoPrevious").addClass("disabled");
	}
});

//Paginación número
$(document).on('click','.paginacion',function(e){
	e.preventDefault();
	_this = $(this);

	if (_this.hasClass("active"))
		return false;

	lista = _this.closest(".lista");
	lista.find("li.paginacion").removeClass("active");
	_this.addClass("active");

	$(".paginacion").each(function(i){
		__this = $(this);
		if(__this.data("pag") === _this.data("pag"))
			__this.addClass("active");
	});

	tbody = lista.find("tbody");
	next = lista.find("li.next");
	prev = lista.find("li.previous");
	datos = lista.data("pag");
	pag = _this.data("pag");
	sig = pag + 1;
	atras = pag - 1;
	ini = (datos.limit * pag);
	fin = ini + datos.limit;

	if (fin < datos.data.length){
		next.removeClass("disabled");
	}

	else{
		next.addClass("disabled");
	}

	if (ini > 0){
		prev.removeClass("disabled");
	}

	else{
		prev.addClass("disabled");
	}

	next.data("pag", sig);
	prev.data("pag", atras);

	var seccion = datos.data.slice(ini, fin);
	tbody.empty();

	$.each(seccion, function(i, v) {
		tbody.append('<tr><th scope="row">'+ v.link +'</th><td>'+ v.nombre +'</td><td>'+ v.descripcion +' </td><td>'+ v.fecha +'</td><td>'+ v.estado +'</td></tr>');
	});

	return false;
});


//Paginación siguiente
$(document).on('click','.next',function(e){
	e.preventDefault();
	_this = $(this);

	if (_this.hasClass("disabled"))
		return false;

	lista = _this.closest(".lista");
	tbody = lista.find("tbody");
	next = lista.find("li.next");
	prev = lista.find("li.previous");
	datos = lista.data("pag");
	pag = _this.data("pag");
	sig = pag + 1;
	ini = (datos.limit * sig);
	fin = ini + datos.limit;

	if (fin < datos.data.length){
		next.removeClass("disabled");
	}

	else{
		next.addClass("disabled");
	}

	if (ini > 0){
		prev.removeClass("disabled");
	}

	else{
		prev.addClass("disabled");
	}

	next.data("pag", sig);
	prev.data("pag", pag);

	currentActive = lista.find("li.active");
	currentActive.removeClass("active");
	currentActive.next().addClass("active");

	var seccion = datos.data.slice(ini, fin);
	tbody.empty();

	$.each(seccion, function(i, v) {
		tbody.append('<tr><th scope="row">'+ v.link +'</th><td>'+ v.nombre +'</td><td>'+ v.descripcion +' </td><td>'+ v.fecha +'</td><td>'+ v.estado +'</td></tr>');
	});

	return false;
});

//Paginación atrás
$(document).on('click','.previous',function(e){
	e.preventDefault();
	_this = $(this);

	if (_this.hasClass("disabled"))
		return false;

	lista = _this.closest(".lista");
	tbody = lista.find("tbody");
	next = lista.find("li.next");
	prev = lista.find("li.previous");
	datos = lista.data("pag");
	pag = _this.data("pag");
	atras = pag - 1;
	ini = (datos.limit * pag);
	fin = ini + datos.limit;

	if (fin < datos.data.length){
		next.removeClass("disabled");
	}

	else{
		next.addClass("disabled");
	}

	if (ini > 0){
		prev.removeClass("disabled");
	}

	else{
		prev.addClass("disabled");
	}

	next.data("pag", pag);
	prev.data("pag", atras);

	currentActive = lista.find("li.active");
	currentActive.removeClass("active");
	currentActive.prev().addClass("active");

	var seccion = datos.data.slice(ini, fin);
	tbody.empty();

	$.each(seccion, function(i, v) {
		tbody.append('<tr><th scope="row">'+ v.link +'</th><td>'+ v.nombre +'</td><td>'+ v.descripcion +' </td><td>'+ v.fecha +'</td><td>'+ v.estado +'</td></tr>');
	});

	return false;
});

$(document).on('click','.cerrar_subgraficas',function(e){
	$this = $(this);
	idproyecto = $this.data('idproyecto');
	$div = $("#show_proyecto_" + idproyecto);
	$div.find('.titulo_subgrafica').fadeOut();
	$div.find('.mostrar_vencidas').fadeOut();
	$div.find('.mostrar_vencer').fadeOut();

});

function generar_subgraficas(options)
{
	var sub_vencidas =  $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencidas").html(loading),
		sub_vencer = $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencer").html(loading);
		$(".mostrar_lista").empty().html(" ");
		$(".mostrar_lista").css({
			"border-width": "0",
			"border": "none"
		});

	$("html, body").animate({
		scrollTop: $("#show_proyecto_"+ options.idproyecto).offset().top
	}, 1000);

	$.ajax({
		url: base_url + "doc/reporte_prioridad/sub_categorias",
		data: options,
		cache: false,
		type: "GET",
		success: function(data) {
			data = JSON.parse(data),
			legend = [];console.log(data);
			$.each(data, function(key, item){

				mostrar = (key === "vencidas" ? sub_vencidas : sub_vencer);
				titulo = (key === "vencidas" ? "VENCIDAS" : "POR VENCER");
				noData = (key === "vencidas" ? "No hay tareas vencidas" : "No hay tareas por vencer");
				$("#show_proyecto_"+ options.idproyecto).css({
					"border-style": "solid",
					"border-color": options.color,
					"border-width": "1px"
				});
				mostrar.html();
				$("#show_proyecto_"+ options.idproyecto).find(".titulo_subgrafica").html("PRIORIDAD - " + options.nombre.toUpperCase()).fadeIn();
				$("#show_proyecto_"+ options.idproyecto).find('.mostrar_vencidas').fadeIn();
				$("#show_proyecto_"+ options.idproyecto).find('.mostrar_vencer').fadeIn();
				$("#show_proyecto_"+ options.idproyecto).find(".titulo_subgrafica").append('<span title="cerrar" class="cerrar_subgraficas" data-idproyecto="' + options.idproyecto +'">X</span>');
				generar_grafica(mostrar, {useHTML:false, text: titulo}, false, item, function(){
					generar_lista(this.point.options);
				}, noData);
			});
		},
		error: function(xhr) {}
	});

}

function generar_lista(options)
{
	divTable = $("#proyecto_lista_"+ options.idproyecto).empty();
	tablaMaster = $(".lista");

	$.ajax({
		url: base_url + 'doc/reporte_prioridad/sub_lista',
		data: options,
		cache: false,
		type: "POST",
		success: function(data) {
			data = JSON.parse(data);
			var limite = 10; // 10 por default
			todas = [];

			$.each(data, function(key, tablas){
				todas[key] = tablaMaster.clone();
				todas[key].find(".panel-heading").html(tablas.header);
				$.each(tablas.data, function(i, v){
					todas[key].find("tbody").append('<tr><th scope="row">'+ v.link +'</th><td>'+ v.nombre +'</td><td>'+ v.descripcion +' </td><td>'+ v.fecha +'</td><td>'+ v.estado +'</td></tr>');
				});
				todas[key].removeClass("hidden");
				divTable.append(todas[key]);
			});

			divTable.css({
				"border-color": options.color,
				"border-style": "solid",
				"border-width": "1px"
			});

			$("html, body").animate({
				scrollTop: $("#show_proyecto_"+ options.idproyecto).find(".mostrar_vencidas").offset().top
			}, 1000);
		},
		error: function(xhr) {}
	});
}

function generar_grafica(jObject, titulo, legend, datos, callback, noData)
{
	var jData = [],
		oObject = jObject;
	jData = datos;
	noData = noData ? noData : "No hay tareas disponibles";
	Highcharts.setOptions({
		lang:{
			noData: '<span style="color:#333333;font-size:18px;">' + noData + '</span>'
		}
	});
	oObject.highcharts({
		noData: {
			useHTML:true
		},
		chart: {
			type: "pie"
		},
		title: titulo,
		credits: {
			enabled: false
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				size: "50%",
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
				return (this.point.customTooltip ? this.point.customTooltip : "Número de actividades para <b>" + this.point.nombre + "</b> es <b>" + this.y + "</b>");
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
			data: jData,
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
	delete jData;
	delete oObject;
}

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
