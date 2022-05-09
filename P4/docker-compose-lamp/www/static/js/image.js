// no uso onload, por que si no sobrecarga al de comentarios y
// no funciona el botón que despliega la caja de comentarios
window.onanimationstart = initialize;
var slideIndex = 1;
showSlides(slideIndex);

function initialize() {
    document.getElementById("prev").addEventListener("click", previous);
    document.getElementById("next").addEventListener("click", next);
}

function next(){
    showSlides(slideIndex += 1);
}
function previous(){
    showSlides(slideIndex -= 1);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("slide");
  var captions = document.getElementsByClassName("text");

  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
    slides[i].style.display = "none";
    captions[i].style.display = "none";
  }

  slides[slideIndex-1].style.display = "block";
  captions[slideIndex-1].style.display = "block";

}