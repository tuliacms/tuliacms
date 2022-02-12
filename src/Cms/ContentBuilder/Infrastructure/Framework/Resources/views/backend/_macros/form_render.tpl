{% macro form_row(form, field, contentType) %}
    {% set fieldType = contentType.field(field) %}

    {% if form[field] is not defined %}
        {% set id = "not-existing-field-popover-" ~ uniqid() %}
        <label>No existing field <a href="#" id="{{ id }}" data-bs-content="If this field is created in NodeType, please check ContentBuilder logs to more informations. Otherwise, You have a typo or You used not existing field in layout."><b>Why?</b></a></label>
        <input class="form-control" type="text" value="The '{{ field }}' field not exists in configuration of this form." disabled readonly>
        <script nonce="{{ csp_nonce() }}">
            $(function () {
                let popover = new bootstrap.Popover(document.querySelector('#{{ id }}'), {
                    container: 'body',
                    placement: 'top'
                });
            });
        </script>
    {% else %}
        {#{% if fieldType.type == 'repeatable' %}
            {% set repeatable_id = "content-builder-repeatable-target-" ~ uniqid() %}
            <div class="mb-3">
                <label class="form-label">{{ form[field].vars.label }}</label>
                <div class="content-builder-repeatable-target" id="{{ repeatable_id }}" data-prototype="{{ form_widget(form[field].vars.prototype)|e }}">
                    {% for group in form[field] %}
                        <div class="content-builder-repeatable-element">
                            <div class="content-builder-repeatable-element-header">
                                <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:move-down" data-toggle="tooltip" title="{{ 'moveDown'|trans({}, 'content_builder') }}"><i class="btn-icon fas fa-chevron-down"></i></button>
                                <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:move-up" data-toggle="tooltip" title="{{ 'moveUp'|trans({}, 'content_builder') }}"><i class="btn-icon fas fa-chevron-up"></i></button>
                                <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:remove" data-toggle="tooltip" title="{{ 'remove'|trans }}"><i class="btn-icon fas fa-times"></i></button>
                            </div>
                            <div class="content-builder-repeatable-element-body">
                                {{ _self.render_subfields(group, fieldType.children, contentType) }}
                            </div>
                        </div>
                    {% endfor %}
                </div>
                <button type="button" class="btn btn-success btn-icon-left" data-form-action="create-from-prototype" data-target="{{ repeatable_id }}"><i class="btn-icon fa fa-plus"></i>Add new row</button>
            </div>
        {% else %}#}
            {{ form_row(form[field]) }}
        {#{% endif %}#}
    {% endif %}
{% endmacro %}

{% macro render_fields(form, fields, contentType) %}
    {% for field in fields %}
        {% set fieldType = contentType.field(field) %}

        {% if fieldType.type starts with '___content_block' %}
            {# Do not render fields that are internal for the Content Block ContentTypes #}
        {% else %}
            {{ _self.form_row(form, field, contentType) }}
        {% endif %}
    {% endfor %}
{% endmacro %}

{% macro render_subfields(form, fields, contentType) %}
    {% for field in fields %}
        {{ _self.form_row(form, field, contentType) }}
    {% endfor %}
{% endmacro %}
