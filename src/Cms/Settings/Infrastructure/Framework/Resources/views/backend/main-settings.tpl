{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#settings-main-tab">
            {{ 'settings'|trans }}
            {{ badge.errors_count(form, [ 'website_name', 'website_favicon', 'administrator_email', 'date_format', 'theme' ]) }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings-maintenance-tab">
            {{ 'maintenance'|trans }}
            {{ badge.errors_count(form, [ 'maintenance_mode', 'maintenance_message' ]) }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings-content-tab">
            {{ 'content'|trans }}
            {{ badge.errors_count(form, [ 'wysiwyg_editor' ]) }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings-content-routing">
            {{ 'frontendRouting'|trans({}, 'settings') }}
            {{ badge.errors_count(form, [ 'url_suffix' ]) }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings-email-tab">
            {{ 'email'|trans }}
            {{ badge.errors_count(form, [ 'mail_from_email', 'mail_from_name', 'mail_transport', 'mail_host', 'mail_port', 'mail_username', 'mail_password', 'mail_encryption', 'mail_sendmailpath' ]) }}
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="settings-main-tab">
        <div class="form-controls-terminator">
            {{ form_row(form.website_name) }}
            {{ form_row(form.website_favicon) }}
            {{ form_row(form.administrator_email) }}
            {{ form_row(form.date_format) }}
        </div>
    </div>
    <div class="tab-pane fade" id="settings-maintenance-tab">
        <div class="form-controls-terminator">
            {{ form_row(form.maintenance_mode) }}
            {{ form_row(form.maintenance_message) }}
        </div>
    </div>
    <div class="tab-pane fade" id="settings-content-tab">
        <div class="form-controls-terminator">
            {{ form_row(form.wysiwyg_editor) }}
        </div>
    </div>
    <div class="tab-pane fade" id="settings-content-routing">
        <div class="form-controls-terminator">
            {{ form_row(form.url_suffix) }}
        </div>
    </div>
    <div class="tab-pane fade" id="settings-email-tab">
        <div class="form-controls-terminator">
            <div class="alert alert-info">
                {{ 'mailSenderEmailInfo'|trans({}, 'settings') }}
            </div>
            {{ form_row(form.mail_from_email) }}
            {{ form_row(form.mail_from_name) }}
            {{ form_row(form.mail_transport) }}
            <div class="settings-mail-group-smtp{{ form.vars.value.mail_transport == 'smtp' ? '' : ' d-none' }}">
                {{ form_row(form.mail_host) }}
                {{ form_row(form.mail_port) }}
                {{ form_row(form.mail_username) }}
                {{ form_row(form.mail_password) }}
                {{ form_row(form.mail_encryption) }}
            </div>
            <div class="settings-mail-group-sendmail{{ form.vars.value.mail_transport == 'sendmail' ? '' : ' d-none' }}">
                {{ form_row(form.mail_sendmailpath) }}
            </div>
            <div class="card">
                <div class="card-header">
                    {{ 'sendTestMessage'|trans({}, 'settings') }}
                </div>
                <div class="card-body">
                    <p>{{ 'sendTestMessageInfo'|trans({}, 'settings') }}</p>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="test-text-message-rerecipient" placeholder="{{ 'email'|trans }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-icon-left submit-test-text-message"><i class="btn-icon fa fa-envelope"></i> {{ 'submit'|trans }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-test-message-status">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ 'testMessageSendStatus'|trans({}, 'settings') }}</h4>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
            </div>
        </div>
    </div>
</div>
<script nonce="{{ csp_nonce() }}">
    $(function () {
        $('#settings_cms_form_mail_transport').change(function () {
            if ($(this).val() == 'smtp') {
                $('.settings-mail-group-smtp').removeClass('d-none');
                $('.settings-mail-group-sendmail').addClass('d-none');
            } else if($(this).val() == 'sendmail') {
                $('.settings-mail-group-smtp').addClass('d-none');
                $('.settings-mail-group-sendmail').removeClass('d-none');
            } else {
                $('.settings-mail-group-smtp').addClass('d-none');
                $('.settings-mail-group-sendmail').addClass('d-none');
            }
        });

        $('#modal-test-message-status').appendTo('body');

        $('.submit-test-text-message').click(function () {
            $('#modal-test-message-status')
                .modal()
                .find('.modal-body')
                .html('<p class="lead text-center my-4"><i class="fa fa-circle-notch fa-w-16 fa-spin fa-lg" style="line-height:1;display:inline-block;vertical-align:middle;"></i><span style="display:inline-block;vertical-align:middle;"> &nbsp; {{ 'loadingTestMailResult'|trans({}, 'settings') }}</span></p>');

            $.ajax({
                method: 'POST',
                url: '{{ path('backend.settings.send_test_email') }}',
                data: {
                    _token:    '{{ csrf_token('cms_settings_test_mail') }}',
                    recipient: $('#test-text-message-rerecipient').val()
                },
                success: function (result) {
                    let content = '';

                    if(result.status === 'success')
                        content += '<div class="alert alert-success">' + result.message + '</div>';
                    else
                        content += '<div class="alert alert-danger">' + result.message + '</div>';

                    if(result.log)
                        content += '<h4>{{ 'mailTransportTestMessageLog'|trans({}, 'settings') }}</h4><pre style="border:1px solid #ddd;padding:10px;border-radius:6px;margin:10px 0 0 0;max-height:300px;overflow:auto"><code>' + result.log + '</code></pre>';

                    $('#modal-test-message-status .modal-body').html(content);
                },
                error: function (result) {
                    $('#modal-test-message-status .modal-body').html('<div class="alert alert-danger">{{ 'testMailSubmitAppErrorConnection'|trans({}, 'settings') }}</div>');
                }
            });
        });
    });
</script>
