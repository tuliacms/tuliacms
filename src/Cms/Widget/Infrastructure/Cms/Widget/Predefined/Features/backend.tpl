{% assets ['jquery_ui', 'filemanager'] %}

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="features-movable" data-prototype="{{ form_widget(form.features.vars.prototype)|e('html_attr') }}">
                {% for feature in form.features %}
                    <div class="card">
                        <div class="card-header">
                            Feature item <i class="fas fa-arrows-alt"></i>
                        </div>
                        <div class="card-body">
                            {{ form_row(feature.label) }}
                            {{ form_row(feature.description) }}
                            {{ form_row(feature.icon) }}
                            {{ form_row(feature.position) }}
                        </div>
                        <div class="card-footer text-right">
                            <button type="button" data-toggle="tooltip" title="{{ 'remove'|trans }}" class="btn btn-icon-only btn-danger features-remove"><i class="btn-icon fas fa-trash"></i></button>
                        </div>
                    </div>
                {% endfor %}
            </div>
            <button type="button" class="btn btn-success btn-icon-left features-add">
                <i class="btn-icon fas fa-plus"></i>
                {{ 'add'|trans }}
            </button>
        </div>
    </div>
</div>

<script type="text/template" id="feature-item-template">
    <div class="card">
        <div class="card-header">
            Feature item <i class="fas fa-arrows-alt"></i>
        </div>
        <div class="card-body"></div>
        <div class="card-footer text-right">
            <button type="button" data-toggle="tooltip" title="{{ 'remove'|trans }}" class="btn btn-icon-only btn-danger features-remove"><i class="btn-icon fas fa-trash"></i></button>
        </div>
    </div>
</script>

<style>
    .features-movable {
        margin: -5px 0 20px -5px;
    }
    .features-movable .card {
        float: left;
        margin: 5px;
        width: 413px;
    }
    .features-movable:after {
        content: "";
        display: table;
        clear: both;
    }
    .features-movable .card-header {
        position: relative;
    }

    .features-movable .card-header:hover {
        cursor: move;
    }

    .features-movable .card-header i {
        position: absolute;
        display: block;
        top: 50%;
        right: 10px;
        font-size: 16px;
        transform: translateY(-50%);
    }

    .features-movable textarea {
        resize: none;
    }
</style>

<script nonce="{{ csp_nonce() }}">
    let globalIndex = 1000;
    let featuresFilepicker = null;

    let appendFilepickerAddon = function (input) {
        input.wrap('<div class="input-group"></div>');
        input.parent().append('<div class="input-group-append"><button class="btn btn-default btn-icon-only features-filepicker" type="button"><i class="btn-icon fas fa-folder-open"></i></button></div>');
    };

    let createNew = function () {
        let prototype = $('.features-movable').data('prototype');
        let element = prototype.replace(/__name__/g, globalIndex);
        let card = $($('#feature-item-template').html());
        card.find('.card-body').append(element);

        globalIndex++;

        $('.features-movable').append(card);
        card.find('input').first().trigger('focus');

        appendFilepickerAddon(card.find('.filepicker-control'));
    };

    let remove = function (card) {
        card.remove();
    };

    let openFilepicker = function (input) {
        if (featuresFilepicker === null) {
            featuresFilepicker = Tulia.Filemanager.create({
                endpoint: '{{ path('backend.filemanager.endpoint') }}',
                filter: {type: ['svg', 'image']},
                multiple: false,
                closeOnSelect: true
            });
        }

        featuresFilepicker.cmd('deselect-all');
        featuresFilepicker.options.onSelect = function (files) {
            if (!files.length) {
                return;
            }

            input.val(files[0].id);
        };
        featuresFilepicker.show();
    };

    $(function () {
        $('.features-movable').sortable({
            handle: '.card-header',
            update: function () {
                $('.features-movable .card').each(function (index) {
                    $(this).find('.position-control').val(index);
                });
            }
        });

        $('.filepicker-control').each(function () {
            appendFilepickerAddon($(this));
        });

        $('.features-add').click(function () {
            createNew();
        });

        $('body').on('click', '.features-remove', function () {
            remove($(this).closest('.card'));
        }).on('click', '.features-filepicker', function () {
            openFilepicker($(this).closest('.input-group').find('input'));
        });
    });
</script>
