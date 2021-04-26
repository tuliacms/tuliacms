{% extends '@backend/homepage/dashboard/homepage-widget.tpl' %}

{% assets ['momentjs'] %}

{% set title = 'lastActivity'|trans %}
{% set icon = 'fas fa-bell' %}

{% block content %}
    <div class="activity-widget">
        <div class="activity-list">
            {% for row in rows %}
                <div class="activity-item">
                    <div class="activity-content">{{ row.message|trans(row.context, row.translationDomain)|raw }}</div>
                    <div class="activity-date" title="{{ row.createdAt|date('Y-m-d H:i:s') }}">{{ row.createdAt|date('Y-m-d H:i:s') }}</div>
                </div>
            {% endfor %}
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            $('.activity-date').each(function () {
                $(this).text(moment($(this).text()).fromNow());
            });
        });
    </script>
{% endblock %}
