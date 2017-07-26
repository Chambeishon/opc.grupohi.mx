$(".box").slice(1).hide();
$(function() {
	$(".proyectos").change(function() {
		$(".box").hide();
		id = $(this).val();
		$("#proyecto_"+ id).show();
	});

	$(".eliminar_rango").on("click", function(event) {
		event.preventDefault();
		if( !confirm("confirmar eliminación")) {
			return false;
		}
		$view = $(".msg_view");
		$div = $("<div/>", {
			class: "alert",
		});
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
				$a.closest("tr").fadeOut(300, function() { $(this).remove(); });
			},
			error: function(xhr) {}
		});
	});

	$(".guardar_rango").on("submit", function(event) {
		event.preventDefault();
		$form = $(this);
		formValues = $form.serialize();
		$view = $(".msg_view");
		$div = $("<div/>", {
			class: "alert",
		});
		$div.hide().appendTo($view);
		tipo = $form.find("input[name='tipo']").val();
		proyecto = $form.find("input[name='idproyecto']").val();
		$tabla = $("#tabla_" + proyecto +"_"+ tipo);

		$.ajax({
			url: $form.attr("action"),
			data: formValues,
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

				setTimeout(function(){
					$div.fadeOut();
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
