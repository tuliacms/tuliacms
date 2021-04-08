{% macro locale_url_preview() %}
    <div class="card bg-light mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ 'websiteLocaleUrlPreview'|trans({}, 'websites') }}</h5>
            {{ 'backend'|trans }}: <code class="website-locale-preview-backend"></code><br />
            {{ 'frontend'|trans }}: <code class="website-locale-preview-frontend"></code>
        </div>
    </div>
{% endmacro %}

{% macro locale_remove_button(id) %}
    <button type="button" class="btn btn-danger btn-icon-left website-locale-remove" data-locale-code="{{ id }}">
        <span class="btn-icon fas fa-times"></span>
        {{ 'removeLocale'|trans({}, 'websites') }}
    </button>
{% endmacro %}
