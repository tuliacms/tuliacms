{% extends '@cms/installator/layout/base.tpl' %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'administrator'|trans({}, 'installator') }}</h1>
                    </div>
                    {{ form_start(form) }}
                    {{ form_errors(form) }}
                    <input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                    <input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                    <input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
                    <div class="row">
                        <div class="col-6">{{ form_row(form.username, { attr: { autofocus: 'autofocus', autocomplete: 'off', tabindex: 1 } }) }}</div>
                        <div class="col-6">{{ form_row(form.password.first, { attr: { tabindex: 3 } }) }}</div>
                    </div>
                    <div class="row">
                        <div class="col-6">{{ form_row(form.email, { attr: { autocomplete: 'off', tabindex: 2 } }) }}</div>
                        <div class="col-6">{{ form_row(form.password.second, { attr: { tabindex: 4 } }) }}</div>
                    </div>
                    <div class="row navigation-buttons">
                        <div class="col">
                            <a href="{{ path('installator.database') }}" class="btn btn-primary btn-icon-left"><i class="btn-icon fas fa-arrow-left"></i> {{ 'goBack'|trans({}, 'installator') }}</a>
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
