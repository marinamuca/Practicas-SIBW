// Mostrar comentarioTextos
window.onload = initialize;
var input;

function initialize() {   
    input = document.getElementById("search");
    input.addEventListener("keyup", search);
    // input.addEventListener("focus", focus);
    // input.addEventListener("blur", blur);
};

function search(key) {
    var div = document.getElementById("live-search");
    div.innerHTML = ""; //CLEAR RESULTS

    if(input.value != ""){
        var ajax = new XMLHttpRequest();
        ajax.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {

                var resultados = JSON.parse(this.responseText);  
                for(var i in resultados){
                    var name = resultados[i]['nombre'];
                    var a = document.createElement("a");

                    console.log(name);
                    var re = new RegExp(input.value, "i"); // toma el valor de input insensitive
                    name = name.replace(re, "<strong>" + name.match(re) + "</strong>");
   
                    a.innerHTML = name;
                    a.href = "producto.php?prod=" + resultados[i]['id'];
                    div.append(a);
                }
            }
        }
        ajax.open("GET", "searchResults.php?input=" + input.value, true);
        ajax.send();
    }
}

function focus(){
    document.getElementById("live-search").classList.add("search-results-show");
}

function blur(){
    document.getElementById("live-search").classList.remove("search-results-show");
}