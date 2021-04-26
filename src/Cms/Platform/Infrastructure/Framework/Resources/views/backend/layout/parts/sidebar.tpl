<aside>
    <div class="sidebar-container">
        <div class="noselect" data-simplebar>
            <a class="cms-logo" href="{{ path('backend.homepage') }}">
                <img class="logo-image" src="{{ asset('/assets/core/backend/theme/images/logo.svg') }}" alt="Tulia CMS" />
            </a>
            <div class="user-area">
                {% set user = user() %}
                <a href="{{ path('backend.me') }}" class="user-avatar" title="{{ 'myAccountUsername'|trans({ username: user.name ?? user.username }) }}">
                    {% if user.avatar is defined and user.avatar %}
                        <img src="{{ asset(user.avatar) }}" />
                    {% endif %}
                    <div class="user-details">
                        <div class="user-name">{{ user.name ?? user.username }}</div>
                        {% if user.email is defined and user.email %}
                            <div class="user-email">{{ user.email }}</div>
                        {% endif %}
                    </div>
                </a>
            </div>
            <div class="lead-menu">
                {{ backend_menu() }}
            </div>
            <div class="sidebar-footer">

            </div>
        </div>
    </div>
</aside>
