(function () {
    let Customizer = function () {
        this.customizedEvents = {};

        this.init = function () {
            let self = this;

            $(function () {
                self.bindLinks();
            });

            this.bindPostMessages();
        };

        this.bindLinks = function () {
            let self = this;

            $('body').on('click', 'a', function (e) {
                e.preventDefault();

                let link = $(this).get(0);

                if(self.isExternal(link))
                {
                    alert('External links are blocked in Customizer mode.');
                    return;
                }

                if(self.isPreloadable(link) === false)
                    return;

                self.open(link.href);
            });
        };

        this.isExternal = function (link) {
            return link.origin !== location.origin && (link.protocol === 'http:' || link.protocol === 'https:');
        };

        this.isPreloadable = function (link) {
            if(link.origin !== location.origin)
                return false;

            if(link.protocol !== location.protocol)
                return false;

            if(link.hash)
                return false;

            if(link.getAttribute('href') === '#')
                return false;

            return true;
        };

        this.bindPostMessages = function () {
            let self = this;

            window.addEventListener('message', function (event) {
                if(event.origin !== document.location.origin)
                    return;

                if(event.data.channel !== 'customizer')
                    return;

                switch(event.data.command)
                {
                    case 'customized': self.callCustomized(event.data.payload.name, event.data.payload.value); break;
                }
            }, false);
        };

        this.open = function (link) {
            parent.postMessage({
                channel : 'customizer',
                port    : null,
                command : 'change-location',
                payload : {
                    href: link
                }
            }, document.location.origin);
        };

        this.callCustomized = function (name, value) {
            if(this.customizedEvents[name])
            {
                for(let i = 0; i < this.customizedEvents[name].length; i++)
                {
                    this.customizedEvents[name][i](value);
                }
            }
        };

        this.customized = function (name, callback) {
            if(! this.customizedEvents[name])
                this.customizedEvents[name] = [ callback ];
            else
                this.customizedEvents[name].push(callback);
        };
    };

    window.Customizer = window.Customizer || Customizer;
})();

const customizer = new Customizer;
customizer.init();
