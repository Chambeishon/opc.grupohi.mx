$(function() {

	// Modificar prioridad
	$(document).on('click','.modificar_prioridad',function(e){
		e.preventDefault();

		$this = $(this);
		$form = $(".form_modificar_prioridad");
		$boton = $('.guardar_modificacion');
		$nombreField = $form.find('.prioridad_nombre');
		$claveField = $form.find('.prioridad_clave');
		$titulo = $(".titulo");
		idprioridad = $this.data('idprioridad');
		$view = $(".msg_view");
		$div = $("<div/>", {
			class: "alert"
		});

		$.ajax({
			url: base_url + "doc/prioridad/obtener/"+ idprioridad,
			cache: false,
			type: "GET",
			success: function(data) {
				data = JSON.parse(data);

				if (data.error) {
					$('#modal-modificar').modal('hide');
					$div.hide().appendTo($view);
					textClass = "alert-" + (data.error ? "danger" : "success");
					$div.addClass(textClass);
					$text = $("<h5/>", {
						text: data.msg
					}).appendTo($div);
					$div.fadeIn();
				}

				else{

					$nombreField.val(data.data.nombre);
					$claveField.val(data.data.clave);

					// Agrega el ID de la prioridad
					$idprioridadInput = $('<input>').attr({
						type: 'hidden',
						name: 'idprioridad'
					}).val(idprioridad).appendTo($form);

					// Guardar los cambios
					$boton.on( "click", function() {

						// Impedir multiples clics
						$thisBoton = $(this);
						$thisBoton.prop('disabled', true);

						$.ajax({
							url: $form.attr("action"),
							data: $form.serialize(),
							cache: false,
							type: "POST",
							success: function(data) {
								data = JSON.parse(data);
								textClass = "alert-" + (data.error ? "danger" : "success");
								$div.addClass(textClass);
								$text = $("<h5/>", {
									text: data.msg
								}).appendTo($div);
								$div.fadeIn();

								// Actualiza la tabla
								if (!data.error)
								{console.log(data.prioridad);
									$tr = $("tr#tr_"+ idprioridad);
									$tr.addClass("success");
									$tdNombre = $tr.find("td.td_nombre").text(data.prioridad.nombre);
									$tdClave = $tr.find("td.td_clave").text(data.prioridad.clave);
								}

								// Revertir todos los cambios realizados
								$form.find("input[type=text], textarea").val("");

								setTimeout(function(){
									$div.fadeOut("slow");
									$view.empty();
									$tr.removeClass("success");
								}, 5000);

								// Regresa el botón a la normalidad
								$thisBoton.prop('disabled', false);
								$('#modal-modificar').modal('hide');

							},
							error: function(xhr) {}
						});
					});
				}

			},
			error: function(xhr) {}
		});

		return false;
	});

	// Eliminar prioridad
	$(document).on('click','.eliminar_prioridad',function(e){
		e.preventDefault();

		if( !confirm("confirmar eliminación")) {
			return false;
		}

		$view = $(".msg_view");
		$div = $("<div/>", {
			class: "alert"
		});
		$div.hide().appendTo($view);
		$a = $(this);
		$.ajax({
			url: $a.attr("href"),
			cache: false,
			type: "GET",
			success: function(data) {
				data = JSON.parse(data);
				textClass = "alert-" + (data.error ? "danger" : "success");
				$div.addClass(textClass);
				$text = $("<h5/>", {
					text: data.msg
				}).appendTo($div);
				$div.fadeIn();

				setTimeout(function(){
					$div.fadeOut();
					$view.empty();
				}, 5000);

				if (!data.error) {
					$a.closest("tr").fadeOut(300, function() { $(this).remove(); });
				}
			},
			error: function(xhr) {}
		});

		return false;
	});

	// Guardar prioridad
	$(".guardar_prioridad").on("submit", function(event) {
		event.preventDefault();
		$form = $(this);
		$form.prop("disabled", true);
		formValues = $form.serialize();
		$view = $(".msg_view");
		$div = $("<div/>", {
			class: "alert"
		});
		$div.hide().appendTo($view);
		$tabla = $("#tabla_prioridades");

		$.ajax({
			url: $form.attr("action"),
			data: formValues,
			cache: false,
			type: "POST",
			success: function(data) {
				data = JSON.parse(data);
				console.log(data);
				textClass = "alert-" + (data.error ? "danger" : "success");
				$div.addClass(textClass);
				$text = $("<h5/>", {
					text: data.msg
				}).appendTo($div);
				$div.fadeIn();

				setTimeout(function(){
					$div.fadeOut("slow", function(){
						$form.prop("disabled", false);
					});
					$view.empty();
				}, 5000);

				// Agrega el nuevo campo a la lista
				if (!data.error) {
					$tabla.children("tbody").append(data.data);
				}
			},
			error: function(xhr) {}
		});

		return false;
	});
});
