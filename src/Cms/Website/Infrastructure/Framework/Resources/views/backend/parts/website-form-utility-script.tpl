<script nonce="{{ csp_nonce() }}">
    let WebsiteLocaleManager = function (options) {
        this.options = options;
        this.triggers = null;
        this.forms = null;

        this.init = function () {
            let self = this;

            this.triggers = $('#website-locale-triggers');
            this.forms = $('#website-locale-forms');

            $('#website-locale-add').on('click', function () {
                self.createNew();
                return false;
            });

            this.triggers.on('click', '.list-group-item', function () {
                self.open($(this).attr('data-locale-code'));
                return false;
            });

            this.forms.on('click', '.website-locale-remove', function () {
                self.remove($(this).attr('data-locale-code'));
                return false;
            });

            this.forms.on('change', '.locale-code-select', function () {
                self.refreshLabel($(this).closest('.locale-container').attr('id'));
            });

            this.forms.on('change', '.locale-default-select', function () {
                self.forceOneDefaultLocale($(this).closest('.locale-container').attr('id'));
            });

            let inputs = [
                '.locale-domain-input',
                '.locale-locale-prefix-input',
                '.locale-path-prefix-input',
            ];

            this.forms.on('change keyup keypress keydown', inputs.join(','), function () {
                self.updatePreview($(this).closest('.locale-container').attr('id'));
            });

            $('.website-backend-prefix-input, .locale-sslmode-select').on('change keyup keypress keydown', function () {
                self.forms.find('.locale-container').each(function () {
                    self.updatePreview($(this).attr('id'));
                });
            });

            this.forms.find('.locale-container').each(function () {
                self.updatePreview($(this).attr('id'));
            });
        };

        this.createNew = function () {
            let id = this.uniqid();

            let form = $($('#form-website-locale-prototype').html().replace(/__name__/g, id));

            this.triggers.append('<a href="#" class="list-group-item" data-locale-code="' + id + '">' + this.trans('newLocale') + '</a>');
            this.forms.append(form);

            form.find('.locale-domain-input').val(this.options.defaults.domain);
            form.find('.locale-code-select').val(this.options.defaults.locale).trigger('change');
            form.find('.locale-default-select').val(0);

            this.open(id);
            this.updatePreview(id);

            Tulia.UI.refresh(form);
        };

        this.remove = function (id) {
            let self = this;

            Tulia.Confirmation.warning().then(function (result) {
                if (result.value) {
                    self.triggers.find('a[data-locale-code="' + id + '"]').remove();
                    self.forms.find('.locale-container#' + id).remove();

                    self.open(self.triggers.find('a').first().attr('data-locale-code'));
                }
            });
        };

        this.open = function (id) {
            this.triggers
                .find('a')
                .removeClass('active')
                .filter('[data-locale-code="' + id + '"]')
                .addClass('active')
            ;

            this.forms
                .find('.locale-container')
                .addClass('d-none')
                .filter('#' + id)
                .removeClass('d-none')
            ;
        };

        this.refreshLabel = function (id) {
            let container = this.getForm(id);

            let text = container.find('.locale-code-select option:selected').text().split('[');
            container.find('.card-header').text(text[0]);

            this.triggers.find('a[data-locale-code="' + id + '"]').text(text[0]);
        };

        this.forceOneDefaultLocale = function (id) {
            let container = this.getForm(id);

            let length = this.forms.find('.locale-default-select option[value="1"]:selected').length;
            let info   = null;

            if (length === 0) {
                info = 'Nie wybrano żadnego języka domyślnego. Proszę o wybranie jednego języka domyślnego.';
            } else if (length > 1) {
                info = 'Wykryto dwa domyślne języki! Naprawiono problem wybierając aktualny język jako domyślny.';
                this.forms.find('.locale-default-select').val(0).trigger('ui:update');
                container.find('.locale-default-select').val(1).trigger('ui:update');
            } else {
                return;
            }

            Tulia.Toasts.warning(info);
        };

        this.updatePreview = function (id) {
            let form = this.getForm(id);

            let domain = form.find('.locale-domain-input').val();
            let localePrefix = form.find('.locale-locale-prefix-input').val();
            let pathPrefix = form.find('.locale-path-prefix-input').val();
            let sslMode = form.find('.locale-sslmode-select').val();
            //let backendPrefix = $('.website-backend-prefix-input').val();
            let backendPrefix = '/administrator';
            let protocol = '';

            if (sslMode === 'FORCE_SSL') {
                protocol = 'https://';
            } else if(sslMode === 'FORCE_NON_SSL') {
                protocol = 'http://';
            } else {
                protocol = 'http://';
            }

            let frontend = protocol + domain + pathPrefix + localePrefix + '/';
            let backend = protocol + domain + pathPrefix + backendPrefix + localePrefix + '/';

            form.find('.website-locale-preview-frontend').text(frontend);
            form.find('.website-locale-preview-backend').text(backend);
        };

        this.getForm = function (id) {
            return this.forms.find('.locale-container#' + id);
        };

        this.uniqid = function () {
            return '_' + Math.random().toString(36).substr(2, 9);
        };

        this.trans = function (key) {
            return this.options.translations[key] ?? null;
        };
    };

    $(function () {
        (new WebsiteLocaleManager({
            defaults: {
                domain: '{{ locale_defaults.domain }}',
                locale: '{{ locale_defaults.locale }}',
            },
            translations: {
                newLocale: '{{ 'newLocale'|trans({}, 'websites') }}'
            }
        })).init();
    });
</script>
