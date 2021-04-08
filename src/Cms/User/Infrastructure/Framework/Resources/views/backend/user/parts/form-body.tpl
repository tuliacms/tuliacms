{{ form_start(form, { attr: { autocomplete: 'off' } }) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}
{{ form_row(form._token) }}

<input type="text" name="username" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
<input type="email" name="email" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />
<input type="password" name="password" style="display: block;position: fixed;left:-1000px;top:-1000px;opacity:0;" tabindex="-1" />

<div class="page-form" id="node-form">
    <div class="page-form-sidebar">
        {{ form_extension_render(manager, 'sidebar', {
            active_first: ['_FIRST_']
        }) }}
    </div>
    <div class="page-form-content">
        <div class="page-form-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        {{ form_row(form.username, { attr: { autofocus: 'autofocus', autocomplete: 'off' } }) }}
                    </div>
                    <div class="col-6">
                        {{ form_row(form.email, { attr: { autocomplete: 'off' } }) }}
                    </div>
                </div>
            </div>
        </div>
        {{ form_extension_render(manager, 'default', {
            active_first: ['_FIRST_']
        }) }}
    </div>
</div>
{{ form_end(form) }}
