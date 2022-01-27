{% macro form_row(form, field) %}
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

{# ########################################## #}

{{ form_start(form) }}
{{ form_errors(form) }}
{% if form._token is defined %}
    {{ form_row(form._token) }}
{% endif %}

<div class="cbb-block-type-edit-panel">
    {% for id, group in layout.section('main').fieldsGroups %}
        {% for field in group.fields %}
            {% if type.field(field).type starts with '___content_block' %}
                {# Do not render fields that are internal for the COntent Block ContentTypes #}
            {% else %}
                {{ _self.form_row(form, field) }}
            {% endif %}
        {% endfor %}
    {% endfor %}
</div>

{{ form_end(form, { render_rest: false }) }}
