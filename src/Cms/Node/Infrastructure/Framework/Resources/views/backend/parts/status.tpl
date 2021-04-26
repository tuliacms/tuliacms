{{ form_row(form.publishedAt) }}

{% set publishedToManually = form.publishedTo.vars.value != '' %}
<div class="node-published-to-selector mb-4">
    <div class="published-to-date-selector{{ publishedToManually ? '' : ' d-none' }}">
        {{ form_row(form.publishedTo) }}
    </div>
    <div class="published-to-checkbox">
        <div class="custom-control custom-checkbox">
            <input type="checkbox" class="custom-control-input" id="node-published-to-switch"{{ publishedToManually ? ' checked="checked"' : '' }} />
            <label class="custom-control-label" for="node-published-to-switch">{{ 'setPublicationEndDate'|trans }}</label>
        </div>
    </div>
</div>
{{ form_row(form.status) }}
