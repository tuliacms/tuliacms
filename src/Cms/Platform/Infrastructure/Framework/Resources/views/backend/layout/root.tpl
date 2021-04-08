{% block beforeall %}{% endblock %}

<!doctype html>
<html lang="{{ current_website().locale.language }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <script nonce="{{ csp_nonce() }}">
            let Tulia = {};{% assets ['backend'] %}
            Tulia.Globals = {
                search_anything: {
                    endpoint: '{{ path('backend.search.root') }}'
                }
            };
        </script>
        {% block beforehead %}{% endblock %}
        {{ theme_head() }}
        <title>{% block title %}{{ title('Tulia CMS Backend') }}{% endblock %}</title>
        {% block head %}{% endblock %}
    </head>
    <body>
        {% block beforebody %}{% endblock %}

        {% block body %}{% endblock %}

        {{ theme_body() }}
        <script nonce="{{ csp_nonce() }}">
            typeof moment !== 'undefined' ? moment.locale('{{ user().locale }}') : null;
        </script>

        {% block afterbody %}{% endblock %}
    </body>
</html>

{% block afterall %}{% endblock %}
