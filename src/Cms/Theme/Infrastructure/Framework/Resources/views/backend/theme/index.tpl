{% extends 'backend' %}

{% block title %}
    {{ 'themes'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">{{ 'themes'|trans }}</li>
{% endblock %}

{% block content %}
    <div class="pane pane-lead">
        <div class="pane-header">
            <i class="pane-header-icon fas fa-palette"></i>
            <h1 class="pane-title">{{ 'themes'|trans }}</h1>
        </div>
        <div class="pane-body">
            {% if usesDefaultTheme %}
                <div class="row">
                    <div class="col">
                        <div class="alert alert-info">
                            <strong>{{ 'defaultThemeInUse'|trans({}, 'themes') }}</strong>
                            <p class="mb-0">{{ 'youUseDefaultThemeInfo'|trans({}, 'themes') }}</p>
                        </div>
                    </div>
                </div>
            {% endif %}
            <div class="row">
                {% for item in themes %}
                    <div class="col-3">
                        <div class="card{{ theme == item ? ' bg-light' : '' }}">
                            {% if theme == item %}
                                <div class="ribbon"><span>{{ 'activeTheme'|trans({}, 'themes') }}</span></div>
                            {% endif %}
                            {% if item.thumbnail %}
                                <img src="{{ asset(item.thumbnail) }}" class="card-img-top" alt="{{ item.name }} theme thumbnail">
                            {% else %}
                                <svg class="bd-placeholder-img card-img-top" style="font-size:1.125rem;text-anchor:middle;" width="100%" height="180" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="xMidYMid slice" focusable="false" role="img">
                                    <title>{{ 'noThumbnailAvailable'|trans }}</title>
                                    <rect width="100%" height="100%" fill="#868e96"></rect>
                                    <text x="50%" y="50%" fill="#ddd" dy=".3em">{{ 'noThumbnailAvailable'|trans }}</text>
                                </svg>
                            {% endif %}
                            <div class="card-body">
                                <h5 class="card-title mb-0">
                                    {{ item.name }}
                                </h5>
                                {% if item.info %}
                                    <p class="card-text mt-3">{{ item.info }}</p>
                                {% endif %}
                                {% if item.parent %}
                                    <hr />
                                    <p class="text-muted m-0">{{ 'childOfTheme'|trans({ name: '<i>' ~ item.parent ~ '</i>' }, 'themes')|raw }}</p>
                                {% endif %}
                            </div>
                            <div class="card-footer">
                                {% if theme == item %}
                                    <a href="{{ path('backend.theme.customize.current') }}" class="btn btn-sm btn-primary">{{ 'customize'|trans({}, 'themes') }}</a>
                                {% endif %}
                                {% if theme != item %}
                                    <form action="{{ path('backend.theme.activate') }}" method="POST">
                                        <input type="hidden" novalidate="novalidate" name="_token" value="{{ csrf_token('theme.activate') }}" />
                                        <input type="hidden" name="theme" value="{{ item.name }}" />
                                        <button type="submit" class="btn btn-sm btn-secondary tulia-click-page-loader">{{ 'activate'|trans({}, 'themes') }}</button>
                                    </form>
                                {% endif %}
                            </div>
                        </div>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
    <style>
        .card {
            position: relative;
        }
        .ribbon {
            position: absolute;
            right: -5px;
            top: -5px;
            z-index: 1;
            overflow: hidden;
            width: 89px;
            height: 89px;
            text-align: right;
        }
        .ribbon span {
            font-size: 10px;
            font-weight: bold;
            color: #FFF;
            text-transform: uppercase;
            text-align: center;
            line-height: 20px;
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
            width: 120px;
            display: block;
            background: #79A70A;
            background: linear-gradient(#2989d8 0%, #1e5799 100%);
            box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
            position: absolute;
            top: 26px;
            right: -24px;
        }
        .ribbon span::before {
            content: "";
            position: absolute;
            left: 0px;
            top: 100%;
            z-index: -1;
            border-left: 3px solid #1e5799;
            border-right: 3px solid transparent;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #1e5799;
        }
        .ribbon span::after {
            content: "";
            position: absolute;
            right: 0px;
            top: 100%;
            z-index: -1;
            border-left: 3px solid transparent;
            border-right: 3px solid #1e5799;
            border-bottom: 3px solid transparent;
            border-top: 3px solid #1e5799;
        }
    </style>
{% endblock %}
