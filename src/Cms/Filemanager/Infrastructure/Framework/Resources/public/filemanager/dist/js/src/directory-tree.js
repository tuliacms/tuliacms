Tulia.Filemanager.DirectoryTree = function (eventDispatcher, model, commandBus, view, cache) {
    this.eventDispatcher = eventDispatcher;
    this.model = model;
    this.commandBus = commandBus;
    this.cache = cache;
    this.tree = view.find('.fm-directory-tree');
    /**
     * this.open store directory name for command, when `open` CMD is called before
     * jstree's `directory-tree` API call. Make root directory opened and selected in tree.
     */
    this.open = null;

    /**
     * Store source tree, fetched from API.
     */
    this.treeSource = null;

    this.init = function () {
        let self = this;

        this.eventDispatcher.on('open', function (directory) {
            self.tree.jstree(true).deselect_all(true);
            self.tree.jstree(true).select_node(directory, false, false);
            self.tree.jstree(true).open_node(directory);
            self.open = directory;
        });

        this.eventDispatcher.on('action.refresh', function () {
            self.cache.remove('directory-tree');
            self.tree.jstree(true).refresh(false, false);
        });

        this.tree.on('select_node.jstree', function (event, data) {
            self.commandBus.cmd('open', data.selected[0]);
        });

        this.tree.jstree({
            plugins : [ 'wholerow' ],
            core: {
                multiple : false,
                data: function (obj, callback) {
                    self.cache.call('directory-tree', Tulia.Filemanager.Cache.TEN_MINUTES, function (callback) {
                        self.model.cmd('directory-tree', {
                            open: self.open
                        }, function (data) {
                            callback(data);
                        }, function () {
                            callback([]);
                        });
                    }, function (data) {
                        self.treeSource = $.extend(true, {}, data);
                        callback.call(obj, data);
                    });
                }
            }
        });
    };

    this.getParent = function (id) {
        if (!id || id === this.root) {
            return this.treeSource[0];
        }

        let parent = this.treeSource[0];
        let findInChildren = function (id, item) {
            for (let i = 0; i < item.children.length; i++) {
                if (item.children[i].id === id) {
                    parent = item;
                }
                 if (item.children[i].children) {
                     findInChildren(id, item.children[i]);
                 }
            }
        };

        findInChildren(id, this.treeSource[0]);

        return parent;
    };
};
