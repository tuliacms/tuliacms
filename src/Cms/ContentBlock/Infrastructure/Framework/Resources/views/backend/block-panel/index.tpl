{% extends '@backend/layout/root.tpl' %}

{% block body %}
    {{ render_content_builder_form_layout(formDescriptor) }}

    <style>
        body {background-color:transparent;}
    </style>

    <script nonce="{{ csp_nonce() }}">
        $(function() {
            window.addEventListener('message', (event) => {
                if (event.data.action === 'validate-form') {
                    $('form').submit();
                }
            }, false);

            window.parent.postMessage({
                action: 'loaded'
            }, "{{ app.request.schemeAndHttpHost }}");

            if ({{ validatedAndReadyToSave ? 'true' : 'false' }}) {
                window.parent.postMessage({
                    action: 'form-valid',
                    fields: {{ data|json_encode|raw }}
                }, "{{ app.request.schemeAndHttpHost }}");
            }
        });
    </script>
{% endblock %}
