var charMap = {
	"à": "a",
	"á": "a",
	"â": "a",
	"é": "e",
	"è": "e",
	"ê": "e",
	"ë": "e",
	"É": "e",
	"ï": "i",
	"î": "i",
	"í": "i",
	"ô": "o",
	"ó": "o",
	"ö": "o",
	"û": "u",
	"ú": "u",
	"ù": "u",
	"ü": "u",
	"ñ": "n"
};

var normalize = function (input) {
	jQuery.each(charMap, function (unnormalizedChar, normalizedChar) {
		var regex = new RegExp(unnormalizedChar, 'gi');
		input = input.replace(regex, normalizedChar);
	});
	if (input.indexOf("(") != -1) {
		input = input.replace("(", "");
	}
	return input;
};

var queryTokenizer = function (q) {
	var normalized = normalize(q);
	return Bloodhound.tokenizers.whitespace(normalized);
};

function getArrayFromJson(se_list) {

	var return_arr = [];

	l = se_list.length;
	for (var index = 0; index < l; index++) {
		var name = se_list[index].name;
		return_arr.push(name);
	}

	return return_arr;
}

function getBloodHound(data, suffix) {

	var orderedPlaceList = getArrayFromJson(data);
	// console.log(orderedPlaceList);

	var se_bloodhound = new Bloodhound({
		datumTokenizer: Bloodhound.tokenizers.obj.whitespace('value'),
		queryTokenizer: queryTokenizer,
		limit: 7,
		sorter: function (a, b) {
			var InputString = jQuery("#keyword_se").val();
			return setPlacesOrderRules(a, b, InputString);

		},
		local: jQuery.map(orderedPlaceList, function (name) {
			// Normalize the name - use this for searching
			var normalized = normalize(name);
			return {
				value: normalized,
				// Include the original name - use this for display purposes
				displayValue: name + suffix
			};
		})
	});

	return se_bloodhound;
}

function processList(se_list_estados, se_list_municipios, se_list_colonias) {
	var se_bloodhound_estados = getBloodHound(se_list_estados, " (Estado)");
	var se_bloodhound_municipios = getBloodHound(se_list_municipios, " (Municipio)");
	var se_bloodhound_colonias = getBloodHound(se_list_colonias, " (Colonia)");

	se_bloodhound_estados.initialize();
	se_bloodhound_municipios.initialize();
	se_bloodhound_colonias.initialize();

	jQuery("#keyword_se").typeahead(
		{
			hint: true,
			highlight: true,
			minLength: 1,
			autoselect: true
		}, {
		name: 'se_list_municipios',
		displayKey: 'displayValue',
		source: se_bloodhound_municipios.ttAdapter(),
		limit: 5,
		templates: {
			header: '<h3 style="margin: 0 20px 5px 20px;padding: 3px 0;border-bottom: 1px solid #ccc;">Municipios</h3>'
		}
	}, {
		name: 'se_list_colonias',
		displayKey: 'displayValue',
		source: se_bloodhound_colonias.ttAdapter(),
		limit: 5,
		templates: {
			header: '<h3 style="margin: 0 20px 5px 20px;padding: 3px 0;border-bottom: 1px solid #ccc;">Colonias</h3>'
		}
	}, {
		name: 'se_list_estados',
		displayKey: 'displayValue',
		source: se_bloodhound_estados.ttAdapter(),
		limit: 5,
		templates: {
			header: '<h3 style="margin: 0 20px 5px 20px;padding: 3px 0;border-bottom: 1px solid #ccc;">Estados</h3>'
		}
	}
	);

	var buscador_farmacia = jQuery("#buscador_farmacia").val();

	/* 	if(buscador_farmacia !== "si"){
		jQuery("#keyword_se").on("focus",function(e){
			jQuery("#search_button").attr("disabled", 'disabled');
			jQuery("#keyword_id_se").val("");
		});
	} */
	
	jQuery("#search_button").attr("disabled","disabled");

	jQuery('.twitter-typeahead').on('typeahead:selected', function(evt, item) {
		console.log('selected');
		jQuery("#search_button").removeAttr("disabled");
	})



	jQuery(".autocompletar").bind("blur keyup", function () {
		var encontrado = false;
		var valor = "";
		var campo = this;
		var se_list_valores = [];
		if(jQuery(campo).val().indexOf(" (Estado)", "") > -1){
			se_list_valores = se_list_estados;
		}else if(jQuery(campo).val().indexOf(" (Municipio)", "") > -1){
			se_list_valores = se_list_municipios;
		}else if(jQuery(campo).val().indexOf(" (Colonia)", "") > -1){
			se_list_valores = se_list_colonias;
		}
		jQuery.each(se_list_valores, function (i, se_element) {
			if (se_element.name == jQuery(campo).val().replace(" (Estado)", "").replace(" (Municipio)", "").replace(" (Colonia)", "")) {
				encontrado = true;
				valor = se_element.id;
				jQuery("#keyword_id_se").val(valor);
				return false;
			}
			if (jQuery(campo).val()) { // para que se habilite el botón de buscar
				encontrado = true;
			}


		});
		if (encontrado) {
			jQuery("#search_button").removeAttr("disabled");
		}
	    /*if(encontrado){
	    	var combo = jQuery(campo).attr("combo");
	    	//remove selected one
			jQuery('option:selected', 'select[name="'+combo+'"]').removeAttr('selected');
			//Using the value
			jQuery('select[name="'+combo+'"]').find('option[value="'+valor+'"]').attr("selected",true);
	    }else
	    {
	    	var combo = jQuery(campo).attr("combo");
	    	//remove selected one
			jQuery('option:selected', 'select[name="'+combo+'"]').removeAttr('selected');
			jQuery(campo).val("");
	    }*/
	});

	jQuery(".json-loaded").removeAttr("disabled");
	jQuery("#keyword_se").removeAttr("disabled");
	jQuery(".json-loaded").removeAttr("readonly");
	jQuery(".json-loaded").css("background", "");
	jQuery("#keyword_se").focus();

}

//Pone reglas para ordenar el listado de palabras propuestas
function setPlacesOrderRules(a, b, InputString) {

	//-1 para a significa que subimos esta selecciÃ³n hasta arriba, 1 hasta abajo, 0 queda igual
	//-1 para b significa que bajamos esta selecciÃ³n hasta abajo, 1 hasta arriba, 0 queda igual

	var priority = 0;

	//Orden alfabético
	if (a.displayValue.localeCompare(b.displayValue) > 0) {
		priority += 3;
	} else if (a.displayValue.localeCompare(b.displayValue) < 0) {
		priority += -3;
	} else {
		priority += 0;
	}

	//Si una palabra contiene "Todo lo que contiene" lo subimos para arriba
	if (a.displayValue.indexOf("(Todos)") != -1) {

		if (b.displayValue.indexOf("(Todos)") != -1) {
			//nada
		} else {
			//return -1;
			priority += -5;
		}
	} else if (b.displayValue.indexOf("(Todos)") != -1) {
		//return 1;
		priority += 5;
	}



	if (a.displayValue.toLowerCase().startsWith(InputString.toLowerCase())) {

		if (b.displayValue.toLowerCase().startsWith(InputString.toLowerCase())) {
			//nada
		} else {
			//return -1;
			priority += -5;
		}
	} else if (b.displayValue.toLowerCase().startsWith(InputString.toLowerCase())) {
		//return 1;
		priority += 5;
	}

	if (priority > 0) {
		return 1;
	} else if (priority == 0) {
		return 0;
	} else if (priority < 0) {
		return -1;
	}

}

jQuery("document").ready(function () {
	loadPredictivo();
});

function loadPredictivo() {

	var se_list_estados_file = "se_list_estados.json";
	var se_list_municipios_file = "se_list_municipios.json";
	var se_list_colonias_file = "se_list_colonias.json";

	var data_estados;
	var data_municipios;
	var data_colonias;

	data_products_json = sessionStorage.getItem("estados_json");

	if (data_products_json != null && typeof data_products_json != "undefined") {
		data_estados = JSON.parse(data_products_json);
		data_municipios = JSON.parse(sessionStorage.getItem("municipios_json"));
		data_colonias = JSON.parse(sessionStorage.getItem("colonias_json"));

		processList(data_estados, data_municipios, data_colonias);
	} else {
		// jQuery("#keyword_se").attr("disabled","disabled");
		// jQuery("#keyword_se").attr("placeholder","Cargando medicamentos...");
		jQuery.getJSON(window.location.protocol + '//' + window.location.host + "/wp-content/plugins/codigos-postales/data/" + se_list_estados_file, function (data) {
			data_estados = data;
			jQuery.getJSON(window.location.protocol + '//' + window.location.host + "/wp-content/plugins/codigos-postales/data/" + se_list_municipios_file, function (data) {
				data_municipios = data;
				jQuery.getJSON(window.location.protocol + '//' + window.location.host + "/wp-content/plugins/codigos-postales/data/" + se_list_colonias_file, function (data) {
					data_colonias = data;

					sessionStorage.setItem("estados_json", JSON.stringify(data_estados));
					sessionStorage.setItem("municipios_json", JSON.stringify(data_municipios));
					sessionStorage.setItem("colonias_json", JSON.stringify(data_colonias));

					processList(data_estados, data_municipios, data_colonias);

				});
			});
		});
	}


}