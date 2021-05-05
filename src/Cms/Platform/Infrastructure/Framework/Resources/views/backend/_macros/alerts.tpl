{#
    Shows alert when there is missing translation for the model.
    Should be used only for EDIT pages.

    <code>
        {{ alerts.translation_missing_info(model.translated) }}
    </code>
#}
{% macro translation_missing_info(translated) %}
    {% if not translated %}
        <div class="alert alert-info alert-dismissible fade show">
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
    {% if app.request.attributes.get('_content_locale') != current_website().defaultLocale.code %}
        <div class="alert alert-info alert-dismissible fade show">
            {{ 'foreignLocaleElementCreationNotificationMessage'|trans }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    {% endif %}
{% endmacro %}
