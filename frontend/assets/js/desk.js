jQuery(document).ready(function ($) {
    const $fileInput = $('#frm_file_input')
    const $msg = $('#frm-msg');
    const $file = $('#frm_desk_file')
    const $upload = $('#frm_desk_upload')
    const $submit = $('#frm_desk_submit')

    function showNotification(text, isSuccess) {
        $msg.text(text);
        $msg.removeClass('msg-success msg-error').addClass('show ' + (isSuccess ? 'msg-success' : 'msg-error'));

        setTimeout(() => {
            $msg.removeClass('show');
        }, 3000);
    }

    $upload.on("click", function (e) {
        e.preventDefault();
        $fileInput.click();
    });

    $fileInput.on('change', function () {
        if (this.files.length > 0) {
            const file_data = this.files[0];
            var form_data = new FormData();
            form_data.append('file', file_data);
            form_data.append('action', 'frm_upload_action');
            form_data.append('security', frm_vars.nonce);
            $upload.prop("disabled", true);
            $submit.prop("disabled", true);
            $.ajax({
                url: frm_vars.ajax_url,
                type: 'POST',
                data: form_data,
                contentType: false,
                processData: false,
                success: function (response) {
                    showNotification('Upload Successful!', true);
                    let name = response.data.url.split('/').pop();
                    if (name.length > 7) {
                        name  = name.slice(0, 10) + '...';
                      }
                    const html = `File (${name}) has been uploaded.`
                    $file.attr('data-url', response.data.url);
                    $file.find('span').first().html(html);
                },
                error: function () {
                    showNotification('Upload Failed!', false);
                },
                complete: function () {
                    $upload.prop("disabled", false);
                    $submit.prop("disabled", false);
                }
            });
        }
    });


    $submit.on('click', function () {
        const file_url = $file.attr('data-url');
        console.log(file_url)
        if (!file_url) {
            showNotification('Please Upload file first than Submit', false);
            return
        }
        var form_data = new FormData();
        form_data.append('file_url', file_url);
        form_data.append('action', 'frm_from_submit');
        $upload.prop("disabled", true);
        $submit.prop("disabled", true);
        $.ajax({
            url: frm_vars.ajax_url,
            type: 'POST',
            data: form_data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success == false) {
                    showNotification(response.data, false);
                    $upload.prop("disabled", false);
                    $submit.prop("disabled", false);
                } else {
                    showNotification(response.data, true);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            },
            error: function (error) {
                showNotification(error.data, false);
                $upload.prop("disabled", false);
                $submit.prop("disabled", false);
            },
        });
    })

    
})