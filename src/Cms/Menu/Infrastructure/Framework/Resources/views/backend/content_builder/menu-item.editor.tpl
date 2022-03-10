{% import '@backend/content_builder/layout/_parts/editor/form_render.tpl' as form_render %}
{% trans_default_domain 'menu' %}

{{ form_render.form_begin(form) }}

<div class="row">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-asterisk"></i> {{ 'basics'|trans }}
            </div>
            <div class="card-body">
                {% for id, group in layout.section('basics').fieldsGroups %}
                    {% for field in group.fields %}
                        {{ form_render.form_row(form, field, contentType) }}
                    {% endfor %}
                {% endfor %}
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-network-wired"></i> {{ 'destination'|trans }}
            </div>
            <div class="card-body">
                {% set item = context.item %}
                {% include relative(_self, '../item/parts/type-homepage.tpl') %}
                {% include relative(_self, '../item/parts/type-url.tpl') %}
                {% for type in context.types %}
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
                <i class="fas fa-border-style"></i> {{ 'options'|trans }}
            </div>
            <div class="card-body">
                {% for id, group in layout.section('options').fieldsGroups %}
                    {% for field in group.fields %}
                        {{ form_render.form_row(form, field, contentType) }}
                    {% endfor %}
                {% endfor %}
            </div>
        </div>
    </div>
</div>

{% include '@backend/menu/item/parts/javascripts.tpl' %}

{{ form_render.form_end(form) }}
