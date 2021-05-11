{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form.type) }}
{{ form_row(form._token) }}

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        {{ form_skeleton_render(form, 'sidebar', {
            active_first: ['status', '_FIRST_']
        }) }}
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    {% if form.slug is defined %}
                        <div class="col-6">
                            {{ form_row(form.title, { attr: { autofocus: 'autofocus' } }) }}
                        </div>
                        <div class="col-6">
                            {{ form_row(form.slug) }}
                        </div>
                    {% else %}
                        <div class="col">
                            {{ form_row(form.title, { attr: { autofocus: 'autofocus' } }) }}
                        </div>
                    {% endif %}
                </div>
            </div>
        </div>
        {{ form_skeleton_render(form, 'default', {
            active_first: ['content', '_FIRST_']
        }) }}
    </div>
</div>
{{ form_end(form) }}

<script nonce="{{ csp_nonce() }}">
    $(function () {
        let show = function () {
            $('.published-to-date-selector').removeClass('d-none');
        };

        let hide = function () {
            $('.published-to-date-selector').addClass('d-none');
            $('#node_form_publishedTo').val('');
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
