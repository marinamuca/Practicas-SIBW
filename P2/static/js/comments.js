// Mostrar comentarioTextos
window.onload = initialize;
var comentarioTexto;

function initialize() {
    document.getElementById("comment-btn").addEventListener("click", showComments);
    document.getElementById("submit-btn").addEventListener("click", submitForm);
    comentarioTexto = document.getElementById("texto-comment");
    comentarioTexto.addEventListener("keypress", detectarPalabras);
};

// Muestra el panel de comentarios
function showComments() {
    document.getElementById("comments").classList.toggle("panel-comment-show");
}

// Detecta cada vez que se termina de escribir una palabra
function detectarPalabras(key) {
    if(key.code == "Space")
        checkBadWords(comentarioTexto);
}

// Cambia las palabrotas por asteriscos
function checkBadWords(comentarioTexto){
    var badWords = ['gilipollas', 'tonto', 'mierda', 'puta', 'idiota'];
    var ultPalabra = [];
    ultPalabra = ultPalabra.concat(comentarioTexto.value.split(" "));
    for (var i = 0; i < badWords.length; i++) {
        if (ultPalabra[ultPalabra.length - 1] == badWords[i]) {
            // Genero tanto asteriscos como letras tiene la palabra
            var asteriscos = '';
            for (var j = 0; j < badWords[i].length; j++) 
                asteriscos += '*';
            // Reemplazo la palabra por los asteriscos
            comentarioTexto.value = comentarioTexto.value.replace(badWords[i], asteriscos);
        }             
    }

  }

function submitForm(){
    var form = document.getElementById("comment-form");

    if(checkFields()){
        //Añadir comentario Texto a lista comentarios
        checkBadWords(comentarioTexto);
        addComment(form.elements["nombre"], form.elements["texto-comment"]);
        clearInput(form.elements["nombre"]);
        clearInput(form.elements["email"]);
        clearInput(form.elements["texto-comment"]);
    }
}

//Resetea el input de un fromulario que se le pasa como parametro
function clearInput(input){
    input.value = ''
}

//Crea un span con el texto y el id que se le pasa como parametro
function createSpan(texto, classname){
    var span = document.createElement("span");
    span.append(document.createTextNode(texto));
    span.classList.add(classname)
    return span;
}

//Crea un li con el id que se le pasa como parametro
function createLi(classname){
    var li = document.createElement("li");
    li.classList.add(classname);
    return li;
}

// Formatea la fecha actual en formato DD/MM/YY - HH:MM
function formatDate(date){
    var dateString = date.getDate() + "/" + date.getMonth() + "/" + date.getFullYear() + " - " + date.getHours() + ":" + ((date.getMinutes()<10?'0':'') + date.getMinutes() + " ");
    return dateString;
}

//Añade un nuevo comentario a la lista de comentarios
function addComment(author, texto){
    var listComments = document.getElementById("comment-list");
    var newcomment = createLi("comment");
    var date = formatDate(new Date());
    newcomment.append(createSpan(date + " ", "date"));
    newcomment.append(createSpan(author.value + ": ", "author"));
    newcomment.append(createSpan(texto.value, "texto"));
    listComments.prepend(newcomment);

}

//Comprueba que los campos del promulario sean correctos
function checkFields(){
    var name = document.getElementById("nombre");
    var email = document.getElementById("email");
    var texto = document.getElementById("texto-comment");
    var correct = true;

    //Compruebo que todos los campos estén llenos
    if(isEmpty(name.value)){
        alert("Debe rellenar el campo nombre.");
        correct = false;
        name.classList.add("error");
    } else {
        name.classList.remove("error");
    }

    if(isEmpty(email.value)){
        alert("Debe rellenar el campo email.");
        correct = false;
        email.classList.add("error");
    } else if(!isValidEmail(email.value)){
        alert("Email no valido.");
        correct = false;
        email.classList.add("error");
    } else {
        email.classList.remove("error");
    }


    if(isEmpty(texto.value)){
        alert("Debe rellenar el campo texto.");
        correct = false;
        texto.classList.add("error");
    } else {
        texto.classList.remove("error");
    }   
        
    return correct;
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



