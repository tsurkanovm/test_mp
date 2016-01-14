$(document).ready(
    function(){

        var container = $("#data-container");
        var read_form = $("#read-form");

        $(document).on('beforeSubmit', "#read-form", function(){
            //console.log( read_form.serialize() );
            $("#data-container").load(read_form.attr('action'), $.param( read_form ), function(response){
                container.text( response );
            });
            return false;
        } );

    }
)
