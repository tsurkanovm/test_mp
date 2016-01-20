$(document).ready(
    function(event){

        var container = $("#data-container");
        var read_form = $("#read-form");
        var safe_action = read_form.data('save_action');

        var files;

        // Add event for file input
        $(document).on('change', "input[type=file]" ,prepareUpload);
        // Add event for read form submit
        $(document).on('beforeSubmit', "#read-form", beforeSubmitReadForm);
        // Add event for write form submit
        $(document).on('beforeSubmit', "#write-form", beforeSubmitWriteForm);
        // Add event for write form submit
        $(document).on('click', "#update", updateButtonClick);
        $(document).on('click', "#write", writeButtonClick);

        // Grab the files and set them to our variable
        function prepareUpload(event)
        {
            files = event.target.files;
            var file_name = $(document.body).data('file_name');
            var file_modified_date = $(document.body).data('file_modified_date');

            if (file_name && file_modified_date) {
                // it's a not first attempt to save file
                if ( (files[0].lastModified == file_modified_date) && (files[0].name == file_name) ) {
                    // nothing changes
                    $(document.body).data('file_modified', false);
                }else{
                    // it's a new one file
                    $(document.body).data('file_modified', true);
                }
            } else{
                // it's a first attempt to save file
                $(document.body).data('file_name',files[0].name);
                $(document.body).data('file_modified_date', files[0].lastModified);
                $(document.body).data('file_modified', true);
            }

        }

        function updateButtonClick (  ) {
            // marked that update-button was clicked
            var update_field = $("input[name|='UploadFileParsingForm[update]']");
            update_field.val( true );

        }

        function writeButtonClick (  ) {
            // marked that write-button was clicked
            var update_field = $("input[name|='UploadFileParsingForm[update]']");
            update_field.val( false );

        }

        function beforeSubmitReadForm (){

            var file_was_modified = $(document.body).data('file_modified');

            // collect files for post request
            var data = new FormData(read_form);
            if ( file_was_modified ) {
                // if it's new file - put him to server data
                $.each(files, function(key, value)
                {
                    data.append(key, value);
                });
            }

            // reset modify flag
            $(document.body).data('file_modified', false);

            // for previous writing tryings - reset error msg
            var message_container = $("#message-container");
            if ( message_container.length ) {
                message_container.text( '');
            }

            // first request for saving file
            $.ajax({
                url: safe_action,
                type: 'POST',
                data: data,
                cache: false,
                processData: false,
                contentType: false,
                success: function(data, textStatus, jqXHR)
                {
                    //container.html( data );
                    // second request for validate form and perform read action
                    $.post(read_form.attr('action'), read_form.serialize()).done(
                        function(result) {
                            container.html( result );
                        }
                    ).fail(
                        function(){
                            container.text( 'Ошибка сервера' );
                        }
                    );

                },
                error: function(jqXHR, textStatus, errorThrown)
                {

                    container.text( 'Ошибка загрузки файла на сервер' );

                }
            });


            return false;
        }


        function beforeSubmitWriteForm (  ){
            var write_form = $("#write-form");
            var post_data = write_form.serialize();
            var message_container = $("#message-container");

            $.post( write_form.attr('action'), post_data ).done(
                function( result ) {
                    message_container.html( result );
                  //  container.append( message_container );
                }
            ).fail(
                function(){
                    message_container.text( 'Ошибка ответа с сервера при записи данных' );
                  //  container.append( message_container );
                }
            );
            return false;
        }
    }
)
