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

{% macro tab_content(id, group, form, contentType) %}
    {% import relative(_self, '../../_macros/form_render.tpl') as form_render %}

    <div class="tab-pane fade {{ group.active ? 'show active' : '' }}" id="tab-{{ id }}">
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
    {% import relative(_self, '../../_macros/form_render.tpl') as form_render %}

    <div class="accordion-section">
        <div
                class="accordion-section-button {{ group.active ? '' : 'collapsed' }}"
                data-bs-toggle="collapse"
                data-bs-target="#form-collapse-sidebar-{{ id }}"
        >
            {{ group.name|trans({}, translationDomain) }}
            {{ badge.errors_count(form, group.fields|default([])) }}
        </div>
        <div
                id="form-collapse-sidebar-{{ id }}"
                class="accordion-collapse collapse {{ group.active ? 'show' : '' }}"
        >
            <div class="accordion-section-body">
                {{ form_render.render_fields(form, group.fields, contentType) }}
            </div>
        </div>
    </div>
{% endmacro %}

{############################################################}

{% assets ['tulia-dynamic-form'] %}

{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

{% import relative(_self, '../../_macros/form_render.tpl') as form_render %}

{# TODO Move styles to separate CSS file #}
<style>
    .content-builder-repeatable-target {
        border: 1px solid #ddd;
        border-bottom: none;
        border-radius: 3px;
        margin-bottom: 10px;
    }

    .content-builder-repeatable-element {
        border-bottom: 1px solid #ddd;
    }

    .content-builder-repeatable-element-header {
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        text-align: right;
    }

    .content-builder-repeatable-element-body {
        padding: 10px 10px 0 10px;
    }
</style>

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        <div class="accordion">
            <div class="accordion-section">
                <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-status">
                    {{ 'publicationStatus'|trans }}
                </div>
                <div id="form-collapse-sidebar-status" class="accordion-collapse collapse show">
                    <div class="accordion-section-body">
                        {% if form.published_at is defined %}
                            {{ form_row(form.published_at) }}

                            {% set publishedToManually = form.published_to.vars.value != '' %}
                            <div class="node-published-to-selector mb-4">
                                <div class="published-to-date-selector{{ publishedToManually ? '' : ' d-none' }}">
                                    {{ form_row(form.published_to) }}
                                </div>
                                <div class="published-to-checkbox">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="node-published-to-switch"{{ publishedToManually ? ' checked="checked"' : '' }} />
                                        <label class="custom-control-label" for="node-published-to-switch">{{ 'setPublicationEndDate'|trans }}</label>
                                    </div>
                                </div>
                            </div>
                        {% endif %}
                        {% if form.status is defined %}
                            {{ form_row(form.status) }}
                        {% endif %}
                        {% if form.author_id is defined %}
                            {{ form_row(form.author_id) }}
                        {% endif %}
                        {% if form.flags is defined %}
                            {{ form_row(form.flags) }}
                        {% endif %}
                        {% if form.visibility is defined %}
                            {{ form_row(form.visibility) }}
                        {% endif %}
                        {% if form.parent_id is defined %}
                            {{ form_row(form.parent_id) }}
                        {% endif %}
                    </div>
                </div>
            </div>
            {% for id, group in layout.section('sidebar').fieldsGroups %}
                {{ _self.section(id, group, form, null, contentType) }}
            {% endfor %}
        </div>
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        {{ form_render.form_row(form, 'title', contentType) }}
                    </div>
                    {% if form.slug is defined %}
                        <div class="col">
                            {{ form_render.form_row(form, 'slug', contentType) }}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs page-form-tabs" role="tablist">
            {% for id, group in layout.section('main').fieldsGroups %}
                {{ _self.tab(id, {
                    active: group.active,
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
                {{ _self.tab_content(id, group, form, contentType) }}
            {% endfor %}

            {{ _self.tab_rest_content('rest', form) }}
        </div>
    </div>
</div>
{{ form_end(form) }}

<script nonce="{{ csp_nonce() }}">
    $(function () {
        let show = function () {
            let d = new Date();

            $('.published-to-date-selector').removeClass('d-none');
            $('#content_builder_form_page_published_to').val(
                d.getFullYear() + '-' +
                (d.getMonth() + 1) + '-' +
                d.getDate() + ' ' +
                d.getHours() + ':' +
                d.getMinutes() + ':' +
                d.getSeconds()
            );
        };
        let hide = function () {
            $('.published-to-date-selector').addClass('d-none');
            $('#content_builder_form_page_published_to').val('');
        };
        $('#node-published-to-switch').change(function () {
            if ($(this).is(':checked')) {
                show();
            } else {
                hide();
            }
        });
    });
</script>
