{% extends '@cms/installator/layout/base.tpl' %}

{% set showFooter = false %}

{% block body %}
    <div class="installator-wrapper">
        <div class="container-fluid installator-container">
            <div class="row">
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
        </div>
    </div>
{% endblock %}

{% block javascripts %}
    <script nonce="{{ csp_nonce() }}">
        let beginStep = function (callback) {
            $.ajax({
                url: '{{ path('installator.steps.prepare') }}',
                success: function () {
                    callback('databaseInstallation');
                }
            });
        };

        let databaseInstallationStep = function (callback) {
            setTimeout(function () {
                callback('userAccount');
            }, 1000);
        };

        let userAccountStep = function (callback) {
            setTimeout(function () {
                callback('websiteConfigure');
            }, 1000);
        };

        let websiteConfigureStep = function (callback) {
            setTimeout(function () {
                callback('assetsInstallation');
            }, 1000);
        };

        let assetsInstallationStep = function (callback) {
            setTimeout(function () {
                callback('finish');
            }, 1000);
        };

        let finishStep = function (callback) {
            setTimeout(function () {
                callback(true);
            }, 1000);
        };

        let steps = {
            'begin': {
                name: 'Przygotowywanie danych...',
                percentage: 10,
                callback: beginStep,
            },
            'databaseInstallation': {
                name: 'Instalacja bazy danych...',
                percentage: 30,
                callback: databaseInstallationStep,
            },
            'userAccount': {
                name: 'Tworzenie konta użytkownika...',
                percentage: 40,
                callback: userAccountStep,
            },
            'websiteConfigure': {
                name: 'Tworzenie konfiguracji witryny...',
                percentage: 50,
                callback: websiteConfigureStep,
            },
            'assetsInstallation': {
                name: 'Instalacja zasobów...',
                percentage: 70,
                callback: assetsInstallationStep,
            },
            'finish': {
                name: 'Finalizowanie instalacji...',
                percentage: 90,
                callback: finishStep,
            },
        };

        let runStep = function (step) {
            let name = $('.installation-process .installation-step-name');
            let progressbar = $('.installation-process .progress-bar');

            name.text(steps[step].name);
            progressbar.width(steps[step].percentage + '%');
            steps[step].callback(function (nextStep) {
                if (nextStep === true) {
                    finishInstallation();
                } else {
                    runStep(nextStep);
                }
            });
        };

        let finishInstallation = function () {
            $('.installation-process .installation-step-name').text('Instalacja zakończona');
            $('.installation-process .progress-bar').width('100%');
        };

        $(function () {
            runStep('begin');
        });
    </script>
{% endblock %}
