{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

{% import relative(_self, 'parts/sidebar.tpl') as sidebar %}
{% import relative(_self, 'parts/main.tpl') as main %}

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        <div class="accordion">
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
        <ul class="nav nav-tabs page-form-tabs" role="tablist">
            {% for id, group in layout.section('main').fieldsGroups %}
                {{ main.tab(id, group, form) }}
            {% endfor %}
            {{ main.tab('rest', {active: false, name: 'otherSettings'}, form) }}
        </ul>
        <div class="tab-content">
            {% for id, group in layout.section('main').fieldsGroups %}
                {{ main.tab_content(id, group, form) }}
            {% endfor %}
            {{ main.tab_rest_content('rest', form) }}
        </div>
    </div>
</div>
{{ form_end(form) }}
