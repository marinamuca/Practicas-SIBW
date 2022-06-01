// Mostrar comentarioTextos
window.onload = initialize;
var comentarioTexto;
var badWords = [];

function initialize() {
    document.getElementById("comment-btn").addEventListener("click", showComments);
    document.getElementById("submit-btn").addEventListener("click", submitForm);
    comentarioTexto = document.getElementById("texto-comment");
    comentarioTexto.addEventListener("keypress", detectarPalabras);
    getBadWords();
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
    var ultPalabra = [];
    ultPalabra = ultPalabra.concat(comentarioTexto.value.split(" "));

    for (var i = 0; i < badWords.length; i++) {
        if (ultPalabra[ultPalabra.length - 1].toLowerCase() == badWords[i].toLowerCase()) {
            
            // Genero tanto asteriscos como letras tiene la palabra
            var asteriscos = '';
            for (var j = 0; j < badWords[i].length; j++) 
                asteriscos += '*';
            // Reemplazo la palabra por los asteriscos
            comentarioTexto.value = comentarioTexto.value.replace(ultPalabra[ultPalabra.length - 1], asteriscos);
        }             
    }

}

//Obtiene mediante Ajax, las Palabras prohibidas de la Base de Datos
function getBadWords(){
    // Definimos la URL que vamos a solicitar via Ajax
    var ajax_url = "badWords.php";

    // Creamos un nuevo objeto encargado de la comunicación
    var ajax_request = new XMLHttpRequest();

    // Definimos como queremos realizar la comunicación
    ajax_request.open( "GET", ajax_url, true );

    //Enviamos la solicitud
    ajax_request.send();

    ajax_request.onreadystatechange = function() {

        if (this.readyState == 4 && this.status == 200 ) {

            var wordsJSON = JSON.parse( this.responseText );  
  
            for (var index in wordsJSON) {
                badWords.push(wordsJSON[index]);      
            }
          
        }
    }
}

function submitForm(){
    var form = document.getElementById("comment-form");
    if(checkFields()){
        //Añadir comentario Texto a lista comentarios
        checkBadWords(comentarioTexto);    
        // TODO ARREGLAR
        // addComment(form.elements["nombre"], form.elements["texto-comment"]);
        // clearInput(form.elements["nombre"]);
        // clearInput(form.elements["email"]);
        enviarComentario(form.elements["texto-comment"].value);
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
    span.innerText = texto;
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
    var dateString = date.getDate() + "/" + date.getMonth() + "/" + date.getFullYear() + " - " + date.getHours() + ":" + ((date.getMinutes()<10?'0':'') + date.getMinutes() + ' ');
    return dateString;
}

//Añade un nuevo comentario a la lista de comentarios
function addComment(author, texto){
    var listComments = document.getElementById("comment-list");
    var newcomment = createLi("comment");
    var date = formatDate(new Date());
    newcomment.append(createSpan(date, "date"));
    newcomment.append(createSpan(author.value + ": ", "author"));
    newcomment.append(createSpan(texto.value, "texto"));
    listComments.prepend(newcomment);

}

//Comprueba que los campos del promulario sean correctos
function checkFields(){
    // var name = document.getElementById("nombre");
    // var email = document.getElementById("email");
    var texto = document.getElementById("texto-comment");
    var correct = true;

    //Compruebo que todos los campos estén llenos
    // if(isEmpty(name.value)){
    //     document.getElementById("error-nombre").innerText="Debe rellenar el campo Nombre.";
    //     correct = false;
    //     name.classList.add("error");
    // } else {
    //     document.getElementById("error-nombre").innerText = "";
    //     name.classList.remove("error");
    // }

    // if(isEmpty(email.value)){
    //     document.getElementById("error-email").innerText="Debe rellenar el campo Email.";
    //     correct = false;
    //     email.classList.add("error");
    // } else if(!isValidEmail(email.value)){
    //     document.getElementById("error-email").innerText="Email no válido.";
    //     correct = false;
    //     email.classList.add("error");
    // } else {
    //     document.getElementById("error-email").innerText = "";
    //     email.classList.remove("error");
    // }
    
    
    if(isEmpty(texto.value)){
        document.getElementById("error-comment").innerText="Debe rellenar el campo Comentario.";
        correct = false;
        texto.classList.add("error");
    } else {
        document.getElementById("error-comment").innerText = "";
        texto.classList.remove("error");
    }   
        
    return correct;
}

function enviarComentario(texto){
    var commentPost = { 
        texto: texto, 
    }
    alert("AA")


    // var post = JSON.stringify(commentPost)
 
    // const url = document.URL;
    // let request = new XMLHttpRequest()
    
    // request.open('POST', url, true)
    // request.setRequestHeader('Content-type', 'application/json; charset=UTF-8')
    // request.send(post);
    
    // request.onload = function () {

    //     if(request.status === 200) {
    //         console.log("Comment successfully send!") 
    //     }
    // }

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "comments.php", true); 
    xhttp.setRequestHeader("Content-Type", "application/json");
    xhttp.onreadystatechange = function() {
    if (this.readyState == 4 && this.status == 200) {
        console.log("Comment successfully send!") 
    }
    };

    xhttp.send(JSON.stringify(commentPost));
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



