/* <script src="{{ asset('js/aliasUpdateAjaxSubmit.js') }}"></script> */
"use strict"

$(document).ready(function() {
    
    var btn = $('#showEditForm');
    var aliasText = $('.hideable');
    var aliasForm = $('.editAliasForm');
    var dialog = $('#dialog');
    var aliasInput = $('input[name="alias"]');
    var aliasSpan = $('.hideable span');

    btn.on('click', () => {
        // btn.hide();
        // aliasForm.show();
        // aliasText.hide();
        toggleDOMElements();
    });
    
    aliasForm.submit( (e) => {
        e.preventDefault();

        let data = {
            id: $('input[name="id"]').val(),
            alias: aliasInput.val(),
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            method: 'PATCH',
            type: 'POST',
            url: aliasForm.attr('action'),
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
                            toggleDOMElements(data);
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
            },
            // complete: function(jqXHR, textStatus) {

            // }
        })
    })

    function toggleDOMElements(data = null) {
        aliasText.toggle();
        aliasForm.toggle();
        btn.toggle();
        updateValues(data);
    }

    function updateValues(data = null) {
        aliasSpan.html((data != null) ? data.success.file.alias : aliasSpan.val());
        aliasInput.attr('placeholder', ((data != null) ? 'Last value: ' + data.success.file.alias : aliasInput.attr('placeholder')));
        aliasInput.val('');
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