{{ form_start(form) }}
{{ form_errors(form) }}
{% if form._token is defined %}
    {{ form_row(form._token) }}
{% endif %}

<input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
<input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
<input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />

{% assets ['tulia-dynamic-form'] %}

{% import relative(_self, '../_parts/editor/form_render.tpl') as form_render %}

<div class="cbb-block-type-edit-panel">
    {% for id, group in layout.section('main').fieldsGroups %}
        {% for field in group.fields %}
            {{ form_render.form_row(form, field, contentType) }}
        {% endfor %}
    {% endfor %}
</div>

{{ form_end(form, { render_rest: false }) }}
