<span title="{{ row.published_at|date('Y-m-d H:i:s') }}">
    {% if date(row.published_at) > date() %}
        {{ 'plannedOnDate'|trans({ date: row.published_at|date('Y-m-d H:i:s') }) }}
    {% else %}
        {{ row.published_at|date('Y-m-d') }}
    {% endif %}
</span>
