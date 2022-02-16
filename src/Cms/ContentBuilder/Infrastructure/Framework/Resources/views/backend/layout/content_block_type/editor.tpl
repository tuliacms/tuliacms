{{ form_start(form) }}
{{ form_errors(form) }}
{% if form._token is defined %}
    {{ form_row(form._token) }}
{% endif %}

{% assets ['tulia-dynamic-form'] %}

{% import relative(_self, '../../_macros/form_render.tpl') as form_render %}

<div class="cbb-block-type-edit-panel">
    {% for id, group in layout.section('main').fieldsGroups %}
        {% for field in group.fields %}
            {{ form_render.form_row(form, field, contentType) }}
        {% endfor %}
    {% endfor %}
</div>

{{ form_end(form, { render_rest: false }) }}
