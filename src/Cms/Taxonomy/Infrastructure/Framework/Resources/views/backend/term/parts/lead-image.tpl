{{ form_row(form.thumbnail) }}
{% if form.thumbnail.vars.value %}
    {{ image(form.thumbnail.vars.value, { size: 'thumbnail-md', attributes: { alt: 'Node thumbnail', class: 'img-thumbnail' } }) }}
{% endif %}
