{% assets ['jquery_ui', 'tulia.contact_forms'] %}

{%- macro renderRequiredAttributes(attributes) -%}
    {%- for name, attr in attributes -%}
        {%- if not (attr.required is defined and attr.required) -%}
            <span style="color:#999">
            {%- set tooltip = '' -%}
        {%- else -%}
            {%- set tooltip = ('required'|trans) ~ ': ' -%}
        {%- endif -%}
        &nbsp;<span data-toggle="tooltip" title="{{ tooltip }}{{ attr.name }}">{{ name }}=""</span>
        {%- if not (attr.required is defined and attr.required) -%}
            </span>
        {%- endif -%}
    {%- endfor -%}
{%- endmacro -%}

{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}

{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

<div class="page-form">
    <div class="page-form-sidebar">
        <div class="accordion">
            <div class="accordion-section">
                <div class="accordion-section-button" data-toggle="collapse" data-target="#form-collapse-receivers">
                    {{ 'shortcode'|trans }}
                </div>
                <div class="collapse show">
                    <div class="accordion-section-body">
                        <p>{{ 'shortcodeToInsertOnPage'|trans }}</p>
                        <textarea class="form-control">[contact_form id="{{ form.id.vars.value }}"]</textarea>
                    </div>
                </div>
            </div>
            <div class="accordion-section">
                <div class="accordion-section-button" data-toggle="collapse" data-target="#form-collapse-receivers">
                    {{ 'receivers'|trans({}, 'forms') }}
                    {{ badge.errors_count(form, [ 'receivers' ]) }}
                </div>
                <div id="form-collapse-receivers" class="collapse show">
                    <div class="accordion-section-body pb-0">
                        {{ form_row(form.receivers) }}
                    </div>
                </div>
            </div>
            <div class="accordion-section">
                <div class="accordion-section-button" data-toggle="collapse" data-target="#form-collapse-sender">
                    {{ 'sender'|trans({}, 'forms') }}
                    {{ badge.errors_count(form, [ 'sender_name', 'sender_email', 'reply_to' ]) }}
                </div>
                <div id="form-collapse-sender" class="collapse show">
                    <div class="accordion-section-body pb-2">
                        {{ form_row(form.sender_name) }}
                        {{ form_row(form.sender_email) }}
                        {{ form_row(form.reply_to) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        {{ form_row(form.name, { attr: { autofocus: 'autofocus' } }) }}
                    </div>
                    <div class="col-6">
                        {{ form_row(form.subject) }}
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-tabs page-form-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#tab-fields">
                    {{ 'fields'|trans({}, 'forms') }}
                    {{ badge.errors_count(form, [ 'fields_template' ]) }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-fields-template">
                    {{ 'fieldsTemplate'|trans({}, 'forms') }}
                    {{ badge.errors_count(form, [ 'fields_template' ]) }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#tab-message">
                    {{ 'messageTemplate'|trans({}, 'forms') }}
                    {{ badge.errors_count(form, [ 'message_template' ]) }}
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-fields">
                {% verbatim %}
                    <div id="app">
                        <h1>{{ msg }}</h1>
                    </div>
                {% endverbatim %}
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="alert alert-info">
                                Here You can define the fields collection that will be used in the form fields template.
                            </div>
                            <div class="contact-form-fields-builder">
                                <div class="form-field-prototype" data-field-name="textarea">
                                    [textarea
                                        <label>name="<input data-option-name="name" class="form-control" type="text" name="" />"</label>
                                        <label>label="<input data-option-name="label" class="form-control" type="text" name="" />"</label>
                                        <label class="field-optional">help="<input data-option-name="help" class="form-control" type="text" name="" />"</label>
                                        <label class="field-optional">constraints="<input data-option-name="constraints" class="form-control" type="text" name="" />"</label>]
                                </div>
                            </div>
                            <div class="form-field-option-legends">
                                {% for field in fieldParsers %}
                                    {% for optionName, option in field.definition.options %}
                                        <div class="form-field-option-legend" data-option-legend-name="{{ field.name }}_{{ optionName }}">
                                            <div class="card">
                                                <div class="card-header">
                                                    <b>{{ optionName }}</b>
                                                </div>
                                                <div class="card-body">
                                                    {% if option.required|default(false) %}<b>Required</b> | {% endif %}
                                                    <i>type:</i> <code>{{ option.type }}</code><br /><br />
                                                    {{ option.name }}
                                                    {% if option.type == 'collection' and option.collection is defined and option.collection is iterable %}
                                                        <br /><br />Values separated by pipe (<code>|</code>). Allowed one or many of the follwing:<br />
                                                        {% for key, val in option.collection %}
                                                            <b>{{ key }}</b> - {{ val }}<br />
                                                        {% endfor %}
                                                    {% endif %}
                                                </div>
                                            </div>
                                        </div>
                                    {% endfor %}
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    .contact-form-fields-builder {
                        margin-bottom: 30px;
                    }
                    .contact-form-fields-builder .form-field-prototype {
                        font-family: monospace;
                    }
                    .contact-form-fields-builder .form-field-prototype label {
                        display: inline;
                    }
                    .contact-form-fields-builder .form-field-prototype label:hover {
                        cursor: pointer;
                    }
                    .contact-form-fields-builder .form-field-prototype .field-optional {
                        color: #999;
                    }
                    .contact-form-fields-builder .form-field-prototype .form-control {
                        display: inline;
                        width: 0;
                        min-width: 0;
                        min-height: 0;
                        height: 25px;
                        line-height: 22px;
                        border: 1px solid transparent;
                        padding: 0;
                        text-align: center;
                    }
                    .contact-form-fields-builder .form-field-prototype .form-control:hover {
                        border: 1px solid #ced4da;
                        outline: none !important;
                        box-shadow: none;
                    }
                    .contact-form-fields-builder .form-field-prototype .form-control:focus {
                        border: 1px solid #ced4da;
                        width: 10px;
                        min-width: 10px;
                        outline: none !important;
                        box-shadow: none;
                    }
                    .form-field-option-legends .form-field-option-legend {
                        display: none;
                        margin-top: 20px;
                    }
                </style>
                <script none="{{ csp_nonce() }}">
                    const showOptionLegend = function (name) {
                        $('.form-field-option-legend[data-option-legend-name=' + name +']').addClass('d-block');
                    };
                    const hideOptionLegend = function (name) {
                        $('.form-field-option-legend').removeClass('d-block');
                    };

                    $('.contact-form-fields-builder .form-field-prototype .form-control').on('change input', function () {
                        this.style.width = ((this.value.length + 0.2) * 8) + 'px'
                    }).on('focus', function () {
                        showOptionLegend(
                            $(this).closest('.form-field-prototype').attr('data-field-name')
                            + '_' +
                            $(this).attr('data-option-name')
                        );
                    }).on('blur', function () {
                        hideOptionLegend(
                            $(this).closest('.form-field-prototype').attr('data-field-name')
                            + '_' +
                            $(this).attr('data-option-name')
                        );
                    });
                </script>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ 'marker'|trans }}</th>
                            <th>{{ 'name'|trans }}</th>
                            <th class="text-right">{{ 'add'|trans }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for field in fieldParsers %}
                            <tr>
                                <th><code>[{{ field.name }}{{ _self.renderRequiredAttributes(field.definition.options) }}]</code></th>
                                <td>{{ field.definition.name }}</td>
                                <td class="text-right"><button type="button" class="btn btn-sm btn-success">{{ 'add'|trans }}</button></td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="tab-message">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            {{ form_row(form.message_template, { attr: { style: 'height:300px;font-family:monospace;font-size:15px;' } }) }}
                        </div>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ 'marker'|trans }}</th>
                            <th>{{ 'description'|trans }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><code>{% verbatim %}{{ contact_form_fields() }}{% endverbatim %}</code></th>
                            <td>Renders all form fields with submitted values as table.</td>
                        </tr>
                        <tr>
                            <th><code>{% verbatim %}{{ contact_form_field('name') }}{% endverbatim %}</code></th>
                            <td>Returns one submitted value of form, by given <code>name</code>.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="tab-pane fade" id="tab-fields-template">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            {{ form_row(form.fields_template, { attr: { style: 'height:300px;font-family:monospace;font-size:15px;' } }) }}
                        </div>
                    </div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ 'marker'|trans }}</th>
                            <th>{{ 'name'|trans }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {% for field in fieldParsers %}
                            <tr>
                                <th><code>[{{ field.name }}{{ _self.renderRequiredAttributes(field.definition.options) }}]</code></th>
                                <td>{{ field.definition.name }}</td>
                            </tr>
                        {% endfor %}
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{ form_end(form) }}
