{# Deprecated #}
{% macro autogenerated_info(autogenerated) %}
    <div class="alert alert-danger">The <code>autogenerated_info()</code> macro is deprecated, please use <code>translation_missing_info()</code>.</div>
    {#{% if autogenerated %}
        <div class="alert alert-info alert-dismissible fade show mb-0">
            {{ 'missingTranslationInThisLocaleSaveToSaveInThisLocale'|trans }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    {% endif %}#}
{% endmacro %}

{#
    Shows alert when there is missing translation for the model.
    Should be used only for EDIT pages.

    <code>
        {{ alerts.translation_missing_info(model.translated) }}
    </code>
#}
{% macro translation_missing_info(translated) %}
    {% if not translated %}
        <div class="alert alert-info alert-dismissible fade show mb-0">
            {{ 'missingTranslationInThisLocaleSaveToSaveInThisLocale'|trans }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    {% endif %}
{% endmacro %}

{#
    Shows alert when current locale is different than default one.
    Should be used only for CREATE pages.

    <code>
        {{ alerts.foreign_locale_creation_info() }}
    </code>
#}
{% macro foreign_locale_creation_info() %}
    {% if not app.request.isDefaultContentLocale %}
        <div class="alert alert-info alert-dismissible fade show mb-0">
            {{ 'foreignLocaleElementCreationNotificationMessage'|trans }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    {% endif %}
{% endmacro %}
