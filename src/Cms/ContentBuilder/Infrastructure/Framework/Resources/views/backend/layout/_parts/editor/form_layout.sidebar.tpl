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

{% macro tab_content(id, active, group, form, contentType) %}
    {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

    <div class="tab-pane fade {{ active ? 'show active' : '' }}" id="tab-{{ id }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    {% for field in group.fields %}
                        {{ form_render.form_row(form, field, contentType) }}
                    {% endfor %}
                </div>
            </div>
        </div>
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

{% macro section(id, group, form, translationDomain, contentType) %}
    {% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
    {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

    <div class="accordion-section">
        <div
            class="accordion-section-button{{ group.active ? '' : ' collapsed' }}"
            data-bs-toggle="collapse"
            data-bs-target="#form-collapse-sidebar-{{ id }}"
        >
            {{ group.name|trans({}, translationDomain) }}
            {{ badge.errors_count(form, group.fields|default([])) }}
        </div>
        <div
            id="form-collapse-sidebar-{{ id }}"
            class="accordion-collapse collapse{{ group.active ? ' show' : '' }}"
        >
            <div class="accordion-section-body">
                {{ form_render.render_fields(form, group.fields, contentType) }}
            </div>
        </div>
    </div>
{% endmacro %}

{% block layout %}
    {% assets ['tulia-dynamic-form'] %}

    {{ form_start(form) }}
    {{ form_errors(form) }}
    {{ form_row(form._token) }}

    <input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
    <input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
    <input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />

    {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}

    <div class="page-form" id="node-form">
        <div class="page-form-sidebar">
            <div class="accordion">
                {% block sidebar_accordion %}{% endblock %}
                {% for id, group in layout.section('sidebar').fieldsGroups %}
                    {{ _self.section(id, group, form, null, contentType) }}
                {% endfor %}
            </div>
        </div>
        <div class="page-form-content">
            {% block page_header %}{% endblock %}
            <ul class="nav nav-tabs page-form-tabs" role="tablist">
                {% for id, group in layout.section('main').fieldsGroups %}
                    {{ _self.tab(id, {
                        active: loop.index0 == 0,
                        name: group.name|trans,
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
                    {{ _self.tab_content(id, loop.index0 == 0, group, form, contentType) }}
                {% endfor %}

                {{ _self.tab_rest_content('rest', form) }}
            </div>
        </div>
    </div>
    {{ form_end(form) }}
{% endblock %}
