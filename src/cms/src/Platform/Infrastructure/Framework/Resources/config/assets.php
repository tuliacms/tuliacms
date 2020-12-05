<?php

return [
    'jquery' => [
        'scripts' => [ '/assets/core/jquery/jquery-3.4.1.min.js' ],
        'group' => 'head',
        'priority' => 1000,
    ],
    'jquery_ui' => [
        'scripts' => [ '/assets/core/jquery-ui/js/jquery-ui.min.js' ],
        'styles' => [ '/assets/core/jquery-ui/css/jquery-ui.min.css' ],
        'group' => 'head',
        'priority' => 500,
    ],
    'popperjs' => [
        'scripts' => [ '/assets/core/popperjs/popper.min.js' ],
        'priority' => 800,
    ],
    'font_awesome' => [
        'priority' => 1000,
        'styles' => [ '/assets/core/font-awesome/css/all.min.css' ],
        'group' => 'head',
    ],
    'animate_css' => [
        'priority' => 1000,
        'styles' => [ '/assets/core/animate-css/animate.min.css' ],
    ],
    'lodash' => [
        'priority' => 1000,
        'scripts' => [ '/assets/core/lodash/lodash.min.js' ],
    ],
    'chosen' => [
        'require' => [ 'chosen.css', 'chosen.js' ],
    ],
    'chosen.css' => [
        'priority' => 500,
        'group' => 'head',
        'styles' => [ '/assets/core/chosen/chosen.min.css' ],
    ],
    'chosen.js' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/chosen/chosen.jquery.min.js' ],
    ],
    'sweetalert2' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/sweetalert2/sweetalert2.min.js' ],
    ],
    'js_cookie' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/js-cookie/js.cookie-2.2.0.min.js' ],
    ],
    'simplebar' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/simplebar/simplebar.min.js' ],
        'styles' => [ '/assets/core/simplebar/simplebar.min.css' ],
    ],
    'datetimepicker' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/bootstrap-datetimepicker/js/tempusdominus-bootstrap-4.min.js', '/assets/core/bootstrap-datetimepicker-custom/defaults.js' ],
        'styles' => [ '/assets/core/bootstrap-datetimepicker/css/tempusdominus-bootstrap-4.min.css' ],
        'require' => [ 'momentjs' ],
    ],
    'momentjs' => [
        'priority' => 800,
        'scripts' => [ '/assets/core/momentjs/moment-with-locales.min.js' ],
    ],
    'vue' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/vuejs/vue.min.js' ],
    ],
    'nestable' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/jquery-nestable/jquery.nestable.js' ],
    ],
    'datatables_core' => [
        'priority' => 800,
        'scripts' => [ '/assets/core/datatables/datatables.min.js' ],
        'styles' => [ '/assets/core/datatables/datatables.min.css' ],
    ],
    'datatables' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/datatables/integrations/js/dataTables.bootstrap4.min.js' ],
        'styles' => [ '/assets/core/datatables/integrations/css/dataTables.bootstrap4.min.css' ],
        'require' => [ 'datatables_core' ],
    ],
    'datatable_tulia' => [
        'priority' => 200,
        'scripts' => [ '/assets/core/datatable-tulia/datatable.js' ],
        'require' => [ 'datatables' ],
    ],
    'jquery_typeahead' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/jquery-typeahead/jquery.typeahead.min.js' ],
        'styles' => [ '/assets/core/jquery-typeahead/jquery.typeahead.min.css' ],
    ],
    'bootstrap' => [
        'require' => [ 'jquery', 'popperjs', 'bootstrap.css', 'bootstrap.js' ],
    ],
    'bootstrap.css' => [
        'priority' => 500,
        'group' => 'head',
        'styles' => [ '/assets/core/bootstrap/css/bootstrap.min.css' ],
    ],
    'bootstrap.js' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/bootstrap/js/bootstrap.min.js' ],
    ],
    'jstree' => [
        'priority' => 500,
        'scripts' => [ '/assets/core/jstree/jstree.min.js' ],
        'styles' => [ '/assets/core/jstree/themes/default/style.min.css' ],
    ],
    'fileapi' => [
        'priority' => 500,
        'group' => 'head',
        'scripts' => [ '/assets/core/fileapi/dist/FileAPI.html5.min.js' ],
    ],
    'masonry' => [
        'priority' => 500,
        'group' => 'head',
        'scripts' => [ '/assets/core/masonry/masonry.pkgd.min.js' ],
    ],
    'backend.font' => [
        'priority' => 100,
        'styles' => [ 'https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&amp;subset=latin-ext' ],
        'group' => 'head',
    ],
    'backend.theme.head' => [
        'priority' => 100,
        'styles' => [ '/assets/core/backend/theme/css/style.css' ],
        'require' => [ 'backend.font', 'animate_css' ],
        'group' => 'head',
    ],
    'backend.theme' => [
        'priority' => 100,
        'scripts' => [
            '/assets/core/backend/theme/js/script.js',
            '/assets/core/backend/theme/js/search-anything.js',
        ],
        'require' => [ 'vue', 'simplebar', 'lodash', 'backend.theme.head' ],
    ],
    'backend' => [
        'priority' => 100,
        'scripts' => [ '/assets/core/backend/selected-elements-actions.js' ],
        'styles' => [ '/assets/core/backend/bootstrap-translations.css' ],
        'require' => [ 'bootstrap', 'font_awesome', 'chosen', 'sweetalert2', 'js_cookie', 'backend.theme' ],
    ],
    'customizer.back' => [
        'scripts' => [ '/assets/core/backend/customizer/customizer.js' ],
        'styles' => [ '/assets/core/backend/customizer/customizer.css' ],
        'require' => [ 'bootstrap', 'font_awesome', 'chosen', 'sweetalert2' ],
    ],
    'customizer.front' => [
        'scripts' => [ '/assets/core/frontend/customizer/customizer.js' ],
    ],
    'frontend' => [
        'priority' => 1000,
        'group' => 'head',
        'styles' => [ '/assets/core/frontend/default-assets/style.css' ],
    ],
    'filemanager' => [
        'group' => 'head',
        'require' => [ 'jquery', 'jstree', 'fileapi' ],
        'styles' => [ '/assets/core/filemanager/css/filemanager.css' ],
        'scripts' => [
            '/assets/core/filemanager/js/src/variables.js',
            '/assets/core/filemanager/js/src/front-controller.js',
            '/assets/core/filemanager/js/src/command-bus.js',
            '/assets/core/filemanager/js/src/container.js',
            '/assets/core/filemanager/js/src/directory-tree.js',
            '/assets/core/filemanager/js/src/event-dispatcher.js',
            '/assets/core/filemanager/js/src/files-collection.js',
            '/assets/core/filemanager/js/src/manager.js',
            '/assets/core/filemanager/js/src/model.js',
            '/assets/core/filemanager/js/src/selection.js',
            '/assets/core/filemanager/js/src/view.js',
            '/assets/core/filemanager/js/src/cache.js',
            '/assets/core/filemanager/js/src/upload.js',
            '/assets/core/filemanager/js/src/config.js',
        ],
    ],
];
