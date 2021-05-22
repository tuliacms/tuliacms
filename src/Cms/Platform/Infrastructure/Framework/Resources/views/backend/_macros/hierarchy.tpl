{% macro change(tree, formSaveUrl, csrfTokenName) %}
    {% assets ['nestable'] %}

    {% set nestableId = 'menu-nestable-' ~ uniqid() %}

    <form action="{{ formSaveUrl }}" id="hierarchy-form" method="post">
        <input type="hidden" value="{{ csrf_token(csrfTokenName) }}" name="_token" />
    </form>

    <div class="alert alert-primary" role="alert">
        <h4 class="alert-heading">{{ 'changeHierarchy'|trans }}</h4>
        <p class="mb-0">{{ 'changeHierarchyMoveElementOnTheThree'|trans }}</p>
    </div>

    <div class="dd nestable2" id="{{ nestableId }}">
        <ol class="dd-list">
            {{ _self.treeLeaf(tree) }}
        </ol>
    </div>

    <style>
        body .dd {max-width:100%}
        body .dd-handle {padding:10px 15px 9px;height:auto;font-size:14px;font-weight:normal;transition:.12s all;}
        body .dd-handle .dd-handle-icon {display:inline-block;margin-right:5px;}
        body .dd-handle:hover {cursor:move;}
        body .dd-empty,
        body .dd-item,
        body .dd-placeholder {line-height:1.2}
        body .dd-item > button {height:27px;width:30px;font-size:16px}
        body .dd-item > button.dd-collapse:before {content:"\f107";font-family:"Font Awesome 5 Free",serif;font-weight:900}
        body .dd-item > button.dd-expand:before {content:"\f106";font-family:"Font Awesome 5 Free",serif;font-weight:900}
    </style>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            let form = $('#hierarchy-form');
            let nest = $('.dd#{{ nestableId }}');

            nest.nestable({
                callback: function() {
                    let data = nest.nestable('toArray');
                    form.find('input.terms-hierarchy').remove();

                    for (let i = 0; i < data.length; i++) {
                        form.append('<input type="hidden" class="terms-hierarchy" name="term[' + data[i].id + ']" value="' + (data[i].parent_id || 0) + '" />');
                    }
                }
            });
        });
    </script>
{% endmacro %}

{% macro treeLeaf(items) %}
    {% for item in items %}
        <li class="dd-item" data-id="{{ item.id }}">
            <div class="dd-handle"><i class="fa fa-arrows-alt dd-handle-icon"></i> {{ item.name }}</div>
            {% if item.children %}
                <ol class="dd-list">
                    {{ _self.treeLeaf(item.children) }}
                </ol>
            {% endif %}
        </li>
    {% endfor %}
{% endmacro %}
