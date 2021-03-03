Tulia.Filemanager = function (options) {
    this.options   = $.extend({}, Tulia.Filemanager.defaults, options);
    this.container = new Tulia.Filemanager.Container(Tulia.Filemanager.diConfig);

    this.init = function () {
        this.container.set('kernel', this);
        this.container.set('options', this.options);
        this.container.set('container', this.container);

        this.initServices();

        if (this.options.showOnInit) {
            this.show();
            this.open(this.options.open);
        }
    };

    this.cmd = function (cmd, ...args) {
        this.container.get('commandBus').cmd(cmd, ...args);
    };

    this.show = function () {
        this.cmd('show');
    };

    this.hide = function () {
        this.cmd('hide');
    };

    this.open = function (directory) {
        this.cmd('open', directory);
    };

    this.initServices = function () {
        let initiable = this.container.getTaggedServices('initiable');

        for (let i = 0; i < initiable.length; i++) {
            this.container.get(initiable[i].service).init();
        }
    };
};

Tulia.Filemanager.create = function (options) {
    let editor = new Tulia.Filemanager(options);
    editor.init();

    return editor;
};
