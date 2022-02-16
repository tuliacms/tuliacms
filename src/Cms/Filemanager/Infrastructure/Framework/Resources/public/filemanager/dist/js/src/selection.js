Tulia.Filemanager.Selection = function (eventDispatcher, commandBus, files, options) {
    this.eventDispatcher = eventDispatcher;
    this.commandBus = commandBus;
    this.files = files;
    this.options = options;
    this.selected = [];

    this.init = function () {
        let self = this;

        this.eventDispatcher.on('view.file.click', function (file) {
            self.toggle(file.id);
        });

        this.eventDispatcher.on('upload.complete.partial', function (files) {
            for (let i = 0; i < files.length; i++) {
                self.select(files[i].id);
            }
        });

        this.eventDispatcher.on('view.file.dblclick', function (file) {
            self.selectOnly(file.id);
        }, 1000);

        this.eventDispatcher.on('selected.change', function () {
            self.commandBus.cmd('view.files.refresh-selection');
        });
    };

    this.getSelected = function () {
        let collection = [];

        for (let i = 0; i < this.selected.length; i++) {
            let file = this.files.find(this.selected[i]);

            if (file) {
                collection.push(file);
            }
        }

        return collection;
    };

    this.toggle = function (id) {
        if (this.isSelected(id)) {
            this.deselect(id);
        } else {
            this.select(id);
        }
    };

    this.select = function (id) {
        if (this.options.multiple) {
            this.selected.push(id);
        } else {
            this.selected = [id];
        }

        this.eventDispatcher.dispatch('selected.change');
    };

    this.selectOnly = function (id) {
        this.selected = [id];
        this.eventDispatcher.dispatch('selected.change');
    };

    this.deselect = function (id) {
        for (let i = 0; i < this.selected.length; i++) {
            if (this.selected[i] === id) {
                this.selected.splice(i, 1);
            }
        }

        this.eventDispatcher.dispatch('selected.change');
    };

    this.isSelected = function (id) {
        for (let i = 0; i < this.selected.length; i++) {
            if (this.selected[i] === id) {
                return true;
            }
        }

        return false;
    };

    this.clear = function () {
        this.selected = [];
        this.eventDispatcher.dispatch('selected.change');
    };
};
