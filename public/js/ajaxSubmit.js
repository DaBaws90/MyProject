"use strict"

$(document).ready(() => {

    // var btnModalTrigger = $('button[data-target="#budgetModal"]');
    var budgetForm = $('#budgetForm');
    // var tableCells = $('#indextable1 tbody tr td');
    // var tableTotals = $('.avoid-sort tr td');
    var dialog = $('#dialog');

    // Form submit handler
    budgetForm.on('submit', (e) => {
        e.preventDefault();

        let requestParams = {
            comparison: $('input[name="comparison"]:checked').val(),
            percentage: $('input[name="percentage"]').val(),
            // products: {!! str_replace("'", "\'", json_encode($products)) !!},
            // ---------------------- ERROR DUE TO NON-EXISTING VAR FROM BLADE TEMPLATE (SCRIPT NEED TO BE PLACED IN THE TEMPLATE INSTEAD OF EXTERNAL JS FILE ) --------------------------------
        }

        let dialogParams = {
            title: '',
            mssg: '',
        }

        console.warn(requestParams.products);

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: budgetForm.attr('action'),
            type: 'POST',
            data: requestParams,
            success: function( data, textStatus, jqXHR ) {

                console.log("success callback")

                console.log(jqXHR);

                try {
                    dialogParams = setParams( jqXHR );
                } catch (error) {
                    dialogParams.mssg = error;
                }

                dialog.html(dialogParams.mssg);
                
                dialog.dialog({
                    width: 'auto',
                    modal: true,
                    show : { effect: "slide", duration: 1000 },
                    hide: { effect: "drop", duration: 800 },
                    // Do action at dialog's creation
                    open: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    // Do action at dialog's open transition finished 
                    focus: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    buttons: [
                        {  "text": "Cancel",
                            "click": function(){ 
                                $(this).dialog("close");
                                console.log("Cancelled")
                            } 
                        },
                        {  "text": "Ok",
                            "click": function() { 
                                $(this).dialog("close"); 
                                console.log("Accepted")
                            } 
                        }
                    ],
                    title: dialogParams.title,
                })
            },
            error: function( jqXHR, textStatus, errorThrown ) {

                console.log("error calback")

                console.log(jqXHR);

                try {
                    dialogParams = setParams( jqXHR, errorThrown );
                } catch (error) {
                    dialogParams.mssg = error;
                }

                dialog.html(dialogParams.mssg);

                dialog.dialog({
                    width: 'auto',
                    modal: true,
                    show : { effect: "slide", duration: 1000 },
                    hide: { effect: "drop", duration: 800 },
                    // Do action at dialog's creation
                    open: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    // Do action at dialog's open transition finished 
                    focus: function(event, ui) {
                        $(".ui-dialog").css({
                            zIndex: '1060',
                        });
                    },
                    buttons: [
                        {  "text": "Ok",
                            "click" : function() { 
                                $(this).dialog("close"); 
                                console.log("Accepted")
                            } 
                        }
                    ],
                    title: dialogParams.title,
                })
            }
        })

        // let test = tableCells.contents().filter(function() {
        //     return this.data ? true : false;
        // })

    });

    // AUX function
    function setParams( jqXHR, errorThrown = null ) {

        console.log("setParams reached")
        
        let dialogParams = {
            title: '',
            mssg: '',
        }

        switch(errorsType(jqXHR)) {

            case 'success':
                dialogParams.title = 'Success';
                dialogParams.mssg = jqXHR.responseJSON.success.message + " (Status code: " + jqXHR.status + ")";
                break;

            case 'percentage':
                dialogParams.title = 'Validation error';
                dialogParams.mssg = jqXHR.responseJSON.errors.percentage + " (Status code: " + jqXHR.status + ")";
                break;

            default:
                dialogParams.title = "Something went wrong (" + errorThrown + ")";
                dialogParams.mssg = "An unknow error occurred. Please, try again later. (Status code: " + jqXHR.status + ")";
                // dialogParams.mssg = jqXHR.responseJSON.errors.error + " (Status code: " + jqXHR.status + ")";
                break;

        }

        return dialogParams;
    }

    // AUX function
    function errorsType( jqXHR ) {

        console.log("errorsType reached")

        if ( jqXHR.responseJSON.errors ) {
            return jqXHR.responseJSON.errors.percentage ? 'percentage' : '';
        }
        else {
            return 'success';
        }
    }

});