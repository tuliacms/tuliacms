<div class="row footer-info">
    <div class="col">
        <span class="text-muted">{{ 'language'|trans }}:</span>
        {% set currentWebsite = current_website() %}
        {% set currentLocale  = currentWebsite.locale %}
        {% set _route = app.request.attributes.get('_route') %}
        <select class="form-control form-control-raw language-selector">
            {% for locale in currentWebsite.locales %}
                <option value="{{ path(_route, { _locale: locale.code }) }}" {{ currentLocale.code == locale.code ? 'selected="selected"' : '' }}>{{ 'languageName'|trans({ code: locale.code }, 'languages') }}</option>
            {% endfor %}
        </select>
    </div>
    <div class="col">
        <p class="text-muted text-right">Tulia CMS {{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}, {{ 'released'|trans({}, 'installator') }} {{ constant('Tulia\\Cms\\Platform\\Version::RELEASED') }}</p>
    </div>
</div>
<script nonce="{{ csp_nonce() }}">
    $('.language-selector').change(function () {
        document.location.href = $(this).val();
    });
</script>
