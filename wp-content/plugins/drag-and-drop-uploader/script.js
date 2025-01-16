jQuery(document).ready(function($) {
    $('#ddu-dropbox').on('drag dragstart dragend dragover dragenter dragleave drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
    })
    .on('dragover dragenter', function() {
        $(this).addClass('is-dragover');
    })
    .on('dragleave dragend drop', function() {
        $(this).removeClass('is-dragover');
    })
    .on('drop', function(e) {
        $('#ddu-upload-status').empty(); // Clear all messages
        let droppedFiles = e.originalEvent.dataTransfer.files;
        let formData = new FormData();

        for (let i = 0; i < droppedFiles.length; i++) {
            formData.append('files[]', droppedFiles[i]);
        }
        
        formData.append('action', 'ddu_file_upload');
        formData.append('security', ddu_ajax_object.ddu_nonce);

        let progressBar = $('<div class="progress-bar"></div>').appendTo('#ddu-upload-status');

        $.ajax({
            url: ddu_ajax_object.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            xhr: function() {
                let xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
                    if (evt.lengthComputable) {
                        let percentComplete = evt.loaded / evt.total * 100;
                        progressBar.css({ width: percentComplete + '%' });
                    }
                }, false);
                return xhr;
            },
            success: function(response) {
                response = JSON.parse(response);
                if (response.error) {
                    $('#ddu-upload-status').html('<div class="error">' + response.error + '</div>');
                } else if (response.message) {
                    let numFiles = droppedFiles.length;
                    $('#ddu-upload-status').html('<div class="success">' + response.message + ' (' + numFiles + ' files uploaded)</div>');
                } else {
                    $('#ddu-upload-status').html('<div class="error">No files uploaded.</div>');
                }
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
                $('#ddu-upload-status').html('<div class="error">Error: ' + error + '</div>');
            }
        });
    });
});
