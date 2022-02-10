window.Tulia = Tulia ?? {};

window.Tulia.DynamicForm = function (form) {
    this.form = form;

    this.init = function () {
        let self = this;

        this.form.on('click', '[data-form-action]', function () {
            self.executeAction($(this).attr('data-form-action'), $(this));
        });

        this.rendered();
    };

    this.executeAction = function (action, target) {
        for (let i in Tulia.DynamicForm.plugins) {
            if (Tulia.DynamicForm.plugins[i].plugin.on[action]) {
                Tulia.DynamicForm.plugins[i].plugin.on[action].call(this, target);
            }
        }
    };

    this.executeRender = function (element, target) {
        for (let i in Tulia.DynamicForm.plugins) {
            if (Tulia.DynamicForm.plugins[i].plugin.render[element]) {
                Tulia.DynamicForm.plugins[i].plugin.render[element].call(this, target);
            }
        }
    };

    this.rendered = function () {
        let self = this;

        this.form.find('[data-dynamic-element]').filter(function () {
            return $(this).attr('data-dynamic-element-rendered') !== 'rendered';
        }).each(function () {
            $(this).attr('data-dynamic-element-rendered', 1);

            self.executeRender($(this).attr('data-dynamic-element'), $(this));
        });
    };

    this.init();
};

window.Tulia.DynamicForm.plugins = [];

window.Tulia.DynamicForm.plugin = function (name, plugin) {
    window.Tulia.DynamicForm.plugins.push({
        name: name,
        plugin: $.extend({}, {
            render: {},
            on: {},
        }, plugin)
    });
};

Tulia.DynamicForm.plugin('collection-field', {
    on: {
        'create-from-prototype': function (button) {
            const target = document.querySelector('#' + button.attr('data-target'));
            const item = document.createElement('div');

            if (!target.dataset.index) {
                target.dataset.index = 98688776;
            }

            let html = target.dataset.prototype.replace(
                /__name__/g,
                target.dataset.index
            );

            html = '<div class="content-builder-repeatable-element">\
                <div class="content-builder-repeatable-element-header">\
                    <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:move-down" data-toggle="tooltip" title=""><i class="btn-icon fas fa-chevron-down"></i></button>\
                    <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:move-up" data-toggle="tooltip" title=""><i class="btn-icon fas fa-chevron-up"></i></button>\
                    <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:remove" data-toggle="tooltip" title=""><i class="btn-icon fas fa-times"></i></button>\
                </div>\
                <div class="content-builder-repeatable-element-body">' + html + '</div>\
            </div>';

            item.innerHTML = html;

            target.appendChild(item);
            target.dataset.index++;

            this.rendered();
        },
        'create-from-prototype:move-down': function (button) {
            let currentElement = button.closest('.content-builder-repeatable-element');
            currentElement.next().insertBefore(currentElement);
        },
        'create-from-prototype:move-up': function (button) {
            let currentElement = button.closest('.content-builder-repeatable-element');
            currentElement.prev().insertAfter(currentElement);
        },
        'create-from-prototype:remove': function (button) {
            Tulia.Confirmation.warning().then((value) => {
                if (value.value) {
                    button.closest('.content-builder-repeatable-element').remove();
                }
            });
        },
    }
});

$(function () {
    $('.tulia-dynamic-form').each(function () {
        new Tulia.DynamicForm($(this));
    });
});
