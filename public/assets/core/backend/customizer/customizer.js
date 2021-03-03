let Customizer = function (options) {
    this.options = options;
    this.anythingChange = false;

    this.init = function () {
        let self = this;

        this.options = $.extend(true, {}, Customizer.defaults, this.options);

        $('[data-show-pane]').click(function () {
            $('.control-pane').removeClass('active');
            $('.control-pane-name-' + $(this).attr('data-show-pane')).addClass('active');
        });

        if (this.options.changed) {
            $('.customizer-save').removeClass('disabled');

            this.anythingChange = true;
        }

        this.bindPostMessages();

        this.bindCustomizerSave();
        this.bindCustomizerClose();
        this.bindCustomizerResolution();
        this.bindCustomizerPanel();
        this.bindCustomizerPredefinedChangeset();

        this.bindControlsChange();
        this.bindDrasticOperationLinks();

        this.resolveMultilingualFields();

        this.showPreviewLoader();
        this.refreshPreview(function () {
            self.hidePreviewLoader();
        });
    };

    this.resolveMultilingualFields = function () {
        let self = this;

        $('.form-group-multilingual .customizer-label').each(function () {
            $(this).append('<span class="customizer-multilingual-badge" data-placement="left" data-toggle="tooltip" title="' + self.trans('multilingual') + '"></span>');
        });

        $('.form-group-multilingual [data-toggle]').tooltip();

        $('.form-group-multilingual .customizer-multilingual-badge').click(function () {
            Tulia.Info.info({
                title: self.trans('multilingual'),
                text: self.trans('multilingualDescription')
            });
        });
    };

    this.bindDrasticOperationLinks = function () {
        $('.customizer-drastic-operation-link').click(function (e) {
            e.preventDefault();

            let href = $(this).attr('href');

            Tulia.Confirmation.warning()
                .then(function (result) {
                    if(result.value)
                        document.location.href = href;
                });
        });
    };

    this.bindPostMessages = function () {
        let self = this;

        window.addEventListener('message', function (event) {
            if (event.origin !== document.location.origin) {
                return;
            }

            if (event.data.channel !== 'customizer') {
                return;
            }

            switch (event.data.command) {
                case 'change-location': self.changePreviewLocation(event.data.payload.href); break;
            }
        }, false);
    };

    this.changePreviewLocation = function (href) {
        let self = this;

        this.showPreviewLoader();
        this.options.paths.preview = href;

        if (href.indexOf('?') === -1) {
            this.options.paths.preview = this.options.paths.preview + '?mode=customizer&changeset=' + this.options.changeset;
        } else {
            this.options.paths.preview = this.options.paths.preview + '&mode=customizer&changeset=' + this.options.changeset;
        }

        this.refreshPreview(function () {
            self.hidePreviewLoader();
        });
    };

    this.bindCustomizerPanel = function () {
        $('.customizer-panel-toggle').click(function () {
            $('.customizer-panel-toggle').removeClass('active');
            $('.customizer-panel-toggle[data-panel=' + ($(this).attr('data-panel') == 'show' ? 'hide' : 'show') + ']').addClass('active');

            $('.customizer').attr('data-panel', $(this).attr('data-panel'));
        });
    };

    this.bindCustomizerPredefinedChangeset = function () {
        let self = this;

        $('.customizer-predefined-changeset-apply').click(function () {
            let id = $(this).attr('data-changeset-id');

            if (!self.options.predefinedChangesets[id]) {
                return;
            }

            Tulia.Confirmation.warning({
                title: self.trans('areYouSure'),
                text: self.trans('thisOperationCannotBeUndone'),
            }).then(function (result) {
                if (!result.value) {
                    return;
                }

                for (let field in self.options.predefinedChangesets[id]) {
                    $('.customizer-control[name="' + field + '"]').val(self.options.predefinedChangesets[id][field]);
                }

                self.showLoader();
                self.updateControls(function () {
                    self.refreshPreview(function () {
                        self.hideLoader();
                        self.hidePreviewLoader();
                    });
                });
            });
        });
    };

    this.bindCustomizerResolution = function () {
        $('.customizer-resolution-change').click(function () {
            $('.customizer-resolution-change').removeClass('active');
            $('.customizer-resolution-change[data-resolution=' + $(this).attr('data-resolution') + ']').addClass('active');

            $('.customizer').attr('data-resolution', $(this).attr('data-resolution'));
        });
    };

    this.bindCustomizerClose = function () {
        let self = this;

        $('.customizer-close').click(function (e) {
            if (self.anythingChange) {
                e.preventDefault();

                Tulia.Confirmation.warning({
                    title: self.trans('cancelChangesQuestion'),
                    text: self.trans('areYouSureToCancelChanges'),
                }).then(function (result) {
                    if(result.value)
                        document.location.href = $('.customizer-close').attr('href');
                });
            }
        });
    };

    this.bindControlsChange = function () {
        let self = this;

        $('.customizer-control').each(function () {
            let tagName = $(this).get(0).tagName;

            if (tagName === 'INPUT') {
                let type = $(this).attr('type');

                if (type == 'radio' || type == 'checkbox') {
                    self.bindSimpleChangeEvent($(this));
                } else {
                    self.bindDelayedChangeEvent($(this));
                }
            }
            else if (tagName === 'TEXTAREA') {
                self.bindDelayedChangeEvent($(this));
            } else {
                self.bindSimpleChangeEvent($(this));
            }
        });
    };

    this.bindDelayedChangeEvent = function (input) {
        let self    = this;
        let timeout = null;

        input.on('keyup keydown keypress change', function () {
            clearTimeout(timeout);

            let callback = function () {
                self.hideLoader();
            };

            if (input.attr('data-transport') === 'postMessage') {
                self.sendPostMessage('customized', {
                    name : input.attr('name'),
                    value: input.val(),
                });
            } else {
                callback = function () {
                    self.refreshPreview(function () {
                        self.hidePreviewLoader();
                        self.hideLoader();
                    });
                };
            }

            timeout = setTimeout(function () {
                self.showLoader();
                self.updateControls(callback, input.attr('data-transport'));
                clearTimeout(timeout);
            }, self.options.changeWaitTime);
        });
    };

    this.bindSimpleChangeEvent = function (input) {
        let self = this;

        input.on('keyup keydown keypress change', function () {
            let callback = function () {
                self.hideLoader();
            };

            if(input.attr('data-transport') === 'postMessage')
            {
                self.sendPostMessage('customized', {
                    name : input.attr('name'),
                    value: input.val(),
                });
            }
            else
            {
                callback = function () {
                    self.refreshPreview(function () {
                        self.hidePreviewLoader();
                        self.hideLoader();
                    });
                };
            }

            self.showLoader();
            self.updateControls(callback, input.attr('data-transport'));
        });
    };

    this.bindCustomizerSave = function () {
        let self = this;

        $('.customizer-save').click(function () {
            Tulia.Confirmation.warning({
                title: self.trans('saveChangesQuestion'),
                text: self.trans('areYouSureToSaveChanges'),
            }).then(function (result) {
                if(result.value)
                    self.saveChangeset();
            });
        });
    };

    this.updateControls = function (callback, transport) {
        this.anythingChange = true;

        $('.customizer-save').removeClass('disabled');

        if(transport !== 'postMessage') {
            this.showPreviewLoader();
        }

        this.submitValue(callback);
    };

    this.submitValue = function (callback) {
        let self = this;

        $.ajax({
            type: 'POST',
            url: this.options.paths.save,
            data: {
                mode: 'temporary',
                data: this.getControlsData(),
            },
            dataType: 'json',
            success: function (data) {
                if (data.status === 'success') {
                    callback ? callback() : null;
                    $('.customizer-form').trigger('tulia:form:submitted');
                }
            },
            error: function (data) {
                self.hidePreviewLoader();
            }
        });
    };

    this.saveChangeset = function () {
        let self = this;
        this.showPreviewLoader();

        $.ajax({
            type: 'POST',
            url: this.options.paths.save,
            data: {
                mode: 'theme',
                data: this.getControlsData(),
            },
            dataType: 'json',
            success: function (data) {
                if(data.status == 'success')
                {
                    Tulia.Info.success('Zapisano.');

                    $('.customizer-form').trigger('tulia:form:submitted');
                    $('.customizer-save').addClass('disabled');
                    self.anythingChange = false;
                    self.refreshPreview(function () {
                        self.hidePreviewLoader();
                    });
                }
            },
            error: function (data) {
                self.hidePreviewLoader();
            }
        });
    };

    this.sendPostMessage = function (command, payload) {
        $('iframe.customizer-preview').get(0).contentWindow.postMessage({
            channel : 'customizer',
            port    : null,
            command : command,
            payload : payload
        }, document.location.origin);
    };

    this.refreshPreview = function (callback) {
        let iframe = $('iframe.customizer-preview');
        let scrollTop = $(iframe.get(0).contentWindow.document).find('html,body').scrollTop();

        let waiter = function () {
            $(iframe.get(0).contentWindow.document).find('html,body').scrollTop(scrollTop);
            callback ? callback() : null;
            $('iframe.customizer-preview').off('load', waiter);
        };

        iframe
            .on('load', waiter)
            .attr('src', this.getRefreshedPreviewPath());
    };

    this.getRefreshedPreviewPath = function () {
        if (this.options.paths.preview.indexOf('?') === -1) {
            return this.options.paths.preview + '?refresh=' + (new Date).getTime();
        } else {
            return this.options.paths.preview + '&refresh=' + (new Date).getTime()
        }
    };

    this.getControlsData = function () {
        let data = {};
        let source = $('.customizer-form').serializeArray();

        for(let i = 0; i < source.length; i++)
        {
            data[source[i].name] = source[i].value;
        }

        return data;
    };

    this.showPreviewLoader = function () {
        $('.preview-loader').addClass('active');
    };

    this.hidePreviewLoader = function () {
        $('.preview-loader').removeClass('active');
    };

    this.showLoader = function () {
        $('.customizer-loader').addClass('active');
    };

    this.hideLoader = function () {
        $('.customizer-loader').removeClass('active');
    };

    this.trans = function (key) {
        return this.options.translations[key];
    };
};

Customizer.defaults = {
    /**
     * ID of the changeset for current Customizer session.
     */
    changeset: '',
    /**
     * Theme name.
     */
    theme: '',
    /**
     * List of paths to system actions.
     */
    paths: {
        // Front preview basepath.
        preview: '',
        // Backend save endpoint path.
        save: '',
    },
    /**
     * How much time script should wait to update preview when something's
     * change in controls. This is applied for controls line text input or
     * textarea, when we bind to events line keydown or keyup.
     */
    changeWaitTime: 600,
    /**
     * This options define, if user change changeset values, and, for exaple, change the locale.
     * Changeset values are stored each time user change any value, and when user change the locale,
     * change set ID is the same, but values from another locale are fetched. So we have to do operations
     * like the changeset was edited, and not saved. It that case, this option should be `true`.
     */
    changed: false,
    /**
     * Predefined changesets. Contain objects where key is a changeset ID and value where there is an
     * associative array with field's names and field's values.
     */
    predefinedChangesets: {},
    /**
     * Translations table.
     */
    translations: {
        cancelChangesQuestion: 'Cancel changes?',
        areYouSureToCancelChanges: 'Are You sure You want to cancel these changes?',
        saveChangesQuestion: 'Save changes?',
        areYouSureToSaveChanges: 'Are You sure? This operation cannot be undone!',
        yes: 'Yes',
        no: 'No',
        multilingual: 'Multilingual',
        multilingualDescription: 'Fields with this badge will be saved only for current language. Rest of fields are saved for all langauges at the same time.',
        areYouSure: 'Are You sure?',
        thisOperationCannotBeUndone: 'This operation cannot be undone!',
    }
};
