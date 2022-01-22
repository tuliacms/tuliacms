{% extends 'backend' %}
{% trans_default_domain 'content_builder' %}

{% block title %}
    {{ 'createContentTypeOf'|trans({ name: type|trans }) }}
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item"><a href="{{ path('backend.content_builder.homepage') }}">{{ 'contentModel'|trans }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ 'createContentTypeOf'|trans({ name: type|trans }) }}</li>
{% endblock %}

{% block content %}
    {% do builderView.addData({ pageTitle: 'createContentTypeOf'|trans({ name: type|trans }) }) %}
    {% include builderView.views[0] with builderView.data %}
{% endblock %}
