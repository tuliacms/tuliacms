{% macro section(id, group, form) %}
    <div class="accordion-section">
        <div
                class="accordion-section-button {{ group.active ? '' : 'collapsed' }}"
                data-bs-toggle="collapse"
                data-bs-target="#form-collapse-sidebar-{{ id }}"
        >
            {{ group.label }}
        </div>
        <div
                id="form-collapse-sidebar-{{ id }}"
                class="accordion-collapse collapse {{ group.active ? 'show' : '' }}"
        >
            <div class="accordion-section-body">
                {% for field in group.fields %}
                    {{ form_row(form[field]) }}
                {% endfor %}
            </div>
        </div>
    </div>
{% endmacro %}
