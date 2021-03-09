{% extends '@cms/installator/layout/base.tpl' %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'requirements'|trans({}, 'installator') }}</h1>
                    </div>
                    <table class="table table-striped table-bordered">
                        <tbody>
                            {% for item in requirements %}
                                {% if item.status == constant('\\Tulia\\Cms\\Installator\\Application\\Requirements\\Requirement::STATUS_REQUIRE') %}
                                    {% set rowClass = 'table-danger' %}
                                    {% set name = item.name %}
                                    {% set solution = item.solution %}
                                {% elseif item.status == constant('\\Tulia\\Cms\\Installator\\Application\\Requirements\\Requirement::STATUS_WARNING') %}
                                    {% set rowClass = 'table-warning' %}
                                    {% set name = item.name %}
                                    {% set solution = item.solution %}
                                {% else %}
                                    {% set rowClass = '' %}
                                    {% set name = item.name %}
                                    {% set solution = '<span class="text-success">' ~ item.cause ~ '</span>' %}
                                {% endif %}
                                <tr class="{{ rowClass }}">
                                    <td>{{ name|raw }}</td>
                                    <td>{{ solution|raw }}</td>
                                </tr>
                            {% endfor %}
                        </tbody>
                    </table>
                    {% if allowGoFurther %}
                        <div class="alert alert-success">
                            {{ 'requirementsMetInfo'|trans({}, 'installator') }}
                        </div>
                        <div class="row navigation-buttons">
                            <div class="col">
                                <a href="{{ path('installator') }}" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-arrow-left"></i> {{ 'goBack'|trans({}, 'installator') }}</a>
                            </div>
                            <div class="col text-right">
                                <a href="{{ path('installator.database') }}" class="btn btn-primary btn-icon-right"><i class="btn-icon fas fa-arrow-right"></i> {{ 'continueInstallation'|trans({}, 'installator') }}</a>
                            </div>
                        </div>
                    {% else %}
                        <div class="alert alert-danger">
                            {{ 'requirementsNotMetInfo'|trans({}, 'installator') }}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    </div>
{% endblock %}
