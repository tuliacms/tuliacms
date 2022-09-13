<footer class="block block-footer block-bg-dark">
    <div class="container-xxl">
        <div class="footer-boxes">
            <div class="footer-info">
                {{ widgets_space('footer_info') }}
            </div>
            <div class="footer-menu">
                {{ widgets_space('footer_quick_links') }}
            </div>
            <div class="footer-contact">
                {% if customizer_get('lisa.footer.contact.type') == '1' %}
                    {{ widgets_space('footer_contact') }}
                {% else %}
                    {% include relative(_self, 'footer-contact.tpl') %}
                {% endif %}
            </div>
        </div>
        <div class="copyrights"><b>Lisa Theme</b> by <a href="https://tuliacms.com/" title="Tulia CMS" target="_blank">Tulia CMS Team</a><br /> {{ 'now'|date('Y') }} &copy; <span class="footer-copyrights" {{ customizer_live_control('lisa.footer.contact.copyrights') }}>{{ customizer_get('lisa.footer.contact.copyrights') }}</span></div>
    </div>
</footer>
