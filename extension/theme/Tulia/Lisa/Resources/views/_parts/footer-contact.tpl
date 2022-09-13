<div class="widget-title">{{ 'contactInfo'|trans({}, 'lisa-theme') }}</div>

{% set phone = customizer_get('lisa.footer.contact.phone') %}
{% set email = customizer_get('lisa.footer.contact.email') %}

<div class="contact-row contact-address" {{ customizer_live_control('lisa.footer.contact.address', { nl2br: true }) }}>{{ customizer_get('lisa.footer.contact.address')|nl2br }}</div>
{% if phone %}
    <div class="contact-row contact-phone"><a href="tel:{{ phone }}"><i class="cr-icon fas fa-phone-alt"></i><span {{ customizer_live_control('lisa.footer.contact.phone') }}>{{ phone }}</span></a></div>
{% endif %}
{% if email %}
    <div class="contact-row contact-email"><a href="mailto:{{ email }}"><i class="cr-icon fas fa-envelope"></i><span {{ customizer_live_control('lisa.footer.contact.email') }}>{{ email }}</span></a></div>
{% endif %}

{% set facebook = customizer_get('lisa.footer.socials.facebook') %}
{% set twitter  = customizer_get('lisa.footer.socials.twitter') %}
{% set youtube  = customizer_get('lisa.footer.socials.youtube') %}

<div class="socials">
    {% if facebook %}
        <a href="{{ facebook }}" target="_blank" rel="noopener, noreferer" class="social-icon social-icon-facebook" title="Facebook"><i class="fab fa-facebook-f"></i></a>
    {% endif %}
    {% if twitter %}
        <a href="{{ twitter }}" target="_blank" rel="noopener, noreferer" class="social-icon social-icon-twitter" title="Twitter"><i class="fab fa-twitter"></i></a>
    {% endif %}
    {% if youtube %}
        <a href="{{ youtube }}" target="_blank" rel="noopener, noreferer" class="social-icon social-icon-youtube" title="Youtube"><i class="fab fa-youtube"></i></a>
    {% endif %}
</div>
