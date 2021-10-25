{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

{% import relative(_self, 'parts/sidebar.tpl') as sidebar %}

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        <div class="accordion">
            {{ dump(layout.section('sidebar').fieldsGroups) }}
            {% for id, group in layout.section('sidebar').fieldsGroups %}
                {{ sidebar.section(id, group, form) }}
            {% endfor %}
        </div>
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    {% for field in layout.section('lead').fieldsGroup('main').fields %}
                        <div class="col">
                            {{ form_row(form[field]) }}
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
        {#{{ form_skeleton_render(form, 'default', {
            active_first: ['content', '_FIRST_']
        }) }}#}
    </div>
</div>
{{ form_end(form) }}
