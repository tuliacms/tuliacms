{% set type   = 'simple:url' %}
{% set active = item.type == type %}

<div class="menu-item-type{{ active ? '' : ' d-none' }}" data-type="{{ type }}">
    <div class="form-group">
        <label for="item_type_url" class="required">{{ 'itemTypeUrl'|trans({}, 'menu') }}</label>
        <input type="text" id="item_type_url" class="form-control item-type-field-autofocus" data-identity="{{ type }}" value="{{ active ? model.identity|raw : '' }}" />
    </div>
</div>
