<div style="position:relative;" class="contact-form-anchor">
    <div id="anchor_{{ form.vars.id }}" style="display:block;position:absolute;left:0;top:-100px"></div>
</div>
{% for messages in get_flashes(['cms.form.submit_success']) %}
    {% for message in messages %}
        <div class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-bs-dismiss="alert"><span>&times;</span></button>
            {{ message|raw }}
        </div>
    {% endfor %}
{% endfor %}
{% for messages in get_flashes(['cms.form.submit_failed']) %}
    {% for message in messages %}
        <div class="alert alert-warning alert-dismissible fade show">
            <button type="button" class="close" data-bs-dismiss="alert"><span>&times;</span></button>
            {{ message|raw }}
        </div>
    {% endfor %}
{% endfor %}
{{ form_start(form) }}
{{ include(template_from_string(template, template_name)) }}
{{ form_errors(form) }}
{{ form_end(form) }}
