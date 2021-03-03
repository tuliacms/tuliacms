Tulia.Filemanager.Upload = function (eventDispatcher, commandBus, view, manager, model) {
    this.eventDispatcher = eventDispatcher;
    this.commandBus = commandBus;
    this.view = view;
    this.manager = manager;
    this.model = model;

    this.totalSize = 0;
    this.files = [];
    this.uploadedFiles = [];

    this.init = function () {
        if(! window.FileAPI) {
            console.error('FileAPI extension not loaded.');
            return;
        }

        this.createDragonDrop();
        this.createFileUploader();
    };

    this.createFileUploader = function () {
        let self = this;

        this.view.find('.fm-upload')
            .change(function (e) {
                self.upload(FileAPI.getFiles(e));
                $(this).val('');
            });
    };

    this.createDragonDrop = function () {
        if (!FileAPI.support.dnd) {
            return;
        }

        let self = this;
        let dndContainer = this.view.find('.fm-dragondrop-area');

        $(document).dnd(function (over){
            if (over) {
                dndContainer.removeClass('fm-hidden');
            } else {
                dndContainer.addClass('fm-hidden');
            }
        }, function (files){
            if(files.length) {
                self.upload(files);
            }
        });
    };

    this.upload = function (files) {
        let self = this;
        let progressbar = this.view.find('.fm-upload-progressbar');
        let meter = progressbar.find('.fm-meter');
        progressbar.removeClass('fm-hidden');

        this.files = [];
        this.uploadedFiles = [];

        for(let i = 0; i < files.length; i++)
        {
            this.totalSize += files[i].size;
            this.files.push({
                name: files[i].name,
                size: files[i].size,
                loaded: 0
            });
        }

        FileAPI.upload({
            url: this.model.createEndpoint('upload'),
            data: {
                directory: this.manager.getCurrentDirectory(),
            },
            files: {
                file: files
            },
            progress: function (evt, file) {
                let value = self.calculateLoadedSize(evt, file) / self.totalSize * 100;
                meter.css('width', value + '%');
                //self.app.view.updateProgressbar(self.calculateLoadedSize(evt, file) / self.totalSize * 100);
            },
            complete: function (err, xhr, file) {
                let json = JSON.parse(xhr.responseText);

                setTimeout(function () {
                    self.eventDispatcher.dispatch('upload.complete', self.uploadedFiles, xhr);
                    self.reset();
                    progressbar.addClass('fm-hidden');
                    meter.css('width', '0%');
                }, 500);

                if(json && json.status === 'error') {
                    swal({
                        title: 'Nie udało się :(',
                        text: json.message,
                        type: 'error'
                    });
                }
            },
            filecomplete: function (err, xhr, file) {
                let json = JSON.parse(xhr.responseText);

                self.uploadedFiles.concat($.extend({}, json.uploaded_files));
                self.eventDispatcher.dispatch('upload.complete.partial', json.uploaded_files, xhr);
            }
        });
    };

    this.calculateLoadedSize = function (event, file) {
        let loaded = 0;

        for(let i = 0; i < this.files.length; i++) {
            if(this.files[i].name === file.name) {
                this.files[i].loaded = event.loaded;
            }

            loaded += this.files[i].loaded;
        }

        return loaded;
    };

    this.reset = function () {
        this.totalSize = 0;
        this.view.find('.m-file-select')
            .val('');
    };
};
