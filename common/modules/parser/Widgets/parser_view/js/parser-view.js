$(document).ready(
    function(event){

        var container = $("#data-container");
        var read_form = $("#read-form");

        var files;

        // Add events for file input
        $('input[type=file]').on('change', prepareUpload);

        // Grab the files and set them to our variable
        function prepareUpload(event)
        {
            files = event.target.files;
            console.log(files);
        }

        $(document).on('beforeSubmit', "#read-form", function(){

            var data = new FormData(read_form);
            $.each(files, function(key, value)
            {
                data.append(key, value);
                //formData.set(name, value, filename);
            });

            console.log(data);
            $.ajax({
                url: read_form.attr('action'),
                type: 'POST',
                data: data,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR)
                {
                    $.post(read_form.attr('action'), read_form.serialize()).done(
                        function(result) {
                            container.text( result );
                        }
                    ).fail(
                        function(){
                            container.text( 'Ошибка сервера' );
                        }
                    );

                },
                error: function(jqXHR, textStatus, errorThrown)
                {

                    console.log('ERRORS: ' + textStatus);

                }
            });


            return false;
        } );

    }
)
