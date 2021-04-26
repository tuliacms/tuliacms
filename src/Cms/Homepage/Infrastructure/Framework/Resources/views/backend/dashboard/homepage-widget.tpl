{% set hideThisWidget = '<a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> ' ~ 'hideThisWidget'|trans ~ '</a>' %}

<div class="widget">
    <div class="widget-inner">
        <div class="pane">
            <div class="pane-header">
                <div class="pane-buttons">
                    <div class="dropdown">
                        <button class="btn btn-icon-only" type="button" data-toggle="dropdown">
                            <i class="btn-icon fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            {% if block('dropdown') is defined %}
                                {{ block('dropdown') }}
                            {% else %}
                                {{ hideThisWidget|raw }}
                            {% endif %}
                        </div>
                    </div>
                </div>
                <i class="pane-header-icon {{ icon|default('fas fa-box')|raw }}"></i>
                <div class="pane-title">{{ title|default('--missing title--')|raw }}</div>
            </div>
            <div class="pane-body px-0">
                {% if block('content') is defined %}
                    {{ block('content') }}
                {% else %}
                    Missing widget content...
                {% endif %}
            </div>
        </div>
    </div>
</div>
