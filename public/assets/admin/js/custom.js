$(document).ready(function(){
    // initilize summernite
    initialize_summernote();

    $(document).on("click", ".delete-record", function (e) {
        e.preventDefault();

        let url = $(this).data("href");

        $.confirm({
            title: "Confirm Delete",
            content: "Are you sure you want to delete this record?",
            type: "red",
            buttons: {
                yes: {
                    text: "Yes, Delete",
                    btnClass: "btn-red",
                    action: function () {
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: { _token: csrf_token },
                            datatype:'JSON',
                            success: function (res) {
                                const success = res.status === true;
                                showAlert(
                                    res.message || (success ? "Record deleted successfully!" : "Unable to delete record!"),
                                    success ? "Success" : "Alert",
                                    success ? "green" : "red",
                                    function () {
                                        if (success) {
                                            location.reload();   // Reload after clicking OK
                                        }
                                    }
                                );
                            },
                            error: function (xhr) {
                                showAlert("Something went wrong!", "Error", "danger");
                            }
                        });
                    }
                },
                no: {
                    text: "Cancel"
                }
            }
        });
    });

});

function initialize_summernote() {
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['misc', ['fullscreen', 'codeview']]
        ],
        disableDragAndDrop: true,
        popover: false,
        dialogsInBody: false,
        dialogsFade: false,

        callbacks: {
            onInit: function() {
                // Remove insert buttons if they show up
                $('.note-insert').remove();
                $('.note-view .note-link').remove();
                $('.note-view .note-picture').remove();
                $('.note-view .note-video').remove();
            }
        }
    });
}

// To show alert
function showAlert(message, title = "Alert", type = "info",callback = null) {
    const typeMap = {
        success: "green",
        danger: "red",
        error: "red",
        warning: "orange",
        info: "blue",
        primary: "blue",
        dark: "black",
        default: "blue"
    };

    $.alert({
        title: title,
        content: message,
        type: typeMap[type] || typeMap.default,
        buttons: {
            ok: {
                text: "OK",
                action: function () {
                    if (callback) callback();   // Run callback when OK is clicked
                }
            }
        }
    });
}

function showLoading(message = "Loading...") {
    window.loadingDialog = $.dialog({
        title: false,
        content: `
            <div class="text-center p-3">
                <div class="spinner-border text-primary"></div>
                <div class="mt-2">${message}</div>
            </div>
        `,
        closeIcon: false,
        backgroundDismiss: false,
        escapeKey: false,
        buttons: {},
        columnClass: "small",
    });
}

function hideLoading() {
    if (window.loadingDialog) {
        window.loadingDialog.close();
    }
}




