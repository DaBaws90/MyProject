"use strict";

$(document).ready(() => {
setTimeout(() => {

    const submitBtns = document.querySelectorAll('.onConfirm');
    let sentence = "";

    // Can't find the way to avoid TypeScript error on console for undefined/null btn 
    // if(submitBtns.length > 0 && submitBtns !== undefined) { // <-- NOT WORKING
        submitBtns.forEach(btn => {
            btn.onclick = () => {
                switch(true) {

                    case hasClass(btn, "save"):
                        sentence = "Los cambios se guardarán. ¿Estás seguro?";
                        return confirm(sentence);
                        break;
                
                    case hasClass(btn, "delete"):
                        sentence = "¿Está seguro de que desea eliminar el registro?";
                        return confirm(sentence);
                        break;

                    case hasClass(btn, "disable"):
                        sentence = "Los siguientes usuarios se habilitarán / deshabilitarán. ¿Desea continuar?";
                        return confirm(sentence);
                        break;

                    case hasClass(btn, "verify"):
                        sentence = "Va a verificar el email del siguiente usuario. ¿Desea continuar?";
                        return confirm(sentence);
                        break;
                
                    default:
                        break;
                }
            }
        
        });
    // }
}, 50);


    function hasClass(object, text) {
        return object.classList.toString().includes(text);
    }

})

