{% extends '@installator/layout/base.tpl' %}

{% set showHeader = false %}
{% set showFooter = false %}

{% block body %}
    <div class="intro-vh-centered">
        <div class="jumbotron">
            <h1 class="display-4">{{ 'tuliaCmsInstallator'|trans({}, 'installator') }}</h1>
            <p class="lead">{{ 'installatorIntroWelcome'|trans({}, 'installator') }}</p>
            <hr class="my-4">
            <a class="btn btn-primary btn-icon-right" href="{{ path('installator.requirements') }}" role="button"><i class="btn-icon fas fa-magic"></i> {{ 'letsStart'|trans({}, 'installator') }}</a>
        </div>
        {% include relative(_self, 'layout/footer.tpl') %}
    </div>
{% endblock %}
