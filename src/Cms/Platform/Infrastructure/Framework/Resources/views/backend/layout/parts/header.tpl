{# Store preview link, used in button in header. #}
{% set previewLink = previewLink ?? path('homepage') %}

<div class="layout-top">
    <header>
        <div class="header-inner">
            <div class="lead-section">
                <div class="search-area" tabindex="-1">
                    <span class="search-area-icon"><i class="fas fa-search"></i></span>
                    <div class="search-area-input">Przeszukaj panel administracyjny...</div>
                </div>
            </div>
            <div class="actions-section">
                <div class="action-box" data-toggle="tooltip" data-placement="left" title="{{ 'fullscreen'|trans }}">
                    <button type="button" class="btn btn-icon-only action-btn toggle-fullscreen"><i class="btn-icon fas fa-expand"></i></button>
                </div>
                {% set __currentWebsite = current_website() %}
                {% if website_list()|length > 1 %}
                    <div class="action-box language-selector noselect" data-toggle="tooltip" data-placement="left" title="{{ 'switchWebsite'|trans }}">
                        <div class="dropdown">
                            {% set __websiteId = __currentWebsite.id %}
                            <button class="btn btn-icon-left action-btn" type="button" data-bs-toggle="dropdown">
                                <i class="btn-icon fas fa-globe"></i>
                                {{ __currentWebsite.name }}
                            </button>
                            <div class="dropdown-menu">
                                {% for item in website_list() %}
                                    <a class="dropdown-item{{ item.id == __websiteId ? ' active' : '' }}" href="{{ item.backendAddress }}" data-website-id="{{ item.id }}">
                                        <span>{{ item.name }}</span>
                                    </a>
                                {% endfor %}
                            </div>
                        </div>
                    </div>
                {% endif %}
                <div class="action-box language-selector noselect" data-toggle="tooltip" data-placement="left" title="{{ 'contentLocale'|trans }}">
                    <div class="dropdown">
                        {% set __locale = __currentWebsite.locale.code %}
                        <button class="btn btn-icon-left action-btn" type="button" data-bs-toggle="dropdown">
                            <i class="btn-icon fa fa-language"></i>
                            {{ 'languageName'|trans({ code: __locale }, 'languages') }}
                        </button>
                        {% if app.request.attributes.has('_route') %}
                            {% set _route = app.request.attributes.get('_route') %}
                            {% set _route_params = app.request.attributes.get('_route_params')|default([]) %}
                        {% else %}
                            {% set _route = 'backend.homepage' %}
                            {% set _route_params = [] %}
                        {% endif %}
                        {% set _route_params = _route_params|merge(app.request.query.all) %}
                        <div class="dropdown-menu">
                            {% for lang in locales() %}
                                {% set _route_params = _route_params|merge({ _locale: lang.code }) %}
                                <a class="dropdown-item{{ lang.code == __locale ? ' active' : '' }}" href="{{ url(_route, _route_params) }}">
                                    <img src="{{ asset('/assets/core/flag-icons/' ~ lang.language ~ '.svg') }}" alt="" />
                                    <span>
                                        {{ 'languageName'|trans({ code: lang.code }, 'languages') }}
                                        {{ lang.isDefault ? ('<span style="font-size:10px;opacity:.4;">[' ~ 'default'|trans ~ ']</span>')|raw : '' }}
                                    </span>
                                </a>
                            {% endfor %}
                        </div>
                    </div>
                </div>
                <div class="action-box notifications-list" data-toggle="tooltip" data-placement="left" title="{{ 'notifications'|trans }}">
                    <div class="dropdown dropdown-prevent-close">
                        <button class="btn btn-icon-only action-btn" type="button" data-bs-toggle="dropdown">
                            <span class="badge badge-primary">12</span>
                            <span class="animate-ring">
                                <i class="btn-icon fa fa-bell"></i>
                            </span>
                        </button>
                        <div class="dropdown-menu notifications-dropdown">
                            <div class="headline">{{ 'notifications'|trans }}</div>
                            <div class="notifications-container">
                                <div id="notifications-scrollarea">
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            <a href="#">Some example text</a> that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            Some example text that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            Some example text that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            Some example text that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            Some example text that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            Some example text that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                    <div class="notification-item">
                                        <div class="notification-headline">Powiadomienie</div>
                                        <div class="notification-content">
                                            Some example text that's free-flowing within the dropdown menu.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="action-box" data-toggle="tooltip" data-placement="left" title="{{ 'pagePreview'|trans }}">
                    <a href="{{ previewLink }}" target="_blank" class="btn btn-icon-only action-btn"><i class="btn-icon fas fa-eye"></i></a>
                </div>
                <div class="action-box" data-toggle="tooltip" data-placement="left" title="{{ 'myAccount'|trans }}">
                    <div class="dropdown">
                        <button class="btn btn-icon-only action-btn" type="button" data-bs-toggle="dropdown">
                            <i class="btn-icon fas fa-user-tie"></i>
                        </button>
                        <div class="dropdown-menu pb-0">
                            <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.me') }}"><i class="dropdown-icon fas fa-user-tie"></i> {{ 'myAccount'|trans }}</a>
                            <a class="dropdown-item dropdown-item-with-icon" href="{{ path('backend.me.personalization') }}"><i class="dropdown-icon fas fa-cogs"></i> {{ 'personalization'|trans }}</a>
                            <a class="dropdown-item dropdown-item-with-icon dropdown-item-logout" href="{{ path('backend.logout') }}"><i class="dropdown-icon fas fa-power-off"></i> {{ 'logout'|trans }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="breadcrumbs-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                {{ layout__breadcrumbsHome|raw }}
                {{ layout__breadcrumbs|raw }}
            </ol>
        </nav>
    </div>
    {% apply spaceless %}
        <div class="alerts-bar">
            {{ flashes() }}
        </div>
    {% endapply %}
</div>
