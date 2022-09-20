<!doctype html>
<html lang="{{ app.request.get('_content_locale') }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{% block title %}{{ title() }}{% endblock %}</title>
        {{ do_action('theme.head') }}
        {% block head %}{% endblock %}
    </head>
    <body class="{{ body_class(app.request) }}">
        {% block beforebody %}{% endblock %}
        {% include relative(_self, '_parts/header.tpl') %}
        {% if not is_homepage() and customizer_get('lisa.layout.breadcrumbs') == 'yes' %}
            <div class="breadcrumbs-container">
                <div class="container-xxl">
                    <div class="row">
                        <div class="col">
                            {{ breadcrumbs() }}
                        </div>
                    </div>
                </div>
            </div>
        {% endif %}
        <div class="container-fluid content-container">
            <div class="row">
                <div class="col">
                    {{ flashes() }}
                </div>
            </div>
        </div>
        {% block content %}{% endblock %}
        {% include relative(_self, '_parts/footer.tpl') %}
        {{ do_action('theme.body') }}
        {% block afterbody %}{% endblock %}
    </body>
</html>
