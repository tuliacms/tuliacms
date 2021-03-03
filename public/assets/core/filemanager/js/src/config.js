Tulia.Filemanager.defaults = {
    /**
     * Target input, to where we should pass the selected file.
     *
     * @var string|jQuery
     */
    targetInput: null,
    /**
     * Element in document which opens the editor, like some button i.e.
     *
     * @var string|jQuery
     */
    openTrigger: null,
    /**
     * Open on init?
     *
     * @var bool
     */
    showOnInit: false,
    /**
     * Ajax endpoint.
     *
     * @var string
     */
    endpoint: null,
    /**
     * Directory to open when manager is shown. Null means root directory.
     *
     * @var null|string
     */
    open: null,
    /**
     * If true, user can select multiple files from multiple directories.
     *
     * @var bool
     */
    multiple: false,
    /**
     * Close modal when click "Select" on selected files?
     *
     * @var bool
     */
    closeOnSelect: true,
    /**
     * Callback function called when used click "Select" on selected files.
     *
     * @param files
     */
    onSelect: function (files) {},
    /**
     * Filter files.
     *
     * @var object
     */
    filter: {
        /**
         * File type. Like: [image, archive]. Use '*' to show all files.
         *
         * @var string|array
         */
        type: '*',
    },
};

Tulia.Filemanager.diConfig = {
    'eventDispatcher': {
        'class': Tulia.Filemanager.EventDispatcher,
    },
    'view': {
        'class': Tulia.Filemanager.View,
        'arguments': [
            '@eventDispatcher',
            '@commandBus',
            '@files',
            '@selection',
            '@options',
        ],
        'tags': [
            {'name':'initiable'},
            {'name':'command','command':'show','method':'cmdShow'},
            {'name':'command','command':'hide','method':'cmdHide'},
            {'name':'command','command':'loader.show','method':'cmdLoaderShow'},
            {'name':'command','command':'loader.hide','method':'cmdLoaderHide'},
            {'name':'command','command':'view.files.render','method':'cmdRenderFiles'},
            {'name':'command','command':'view.files.refresh-selection','method':'cmdRefreshFilesSelection'},
            {'name':'command','command':'status','method':'cmdStatus'}
        ]
    },
    'directoryTree': {
        'class': Tulia.Filemanager.DirectoryTree,
        'arguments': [
            '@eventDispatcher',
            '@model',
            '@commandBus',
            '@view',
            '@cache',
        ],
        'tags': [
            {'name':'initiable'}
        ]
    },
    'model': {
        'class': Tulia.Filemanager.Model,
        'arguments': [
            '@options',
        ],
    },
    'selection': {
        'class': Tulia.Filemanager.Selection,
        'arguments': [
            '@eventDispatcher',
            '@commandBus',
            '@files',
            '@options',
        ],
        'tags': [
            {'name':'initiable'}
        ]
    },
    'commandBus': {
        'class': Tulia.Filemanager.CommandBus,
        'arguments': [
            '@container',
        ],
        'tags': [
            {'name':'initiable'}
        ]
    },
    'manager': {
        'class': Tulia.Filemanager.Manager,
        'arguments': [
            '@eventDispatcher',
            '@commandBus',
            '@files',
            '@directoryTree',
            '@selection',
            '@cache',
            '@model',
            '@options',
        ],
        'tags': [
            {'name':'initiable'},
            {'name':'command','command':'open','method':'cmdOpen'},
            {'name':'command','command':'back','method':'cmdBack'},
            {'name':'command','command':'refresh','method':'cmdRefresh'},
            {'name':'command','command':'order-by-name-desc','method':'cmdOrderByNameDesc'},
            {'name':'command','command':'order-by-name-asc','method':'cmdOrderByNameAsc'},
            {'name':'command','command':'order-by-created-desc','method':'cmdOrderByCreationDesc'},
            {'name':'command','command':'order-by-created-asc','method':'cmdOrderByCreationAsc'},
            {'name':'command','command':'select','method':'cmdSelect'},
            {'name':'command','command':'deselect-all','method':'cmdDeselectAll'}
        ]
    },
    'upload': {
        'class': Tulia.Filemanager.Upload,
        'arguments': [
            '@eventDispatcher',
            '@commandBus',
            '@view',
            '@manager',
            '@model',
        ],
        'tags': [
            {'name':'initiable'},
        ]
    },
    'files': {
        'class': Tulia.Filemanager.FilesCollection
    },
    'cache': {
        'class': Tulia.Filemanager.Cache
    }
};

Tulia.Filemanager.template = '<div class="filemanager filemanager-container">\n' +
    '        <div class="fm-bg" data-action="editor.close"></div>\n' +
    '        <div class="fm-fg">\n' +
    '            <div class="fm-toolbar">\n' +
    '                <div class="fm-upload-btn">\n' +
    '                    <button type="button" class="btn btn-primary">\n' +
    '                        <span>Upload file</span>\n' +
    '                        <input type="file" class="fm-upload" multiple />\n' +
    '                    </button>\n' +
    '                </div>\n' +
    '                <div class="fm-buttons">\n' +
    '                    <button type="button" class="btn btn-outline-secondary btn-icon-only" data-cmd="back" title="Go back"><i class="btn-icon fas fa-chevron-left"></i></button>\n' +
    '                    <button type="button" class="btn btn-outline-secondary btn-icon-only" data-action="refresh" title="Refresh"><i class="btn-icon fas fa-sync-alt"></i></button>\n' +
    '                    <button type="button" class="btn btn-outline-secondary btn-icon-only" data-action="new-directory" title="New directory"><i class="btn-icon fas fa-folder-plus"></i></button>\n' +
    '                    <div class="dropdown d-inline-block">\n' +
    '                        <button class="btn btn-outline-secondary btn-icon-only" type="button" data-toggle="dropdown" title="Order by"><i class="btn-icon fas fa-sort-alpha-down"></i></button>\n' +
    '                        <div class="dropdown-menu">\n' +
    '                            <a class="dropdown-item active dropdown-item-with-icon" href="#" data-cmd="order-by-created-desc"><i class="dropdown-icon fas fa-sort-amount-down-alt"></i>Newest first</a>\n' +
    '                            <a class="dropdown-item dropdown-item-with-icon" href="#" data-cmd="order-by-created-asc"><i class="dropdown-icon fas fa-sort-amount-down"></i>Oldest first</a>\n' +
    '                            <a class="dropdown-item dropdown-item-with-icon" href="#" data-cmd="order-by-name-asc"><i class="dropdown-icon fas fa-sort-alpha-down"></i>By name</a>\n' +
    '                            <a class="dropdown-item dropdown-item-with-icon" href="#" data-cmd="order-by-name-desc"><i class="dropdown-icon fas fa-sort-alpha-down-alt"></i>By name</a>\n' +
    '                        </div>\n' +
    '                    </div>\n' +
    '                </div>\n' +
    '                <div class="fm-close">\n' +
    '                    <button type="button" data-action="editor.close"><i class="fas fa-times"></i></button>\n' +
    '                </div>\n' +
    '            </div>\n' +
    '            <div class="fm-tree">\n' +
    '                <div class="fm-directory-tree"></div>\n' +
    '            </div>\n' +
    '            <div class="fm-body">\n' +
    '                <div class="fm-files-list-loader fm-active"></div>\n' +
    '                <div class="fm-files-list"></div>\n' +
    '            </div>\n' +
    '            <div class="fm-bottombar">\n' +
    '                <div class="fm-upload-progressbar fm-hidden"><span class="fm-meter"></span></div>\n' +
    '                <div class="fm-status">\n' +
    '                    Current status\n' +
    '                </div>\n' +
    '                <div class="fm-select-btn">\n' +
    '                    <button type="button" class="btn btn-primary btn-icon-left" disabled data-cmd="select"><i class="btn-icon fas fa-check"></i>Select</button>\n' +
    '                </div>\n' +
    '            </div>\n' +
    '        </div>\n' +
    '        <div class="fm-dragondrop-area fm-hidden"><div><div><div>Upuść pliki tutaj...</div></div></div></div>\n' +
    '    </div>';
