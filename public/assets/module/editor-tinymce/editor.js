$(function () {
    let minHeight = 300;
    let windowHeight = window.screen.height;

    switch (windowHeight) {
        case 1080:
            minHeight = 635;
            break;
        case 900:
            minHeight = 567;
            break;
        case 768:
            minHeight = 435;
            break;
    }

    tinymce.init({
        selector: 'textarea.tinymce-control',
        plugins: 'autoresize advlist autolink lists link image charmap hr anchor searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking save table contextmenu directionality paste textcolor colorpicker textpattern codemirror stickytoolbar","toolbar1":"undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | grid-generator | page-link image media link unlink | styleselect | fontsizeselect | fullscreen',
        toolbar: 'autoresize advlist autolink lists link image charmap hr anchor searchreplace wordcount visualblocks visualchars code fullscreen media nonbreaking save table contextmenu directionality paste textcolor colorpicker textpattern codemirror stickytoolbar","toolbar1":"undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | outdent indent | grid-generator | page-link image media link unlink | styleselect | fontsizeselect | fullscreen',
        menubar: 'file edit insert view format table tools help',
        autoresize_bottom_margin: 0,
        autoresize_on_init: true,
        min_height: minHeight,
        setup: function (editor) {
            let getContentsHeight = function () {
                return $(editor.iframeElement).innerHeight();
            };
            editor.on('init', function () {
                $('.tinymce-control-container').removeClass('loading');
            });
            editor.on('keydown', function () {
                getContentsHeight()
            });
        }
    });
});
