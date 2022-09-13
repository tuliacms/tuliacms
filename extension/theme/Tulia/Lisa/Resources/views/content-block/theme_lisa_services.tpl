<div class="block block-services block-bg-dark">
    <div class="container-xxl">
        <div class="row">
            <div class="col-12 col-lg-6">
                <p class="lead">{{ intro|default }}</p>
                <h2>{{ headline|default }}</h2>
            </div>
            <div class="col-12 col-lg-6">
                <p class="services-slogan">{{ short_text|default }}</p>
            </div>
        </div>
        <div class="services-collection">
            <div class="row">
                {% for service in services %}
                    <div class="col-12 col-sm-12 col-md-6 col-lg-4">
                        <a href="{{ service.service_link|default|raw }}" class="service-item service-item-hoverable">
                            {% if service.service_icon is defined %}
                                <div class="service-icon">
                                    <i class="{{ service.service_icon|default|raw }}"></i>
                                </div>
                            {% endif %}
                            {{ service.service_content|default|raw }}
                        </a>
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
</div>
