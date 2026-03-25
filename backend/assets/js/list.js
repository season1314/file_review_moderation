var page = 1
var currentId = null
var currentType =null
var currentText = ''
jQuery(document).ready(function ($) {
    const $msg = $('#frm-msg');
    function showNotification(text, isSuccess) {
        $msg.text(text);
        $msg.removeClass('msg-success msg-error').addClass('show ' + (isSuccess ? 'msg-success' : 'msg-error'));

        setTimeout(() => {
            $msg.removeClass('show');
        }, 3000);
    }

    $(document).on("click", "[name='list_opt']", function () {
        $(".app-review-box").addClass("disabled");
        if (this.dataset.page) {
            page = this.dataset.page
        } else {
            page = 1
        }
        const status = $("select[name='status']").val();
        const search = $("input[name='search_email']").val();
        list($, page, status, search)
    });


    $(document).on("click", ".frm-approve", function () {
        currentId = $(this).data('id');
        currentType = 'approved'
        $('#frm-confirm-text').css('display', 'none')
        $('#frm-confirm-overlay').css('display', 'flex');
    })

    $(document).on("click", ".frm-reject", function () {
        currentId = $(this).data('id');
        currentType = 'rejected'
        $('#frm-confirm-text').css('display', 'block')
        $('#frm-confirm-overlay').css('display', 'flex');
    })

    $(document).on("click", ".frm-reset", function () {
        currentId = $(this).data('id');
        currentType = 'pending'
        $('#frm-confirm-overlay').css('display', 'flex');
    })

    $('#frm-confirm-yes').on('click', function () {
        currentText = $('#frm-confirm-text').val()
        $(".app-review-box").addClass("disabled");
        $('#frm-confirm-text').val('')
        $('#frm-confirm-text').css('display', 'none')
        $('#frm-confirm-overlay').css('display', 'none');
        opt($, currentType, currentId, currentText);
    });

    $('#frm-confirm-no').on('click', function () {
        $('#frm-confirm-text').css('display', 'none')
        $('#frm-confirm-text').val('')
        $('#frm-confirm-overlay').css('display', 'none');
    });


    function opt($, status, id, notes) {
        var form_data = new FormData();
        form_data.append('id', id);
        form_data.append('status', status);
        form_data.append('notes', notes);
        form_data.append('action', 'frm_app_opt');
        $.ajax({
            url: frm_vars.ajax_url,
            type: 'POST',
            data: form_data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    showNotification('Upload Successful!', true);
                    const _status = $("select[name='status']").val();
                    if(_status){
                        list($, 1, _status, '')
                    }else{
                        list($, page, _status, '')
                    }
                } else {
                    $(".app-review-box").removeClass("disabled");
                    $('.app-review-list').scrollTop(0);
                }
            },
            error: function () {
                $(".app-review-box").removeClass("disabled");
                $('.app-review-list').scrollTop(0);
            }
        });
    }


    function list($, page, status, search) {
        var form_data = new FormData();
        form_data.append('page', page);
        form_data.append('status', status);
        form_data.append('search', search);
        form_data.append('action', 'frm_list');
        $.ajax({
            url: frm_vars.ajax_url,
            type: 'POST',
            data: form_data,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.success) {
                    if (response.data.rows) {
                        const list = response.data.rows;
                        const total = response.data.total_pages
                        let html = '';
                        list.forEach(row => {
                            html += `
                                <tr>
                                    <td>${row.id}</td>
                                    <td>${row.email}</td>
                                    <td>${renderStatus(row.status)}</td>
                                    <td><a href="${row.file_url}" target="_blank">View</a></td>
                                    <td>${row.last_notes}</td>
                                    <td>${row.created_at}</td>
                                    <td>
                                        ${row.status == 'pending' ? `
                                            <button class="button button-primary frm-approve" data-id="${row.id}">Approve</button>
                                            <button style="margin-left:5px" class="button button-secondary frm-reject" data-id="${row.id}">Reject</button>
                                            `: `<button class="button button-secondary frm-reset" data-id="${row.id}">Reset</button>`
                                }
                                    </td>
                                </tr>
                            `;
                        });
                        $('.app-review-list tbody').html(html);
                        if (total < 1) {
                            $(".app-review-pagination").html('')
                        } else {
                            const pagination_html = pagination(total)
                            $(".app-review-pagination").html(pagination_html)
                        }
                    }
                }
            },
            error: function () {
            },
            complete: function () {
                $(".app-review-box").removeClass("disabled");
                $('.app-review-list').scrollTop(0);
            }
        });
    }
})


function renderStatus(status) {
    if (status === 'pending') {
        return `<span style="color:orange;">Pending</span>`;
    } else if (status === 'approved') {
        return `<span style="color:green;">Approved</span>`;
    } else if (status === 'rejected') {
        return `<span style="color:red;">Rejected</span>`;
    }
}

function pagination(total) {
    let html = `<div class="tablenav"><div class="tablenav-pages">`
    let int_page = parseInt(page);
    if (page > 1) {
        html += `<a class="button" data-page="${int_page - 1}" name="list_opt" style="margin:0 2px">«</a>`
    }
    for (let i = 1; i <= total; i++) {
        if (i == int_page) {
            html += `<span class="button button-primary" style="margin:0 2px">${i}</span>`
        } else {
            html += `<a class="button" name="list_opt" data-page="${i}" style="margin:0 2px">${i}</a>`
        }
    }
    if (page < total) {
        html += `<a class="button" data-page="${int_page + 1}" name="list_opt" style="margin:0 2px">»</a>`
    }
    html += `</div></div>`
    return html
}