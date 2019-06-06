"use strict";

$(document).ready(() => {
    let sentence = ""

    $(document).on('click', '.onConfirm', function(e) {
        // e.preventDefault()
        // console.log(this)
        // console.log(`Btn #${$(this).index('.onConfirm')} pressed`)
        // console.log($('.onConfirm'))

        switch(true) {

            case hasClass(this, "save"):
                sentence = "Los cambios se guardarán. ¿Estás seguro?";
                return confirm(sentence);
                break;
        
            case hasClass(this, "delete"):
                sentence = "¿Está seguro de que desea eliminar el registro?";
                return confirm(sentence);
                break;

            case hasClass(this, "disable"):
                sentence = "Los siguientes usuarios se habilitarán / deshabilitarán. ¿Desea continuar?";
                return confirm(sentence);
                break;

            case hasClass(this, "verify"):
                sentence = "Va a verificar el email del siguiente usuario. ¿Desea continuar?";
                return confirm(sentence);
                break;
        
            default:
                break;
        }
    })
})

function hasClass(object, text) {
    return object.classList.toString().includes(text);
}