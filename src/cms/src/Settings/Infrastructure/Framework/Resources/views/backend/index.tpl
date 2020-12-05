{% extends 'backend' %}

{% block title %}
    {% if group.id == 'cms' %}
        {{ 'settings'|trans }}
    {% else %}
        {{ 'settings'|trans }} - {{ group.name|trans({}, group.translationDomain) }}
    {% endif %}
{% endblock %}

{% block breadcrumbs %}
    {% if group.id != 'cms' %}
        <li class="breadcrumb-item"><a href="{{ path('backend.settings') }}">{{ 'settings'|trans }}</a></li>
    {% endif %}
    <li class="breadcrumb-item active" aria-current="page">{{ group.name|trans({}, group.translationDomain) }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="#" class="btn btn-success btn-icon-left" data-submit-form="{{ form.vars.id }}"><i class="btn-icon fas fa-save"></i> {{ 'save'|trans }}</a>
            </div>
            <i class="pane-header-icon fas fa-cogs"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="layout-with-sidebar">
                <div class="layout-sidebar">
                    <ul class="list-group">
                        {% for item in groups %}
                            <a href="{{ path('backend.settings', { group: item.id }) }}" class="list-group-item list-group-item-with-icon{{ group.id == item.id ? ' active' : '' }}">
                                {% if item.icon %}
                                    <span class="list-group-item-icon {{ item.icon }}"></span>
                                {% endif %}
                                {{ item.name|trans({}, item.translationDomain) }}
                            </a>
                        {% endfor %}
                    </ul>
                </div>
                <div class="layout-content">
                    {{ form_start(form) }}
                        <input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                        <input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                        <input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                        {{ form_errors(form) }}
                        {% include view.name with view.data %}
                        {{ form_rest(form) }}
                    {{ form_end(form) }}
                </div>
            </div>
        </div>
    </div>
{% endblock %}
