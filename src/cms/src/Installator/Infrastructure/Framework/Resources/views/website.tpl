{% extends '@installator/layout/base.tpl' %}

{% import '@backend/website/parts/macros.tpl' as this %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'website'|trans({}, 'installator') }}</h1>
                    </div>
                    {{ form_start(form) }}
                    {{ form_errors(form) }}
                    <div class="row">
                        <div class="col-6">{{ form_row(form.name, { attr: { autofocus: 'autofocus' } }) }}</div>
                        <div class="col-6">{{ form_row(form.backend_prefix, { attr: { class: 'website-backend-prefix-input' } }) }}</div>
                    </div>
                    <div id="website-locale-forms">
                        <div class="locale-container" id="{{ (form.code.vars.value)|md5 }}">
                            <div class="card">
                                <div class="card-header">
                                    {{ 'languageName'|trans({ code: form.code.vars.value }, 'languages') }}
                                </div>
                                <div class="card-body">
                                    {{ this.locale_url_preview() }}

                                    {{ form_row(form.domain, { attr: { class: 'locale-domain-input' } }) }}
                                    {{ form_row(form.path_prefix, { attr: { class: 'locale-path-prefix-input' } }) }}
                                    {{ form_row(form.locale_prefix, { attr: { class: 'locale-locale-prefix-input' } }) }}
                                    {{ form_row(form.code, { attr: { class: 'locale-code-select' } }) }}
                                    {{ form_row(form.ssl_mode) }}
                                </div>
                            </div>
                        </div>
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
    {% include '@backend/website/parts/website-form-utility-script.tpl' %}
{% endblock %}
