Tulia.Filemanager.CommandBus = function (container) {
    this.container = container;
    this.commands = [];

    this.init = function () {
        let commands = this.container.getTaggedServices('command');

        for (let i = 0; i < commands.length; i++) {
            this.commands.push({
                service: commands[i].service,
                command: commands[i].tag.command,
                method:  commands[i].tag.method,
            });
        }
    };

    this.cmd = function (cmd, ...args) {
        for (let i = 0; i < this.commands.length; i++) {
            if (this.commands[i].command === cmd) {
                let name = this.commands[i].service;
                let service = this.container.get(name);

                if (service) {
                    let method = this.commands[i].method;

                    if (service[method]) {
                        service[method].apply(service, args);
                    } else {
                        throw new Error(`Method '${method}' of service '${name}' not found, command cannot be called.`);
                    }
                } else {
                    throw new Error(`Service '${name}' not found, command cannot be called.`);
                }
            }
        }
    }
};
