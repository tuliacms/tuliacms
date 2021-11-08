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
            })
        </script>
    {% else %}
        {{ form_row(form[field]) }}
    {% endif %}
{% endmacro %}

{% macro tab(id, group, form) %}
    {% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
    <li class="nav-item">
        <a
                href="#"
                class="nav-link {{ group.active ? 'active' : '' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab-{{ id }}"
        >
            {{ group.name }}
            {{ badge.errors_count(form, group.fields|default([])) }}
        </a>
    </li>
{% endmacro %}

{% macro tab_content(id, group, form) %}
    <div class="tab-pane fade {{ group.active ? 'show active' : '' }}" id="tab-{{ id }}">
        {% if group.interior == 'default' %}
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {% endif %}
                    {% for field in group.fields %}
                        <div class="col">
                            {{ _self.form_row(form, field) }}
                        </div>
                    {% endfor %}
                    {% if group.interior == 'default' %}
                </div>
            </div>
        </div>
        {% endif %}
    </div>
{% endmacro %}

{% macro tab_rest_content(id, form) %}
    <div class="tab-pane fade" id="tab-{{ id }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="empty-form-section-placeholder" data-placeholder="{{ 'thereAreNoOtherSettings'|trans }}">{{ form_rest(form) }}</div>
                </div>
            </div>
        </div>
    </div>
{% endmacro %}

{% macro section(id, group, form, translationDomain) %}
    {% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
    <div class="accordion-section">
        <div
                class="accordion-section-button {{ group.active ? '' : 'collapsed' }}"
                data-bs-toggle="collapse"
                data-bs-target="#form-collapse-sidebar-{{ id }}"
        >
            {{ group.label|trans({}, translationDomain) }}
            {{ badge.errors_count(form, group.fields|default([])) }}
        </div>
        <div
                id="form-collapse-sidebar-{{ id }}"
                class="accordion-collapse collapse {{ group.active ? 'show' : '' }}"
        >
            <div class="accordion-section-body">
                {% for field in group.fields %}
                    {{ _self.form_row(form, field) }}
                {% endfor %}
            </div>
        </div>
    </div>
{% endmacro %}

{############################################################}

{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        <div class="accordion">
            {% for id, group in layout.section('sidebar').fieldsGroups %}
                {{ _self.section(id, group, form, type.translationDomain) }}
            {% endfor %}
        </div>
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    {% for field in layout.section('lead').fieldsGroup('main').fields %}
                        <div class="col">
                            {{ _self.form_row(form, field) }}
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs page-form-tabs" role="tablist">
            {% for id, group in layout.section('main').fieldsGroups %}
                {{ _self.tab(id, {
                    active: group.active,
                    name: group.name|trans({}, type.translationDomain),
                    fields: group.fields
                }, form) }}
            {% endfor %}

            {{ _self.tab('rest', {
                active: false,
                name: 'otherSettings'|trans({}, 'messages'),
                fields: []
            }, form) }}
        </ul>
        <div class="tab-content">
            {% for id, group in layout.section('main').fieldsGroups %}
                {{ _self.tab_content(id, group, form) }}
            {% endfor %}

            {{ _self.tab_rest_content('rest', form, type.translationDomain) }}
        </div>
    </div>
</div>
{{ form_end(form) }}
