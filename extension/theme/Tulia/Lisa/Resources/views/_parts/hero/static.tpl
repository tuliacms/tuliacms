{% set link = customizer_get('hero.static.link') %}

<div class="hero hero-static">
    {% if link %}
        <a href="{{ link }}" class="hero-link-wrapper">
    {% endif %}

    {% set default_bg_desktop = asset('/assets/theme/tulia/lisa/theme/images/hero-image.jpg') %}
    {% set default_bg_mobile = asset('/assets/theme/tulia/lisa/theme/images/hero-image.mobile.jpg') %}

    {% set static_bg_desktop
        = customizer_get('hero.static.background')
            ? image_url(customizer_get('hero.static.background'), 'original')
            : default_bg_desktop
    %}
    {% set static_bg_mobile
        = customizer_get('hero.static.background_mobile')
            ? image_url(customizer_get('hero.static.background_mobile'), 'mobile_banner')
            : default_bg_mobile
    %}

    <div
        class="hero-image d-xxl-block d-xl-block d-lg-block d-md-block d-sm-none d-none"
        {{ customizer_live_control('hero.static.background', { type: 'background-image', image_size: 'original', default: default_bg_desktop }) }}
        style="background-image:url('{{ static_bg_desktop }}');"
    ></div>
    <div
        class="hero-image d-xxl-none d-xl-none d-lg-none d-md-none d-sm-block d-block"
        {{ customizer_live_control('hero.static.background_mobile', { type: 'background-image', image_size: 'mobile_banner', default: default_bg_mobile }) }}
        style="background-image:url('{{ static_bg_mobile }}');"
    ></div>
    <div class="hero-text">
        <div class="hero-headline" {{ customizer_live_control('hero.static.headline') }}>
            {{ customizer_get('hero.static.headline') }}
        </div>
        <div class="hero-description" {{ customizer_live_control('hero.static.description') }}>
            {{ customizer_get('hero.static.description') }}
        </div>

        {% if customizer_get('hero.static.button.show') == 'yes' and link %}
            <button type="button" class="btn btn-primary btn-inversed btn-icon-right">
                <span {{ customizer_live_control('hero.static.button.label') }}>{{ customizer_get('hero.static.button.label') }}</span>
                <i class="btn-icon fas fa-chevron-right"></i>
            </button>
        {% endif %}
    </div>

    {% if link %}
        </a>
    {% endif %}
</div>
