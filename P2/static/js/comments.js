// Mostrar comentarios
window.onload = listeners;
var comentario;

function listeners() {
    document.getElementById("comment-btn").addEventListener("click", showComments);
    document.getElementById("submit-btn").addEventListener("click", submitForm);
    comentario = document.getElementById("texto-comment");
    comentario.addEventListener("keypress", detectarPalabras);
};

function showComments() {
    document.getElementById("comments").classList.toggle("panel-comment-show");
}

function detectarPalabras(key) {
    if(key.code == "Space")
        checkBadWords(comentario);
}

function checkBadWords(comentario){
    var badWords = ['gilipollas', 'tonto', 'mierda', 'puta'];
    var ultPalabra = [];
    ultPalabra = ultPalabra.concat(comentario.value.split(" "));
    for (var i = 0; i < badWords.length; i++) {
        if (ultPalabra[ultPalabra.length - 1] == badWords[i]) {
            // Genero tanto asteriscos como letras tiene la palabra
            var asteriscos = '';
            for (var j = 0; j < badWords[i].length; j++) 
                asteriscos += '*';
            // Reemplazo la palabra por los asteriscos
            comentario.value = comentario.value.replace(badWords[i], asteriscos);
        }             
    }

  }

function submitForm(){
    
    if(checkFields()){
        //Añadir Comentario a lista

    }
}

//Comprueba que los campos del promulario sean correctos
function checkFields(){
    var name = document.getElementById("nombre");
    var email = document.getElementById("email");
    var texto = document.getElementById("texto-comment");
    var error = false;

    //Compruebo que todos los campos estén llenos
    if(isEmpty(name.value)){
        alert("Debe rellenar el campo nombre.");
        error = true;
        name.classList.add("error");
    } else {
        name.classList.remove("error");
    }

    if(isEmpty(email.value)){
        alert("Debe rellenar el campo email.");
        error = true;
        email.classList.add("error");
    } else if(!isValidEmail(email.value)){
        alert("Email no valido.");
        error = true;
        email.classList.add("error");
    } else 
        email.classList.remove("error");


    if(isEmpty(texto.value)){
        alert("Debe rellenar el campo texto.");
        error = true;
        texto.classList.add("error");
    } else {
        texto.classList.remove("error");
    }   
        
    return error;
}

//Comprueba si un campo está vacio
function isEmpty(campo){
    if(campo == ""){
        return true;
    }
    return false;
}

//Comprueba si el email pasado como parametro es un email en formato válido
function isValidEmail(email){
    // email = comienza con cadena de letras, numeros y/o . que no puede acabar por ., seguido de @ una cadena de letras, un punto y los diferentes niveles del dominio (. y entre 2 y 4 letras)
    var exprRegular = /^[A-Za-z0-9]+([\.]?[A-Za-z0-9]+)*@[A-Za-z]+([\.]?([A-Za-z]{2,4})+)*(\.[A-Za-z]{2,4})+/;
    return exprRegular.test(email);
}



