Tulia.ElementsActions = function (options) {
    this.options = options;
    this.root = null;

    this.init = function () {
        this.options = $.extend({}, Tulia.ElementsActions.defaults, this.options);
        this.root    = this.options.root ? this.options.root : $('body');

        for(let i in this.options.actions)
        {
            this.options.actions[i] = $.extend({}, Tulia.ElementsActions.actionsDefaults, this.options.actions[i]);
        }

        let self = this;

        this.root.find(this.options.selectors.selected).click(function () {
            let action = $(this).attr('data-action');

            if(! self.options.actions[action])
                return;

            let elements = self.getSelectedItems();

            if(! elements)
                return;

            let options = {
                action   : self.options.actions[action].action,
                headline : self.options.actions[action].headline,
                question : self.options.actions[action].question,
                elements : elements
            };

            if(! self.options.actions[action].confirmation)
                return self.proceed(options);

            self.prepareAndOpenModal(options);
        });

        this.root.find(this.options.selectors.single).click(function () {
            let action = $(this).attr('data-action');

            if(! self.options.actions[action])
                return;

            let options = {
                action   : self.options.actions[action].action,
                headline : self.options.actions[action].headline,
                question : self.options.actions[action].question,
                elements : [{
                    id   : $(this).closest('tr').attr('data-element-id'),
                    name : $(this).closest('tr').attr('data-element-name')
                }]
            };

            if(! self.options.actions[action].confirmation)
                return self.proceed(options);

            self.prepareAndOpenModal(options);
        });

        let dropdown = this.root.find(this.options.selectors.selected).closest('.dropdown');

        if(dropdown.length === 0)
            return;

        let btn = dropdown.find('[data-toggle=dropdown]');
        btn.prop('disabled', 'disabled');

        $(this.options.selectors.checkbox).change(function () {
            if(self.getSelectedItems().length === 0)
            {
                btn.prop('disabled', 'disabled');
            }
            else
            {
                btn.prop('disabled', false);
            }
        });
    };

    this.getSelectedItems = function () {
        let elements = [];

        $(this.options.selectors.checkbox + ':checked').each(function () {
            elements.push({
                id   : $(this).closest('tr').attr('data-element-id'),
                name : $(this).closest('tr').attr('data-element-name')
            });
        });

        return elements;
    };

    this.prepareAndOpenModal = function (options) {
        let self  = this;
        let html  = options.question;
        let users = [];

        for(key in options.elements)
        {
            users.push(options.elements[key].name)
        }

        html = html + '<br /> [ ' + users.join(', ') + ' ]';

        Tulia.Confirmation.warning({
            title: options.headline
        }).then(function (result) {
            if(result.value)
                self.proceed(options);
        });
    };

    this.proceed = function (options) {
        Tulia.PageLoader.show();

        let form = $('<form action="" method="POST"></form>');
        form.attr('action', options.action);
        form.addClass('d-none');

        for(key in options.elements)
        {
            form.append('<input type="hidden" name="ids[]" value="' + options.elements[key].id + '" />');
        }

        form.appendTo('body');
        form.trigger('submit');
    };

    this.init();
};


Tulia.ElementsActions.defaults = {
    actions: {},
    root: null,
    selectors: {
        selected: '.action-element-selected',
        single  : '.action-element-single',
        checkbox: '.action-element-checkbox',
    },
};

Tulia.ElementsActions.actionsDefaults = {
    headline: null,
    question: null,
    action: null,
    confirmation: true,
};
