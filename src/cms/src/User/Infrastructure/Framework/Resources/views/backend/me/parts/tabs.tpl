{% set activeTab = activeTab|default('my-account') %}

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link{{ activeTab == 'my-account' ? ' active' : '' }}" href="{{ path('backend.me') }}">{{ 'myAccount'|trans }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ activeTab == 'personalization' ? ' active' : '' }}" href="{{ path('backend.me.personalization') }}">{{ 'personalization'|trans }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ activeTab == 'edit' ? ' active' : '' }}" href="{{ path('backend.me.edit') }}">{{ 'editAccount'|trans({}, 'users') }}</a>
    </li>
    <li class="nav-item">
        <a class="nav-link{{ activeTab == 'password' ? ' active' : '' }}" href="{{ path('backend.me.password') }}">{{ 'changePassword'|trans({}, 'users') }}</a>
    </li>
</ul>
