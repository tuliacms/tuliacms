{% embed '@backend/content_builder/layout/_parts/editor/form_layout.sidebar.tpl' %}
    {% block page_header %}
        {% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        {{ form_render.form_row(form, 'title', contentType) }}
                    </div>
                    {% if form.slug is defined %}
                        <div class="col">
                            {{ form_render.form_row(form, 'slug', contentType) }}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
    {% endblock %}
    {% block sidebar_accordion %}
        <div class="accordion-section">
            <div class="accordion-section-button" data-bs-toggle="collapse" data-bs-target="#form-collapse-sidebar-status">
                {{ 'publicationStatus'|trans }}
            </div>
            <div id="form-collapse-sidebar-status" class="accordion-collapse collapse show">
                <div class="accordion-section-body">
                    {% if form.published_at is defined %}
                        {{ form_row(form.published_at) }}

                        {% set publishedToManually = form.published_to.vars.value != '' %}
                        <div class="node-published-to-selector mb-4">
                            <div class="published-to-date-selector{{ publishedToManually ? '' : ' d-none' }}">
                                {{ form_row(form.published_to) }}
                            </div>
                            <div class="published-to-checkbox">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="node-published-to-switch"{{ publishedToManually ? ' checked="checked"' : '' }} />
                                    <label class="custom-control-label" for="node-published-to-switch">{{ 'setPublicationEndDate'|trans }}</label>
                                </div>
                            </div>
                        </div>
                    {% endif %}
                    {% if form.status is defined %}
                        {{ form_row(form.status) }}
                    {% endif %}
                    {% if form.author_id is defined %}
                        {{ form_row(form.author_id) }}
                    {% endif %}
                    {% if form.flags is defined %}
                        {{ form_row(form.flags) }}
                    {% endif %}
                    {% if form.visibility is defined %}
                        {{ form_row(form.visibility) }}
                    {% endif %}
                    {% if form.parent_id is defined %}
                        {{ form_row(form.parent_id) }}
                    {% endif %}
                </div>
            </div>
        </div>
    {% endblock %}
{% endembed %}

<script nonce="{{ csp_nonce() }}">
    $(function () {
        let show = function () {
            let d = new Date();

            $('.published-to-date-selector').removeClass('d-none');
            $('#content_builder_form_page_published_to').val(
                d.getFullYear() + '-' +
                (d.getMonth() + 1) + '-' +
                d.getDate() + ' ' +
                d.getHours() + ':' +
                d.getMinutes() + ':' +
                d.getSeconds()
            );
        };
        let hide = function () {
            $('.published-to-date-selector').addClass('d-none');
            $('#content_builder_form_page_published_to').val('');
        };
        $('#node-published-to-switch').change(function () {
            if ($(this).is(':checked')) {
                show();
            } else {
                hide();
            }
        });
    });
</script>
