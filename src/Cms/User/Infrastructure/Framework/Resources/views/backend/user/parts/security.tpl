<div class="container-fluid">
    <div class="row">
        <div class="col">
            {{ form_row(form.password, { attr: { autocomplete: 'off' } }) }}
            {{ form_row(form.roles) }}
            {{ form_row(form.enabled) }}
        </div>
    </div>
</div>
