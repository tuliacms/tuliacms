<div class="block block-call-to-action block-margin-bottom-0 block-margin-top-0">
    <div class="container-xxl">
        <div class="row">
            <div class="col">
                <div class="block-inner">
                    <div class="block-text">
                        <span class="block-icon {{ icon_bg|default|raw }}"></span>
                        {{ text|default|raw }}
                    </div>
                    <div class="block-button">
                        <a href="{{ btn_url|default('#')|raw }}" class="btn btn-primary btn-lg btn-inversed btn-icon-right">
                            <span>{{ text_btn|default|raw }}</span>
                            <i class="btn-icon {{ icon_btn|default|raw }}"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
