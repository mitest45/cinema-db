$(document).ready(function(){

    // initilize summernite
    initialize_summernote();
});

function initialize_summernote() {
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });
}
