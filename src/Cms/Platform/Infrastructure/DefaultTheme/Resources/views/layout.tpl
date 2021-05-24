{% assets ['bootstrap', 'frontend'] %}

<!doctype html>
<html lang="{{ current_website().locale.language }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{% block title %}{{ title(option('website_name')) }}{% endblock %}</title>
        {{ theme_head() }}
        {% block head %}{% endblock %}
    </head>
    <body class="{{ body_class(app.request) }}">
        {% block beforebody %}{% endblock %}
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="{{ path('homepage') }}">{{ option('website_name') }}</a>
            {{ show_menu('2e996c06-0a74-4441-88cc-d0573bed8256') }}
        </nav>
        {{ breadcrumbs() }}
        <div class="container">
            <div class="row">
                <div class="col">
                    {{ flashes() }}
                    {% block content %}This is a default theme view. That means there is no Theme defined in CMS.{% endblock %}
                </div>
            </div>
        </div>
        <footer class="border-top">
            <p class="text-center text-muted my-4">{{ 'now'|date('Y') }} &copy; {{ option('website_name') }}</p>
        </footer>
        {{ theme_body() }}
        {% block afterbody %}{% endblock %}
    </body>
</html>
