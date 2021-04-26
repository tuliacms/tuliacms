{% extends '@cms/installator/layout/base.tpl' %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'readyToInstall'|trans({}, 'installator') }}</h1>
                    </div>
                    <p class="lead">Konfiguracja dobiegła końca. Możesz jeszcze wrócić i poprawić wprowadzone dane, lub kontynuować instalację.</p>
                    <p class="lead">Instalator zainstaluje system Tulia CMS a po wszystkim przeniesie Cię na stronę podsumowania.</p>
                    <div class="row navigation-buttons">
                        <div class="col">
                            <a href="{{ path('installator.user') }}" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-arrow-left"></i> {{ 'goBack'|trans({}, 'installator') }}</a>
                        </div>
                        <div class="col text-right">
                            <a href="{{ path('installator.install') }}" class="btn btn-primary btn-icon-right"><i class="btn-icon fas fa-arrow-right"></i> {{ 'install'|trans({}, 'installator') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}
