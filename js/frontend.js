/* **************************** */
/* Valores de imputs iniciales */
/* **************************** */

CP_initial_input_value('credenciales', 3);

CP_initial_input_value('shortcode', 1);

/* **************************** */
/* **************************** */

let credenciales_saver = document.getElementById('db_credentials_saver');
let shortcode_saver = document.getElementById('shortcode_saver');
let generar_posts = document.getElementById('generar_btn');

/* credenciales_saver.addEventListener('click', function () {
    // obtener datos de los imputs
    let usuario_db = document.getElementById('credenciales_1').value;
    let pwd_db = document.getElementById('credenciales_2').value;
    let schema_db = document.getElementById('credenciales_3').value;

    // validar que los imputs no estén vacios
    if (usuario_db && pwd_db && schema_db) {

        CP_save_inputs('credenciales', 3);

        document.getElementById('msg_cred_error').style.display = 'none';
        document.getElementById('msg_cred_exito').style.display = 'block';

    } else {
        document.getElementById('msg_cred_error').style.display = 'block';
        document.getElementById('msg_cred_exito').style.display = 'none';

    }

}); */

shortcode_saver.addEventListener('click', function () {
    let shortcode = document.getElementById('shortcode_1').value;
    console.log(shortcode);
    if(shortcode){
        CP_save_inputs('shortcode', 1);
        document.getElementById('msg_shortcode_exito').style.display = 'block';
        document.getElementById('msg_shortcode_error').style.display = 'none';
    }else{
        document.getElementById('msg_shortcode_exito').style.display = 'none';
        document.getElementById('msg_shortcode_error').style.display = 'block';
    }
});

generar_posts.addEventListener('click', function () {
    // console.log('generar posts button clicked!');
    //TODO: generar posts atuomaticos
});

function CP_initial_input_value(input_slug, total_fields){
    
    let url ;
    
    if(input_slug == 'credenciales'){
        url = window.location.protocol + '//' + window.location.host + '/wp-content/plugins/codigos-postales/data/credenciales_db.json';
    }else{
        url = window.location.protocol + '//' + window.location.host + '/wp-content/plugins/codigos-postales/data/shortcode_name.json'
    }

    let req = new XMLHttpRequest();

    req.onreadystatechange = function () {
        if (req.readyState == 4) {
            var credencial = JSON.parse(this.responseText);

            for(var i=1 ; i <= total_fields; i++ ){
                document.getElementById( input_slug + '_' + i).value = credencial[input_slug + '_' + i]; //asigno el valor del json a cada input
            }

            
        }
    };
    req.open('GET', url , true);
    req.send(null);
}

function CP_save_inputs(input_slug , total_fields){
    var url = null;
    if(input_slug == 'credenciales'){
        url = window.location.protocol + '//' + window.location.host + '/wp-content/plugins/codigos-postales/ws/save_credentials.php';
    }else{
        url = window.location.protocol + '//' + window.location.host + '/wp-content/plugins/codigos-postales/ws/save_shortcode.php'
    }
    
    var formData = new FormData();
    
    for(var i=1; i <= total_fields; i++){
        let input = document.getElementById(input_slug + '_' + i );
        formData.append(input.name, input.value);
    }

    var http = new XMLHttpRequest();
        http.onreadystatechange = function () {
            if (http.readyState == 4) {
            //    msg_exito = this.responseText;
            }
            console.log(this.responseText);
        };

        http.open('POST', url , true);
        // http.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        http.send(formData);

}