{% extends 'theme' %}

{% block content %}
    <h1>{{ term.name }}</h1>

    {{ edit_links(term) }}

    {% if term.thumbnail %}
        <p>{{ image(term.thumbnail, { size: 'node-thumbnail' }) }}</p>
    {% endif %}

    <div class="row">
        {% for node in nodes %}
            <div class="col-4">
                {% if node.thumbnail %}
                    <a href="{{ node_path(node) }}" title="{{ node.title }}">
                        {{ image(node.thumbnail, { size: 'node-thumbnail' }) }}
                    </a>
                {% endif %}
                <h2><a href="{{ node_path(node) }}" title="{{ node.title }}">{{ node.title }}</a></h2>
                {{ edit_links(node) }}
                <p>{{ node.introduction }}</p>
                <a href="{{ node_path(node) }}" title="{{ node.title }}" class="btn btn-primary">{{ 'readMore'|trans }}</a>
            </div>
        {% endfor %}
    </div>

    {{ paginator.position('center')|raw }}
{% endblock %}
