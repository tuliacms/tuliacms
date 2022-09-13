{% include relative(_self, 'navbar.tpl') %}
{% if is_homepage() %}
    {% include relative(_self, 'hero/static.tpl') %}
{% else %}
    <div class="header-pillow"></div>
{% endif %}
