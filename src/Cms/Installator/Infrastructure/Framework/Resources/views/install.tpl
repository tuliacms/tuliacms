{% extends '@cms/installator/layout/base.tpl' %}

{% set showFooter = false %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row installation-progress">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'installation'|trans({}, 'installator') }}</h1>
                        <p class="lead">{{ 'pleaseWaitUntilEnd'|trans({}, 'installator') }}</p>
                    </div>
                    <div class="installation-process">
                        <p class="installation-step-name">Instalacja bazy danych</p>
                        <div class="progress"><div class="progress-bar" role="progressbar" style="width:2%"></div></div>
                    </div>
                </div>
            </div>
            <div class="row installation-done d-none">
                <div class="col">
                    <div class="header">
                        <h1>{{ 'installationComplete'|trans({}, 'installator') }}</h1>
                        <p class="lead">{{ 'installationCompleteInformations'|trans({}, 'installator') }}</p>
                    </div>
                    <div class="text-center py-5">
                        <a href="" id="system-link-panel" class="btn btn-primary btn-icon-right"><i class="btn-icon fas fa-lock"></i> {{ 'administrationPanel'|trans({}, 'installator') }}</a>
                        <a href="" id="system-link-frontend" class="btn btn-primary btn-icon-right" target="_blank"><i class="btn-icon fas fa-angle-double-right"></i> {{ 'frontend'|trans({}, 'installator') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
{% endblock %}

{% block javascripts %}
    <script nonce="{{ csp_nonce() }}">
        let beginStep = function (callback) {
            $.ajax({
                url: '{{ path('installator.steps.prepare') }}',
                success: function () {
                    callback('adminAccount');
                }
            });
        };

        let adminAccountStep = function (callback) {
            $.ajax({
                url: '{{ path('installator.steps.admin_account') }}',
                success: function () {
                    callback('websiteConfigure');
                }
            });
        };

        let websiteConfigureStep = function (callback) {
            $.ajax({
                url: '{{ path('installator.steps.website') }}',
                success: function () {
                    callback('assetsInstallation');
                }
            });
        };

        let assetsInstallationStep = function (callback) {
            $.ajax({
                url: '{{ path('installator.steps.assets') }}',
                success: function () {
                    callback('finish');
                }
            });
        };

        let finishStep = function (callback) {
            $.ajax({
                url: '{{ path('installator.steps.finish') }}',
                success: function (data) {
                    callback(true, data);
                }
            });
        };

        let steps = {
            'begin': {
                name: '{{ 'installationStep.prepare'|trans({}, 'installator') }}',
                percentage: 10,
                callback: beginStep,
            },
            'adminAccount': {
                name: '{{ 'installationStep.creatingAdminAccount'|trans({}, 'installator') }}',
                percentage: 30,
                callback: adminAccountStep,
            },
            'websiteConfigure': {
                name: '{{ 'installationStep.creatingWebsite'|trans({}, 'installator') }}',
                percentage: 50,
                callback: websiteConfigureStep,
            },
            'assetsInstallation': {
                name: '{{ 'installationStep.publishingAssets'|trans({}, 'installator') }}',
                percentage: 70,
                callback: assetsInstallationStep,
            },
            'finish': {
                name: '{{ 'installationStep.finishingInstallation'|trans({}, 'installator') }}',
                percentage: 90,
                callback: finishStep,
            },
        };

        let runStep = function (step) {
            let name = $('.installation-process .installation-step-name');
            let progressbar = $('.installation-process .progress-bar');

            name.text(steps[step].name);
            progressbar.width(steps[step].percentage + '%');
            steps[step].callback(function (nextStep, payload) {
                if (nextStep === true) {
                    finishInstallation(payload);
                } else {
                    runStep(nextStep);
                }
            });
        };

        let finishInstallation = function (payload) {
            $('.installation-process .installation-step-name').text('{{ 'installationFinished'|trans({}, 'installator') }}');
            $('.installation-process .progress-bar').width('100%');

            $('.installation-progress').addClass('d-none');
            $('.installation-done').removeClass('d-none');

            $('#system-link-panel').attr('href', payload.website.panel_url);
            $('#system-link-frontend').attr('href', payload.website.frontend);
        };

        $(function () {
            runStep('begin');
        });
    </script>
{% endblock %}
