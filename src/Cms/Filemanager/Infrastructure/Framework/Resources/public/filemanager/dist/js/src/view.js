Tulia.Filemanager.View = function (eventDispatcher, commandBus, files, selection, options) {
    this.eventDispatcher = eventDispatcher;
    this.commandBus = commandBus;
    this.files = files;
    this.selection = selection;
    this.options = options;
    this.container = $(Tulia.Filemanager.template).appendTo('body');

    this.loaders = {
        'files-list' : {
            'selector': '.fm-files-list-loader',
            'activeClass': 'fm-active'
        }
    };

    this.init = function () {
        let self = this;
        this.container.find('[data-action]').click(function (e) {
            e.preventDefault();
            self.eventDispatcher.dispatch('action.' + $(this).attr('data-action'));
        });

        this.container.find('[data-cmd]').click(function (e) {
            e.preventDefault();
            self.commandBus.cmd($(this).attr('data-cmd'));
        });

        $(this.options.openTrigger).click(function () {
            self.cmdShow();
        });

        this.container.on('click', '.fm-file[data-type=file]', function () {
            self.eventDispatcher.dispatch('view.file.click', {
                id: $(this).attr('data-file-id'),
            });
        });

        this.container.on('dblclick', '.fm-file[data-type=file]', function () {
            self.eventDispatcher.dispatch('view.file.dblclick', {
                id: $(this).attr('data-file-id'),
            });
        });

        this.container.on('click', '.fm-file[data-type=directory]', function () {
            self.eventDispatcher.dispatch('view.directory.click', {
                id: $(this).attr('data-file-id'),
            });
        });

        self.eventDispatcher.on('action.editor.close', function () {
            self.cmdHide();
        });

        self.eventDispatcher.on('selected.change, view.rendered', function () {
            let selected = self.selection.getSelected();

            if (selected.length) {
                self.find('.fm-select-btn button').removeAttr('disabled');
                if (selected.length === 1) {
                    self.commandBus.cmd('status', 'Selected: <b>' + selected[0].name + '</b> <i>' + selected[0].size_formatted + '</i>');
                } else {
                    self.commandBus.cmd('status', 'Selected files: ' + selected.length);
                }
            } else {
                self.find('.fm-select-btn button').attr('disabled', 'disabled');
                self.commandBus.cmd('status', '');
            }
        });
    };

    this.find = function (selector) {
        return this.container.find(selector);
    };

    this.cmdStatus = function (status) {
        this.container.find('.fm-status').html(status);
    };

    this.cmdShow = function () {
        this.eventDispatcher.dispatch('view.opened');
        this.container.addClass('fm-opened');
    };

    this.cmdHide = function () {
        this.container.removeClass('fm-opened');
        this.eventDispatcher.dispatch('view.hidden');
    };

    this.cmdLoaderShow = function (type) {
        if (this.loaders[type]) {
            this.container.find(this.loaders[type].selector).addClass(this.loaders[type].activeClass);
        }
    };

    this.cmdLoaderHide = function (type) {
        if (this.loaders[type]) {
            this.container.find(this.loaders[type].selector).removeClass(this.loaders[type].activeClass);
        }
    };

    this.cmdRenderFiles = function () {
        let self = this;
        let f = this.files.getAll();
        let c = this.container.find('.fm-files-list');
        c.empty();

        for (let i = 0; i < f.length; i++) {
            let file = '';

            if (f[i].type === 'directory') {
                file = '<div class="fm-file-outer">\n' +
                    '    <div class="fm-file fm-file-type-' + f[i].type + '" data-type="directory" data-file-id="' + f[i].id + '">\n' +
                    '        <div class="fm-file-inner">\n' +
                    '            <div class="fm-file-preview"><span class="fm-file-image"></span></div>\n' +
                    '            <div class="fm-file-name">' + f[i].name + '</div>\n' +
                    '        </div>\n' +
                    '    </div>\n' +
                    '</div>';
            } else {
                let isSelected = self.selection.isSelected(f[i].id);

                file = '<div class="fm-file-outer">\n' +
                    '    <div class="fm-file fm-file-type-' + f[i].metadata.type + (isSelected ? ' fm-file-active' : '') + '" data-type="file" data-file-id="' + f[i].id + '">\n' +
                    '        <div class="fm-file-inner">\n' +
                    '            <div class="fm-file-extension">' + f[i].metadata.extension + '</div>\n' +
                    '            <div class="fm-file-preview">\n' +
                    '                <span class="fm-file-check"></span>' +
                    '                <span class="fm-file-image" style="background-image:url(' + f[i].preview + ')"></span>\n' +
                    '            </div>\n' +
                    '            <div class="fm-file-name" title="' + f[i].name + '">' + f[i].name + '</div>\n' +
                    '        </div>\n' +
                    '    </div>\n' +
                    '</div>';
            }

            c.append(file);
        }

        this.eventDispatcher.dispatch('view.rendered');
    };

    this.cmdRefreshFilesSelection = function () {
        let self = this;

        this.container.find('.fm-file').each(function () {
            let id = $(this).attr('data-file-id');

            if (self.selection.isSelected(id)) {
                $(this).addClass('fm-file-active');
            } else {
                $(this).removeClass('fm-file-active');
            }
        });
    };
};
