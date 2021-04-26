{% extends '@cms/installator/layout/base.tpl' %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'database'|trans({}, 'installator') }}</h1>
                    </div>
                    {% if connectionError %}
                        <div class="alert alert-danger">
                            {{ connectionError }}
                        </div>
                    {% endif %}
                    {{ form_start(form) }}
                        <input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                        <input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                        <input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                        {{ form_errors(form) }}
                        <div class="row">
                            <div class="col-6">{{ form_row(form.host, { attr: { tabindex: 1 } }) }}</div>
                            <div class="col-6">{{ form_row(form.name, { attr: { tabindex: 4 } }) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6">{{ form_row(form.port, { attr: { tabindex: 2 } }) }}</div>
                            <div class="col-6">{{ form_row(form.username, { attr: { tabindex: 5 } }) }}</div>
                        </div>
                        <div class="row">
                            <div class="col-6">{{ form_row(form.prefix, { attr: { tabindex: 3 } }) }}</div>
                            <div class="col-6">{{ form_row(form.password, { attr: { tabindex: 6 } }) }}</div>
                        </div>
                        <div class="row navigation-buttons">
                            <div class="col">
                                <a href="{{ path('installator.requirements') }}" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-arrow-left"></i> {{ 'goBack'|trans({}, 'installator') }}</a>
                            </div>
                            <div class="col text-right">
                                <button type="submit" class="btn btn-primary btn-icon-right"><i class="btn-icon fas fa-arrow-right"></i> {{ 'continueInstallation'|trans({}, 'installator') }}</button>
                            </div>
                        </div>
                    {{ form_end(form) }}
                </div>
            </div>
        </div>
    </div>
{% endblock %}
