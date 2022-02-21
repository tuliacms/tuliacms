{% embed '@backend/content_builder/layout/_parts/editor/form_layout.sidebar.tpl' %}
    {% block page_header %}
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        {{ form_row(form.email, { attr: { autocomplete: 'off' } }) }}
                    </div>
                </div>
            </div>
        </div>
    {% endblock %}
{% endembed %}
