{% macro form_begin(form) %}
    {% assets ['tulia-dynamic-form'] %}

    {{ form_start(form) }}
    {{ form_errors(form) }}
    {% if form._token is defined %}
        {{ form_row(form._token) }}
    {% endif %}

    <input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
    <input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
    <input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
{% endmacro %}

{% macro form_end(form) %}
    {{ form_end(form, { render_rest: false }) }}
{% endmacro %}

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
