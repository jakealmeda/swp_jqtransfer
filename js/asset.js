( function($) {

    var WindowWidth;

    $( document ).ready( function() {
        SWPExplodeVariables();
    });


    // execute function when window size changes
    $( window ).resize( function() {
        SWPExplodeVariables();
    });


    // main function
    function SWPExplodeVariables() {

        WindowWidth = $( window ).width();

        $.each( jqtransfer_extra_var, function( index, value ) {

            //alert( value.spk_source + ' | ' + value.spk_source_hide + ' | ' + value.spk_target + ' | ' + value.spk_target_hide + ' | ' + value.spk_trigger );
            if( WindowWidth <= value.spk_trigger ) {
                SWPExecTransfer( value.spk_source, value.spk_target, value.spk_source_hide );
            } else {
                SWPExecTransfer( value.spk_target, value.spk_source, value.spk_target_hide );
            }
            
        });

    }

    // handle the actual transfers
    function SWPExecTransfer( Source, Target, HideMe ) {
        //  Hideme's value is either "checked" or "no"

        // assign to var
        TransferThis = $( Source ).html();
        
        // remove previous contents
        $( Source ).empty();
        //$( Source ).html( '' );
        
        // hide container
        if( HideMe == 'checked' ) {
            $( Source ).hide();
        }
        
        // transfer
        if( $( Target ).is( ':hidden' ) ) {
            $( Target ).removeAttr( 'style' ).append( TransferThis );
        } else {
            $( Target ).append( TransferThis );
        }
    }

})( jQuery );