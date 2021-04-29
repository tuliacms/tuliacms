{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}
{% import '@backend/website/parts/macros.tpl' as this %}

{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        <div class="accordion">
            <div class="accordion-section">
                <div class="accordion-section-button" data-toggle="collapse" data-target="#form-collapse-basics">
                    {{ 'description'|trans }}
                </div>
                <div id="form-collapse-basics" class="collapse show">
                    <div class="accordion-section-body">
                        {{ 'websitesLongDescription'|trans({}, 'websites') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        {{ form_row(form.name, { attr: { autofocus: 'autofocus' } }) }}
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs page-form-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-basics">
                    {{ 'languages'|trans }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade active show" id="tab-basics">
                <div class="container-fluid mb-4">
                    <div class="row">
                        <div class="col">
                            <div class="layout-with-sidebar">
                                <div class="layout-sidebar">
                                    <div class="list-group" id="website-locale-triggers">
                                        {% for key, locale in form.locales %}
                                            <a href="#" class="list-group-item{{ loop.index == 1 ? ' active' : '' }}" data-locale-code="{{ (locale.code.vars.value ~ key)|md5 }}">
                                                {{ 'languageName'|trans({ code: locale.code.vars.value }, 'languages') }}
                                            </a>
                                        {% endfor %}
                                    </div>
                                    <div class="list-group mt-4">
                                        <a href="#" id="website-locale-add" class="list-group-item list-group-item-with-icon list-group-item-success">
                                            <span class="list-group-item-icon fas fa-plus"></span>
                                            {{ 'addLocale'|trans({}, 'websites') }}
                                        </a>
                                    </div>
                                </div>
                                <div class="layout-content" id="website-locale-forms">
                                    {% for key, locale in form.locales %}
                                        <div class="locale-container{{ loop.index == 1 ? '' : ' d-none' }}" id="{{ (locale.code.vars.value ~ key)|md5 }}">
                                            <div class="card">
                                                <div class="card-header">
                                                    {{ 'languageName'|trans({ code: locale.code.vars.value }, 'languages') }}
                                                </div>
                                                <div class="card-body">
                                                    {{ this.locale_url_preview() }}

                                                    {{ form_row(locale.domain, { attr: { class: 'locale-domain-input' } }) }}
                                                    {{ form_row(locale.path_prefix, { attr: { class: 'locale-path-prefix-input' } }) }}
                                                    {{ form_row(locale.locale_prefix, { attr: { class: 'locale-locale-prefix-input' } }) }}
                                                    {{ form_row(locale.code, { attr: { class: 'locale-code-select' } }) }}
                                                    {{ form_row(locale.is_default, { attr: { class: 'locale-default-select' } }) }}
                                                    {{ form_row(locale.ssl_mode) }}
                                                </div>
                                                <div class="card-footer text-right">
                                                    {{ this.locale_remove_button((locale.code.vars.value ~ key)|md5) }}
                                                </div>
                                            </div>
                                        </div>
                                    {% endfor %}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="template" id="form-website-locale-prototype">
    <div class="locale-container" id="__name__">
        <div class="card">
            <div class="card-header">{{ 'newLocale'|trans({}, 'websites') }}</div>
            <div class="card-body">
                {{ this.locale_url_preview() }}

                {{ form_row(form.locales.vars.prototype.domain, { attr: { class: 'locale-domain-input' } }) }}
                {{ form_row(form.locales.vars.prototype.path_prefix, { attr: { class: 'locale-path-prefix-input' } }) }}
                {{ form_row(form.locales.vars.prototype.locale_prefix, { attr: { class: 'locale-locale-prefix-input' } }) }}
                {{ form_row(form.locales.vars.prototype.code, { attr: { class: 'locale-code-select' } }) }}
                {{ form_row(form.locales.vars.prototype.is_default, { attr: { class: 'locale-default-select' } }) }}
                {{ form_row(form.locales.vars.prototype.ssl_mode) }}
            </div>
            <div class="card-footer text-right">
                {{ this.locale_remove_button('') }}
            </div>
        </div>
    </div>
</script>

{{ form_rest(form) }}
{% include relative(_self, 'website-form-utility-script.tpl') %}
{{ form_end(form) }}
