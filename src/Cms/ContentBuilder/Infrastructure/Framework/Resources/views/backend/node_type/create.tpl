{% extends 'backend' %}
{% trans_default_domain 'content_builder' %}
{% assets ['content_builder.layout_builder'] %}

{% block title %}
    {{ 'createNodeType'|trans }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.content_builder.homepage') }}">{{ 'contentModel'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'createNodeType'|trans }}</li>
{% endblock %}

{% block content %}
    {% include relative(_self, 'parts/layout-builder.tpl') with { pageTitle: 'createNodeType'|trans } %}
{% endblock %}
