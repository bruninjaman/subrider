var service = document.getElementById("service");
var peca = document.getElementById("pecas");
var adiantamento = document.getElementById("adiantamento");

var divservice = document.getElementById("form_services");
var divpeca = document.getElementById("form_pecas");
var divadiantamento = document.getElementById("form_adiantamento");


function tipo_item_onchange() {
  if (service.checked) {
    divservice.style.display = "block";
    divpeca.style.display = "none";
    divadiantamento.style.display = "none";
  }
  if (peca.checked) {
    divpeca.style.display = "block";
    divservice.style.display = "none";
    divadiantamento.style.display = "none";
  }
  if (adiantamento.checked) {
    divadiantamento.style.display = "block";
    divpeca.style.display = "none";
    divservice.style.display = "none";
  }
}
