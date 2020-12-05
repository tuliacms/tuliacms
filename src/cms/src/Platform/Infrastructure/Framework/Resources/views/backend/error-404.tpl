{% extends 'backend' %}

{% block title %}
    {{ 'anErrorOccured'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ block('title') }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-exclamation-triangle"></i>
            <h1 class="pane-title">{{ block('title') }}</h1>
        </div>
        <div class="pane-body">
            <div class="jumbotron p-5 mb-0">
                <p class="m-0 p-0 text-center" style="font-size:20px;font-weight:bold;">{{ exception.message }}</p>
            </div>
        </div>
    </div>
{% endblock %}
