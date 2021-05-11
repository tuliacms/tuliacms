{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}

{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        <div class="text-center" style="padding:24px 10px;border-bottom:1px solid #ddd;">
            <span style="font-size:21px;display:block;">
                {{ widget.info.name|default('_name not provided_')|trans({}, widget.info.translation_domain|default('widgets')) }}
            </span>
            <span style="font-size:10px;text-transform:uppercase;line-height:1.2;margin-top:-1px;display:block;opacity:.6;">
                {{ 'widgetType'|trans({}, 'widgets') }}
            </span>
        </div>
        {{ form_skeleton_render(manager, 'sidebar', {
            active: ['status', 'look']
        }) }}
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        {{ form_row(form.name, { attr: { autofocus: 'autofocus' } }) }}
                    </div>
                    <div class="col-6">
                        {{ form_row(form.space) }}
                    </div>
                </div>
            </div>
        </div>
        {{ form_skeleton_render(manager, 'default', {
            active: ['widget']
        }) }}
    </div>
</div>

{{ form_end(form) }}
