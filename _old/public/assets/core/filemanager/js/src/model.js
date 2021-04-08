Tulia.Filemanager.Model = function (options) {
    this.options = options;

    this.cmd = function (cmd, data, success, error, options) {
        options = options || {};

        options.url = this.createEndpoint(cmd);
        options.method = 'POST';
        options.data = data || {};
        options.success = success || function () {};
        options.error = error || function () {};
        options.dataType = 'json';

        $.ajax(options);
    };

    this.createEndpoint = function (cmd) {
        return this.options.endpoint + '?cmd=' + cmd;
    };
};
