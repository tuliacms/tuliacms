<div class="block block-bg-lightgray block-company-in-numbers">
    <div class="container-xxl">
        <div class="row">
            <div class="col">
                <p class="lead">{{ intro|default|raw }}</p>
                <h2>{{ headline|default|raw }}</h2>
                <div class="block-numbers row">
                    {% for number in numbers %}
                        <div class="block-number-item col-12 col-sm-6 col-xl-3">
                            <div class="block-number-item-inner">
                                <div class="block-number">
                                    <span class="block-number-counter">{{ number.number_value|default|raw }}</span>
                                    {% if number.number_label_after is defined and number.number_label_after is not empty %}
                                        <span class="block-number-after">{{ number.number_label_after|default|raw }}</span>
                                    {% endif %}
                                </div>
                                <div class="block-number-label">{{ number.number_label|default|raw }}</div>
                            </div>
                        </div>
                    {% endfor %}
                </div>
            </div>
        </div>
    </div>
</div>
