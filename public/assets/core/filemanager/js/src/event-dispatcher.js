Tulia.Filemanager.EventDispatcher = function () {
    this.events = [];

    this.on = function (events, listener, priority) {
        events = events.split(',');
        priority = priority || 100;

        for (let i = 0; i < events.length; i++) {
            let name = events[i].trim();

            if (this.events[name]) {
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            } else {
                this.events[name] = [];
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            }

            this.events[name].sort(function (a, b) {
                return b.priority - a.priority;
            });
        }

        return this;
    };

    this.dispatch = function (name, ...args) {
        if (! this.events[name]) {
            return this;
        }

        args = args || [];

        let self = this;

        for (let i = 0; i < this.events[name].length; i++) {
            this.events[name][i].listener.apply(self, args);
        }

        return this;
    };
};
