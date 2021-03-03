Tulia.Filemanager.Container = function (config) {
    this.config = config;
    this.objects = [];

    this.get = function (name) {
        if (this.objects[name]) {
            return this.objects[name];
        }

        return this.objects[name] = this.build(name);
    };

    this.set = function (name, obj) {
        this.objects[name] = obj;
    };

    this.build = function (name) {
        let definition = this.config[name];
        let arguments = this.resolveArguments(definition.arguments);

        obj = new definition.class(...arguments);

        if (definition.calls) {
            for (let i = 0; i < definition.calls.length; i++) {
                obj[i].apply(obj, this.resolveArguments(definition.calls[i]));
            }
        }

        return obj;
    };

    this.getTaggedServices = function (tag) {
        let tagged = [];

        for (let name in this.config) {
            if (this.config[name].tags) {
                for (let def in this.config[name].tags) {
                    if (this.config[name].tags[def].name === tag) {
                        tagged.push({
                            'service': name,
                            'tag': this.config[name].tags[def],
                        });
                    }
                }
            }
        }

        return tagged;
    };

    this.resolveArguments = function (args) {
        let arguments = [];

        if (args) {
            for (let i = 0; i < args.length; i++) {
                arguments.push(this.resolveArgument(args[i]));
            }
        }

        return arguments;
    };

    this.resolveArgument = function (arg) {
        if (typeof(arg) === 'string') {
            if (arg.substring(0, 1) === '@') {
                return this.get(arg.substring(1));
            } else {
                return arg;
            }
        }

        return arg;
    };
};
