{{ form_start(form) }}
{{ form_errors(form) }}
{{ form_row(form.id) }}

{{ dump(item.metadata) }}

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-asterisk"></i> Basics
            </div>
            <div class="card-body">
                {{ form_row(form.name) }}
                {{ form_row(form.type) }}
                {% if persistMode == 'create' %}
                    {{ form_row(form.parentId) }}
                {%  endif %}
                {{ form_row(form.visibility) }}
                {{ form_row(form.identity) }}
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-network-wired"></i> Destination
            </div>
            <div class="card-body">
                {% include relative(_self, 'type-homepage.tpl') %}
                {% include relative(_self, 'type-url.tpl') %}
                {% for type in types %}
                    <div class="menu-item-type{{ item.type == type.type.type ? '' : ' d-none' }}" data-type="{{ type.type.type }}">
                        {{ type.selector.render(type.type, item.identity)|raw }}
                    </div>
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-border-style"></i> Options
            </div>
            <div class="card-body">
                {{ form_row(form.hash, { input_addon_prepend: '#' }) }}
                {{ form_row(form.target) }}
                {{ form_rest(form) }}
            </div>
        </div>
    </div>
</div>

{% include relative(_self, 'javascripts.tpl') %}
{{ form_end(form) }}
