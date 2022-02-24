{% embed '@backend/content_builder/layout/_parts/editor/form_layout.sidebar.tpl' %}
    {% block page_header %}
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        {% if form.email is defined %}
                            {{ form_row(form.email, { attr: { autocomplete: 'off' } }) }}
                        {% else %}
                            <div class="mb-3">
                                <label class="form-label">{{ 'email'|trans }}</label>
                                <input type="text" disabled="disabled" class="form-control" value="{{ context.user_email }}" />
                            </div>
                        {% endif %}
                    </div>
                </div>
            </div>
        </div>
    {% endblock %}
{% endembed %}
