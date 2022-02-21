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
        {{ form_row(form[field]) }}
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
