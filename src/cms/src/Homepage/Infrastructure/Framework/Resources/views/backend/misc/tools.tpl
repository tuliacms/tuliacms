{% extends 'backend' %}

{% block title %}
    {{ 'tools'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'tools'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-tools"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="row row-tiles">
                {% for tile in tiles %}
                    <div class="col-2">
                        <a href="{{ tile.link }}" class="tile">
                            <i class="tile-icon {{ tile.icon }}"></i>
                            <span class="tile-header">{{ tile.name }}</span>
                            <span class="tile-text">{{ tile.description }}</span>
                        </a>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
{% endblock %}
