{% extends 'theme' %}

{% block content %}
    <h1>{{ node.title }}</h1>

    <p>
        {{ 'publishedAtDate'|trans({ date: format_date(node.publishedAt) }) }}<br />
        {% if category %}
            {{ 'category'|trans }}: <a href="{{ term_path(category) }}">{{ category.name }}</a>
        {% endif %}
    </p>

    {{ edit_links(node) }}

    <p>{{ node.introduction }}</p>

    {# {% set author = gate('user').author(node) %}
    {% if author %}
        <p>
            {% if author.avatar %}
                <img src="{{ asset(author.avatar) }}" alt="" class="img-thumbnail" style="width:50px">
            {% endif %}
            <span>{{ 'author'|trans }}: {{ author.name }}</span>
        </p>
    {% endif %} #}

    {% if node.thumbnail %}
        <p>{{ image(node.thumbnail, { size: 'node-thumbnail' }) }}</p>
    {% endif %}

    {{ node.content|raw }}

    {# {% set tags = gate('taxonomy').tags(node) %}
    {% if tags|length > 0 %}
        <p>
            {{ 'tags'|trans }}:
            {% for tag in tags %}
                <a href="{{ taxonomy_path(tag) }}">{{ tag.name }}</a>{% if loop.index != tags|length %},{% endif %}
            {% endfor %}
        </p>
    {% endif %} #}
{% endblock %}
