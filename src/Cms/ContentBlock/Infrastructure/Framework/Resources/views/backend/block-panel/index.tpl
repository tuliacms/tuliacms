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

            let lastBodyHeight = 0;
            let body = document.body,
                html = document.documentElement;
            let bodyHeightUpdater = function () {
                let height = Math.max( body.scrollHeight, body.offsetHeight,
                    html.clientHeight, html.scrollHeight, html.offsetHeight );

                if (height !== lastBodyHeight) {
                    lastBodyHeight = height;

                    window.parent.postMessage({
                        action: 'height-changed',
                        height: height
                    }, "{{ app.request.schemeAndHttpHost }}");
                }
            };

            setInterval(bodyHeightUpdater, 300);
        });
    </script>
{% endblock %}
