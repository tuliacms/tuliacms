{% extends 'backend' %}

{% block title %}
    {{ 'usersList'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'usersList'|trans({}, 'users') }}</li>
{% endblock %}

{% import '@backend/_macros/datatable/generator.tpl' as generator %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="{{ path('backend.user.create') }}" class="btn btn-success btn-icon-left"><i class="btn-icon fas fa-plus"></i> {{ 'add'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        {{ generator.generate(datatable, {
            data_endpoint: path('backend.user.datatable')
        }) }}
    </div>
{% endblock %}
