<header>
    <div class="container-xxl">
        <div class="row">
            <div class="col">
                <nav class="navbar">
                    <a class="logo logo-text" href="{{ path('homepage') }}"{{ customizer_live_control('lisa.header.logo.text') }}>{{ customizer_get('lisa.header.logo.text') }}</a>
                    <button class="hamburger" type="button">
                        <span class="hamburger-box hamburger--squeeze">
                            <span class="hamburger-inner"></span>
                        </span>
                    </button>
                    <div class="header-menu">
                        {{ widgets_space('mainmenu') }}
                        {% if customizer_get('lisa.header.show_language_switcher') == 'yes' and current_website().locales|length %}
                            <div class="dropdown language-switcher">
                                <a href="#" class="text-white dropdown-toggle text-uppercase" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ current_website().locale.language }}
                                </a>
                                <ul class="dropdown-menu">
                                    {% for locale in current_website().locales %}
                                        <li><a href="{{ path('homepage', { _locale: locale.code }) }}" class="dropdown-item">{{ locale.code|trans_locale }}</a></li>
                                    {% endfor %}
                                </ul>
                            </div>
                        {% endif %}
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
