/* <script src="{{ asset('js/aliasUpdateAjaxSubmit.js') }}"></script> */
"use strict"

$(document).ready(function() {
    
    // var btn = $('.showEditForm');
    var aliasText = $('.hideable');
    var aliasForm = $('.editAliasForm');
    var dialog = $('#dialog');
    var aliasInput = $('input[name="alias"]');
    var aliasSpan = $('.hideable span');
    var cancelAlias = $('.cancelEditAlias')

    $('.myTable').on('click', '.showEditForm', function() {
        let index = $(this).index('.showEditForm');
        // console.log("Button #" + index + " clicked!")
        toggleDOMElements(index)
    })

    $(document).on('blur', 'input[name="alias"]', function() {
        let index = $(this).index('input[name="alias"]')
        cancelEditAlias(index)
    })

    $('.myTable').on('click', '.cancelEditAlias', function(e) {
        e.preventDefault();
        let index = $(this).index('.cancelEditAlias')
        // console.log(`Boton #${index} pulsado`)
        cancelEditAlias(index)
    })

    function cancelEditAlias(index) {

        let data = {
            success: {
                file: {
                    alias: aliasInput.eq(index).attr('placeholder').split(':')[1]
                }
            }
        }
        toggleDOMElements(index, data)
    }

    $(document).on('submit', '.editAliasForm', function(e) {

        e.preventDefault();
        let index = $(this).index('.editAliasForm')
        // console.log("Form #" + index)

        let data = {
            id: $(this).children('input[name="id"]').val(),
            alias: $('input[name="alias"]').eq(index).val(),
        }
        // console.log("ID del fichero: " + data.id, "Alias: " + data.alias)

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            method: 'PATCH',
            type: 'POST',
            url: $(this).attr('action'),
            data: data,
            success: function( data, textStatus, jqXHR ) {

                let params;

                try{
                    params = setDialog( jqXHR );
                }
                catch(err) {
                    params.title = 'An unexpected error occurred'
                    params.sentence = err;
                }

                dialog.html(params.sentence);

                dialog.dialog({
                    width: 'auto',
                    title: params.title,
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                            toggleDOMElements(index, data);
                        }
                    }
                });
            },
            error: function( jqXHR, textStatus, errorThrown ) {
                
                let params;
                
                try{
                    params = setDialog( jqXHR, errorThrown );
                }
                catch(err) {
                    params.title = 'An unexpected error occurred'
                    params.sentence = err;
                }

                dialog.html(params.sentence);
                
                dialog.dialog({
                    width: 'auto',
                    title: params.title,
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            }
        })
    })

    function toggleDOMElements(index, data = null) {
        // console.log("Elemento #" + index, "Datos: " + data)
        aliasText.eq(index).toggle();
        aliasForm.eq(index).toggle();
        // cancelAlias.eq(index).toggle();


        // $('input[name="alias"]').eq(index).attr('autofocus', true)
        updateValues(index, data);
    }

    function updateValues(index, data = null) {
        aliasSpan.eq(index).html((data != null) ? data.success.file.alias : aliasSpan.eq(index).val());
        aliasInput.eq(index).attr('placeholder', ((data != null) ? 'Last value: ' + data.success.file.alias : aliasInput.eq(index).attr('placeholder')));
        aliasInput.eq(index).val('');
    }

    function setDialog(jqXHR, errorThrown = null ) {
        
        let params = {
            title: '',
            sentence: '',
        };

        if( jqXHR.responseJSON.errors ) {

            if( jqXHR.responseJSON.errors.alias ) {
                params.title = 'Validation error';
                params.sentence = jqXHR.responseJSON.errors.alias + ' (Status code: ' + jqXHR.status + ')';
            }
            else {
                params.title = 'Something went wrong (' + errorThrown +')';
                params.sentence = jqXHR.responseJSON.errors.update + ' (Status code: ' + jqXHR.status + ')';
            }

        }
        else {
            params.title = 'Success';
            params.sentence = jqXHR.responseJSON.success.update + ' (Status code: ' + jqXHR.status + ')';
        }

        return params;
    } 

});