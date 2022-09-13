<div class="block block-text">
    <div class="container-xxl">
        <div class="row">
            <div class="col">
                {% if intro is defined and not intro|empty %}
                    <p class="lead">{{ intro }}</p>
                {% endif %}
                {% if headline is defined and not headline|empty %}
                    <h2>{{ headline }}</h2>
                {% endif %}

                {{ content|default|raw }}
            </div>
        </div>
    </div>
</div>
