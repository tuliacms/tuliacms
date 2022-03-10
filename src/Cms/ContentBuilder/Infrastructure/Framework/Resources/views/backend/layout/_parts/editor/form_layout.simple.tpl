{% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

{{ form_render.form_begin(form) }}

<div class="cbb-block-type-edit-panel">
    {% for id, group in layout.section('main').fieldsGroups %}
        {% for field in group.fields %}
            {{ form_render.form_row(form, field, contentType) }}
        {% endfor %}
    {% endfor %}
</div>

{{ form_render.form_end(form) }}
