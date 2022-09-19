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
                    </div>
                    {{ dump(customizer_get('lisa.header.show_language_switcher')) }}
                    {% if customizer_get('lisa.header.show_language_switcher') == 'yes' and current_website().locales|length %}
                        asdasd
                    {% endif %}
                </nav>
            </div>
        </div>
    </div>
</header>
