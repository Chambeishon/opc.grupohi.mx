$(function() {

	// Modificar prioridad
	$(document).on('click','.modificar_prioridad',function(e){
		e.preventDefault();

		$this = $(this);
		$form = $(".guardar_prioridad");
		$titulo = $(".titulo");
		$nombreField = $(".prioridad_nombre");
		$claveField = $(".prioridad_clave");
		$boton = $(".boton_form");
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
					$div.hide().appendTo($view);
					textClass = "alert-" + (data.error ? "danger" : "success");
					$div.addClass(textClass);
					$text = $("<h5/>", {
						text: data.msg
					}).appendTo($div);
					$div.fadeIn();
				}

				else{
					$nombreField.css("border", "1px dotted #336600");
					$nombreField.val(data.data.nombre);
					$claveField.css("border", "1px dotted #336600");
					$claveField.val(data.data.clave);

					// Agrega el ID de la prioridad
					$idprioridadInput = $('<input>').attr({
						type: 'hidden',
						name: 'idprioridad'
					}).val(idprioridad).appendTo($form);
					$boton.text("Modificar");

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
								{
									$tr = $("tr#tr_"+ idprioridad);
									$tr.css("border-bottom", "1px dotted #336600");
									$tdNombre = $tr.find(".td_nombre").text($nombreField.val());
									$tdClave = $tr.find(".td_clave").text($claveField.val());
								}

								// Revertir todos los cambios realizados
								$nombreField.css("border", "1px solid #cccccc");
								$nombreField.val();
								$claveField.css("border", "1px solid #cccccc");
								$claveField.val();
								$boton.text("Agregar");

								setTimeout(function(){
									$div.fadeOut("slow");
									$view.empty();
									$tr.css("border-bottom", "0 solid #fff");
									$nombreField.val("");
									$claveField.val("");
								}, 5000);

								// Regresa el botón a la normalidad
								$thisBoton.prop('disabled', false);

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
