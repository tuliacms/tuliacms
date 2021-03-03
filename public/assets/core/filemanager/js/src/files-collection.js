Tulia.Filemanager.FilesCollection = function () {
    this.collection = [];

    this.setAll = function (collection) {
        this.collection = collection;
    };

    this.getAll = function () {
        return this.collection;
    };

    this.prependFile = function (file) {
        for (let i = 0; i < this.collection.length; i++) {
            if (this.collection[i].type === 'file') {
                this.collection.splice(i, 0, file);
            }
        }
    };

    this.prependDirectory = function (file) {
        this.collection.unshift(file);
    };

    this.find = function (id) {
        for (let i = 0; i < this.collection.length; i++) {
            if (this.collection[i].id === id) {
                return this.collection[i];
            }
        }

        return null;
    };

    this.getFiles = function () {
        return this.collection;
    };

    this.getDirectories = function () {
        return this.collection;
    };
};
