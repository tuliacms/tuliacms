Tulia.Filemanager.Manager = function (eventDispatcher, commandBus, files, directoryTree, selection, cache, model, options) {
    this.eventDispatcher = eventDispatcher;
    this.commandBus = commandBus;
    this.files = files;
    this.directoryTree = directoryTree;
    this.selection = selection;
    this.cache = cache;
    this.model = model;
    this.options = options;

    this.root = '00000000-0000-0000-0000-000000000000';
    this.currentDirectory = null;
    this.orderBy = 'created';
    this.orderDir = 'desc';

    this.init = function () {
        let self = this;

        this.eventDispatcher.on('view.directory.click', function (data) {
            self.commandBus.cmd('open', data.id);
        });

        this.eventDispatcher.on('action.refresh', function () {
            self.commandBus.cmd('refresh', {cache: false});
        });

        this.eventDispatcher.on('upload.complete', function () {
            self.commandBus.cmd('refresh', {cache: false});
        });

        this.eventDispatcher.on('view.opened', function () {
            self.commandBus.cmd('refresh', {cache: false});
        });

        this.eventDispatcher.on('view.file.dblclick', function () {
            self.commandBus.cmd('select');
        });
    };

    this.cmdOpen = function (directory) {
        if (!directory) {
            directory = this.root;
        }

        if (this.currentDirectory === directory) {
            return;
        }

        this.currentDirectory = directory;

        this.eventDispatcher.dispatch('open', directory);
        this.cmdRefresh({cache: true});
    };

    this.cmdRefresh = function (params) {
        let self = this;

        params = $.extend({}, {
            cache: false,
        }, params);

        if (!params.cache) {
            this.cache.remove(this.getCacheKey());
        }

        this.commandBus.cmd('loader.show', 'files-list');

        this.cache.call(this.getCacheKey(), Tulia.Filemanager.Cache.TEN_MINUTES, function (callback) {
            self.model.cmd('ls', {
                directory: self.getCurrentDirectory(),
                orderBy  : self.orderBy,
                orderDir : self.orderDir,
                filter   : self.options.filter
            }, function (data) {
                callback(data);
            }, function () {
                callback([]);
            });
        }, function (data) {
            self.files.setAll(data);
            self.commandBus.cmd('view.files.render');
            self.commandBus.cmd('loader.hide', 'files-list');
        });
    };

    this.cmdBack = function () {
        let parent = this.directoryTree.getParent(this.currentDirectory);

        if (parent) {
            this.eventDispatcher.dispatch('open', parent.id);
        } else {
            this.eventDispatcher.dispatch('open', this.root);
        }
    };

    this.cmdOrderByNameDesc = function () {
        this.orderBy = 'name';
        this.orderDir = 'desc';
        this.cmdRefresh({cache: true});
    };

    this.cmdOrderByNameAsc = function () {
        this.orderBy = 'name';
        this.orderDir = 'asc';
        this.cmdRefresh({cache: true});
    };

    this.cmdOrderByCreationDesc = function () {
        this.orderBy = 'created';
        this.orderDir = 'desc';
        this.cmdRefresh({cache: true});
    };

    this.cmdOrderByCreationAsc = function () {
        this.orderBy = 'created';
        this.orderDir = 'asc';
        this.cmdRefresh({cache: true});
    };

    this.cmdSelect = function () {
        if (this.options.closeOnSelect) {
            this.commandBus.cmd('hide');
        }

        this.options.onSelect(this.selection.getSelected());
    };

    this.cmdDeselectAll = function () {
        this.options.onSelect(this.selection.getSelected());
    };

    this.getCurrentDirectory = function () {
        return this.currentDirectory ? this.currentDirectory : this.root;
    };

    this.getCacheKey = function () {
        return this.currentDirectory + this.orderBy + this.orderDir + JSON.stringify(this.options.filter);
    };
};
