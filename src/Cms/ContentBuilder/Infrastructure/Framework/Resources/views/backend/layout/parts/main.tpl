{% macro tab(id, group, form) %}
    <li class="nav-item">
        <a
                href="#"
                class="nav-link {{ group.active ? 'active' : '' }}"
                data-bs-toggle="tab"
                data-bs-target="#tab-{{ id }}"
        >
            {{ group.name }}
        </a>
    </li>
{% endmacro %}

{% macro tab_content(id, group, form) %}
    <div class="tab-pane fade {{ group.active ? 'show active' : '' }}" id="tab-{{ id }}">
        {% if group.interior == 'default' %}
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
        {% endif %}
        {% for field in group.fields %}
            <div class="col">
                {{ form_row(form[field]) }}
            </div>
        {% endfor %}
        {% if group.interior == 'default' %}
                    </div>
                </div>
            </div>
        {% endif %}
    </div>
{% endmacro %}

{% macro tab_rest_content(id, form) %}
    <div class="tab-pane fade" id="tab-{{ id }}">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="empty-form-section-placeholder" data-placeholder="{{ 'thereAreNoOtherSettings'|trans }}">{{ form_rest(form) }}</div>
                </div>
            </div>
        </div>
    </div>
{% endmacro %}
