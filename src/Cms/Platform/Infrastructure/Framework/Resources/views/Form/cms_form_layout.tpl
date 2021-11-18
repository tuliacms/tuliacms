{% use 'bootstrap_5_layout.html.twig' %}

{#
    - Prepends text addon
    {{ form_row(form.name, { input_addon_prepend: '#' }) }}
    - Appends text addon
    {{ form_row(form.name, { input_addon: '#' }) }}
#}
{% block form_widget_simple -%}
    {%- if input_addon is defined or input_addon_prepend is defined -%}
        <div class="input-group">
            {% if input_addon_prepend is defined %}
                <div class="input-group-prepend">
                    <span class="input-group-text">{{ input_addon_prepend }}</span>
                </div>
            {% endif %}
    {%- endif -%}
        {{- parent() -}}
    {%- if input_addon is defined or input_addon_prepend is defined -%}
        {% if input_addon is defined %}
            <div class="input-group-append">
                <span class="input-group-text">{{ input_addon }}</span>
            </div>
        {% endif %}
        </div>
    {%- endif -%}
{%- endblock form_widget_simple %}




{% block filepicker_widget -%}
    {% assets ['filemanager'] %}

    <div class="input-group">
        {{- block('form_widget_simple') -}}
        <div class="input-group-append">
            <button class="btn btn-default btn-icon-only" type="button">
                <i class="btn-icon fas fa-folder-open"></i>
            </button>
        </div>
    </div>

    {% if value %}
        {{ image(value, { size: 'thumbnail-md', attributes: { alt: 'Node thumbnail', class: 'img-thumbnail' } }) }}
    {% endif %}

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            Tulia.Filemanager.create({
                targetInput: '#{{ id }}',
                openTrigger: $('#{{ id }}').closest('.input-group').find('button'),
                endpoint: '{{ path('backend.filemanager.endpoint') }}',
                filter: {
                    type: {{ filter.type == '*' ? "'*'" : filter.type|json_encode|raw }}
                },
                multiple: false,
                closeOnSelect: true,
                onSelect: function (files) {
                    if (!files.length) {
                        return;
                    }

                    $('#{{ id }}').val(files[0].id);
                }
            });
        });
    </script>
{%- endblock filepicker_widget %}




{% block datetime_widget -%}
    {% assets ['datetimepicker'] %}

    {% set fieldId = uniqid() %}
    {% set attr    = attr|merge({ class: (attr.class|default('') ~ ' datetimepicker-input'), 'data-target': '#datetimepicker-' ~ fieldId }) -%}

    {% if not valid %}
        {% set attr = attr|merge({ class: (attr.class ~ ' form-control is-invalid') }) -%}
    {% endif %}

    <div class="input-group date" id="datetimepicker-{{ fieldId }}" data-target-input="nearest">
        {{- block('form_widget_simple') -}}
        <div class="input-group-append">
            <button class="btn btn-default btn-icon-only" type="button" data-target="#datetimepicker-{{ fieldId }}" data-toggle="datetimepicker">
                <i class="btn-icon fas fa-calendar-alt"></i>
            </button>
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('#datetimepicker-{{ fieldId }}').datetimepicker({
                locale: '{{ user_locale() }}',
                format: '{{ 'Y-m-d H:i:s'|format_php_to_momentjs }}'
            });

            $('.datetimepicker-input').not('.datetimepicker-input-autoopen').addClass('datetimepicker-input-autoopen').on('click focus', function () {
                $($(this).attr('data-target')).datetimepicker('show');
            });
        });
    </script>
{%- endblock datetime_widget %}





{% block wysiwyg_editor_widget -%}
    {{ wysiwyg_editor(full_name, value, { id: id }) }}
{%- endblock wysiwyg_editor_widget %}




{% block tulia_editor_widget -%}
    {{ tulia_editor(full_name, value, entity, { id: id }) }}
{%- endblock tulia_editor_widget %}




{% block typeahead_widget -%}
    {% assets ['jquery_typeahead'] %}

    {% set fieldId = uniqid() %}

    {%  if not debug %}
        {% set attr = attr|merge({ style: 'display:none;' }) -%}
    {% endif %}

    <div class="typeahead__container">
        <div class="typeahead__field">
            <div class="typeahead__query">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input class="js-typeahead form-control {{ typeahead_attr.class|default('') }}" id="typeahead-entity-{{ fieldId }}" name="q" autocomplete="off" value="{{ display_value }}" />
                    {% if not multiple %}
                        <div class="input-group-append{{ value ? '' : ' d-none' }} js_typeahead__remove_value">
                            <button type="button" class="btn btn-default btn-icon-only" data-toggle="tooltip" title="{{ 'delete'|trans }}"><i class="btn-icon fas fa-times"></i></button>
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
    {{- block('form_widget_simple') -}}
    {% if multiple %}
        <div class="typeahead__multiple_list">
            <div>Category <span>&times;</span></div>
            <div>Archive <span>&times;</span></div>
            <div>2019 <span>&times;</span></div>
        </div>
    {% endif %}

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            let typeahead = $('#typeahead-entity-{{ fieldId }}');
            let cont      = typeahead.closest('.typeahead__container');
            let target    = $('#{{ id }}');

            let updateRemoveValueBtn = function () {
                if (target.val()) {
                    cont.find('.js_typeahead__remove_value').removeClass('d-none');
                } else {
                    cont.find('.js_typeahead__remove_value').addClass('d-none');
                }
            };

            let onClickSingular = function (node, a, item, event) {
                target.val(item.id).trigger('change');

                updateRemoveValueBtn();
            };

            typeahead.typeahead({
                minLength: 3,
                order: 'asc',
                dynamic: true,
                delay: 500,
                cancelButton: false,
                href: function () {
                    return null;
                },
                filter: false,
                emptyTemplate: '{{ 'noResultsForQuery'|trans({ query: '"<i>{{query}}</i>"' }) }}',
                source: {
                    result: {
                        display: '{{ display_prop }}',
                        ajax: function (query) {
                            return {
                                path: 'result',
                                type: 'GET',
                                url: '{{ typeahead_url }}',
                                data: { q: query }
                            }
                        }
                    },
                },
                callback: {
                    onClick: onClickSingular
                },
                debug: true
            });

            typeahead.change(function () {
                if (! $(this).val()) {
                    target.val('');
                }

                updateRemoveValueBtn();
            }).on('keydown keypress', function (e) {
                let enter = 27;
                if (e.which === enter) {
                    e.preventDefault();
                    return false;
                }
            });

            cont.find('.js_typeahead__remove_value button').click(function () {
                typeahead.val('');
                target.val('').trigger('change');
                updateRemoveValueBtn();
                typeahead.trigger('focus');
            });
        });
    </script>
{%- endblock typeahead_widget %}

{% block tulia_submit_row -%}
    {{- form_widget(form, {
        icon: 'fas fa-save',
        attr: attr|merge({ class: attr.class is defined ? 'btn btn-success btn-icon-left ' ~ attr.class : 'btn btn-success btn-icon-left' })
    }) -}}
{%- endblock tulia_submit_row %}

{% block tulia_submit_widget -%}
    <a href="#" data-submit-form="{{ form.parent.vars.id }}" {{ block('widget_attributes') }}><i class="btn-icon {{ icon }}"></i> {{ label|trans(label_translation_parameters) }}</a>
{%- endblock tulia_submit_widget %}

{% block tulia_cancel_row -%}
    {{- form_widget(form, {
        icon: icon|default('fas fa-times'),
        attr: attr|merge({ class: attr.class is defined ? 'btn btn-secondary btn-icon-left ' ~ attr.class : 'btn btn-secondary btn-icon-left' })
    }) -}}
{%- endblock tulia_cancel_row %}

{% block tulia_cancel_widget -%}
    <a href="{{ route ? path(route, route_params) : '#' }}" {{ block('widget_attributes') }}><i class="btn-icon {{ icon }}"></i> {{ label|trans(label_translation_parameters) }}</a>
{%- endblock tulia_cancel_widget %}
