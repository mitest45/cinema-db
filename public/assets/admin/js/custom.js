$(document).ready(function(){

    // initilize summernite
    initialize_summernote();
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
