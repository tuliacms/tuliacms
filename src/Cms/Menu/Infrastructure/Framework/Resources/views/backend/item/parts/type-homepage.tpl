{% set type   = 'simple:homepage' %}
{% set active = item.type == type %}

<div class="menu-item-type{{ active ? '' : ' d-none' }}" data-type="{{ type }}">
    <div class="form-group">
        <label>{{ 'homepage'|trans }}</label>
        <input type="text" disabled readonly value="{{ 'homepage'|trans }}" class="form-control" />
    </div>
</div>
