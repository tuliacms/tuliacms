{% assets ['bootstrap', 'backend'] %}
<!doctype html>
<html lang="{{ current_website().locale.language }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    {% block beforehead %}{% endblock %}
    {{ theme_head() }}
    <title>{% block title %}{{ 'tuliaCmsInstallator'|trans({}, 'installator') }}{% endblock %}</title>
    {% block head %}{% endblock %}
    <meta name="robots" content="noindex,nofollow" />
</head>
<body>
    {% if showHeader is not defined %}
        {% set showHeader = true %}
    {% endif %}
    {% if showFooter is not defined %}
        {% set showFooter = true %}
    {% endif %}

    <div class="installator-container">
        {% if showHeader %}
            {% include relative(_self, 'header.tpl') %}
        {% endif %}

        {{ flashes() }}

        {% block body %}{% endblock %}

        {% if showFooter %}
            {% include relative(_self, 'footer.tpl') %}
        {% endif %}
    </div>

    {{ theme_body() }}
    <style>{% include relative(_self, 'style.css') %}</style>
</body>
</html>
