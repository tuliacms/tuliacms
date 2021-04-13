{% extends 'theme' %}

{% block content %}
    <pre><code>{{ path('_wdt', { token: '12c524' }) }}</code></pre>
    <pre><code>{{ url('_wdt', { token: '12c524' }) }}</code></pre>
{% endblock %}
