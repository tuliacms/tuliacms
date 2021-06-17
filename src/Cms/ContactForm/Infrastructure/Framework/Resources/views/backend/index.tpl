{% extends 'backend' %}

{% block title %}
    {{ 'forms'|trans({}, 'contact-form') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'forms'|trans({}, 'contact-form') }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.contact_form.create') }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'create'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-window-restore"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>

        {{ generator.generate(datatable, {
            data_endpoint: path('backend.contact_form.datatable')
        }) }}
    </div>
{% endblock %}
