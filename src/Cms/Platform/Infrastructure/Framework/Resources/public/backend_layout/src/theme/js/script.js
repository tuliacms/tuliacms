$(function () {
    /*var stickySidebar = new StickySidebar('main aside', {
        topSpacing: 51
    });*/

    if (Tulia.Menu) {
        let menu = new Tulia.Menu('aside .lead-menu');
    }
    if (Tulia.SearchAnything) {
        let se = new Tulia.SearchAnything('.search-anything-container');
    }
    if (Tulia.Toasts) {
        let toasts = new Tulia.Toasts();
    }
    if (Tulia.CheckboxSelectAll) {
        let csa = new Tulia.CheckboxSelectAll();
    }

    Tulia.Form.createForEach('form');
    Tulia.PageLoader.init();

    if(typeof(SimpleBar) !== 'undefined')
    {
        let node = document.getElementById('notifications-scrollarea');

        if(node)
        {
            let scrollbar = new SimpleBar(node);

            $('.notifications-list .dropdown').on('shown.bs.dropdown', function () {
                scrollbar.recalculate();
            });
        }
    }

    $(document).on('click', '.dropdown-prevent-close .dropdown-menu', function (e) {
        e.stopPropagation();
    });

    $('.toggle-fullscreen').click(function () {
        Tulia.Fullscreen.toggle();
    });

    let body = $('body');
    let headerScroll = new Tulia.ScrollDecider(3, function () {
        body.removeClass('header-fixed');
    }, function () {
        body.addClass('header-fixed');
    });

    headerScroll.start();

    Tulia.UI.refresh(body);
});

Tulia = Tulia ?? {};

Tulia.UI = {};
Tulia.UI.refresh = function (container) {
    if($.fn.chosen) {
        container.find('select')
            .not('.ui-done-select')
            .not('.form-control-raw')
            .addClass('ui-done-select')
            .chosen({
                search_contains: true,
                width: '100%'
            })
            .on('ui:update', function () {
                $(this).trigger('chosen:updated');
            })
        ;
    }

    if(bootstrap.Tooltip) {
        let tooltipTriggerList = [].slice.call(container.get(0).querySelectorAll('[data-bs-toggle="tooltip"]'));
        let tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
};





Tulia.PageLoader = {
    loader: null,
    init: function () {
        this.getLoader();

        $('body').on('click', '.tulia-click-page-loader', function () {
            Tulia.PageLoader.show();
        });
    },
    show: function () {
        this.getLoader().addClass('active');
    },
    hide: function () {
        this.getLoader().removeClass('active');
    },
    getLoader: function () {
        if(this.loader)
            return this.loader;

        this.loader = $('<div class="page-loader">Please wait...</div>');
        this.loader.appendTo('body');

        return this.loader;
    }
};





Tulia.Form = function (form, options) {
    this.form = form;
    this.options = options;
    this.controls = null;

    this.init = function () {
        if(typeof(this.form) === 'string')
        {
            this.form = $(this.form);
        }

        this.bindEvents();
        this.bindSubmitters();
        this.bindLeaveUnsavedNotice();
    };

    this.bindEvents = function () {
        let self = this;

        this.form.on('tulia:form:submitted', function () {
            self.submitted();
        });
    };

    this.bindSubmitters = function () {
        let self = this;
        let selector = this.getFormId();

        $('[data-submit-form]').each(function () {
            let btnSelector = $(this).attr('data-submit-form');

            if(btnSelector === selector || btnSelector === '#' + selector)
            {
                $(this).click(function (e) {
                    e.preventDefault();
                    Tulia.PageLoader.show();

                    // Set timeout (150ms) to wait until browser done page loader animation.
                    setTimeout(function () {
                        self.form.trigger('submit');
                    }, 150);
                });
            }
        });
    };

    this.bindLeaveUnsavedNotice = function () {
        let self = this;

        this.controls = this.form.serialize();

        $('body').on('click', 'a', function (e) {
            let a = $(this);

            if(self.isValidLink(a) && self.isPrevented(a) === false)
            {
                if(self.controls == self.form.serialize())
                    return;

                e.preventDefault();

                Tulia.Confirmation.warning({
                    title: 'Unsaved form!',
                    text: 'Do You want cancel form?'
                }).then(function (result) {
                    if(result.value)
                        document.location.href = a.prop('href');
                });
            }
        });
    };

    this.submitted = function () {
        this.controls = this.form.serialize();
    };

    this.isPrevented = function (a) {
        return a.hasClass('tulia-form-prevent-confirm');
    };

    this.isValidLink = function (a) {
        let propHref = a.prop('href');
        let attrHref = a.attr('href');

        if (! propHref || ! attrHref) {
            return false;
        }

        if (attrHref === '#') {
            return false;
        }

        if (attrHref.substring(0, 1) === '#') {
            return false;
        }

        if (attrHref === 'javascript:;') {
            return false;
        }

        return true;
    };

    this.getFormId = function () {
        return this.form.attr('id');
    };

    this.init();
};

Tulia.Form.createForEach = function (selector, options) {
    $(selector).each(function () {
        new Tulia.Form($(this), options);
    });
};

Tulia.Form.defaults = {

};





Tulia.Info = {
    fire: function (options) {
        return Tulia.Info.swal.fire(options);
    },
    info: function (options) {
        if(typeof(options) === 'string')
        {
            options = {
                title: options
            }
        }

        options = $.extend(true, {}, {
            title: 'Operation done',
            type: 'info',
            customClass: {
                confirmButton: 'btn btn-primary'
            },
            focusConfirm: false,
            showCancelButton: false,
            confirmButtonText: 'Ok',
        }, options);

        return Tulia.Info.fire(options);
    },
    success: function (options) {
        if(typeof(options) === 'string')
        {
            options = {
                title: options
            }
        }

        options = $.extend(true, {}, {
            title: 'Operation done',
            type: 'success',
            customClass: {
                confirmButton: 'btn btn-success'
            },
            focusConfirm: false,
            showCancelButton: false,
            confirmButtonText: 'Ok',
        }, options);

        return Tulia.Info.fire(options);
    }
};

Tulia.Info.swal = null;







Tulia.Confirmation = {
    fire: function (options) {
        return Tulia.Confirmation.swal.fire(options);
    },
    warning: function (options) {
        options = $.extend(true, {}, {
            title: 'Are You sure?',
            text: 'This operation cannot be undone!',
            type: 'warning',
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }, options);

        return Tulia.Confirmation.fire(options);
    },
    confirm: function (options) {
        options = $.extend(true, {}, {
            title: 'Are You sure?',
            text: 'You really want to do this operation?',
            type: 'warning',
            customClass: {
                confirmButton: 'btn btn-success',
                cancelButton: 'btn btn-secondary',
            },
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }, options);

        return Tulia.Confirmation.fire(options);
    }
};

Tulia.Confirmation.swal = null;



if(typeof(Swal) !== 'undefined')
{
    Tulia.Info.swal =
        Tulia.Confirmation.swal = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-danger',
                cancelButton: 'btn btn-secondary',
            },
            buttonsStyling: false
        });
}






Tulia.CheckboxSelectAll = function() {
    this.init = function() {
        $('input[type=checkbox][data-select-all]').each(function () {
            new Tulia.CheckboxSelectAll.Manager($(this), $($(this).attr('data-select-all')));
        });
    };

    this.init();
};

Tulia.CheckboxSelectAll.Manager = function (root, checkboxes) {
    this.root       = root;
    this.checkboxes = checkboxes;

    this.init = function() {
        let self = this;

        this.checkboxes.change(function () {
            self.defineRootCheck();
        });

        this.root.change(function () {
            if($(this).is(':checked'))
                self.checkAll();
            else
                self.uncheckAll();
        });
    };

    this.defineRootCheck = function () {
        let all = this.checkboxes.length;
        let checked = this.checkboxes.filter(':checked').length;

        if(checked === all)
            this.root.prop('checked', 'checked');
        else
            this.root.prop('checked', false);
    };

    this.checkAll = function () {
        this.checkboxes.prop('checked', 'checked').trigger('change');
    };

    this.uncheckAll = function () {
        this.checkboxes.prop('checked', false).trigger('change');
    };

    this.init();
};






Tulia.ScrollDecider = function(breakpoint, beforeCallback, afterCallback) {
    this.breakpoint         = breakpoint;
    this.beforeCallback     = beforeCallback;
    this.afterCallback      = afterCallback;
    this.isBeforeBreakpoint = false;

    this.start = function() {
        var self = this;

        $(window).scroll(function() {
            self.decide();
        });

        self.decide();
    };

    this.decide = function() {
        if(this.isBeforeBreakpoint)
        {
            if($(window).scrollTop() < this.breakpoint)
            {
                this.isBeforeBreakpoint = false;
                this.beforeCallback();
            }
        }
        else
        {
            if($(window).scrollTop() > this.breakpoint)
            {
                this.isBeforeBreakpoint = true;
                this.afterCallback();
            }
        }
    };
};






Tulia.Fullscreen = {
    status: false,
    element: document.documentElement,
    toggle: function () {
        this.status ? this.off() : this.on();
    },
    on: function () {
        if (this.element.requestFullscreen)
            this.element.requestFullscreen();
        else if (this.element.mozRequestFullScreen)
            this.element.mozRequestFullScreen();
        else if (this.element.webkitRequestFullscreen)
            this.element.webkitRequestFullscreen();
        else if (this.element.msRequestFullscreen)
            this.element.msRequestFullscreen();

        this.status = true;
    },
    off: function () {
        if (document.exitFullscreen)
            document.exitFullscreen();
        else if (document.mozCancelFullScreen)
            document.mozCancelFullScreen();
        else if (document.webkitExitFullscreen)
            document.webkitExitFullscreen();
        else if (document.msExitFullscreen)
            document.msExitFullscreen();

        this.status = false;
    }
};








Tulia.Menu = function (selector) {
    this.selector = selector;
    this.menu     = null;

    this.init = function () {
        this.menu = $(this.selector);

        let self = this;

        this.menu.find('li.has-dropdown > a').click(function (e) {
            if ($(this).next('ul').length) {
                e.preventDefault();
                $(this).parent().addClass('animated').toggleClass('dropdown-opened');

                self.updateOpenedDropdownInStorage();
            }
        });
    };

    this.updateOpenedDropdownInStorage = function () {
        let ids = [];

        this.menu.find('.dropdown-opened').each(function () {
            if ($(this).find('> ul').length) {
                ids.push($(this).attr('data-item-id'));
            }
        });

        Cookies.set('aside-menuitems-opened', ids.join('|'));
    };

    this.init();
};










Tulia.Toasts = function () {
    this.container = null;

    this.init = function () {
        Tulia.Toasts.instance = this;

        this.container = $('<div aria-live="polite" aria-atomic="true" style="position:fixed;top:65px;right:15px;z-index:1000;"></div>');

        $('body').append(this.container);
    };

    this.show = function (parameters) {
        let toast = $(Tulia.Toasts.defaults.template);
        toast.find('.toast-body').text(parameters.content);
        toast.find('strong').text(parameters.headline);

        this.container.append(toast);
        toast.toast({ delay: 3000 }).toast('show');
    };

    this.init();
};

Tulia.Toasts.defaults = {
    template: '<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">\
      <div class="toast-header">\
        <strong class="mr-auto"></strong>\
        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">\
          <span aria-hidden="true">&times;</span>\
        </button>\
      </div>\
      <div class="toast-body"></div>\
    </div>'
};

Tulia.Toasts.instance = null;

Tulia.Toasts.success = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'success',
            headline: 'Powodzenie',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.danger = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'danger',
            headline: 'Błąd',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.warning = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'warning',
            headline: 'Uwaga',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};

Tulia.Toasts.info = function (parameters) {
    if(typeof parameters == 'string')
    {
        parameters = {
            theme: 'info',
            headline: 'Informacja',
            content: parameters
        };
    }

    Tulia.Toasts.instance.show(parameters);
};
