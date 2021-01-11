var buscador_form = document.getElementById("buscador_form");

buscador_form.addEventListener("submit", buscar);

function buscar(e){

	var lcmn_s = document.getElementById("lcmn_s");
	var httpRequest = new XMLHttpRequest();
    var formData = new FormData();

    formData.append(lcmn_s.name, lcmn_s.innerText);
 
	httpRequest.onreadystatechange = function() { // Call a function when the state changes.
    	if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
    		var response = JSON.parse(httpRequest.responseText);
        	if(response["result"] == "success"){
        		window.location.href = response["url"];	
        	}else{
				alert(httpRequest.responseText);
				alert("Un error sucedió. Inténtalo más tarde.");
			}
    	}
	}

	httpRequest.open('POST', window.location.protocol + "//" + window.location.hostname + "/wp-content/plugins/codigos-postales/ws/buscador/redirect_to_results.php");
    httpRequest.send(formData);

	e.preventDefault();
	return false;
}


// autoComplete.js input eventListener for search results event
document.querySelector("#autoComplete").addEventListener("results", (event) => {
	console.log(event);
});

var count_precios = 0;

// The autoComplete.js Engine instance creator
const autoCompleteJS = new autoComplete({
	name: "food & drinks",
	data: {
		src: async function () {
			count_precios = 0;
			// Loading placeholder text
			document
				.querySelector("#autoComplete")
				.setAttribute("placeholder", "Loading...");
			// Fetch External Data Source
			const source = await fetch(
				"https://cp.guiapaqueteria.com/wp-content/plugins/codigos-postales/data/buscador.json",{mode: 'same-origin'}
			);

			const data = await source.json();

			//const data = await source1.json();
			// Post loading placeholder text
			document
				.querySelector("#autoComplete")
				.setAttribute("placeholder", "Busca");
			// Returns Fetched data
			return data;
		},
		key: ["e","c","m"]
	},sort: (a, b) => {
		var priority = 0;

		//Priorizamos los estados en primero, y municipios en segundo
		if(a.value.e != ""){
			priority += -5;
		}else if(a.value.m != ""){
			priority += -3;
		}
		if(b.value.e != ""){
			priority += 5;
		}else if(b.value.m != ""){
			priority += 3;
		}

		

		if(priority>0){
					return 1;
				}else if(priority==0){
					return 0;
				}else if(priority<0){
					return -1;
				}
	},
	trigger: {
		event: ["input", "focus"]
	},
	placeHolder: "Busca",
	searchEngine: "strict",
	highlight: true,
	maxResults: 10,
	resultItem: {
		content: (data, element) => {
			// Prepare Value's Key
			var key = Object.keys(data).find((key) => data[key] === element.innerText);

			var show_key = "";
			if(key === "e"){
				show_key = "Estado";
			}else if(key === "c"){
				show_key = "Colonia";
			}else if(key === "m"){
				show_key = "Municipio";
			}

			// Modify Results Item
			element.style = "display: flex; justify-content: space-between;";
			element.innerHTML = `<span style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;">
        ${element.innerText}</span>
        <span style="display: flex; align-items: center; font-size: 13px; font-weight: 100; text-transform: uppercase; color: rgba(0,0,0,.2);">
      ${show_key}</span>`;
			
			}


	},
	noResults: (dataFeedback, generateList) => {
		// Generate autoComplete List
		generateList(autoCompleteJS, dataFeedback, dataFeedback.results);
		// No Results List Item
		const result = document.createElement("li");
		result.setAttribute("class", "no_result");
		result.setAttribute("tabindex", "1");
		result.innerHTML = `<span style="display: flex; align-items: center; font-weight: 100; color: rgba(0,0,0,.2);">Found No Results for "${dataFeedback.query}"</span>`;
		document
			.querySelector(`#${autoCompleteJS.resultsList.idName}`)
			.appendChild(result);
	},
	onSelection: (feedback) => {
		document.querySelector("#autoComplete").blur();
		// Prepare User's Selected Value
		const selection = feedback.selection.value["id"];
		// Render selected choice to selection div
		document.querySelector(".selection").innerHTML = selection;
		// Replace Input value with the selected value
		document.querySelector("#autoComplete").value = feedback.selection.value[feedback.selection.key];
		// Console log autoComplete data feedback
		console.log(feedback);
	}
});