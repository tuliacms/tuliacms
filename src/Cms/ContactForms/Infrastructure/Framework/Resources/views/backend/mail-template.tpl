{% set form_fields %}
    <table>
        <tbody>
            {% for field in form.fields %}
                {% if data[field.name] is defined %}
                    <tr>
                        <th>{{ field.options.label }}</th>
                        <td>{{ data[field.name] }}</td>
                    </tr>
                {% endif %}
            {% endfor %}
        </tbody>
    </table>
{% endset %}

{{ render_string(message, {
    __contact_form_fields: form_fields,
    __contact_form_data: data
}) }}
