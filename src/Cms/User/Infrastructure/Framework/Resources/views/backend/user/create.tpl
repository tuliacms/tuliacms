{% extends 'backend' %}

{% block title %}
    {{ 'createUser'|trans({}, 'users') }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.user') }}">{{ 'usersList'|trans({}, 'users') }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'createUser'|trans({}, 'users') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                {{ form_row(form.cancel) }}
                {{ form_row(form.save) }}
            </div>
            <i class="pane-header-icon fas fa-file-powerpoint"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body p-0">
            {% include relative(_self, 'parts/form-body.tpl') %}
        </div>
    </div>
{% endblock %}
