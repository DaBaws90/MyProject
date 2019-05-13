(function() {
  var counter = 1;
  var btn = document.getElementById('btn');
  var form = document.getElementById('demo');
  var addInput = function() {
    counter++;
    var container = document.createElement("div");
    container.innerHTML = '<input name="'+counter+'" value="input" />';
    form.appendChild(container);
  };
  btn.addEventListener('click', function() {
    addInput();
  }.bind(this));
})();

    var num = 1;
document.getElementById('add').addEventListener("click",addInput);
function addInput(){
var newInput = '<input type="text" name="input'+num+'"/><br> <br>';
   document.getElementById('demo').innerHTML += newInput;  
   num++;
}