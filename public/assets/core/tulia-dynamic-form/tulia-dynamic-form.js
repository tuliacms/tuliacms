window.Tulia = Tulia ?? {};

window.Tulia.DynamicForm = function (form) {
    this.form = form;

    this.init = function () {
        let self = this;

        this.form.on('click', '[data-form-action]', function () {
            self.executeAction($(this).attr('data-form-action'), $(this));
        });
    };

    this.executeAction = function (action, target) {
        for (let i in Tulia.DynamicForm.plugins) {
            if (Tulia.DynamicForm.plugins[i].plugin.on[action]) {
                Tulia.DynamicForm.plugins[i].plugin.on[action].call(target.get(0), this.form);
            }
        }
    };

    this.init();
};

window.Tulia.DynamicForm.plugins = [];

window.Tulia.DynamicForm.plugin = function (name, plugin) {
    window.Tulia.DynamicForm.plugins.push({
        name: name,
        plugin: $.extend({}, {
            on: {},
        }, plugin)
    });
};

Tulia.DynamicForm.plugin('collection-field', {
    on: {
        'create-from-prototype': function () {
            const target = document.querySelector('#' + $(this).attr('data-target'));
            const item = document.createElement('div');
            item.classList.add('content-builder-repeatable-element');

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
        },
        'create-from-prototype:move-down': function () {
            let currentElement = $(this).closest('.content-builder-repeatable-element');
            currentElement.next().insertBefore(currentElement);
        },
        'create-from-prototype:move-up': function () {
            let currentElement = $(this).closest('.content-builder-repeatable-element');
            currentElement.prev().insertAfter(currentElement);
        },
        'create-from-prototype:remove': function () {
            Tulia.Confirmation.warning().then((value) => {
                if (value.value) {
                    $(this).closest('.content-builder-repeatable-element').remove();
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
