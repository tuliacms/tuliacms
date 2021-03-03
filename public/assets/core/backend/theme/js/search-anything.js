let tuliaSearchData = {
    'page': {
        label: 'Strony',
        icon: 'fas fa-file-powerpoint',
        hits: [
            {
                image: 'https://picsum.photos/200/200?v=' + _.uniqueId(),
                title: 'Nazwa witryny',
                link: '#href',
                description: 'In maximus vitae felis ut tincidunt. Maecenas mi ex, mattis sit amet turpis a, scelerisque scelerisque mi. Fusce tempor nisi augue, vel pretium ante rutrum eu.',
                tags: [
                    {
                        tag: 'In maximus vitae',
                        icon: 'fas fa-file-powerpoint'
                    },
                    {
                        tag: 'Maecenas mi',
                        icon: 'fas fa-folder-open'
                    },
                    {
                        tag: 'Fusce tempor nisi augue, vel'
                    },
                    {
                        tag: 'Praesent et felis aliquet',
                        icon: 'fas fa-file-powerpoint'
                    },
                    {
                        tag: 'Praesent et felis aliquet leo luctus',
                        icon: 'fas fa-folder-open'
                    },
                    {
                        tag: 'Donec euismod et'
                    },
                ]
            },
            {
                title: 'Favikona',
                link: '#href',
                description: 'Praesent et felis aliquet leo luctus egestas id a eros. Donec ultrices luctus semper. Fusce varius felis nec dolor molestie aliquet.'
            },
            {
                title: 'Treść wiadomości',
                link: '#href',
                description: 'Nam nec egestas ipsum, at consectetur dui. Donec euismod et velit finibus euismod. Praesent et felis aliquet leo luctus egestas id a eros.'
            },
            {
                title: 'Nazwa witryny',
                link: '#href',
                description: 'Vestibulum facilisis fermentum lorem eleifend feugiat.'
            },
            {
                title: 'Favikona',
                link: '#href',
                description: 'Suspendisse potenti. Quisque nibh neque, porttitor ut eros nec, vestibulum egestas lacus. Morbi fringilla, ante venenatis dapibus porttitor, ante nulla mollis erat, sit amet consectetur leo lacus sed massa.'
            },
            {
                title: 'Treść wiadomości',
                link: '#href',
                description: 'Morbi fringilla, ante venenatis dapibus porttitor, ante nulla mollis erat.'
            },
        ]
    },
    'shop-products': {
        label: 'Produkty',
        icon: 'fas fa-shopping-cart',
        hits: [
            {
                image: 'https://picsum.photos/200/200?v=' + _.uniqueId(),
                title: 'Lasocki 4 Man RH5600 Czarne',
                link: '#href',
                tags: [
                    {
                        tag: '149.99 zł',
                        icon: 'fas fa-money-bill-alt'
                    },
                    {
                        tag: 'Dostępne 19 szt.',
                        icon: 'fas fa-mountain'
                    },
                ],
            },
            {
                image: 'https://picsum.photos/200/200?v=' + _.uniqueId(),
                title: 'Lasocki Woman Black Widow',
                link: '#href',
                tags: [
                    {
                        tag: '99.99 zł',
                        icon: 'fas fa-money-bill-alt'
                    },
                    {
                        tag: 'Dostępne 1 szt.',
                        icon: 'fas fa-mountain'
                    },
                ],
            },
        ]
    },
    'settings': {
        label: 'Ustawienia',
        icon: 'fas fa-cogs',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
        ]
    },
    'asd': {
        label: 'Ustawienia',
        icon: 'fas fa-tools',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
        ]
    },
    '123': {
        label: 'Ustawienia',
        icon: 'fas fa-dice-d6',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
        ]
    },
    'zxc': {
        label: 'Ustawienia',
        icon: 'fas fa-question-circle',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
        ]
    },
    'ty': {
        label: 'Ustawienia',
        icon: '',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
        ]
    },
    '234': {
        label: 'Ustawienia',
        icon: '',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
        ]
    },
    'sgsdfg': {
        label: 'Ustawienia',
        icon: '',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
        ]
    },
    '345': {
        label: 'Ustawienia',
        icon: '',
        hits: [
            {
                title: 'Nazwa witryny',
                link: '#href'
            },
            {
                title: 'Favikona',
                link: '#href'
            },
            {
                title: 'Treść wiadomości',
                link: '#href'
            },
        ]
    }
};

Tulia.SearchAnything = function (selector, options) {
    this.selector  = selector;
    this.options   = options;
    this.container = null;
    this.template  = null;
    this.query     = null;
    this.providers = [];
    this.loading   = false;
    this.results   = [];
    this.debounceSearch = null;
    this.elm = {
        query: null,
        intro: null,
        results: null
    };

    this.init = function () {
        this.container = $(this.selector);
        this.options   = $.extend({}, Tulia.SearchAnything.defaults, this.options);

        let self = this;

        $(this.options.trigger).click(function () {
            self.open();
        });

        $('body').keydown(function (e) {
            if (self.isOpened() && e.which === 27) {
                self.close();
            }
        });

        this.debounceSearch = _.debounce(self.search, 500);

        this.createView();
    };

    this.search = function () {
        let self = this;
        let left = this.providers.length;

        this.results = [];
        this.elm.searchResults.empty();

        for (let i in this.providers) {
            this.searchInProvider(this.providers[i], function (result) {
                left--;

                if (left === 0) {
                    self.loading = false;
                }

                self.results.push(result);
                self.refreshLoader();
                self.appendResults(self.providers[i], result);
            });
        }
    };

    this.searchInProvider = function (provider, callback) {
        if (this.options.endpoint) {
            $.ajax({
                url: this.options.endpoint + '/search',
                data: {
                    p: provider,
                    q: this.query
                },
                dataType: 'json',
                success: function (data) {
                    callback(data);
                },
                error: function () {
                    callback({});
                }
            });
        } else {
            setTimeout(function () {
                callback(tuliaSearchData[provider]);
            }, this.randomInt(300, 1000));
        }
    };

    this.randomInt = function (min, max) {
        return min + Math.floor((max - min) * Math.random());
    };

    this.appendResults = function (provider, results) {
        if (!results.hits || results.hits.length === 0) {
            return;
        }

        let html = '<div class="search-result-group" style="order: ' + this.getProviderOrder(provider) + '">' +
            '<i class="section-icon ' + results.icon + '"></i>' +
            '   <div class="section-hl">' + results.label + '</div>' +
            '   <div class="result-links">';

        for (let i in results.hits) {
            let hit = results.hits[i];
            let tags = '';

            if (hit.tags && hit.tags.length) {
                tags = '<div class="link-tags">';
                for (let t in hit.tags) {
                    tags += '<span class="link-tag"><i class="link-tag-icon ' + hit.tags[t].icon + '"></i> ' + hit.tags[t].tag + '</span>';
                }
                tags += '</div>';
            }

            html += '<a class="result-link ' + (hit.image ? 'has-image' : '') + '" href="' + hit.link +'">' +
                (hit.image ? '<div class="link-image"><div class="link-image-item" style="background-image:url(' + hit.image + ')"></div></div>' : '') +
                '    <div class="link-details">' +
                '        <div class="link-head">' +
                '            <span class="link-label" title="' + hit.title + '">' +
                '                ' + hit.title +
                '            </span>' +
                '        </div>' +
                '        <div class="link-body">' +
                (hit.description ? '<div class="link-description">' + hit.description + '</div>' : '') +
                '            ' + tags +
                '        </div>' +
                '    </div>' +
                '</a>';
        }

        html += '</div></div>';

        this.elm.searchResults.append(html);
    };

    this.getProviderOrder = function (provider) {
        for (let i in this.providers) {
            if (this.providers[i] === provider) {
                return i;
            }
        }

        return 10000;
    };

    this.fetchProviders = function () {
        let self = this;

        if (this.options.endpoint) {
            $.ajax({
                url: this.options.endpoint + '/providers',
                dataType: 'json',
                success: function (data) {
                    self.providers = data;
                }
            });
        } else {
            for (let i in tuliaSearchData) {
                this.providers = ["shop-products", "page", "settings", "asd", "zxc", "ty", "123", "234", "345"];
            }
        }
    };

    this.refreshLoader = function () {
        if (this.loading) {
            this.container.find('.tsa-loading-show').removeClass('d-none');
            this.container.find('.tsa-loading-hide').addClass('d-none');
        } else {
            this.container.find('.tsa-loading-show').addClass('d-none');
            this.container.find('.tsa-loading-hide').removeClass('d-none');
        }

        if (this.results.length === 0) {
            this.elm.searchResults.addClass('d-none');
            this.elm.searchLoader.removeClass('d-none');
        } else {
            this.elm.searchResults.removeClass('d-none');
            this.elm.searchLoader.addClass('d-none');
        }
    };

    this.refreshView = function () {
        if (this.loading) {
            this.elm.intro.addClass('d-none');
            this.elm.results.removeClass('d-none');
        } else {
            this.elm.intro.removeClass('d-none');
            this.elm.results.addClass('d-none');
        }
    };

    this.createView = function () {
        let self = this;

        this.elm.query   = this.container.find('.query');
        this.elm.queryPreview = this.container.find('.tsa-query-preview');
        this.elm.intro   = this.container.find('.search-info');
        this.elm.results = this.container.find('.search-results-wrapper');
        this.elm.searchResults = this.container.find('.search-results');
        this.elm.searchLoader  = this.container.find('.search-loader');

        this.container.find('.closer').click(function () {
            self.close();
        });
        this.elm.query.on('change keydown keyup', function () {
            let query = $(this).val();

            if (self.query === query) {
                return;
            }

            self.query = query;

            self.elm.queryPreview.text(query);
            self.results = [];

            if (query) {
                self.loading = true;
                self.debounceSearch();
            } else {
                self.loading = false;
                self.debounceSearch.cancel();
            }

            self.refreshLoader();
            self.refreshView();
        });
    };

    this.isOpened = function () {
        return this.container.hasClass('opened');
    };

    this.open = function () {
        this.fetchProviders();
        $('body').addClass('prevent-scroll');
        this.container.addClass('opened');
        this.container.find('.query')
            .val('')
            .trigger('change')
            .focus();
    };

    this.close = function () {
        this.results = [];
        this.container.removeClass('opened');
        this.debounceSearch.cancel();
        $('body').removeClass('prevent-scroll');
    };

    this.init();
};

Tulia.SearchAnything.defaults = {
    trigger: '.search-area',
    template: '#search-anything-template',
    endpoint: Tulia.Globals.search_anything.endpoint
};
