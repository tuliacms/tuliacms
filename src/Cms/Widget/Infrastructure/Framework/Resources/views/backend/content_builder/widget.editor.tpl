{% embed '@backend/content_builder/layout/_parts/editor/form_layout.sidebar.tpl' %}
    {% block page_header %}
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        {{ form_row(form.name, { attr: { autofocus: 'true' } }) }}
                    </div>
                    <div class="col-6">
                        {{ form_row(form.space) }}
                    </div>
                </div>
            </div>
        </div>
    {% endblock %}
{% endembed %}
