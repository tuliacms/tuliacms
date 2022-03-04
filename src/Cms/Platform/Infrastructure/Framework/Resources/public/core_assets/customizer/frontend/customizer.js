(function () {
    let Customizer = function () {
        this.customizedEvents = {};

        this.init = function () {
            let self = this;

            $(function () {
                self.bindLinks();
            });

            this.bindPostMessages();
            this.bindLiveControls();
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

        this.bindLiveControls = function () {
            $('[data-tulia-customizer-live-control]').each(function () {
                let options = JSON.parse($(this).attr('data-tulia-customizer-live-control'));

                customizer.customized(options.control, (value) => {
                    if (!value) {
                        value = options.default;
                    }

                    if (options.nl2br) {
                        value = value.replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br />$2');
                    }

                    switch (options.type) {
                        case 'background-image':
                            if (/^[0-9a-f]{8}-[0-9a-f]{4}-[0-5][0-9a-f]{3}-[089ab][0-9a-f]{3}-[0-9a-f]{12}$/i.test(value)) {
                                value = '/media/resolve/image/node_thumbnail/' + value + '/image.jpg';
                            }

                            $(this).css('background-image', 'url(' + value + ')');
                            break;
                        // default means case: 'inner-text'
                        default:
                            $(this).text(value);
                            break;
                    }
                });
            });
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
