{% extends '@backend/layout/root.tpl' %}

{% block beforeall %}
    {% assets [ 'customizer.back', 'backend.theme' ] %}
{% endblock %}

{% block body %}
    <div class="customizer" data-resolution="desktop" data-panel="show">
        <div class="side-panel">
            <div class="panel-headline">
                <div class="headline-btns">
                    <a href="{{ path('backend.theme.customize.left', { changeset: changeset.id, returnUrl: returnUrl }) }}" class="customizer-close" data-bs-toggle="tooltip" data-bs-placement="right" title="{{ 'close'|trans }}"><i class="fas fa-times"></i></a>
                    {% if locales()|length > 1 %}
                        {% set contentLocale = current_website().locale.code %}
                        <div class="dropdown d-inline" data-bs-toggle="tooltip" data-bs-placement="left" title="{{ 'changeLanguage'|trans }}">
                            <a class="btn btn-dark btn-sm btn-icon-only" href="#" data-bs-toggle="dropdown">
                                <i class="btn-icon fas fa-language"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right">
                                <h6 class="dropdown-header">{{ 'changeLanguage'|trans }}</h6>
                                {% for lang in locales() %}
                                    <a class="tulia-form-prevent-confirm dropdown-item{{ lang.code == contentLocale ? ' active' : '' }}" href="{{ path('backend.theme.customize', { theme: theme.name, _locale: lang.code, changeset: changeset.id }) }}">{{ 'languageName'|trans({ code: lang.code }, 'languages') }}</a>
                                {% endfor %}
                            </div>
                        </div>
                    {% endif %}
                    <a href="#" class="btn btn-primary btn-sm customizer-save disabled">{{ 'publish'|trans({}, 'customizer') }}</a>
                    <div class="dropdown d-inline">
                        <a class="btn btn-dark btn-sm btn-icon-only btn-customizer-more" href="#" data-bs-toggle="dropdown">
                            <i class="btn-icon fas fa-ellipsis-v"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <h6 class="dropdown-header">{{ 'layouts'|trans({}, 'customizer') }}</h6>
                            {#<a class="dropdown-item customizer-layouts-list dropdown-item-with-icon" href="#" data-bs-toggle="modal" data-target="#modal-customizer-layouts"><i class="dropdown-icon fas fa-folder-open"></i> {{ 'browseLayouts'|trans({}, 'customizer') }}</a>#}
                            <a class="dropdown-item customizer-layouts-save dropdown-item-with-icon" href="#"><i class="dropdown-icon fas fa-save"></i> {{ 'saveCurrentLayout'|trans({}, 'customizer') }}</a>
                            {% if theme.parent %}
                                <div class="dropdown-divider"></div>
                                <h6 class="dropdown-header">{{ 'tools'|trans({}, 'customizer') }}</h6>
                                <a class="dropdown-item dropdown-item-with-icon" href="#" data-bs-toggle="modal" data-target="#modal-customizer-copy-settings"><i class="dropdown-icon fas fa-copy"></i> {{ 'copySettingsFromParentTheme'|trans({}, 'customizer') }}</a>
                            {% endif %}
                            {#<a class="dropdown-item dropdown-item-with-icon" href="#" data-bs-toggle="modal" data-target="#modal-customizer-reset-settings"><i class="dropdown-icon fas fa-eraser"></i> {{ 'resetCustomizerSettings'|trans({}, 'customizer') }}</a>#}
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-controls">
                <form class="customizer-form">
                    {{ customizerView|raw }}
                </form>
                <div class="control-pane control-pane-name-home active" data-section="home">
                    <div class="home-control-pane-headline">
                        <p>{{ 'themeCustomize'|trans({}, 'customizer') }}</p>
                        <h4>{{ theme.name }}</h4>
                    </div>
                    <div class="controls-list">
                        {% for section in customizerView.structure %}
                            {% if section.parent is empty %}
                                <div class="control-trigger" data-show-pane="{{ section.code|str_replace('.', '_') }}">
                                    {{ section.label|trans({}, section.transationDomain) }}
                                </div>
                            {% endif %}
                        {% endfor %}
                        <div class="control-trigger" data-show-pane="layouts">
                            {{ 'browseLayouts'|trans({}, 'customizer') }}
                        </div>
                    </div>
                </div>
                <div class="control-pane control-pane-name-layouts" data-section="layouts">
                    <div class="control-pane-headline">
                        <button type="button" class="control-pane-back" data-show-pane="home">
                            <i class="icon fas fa-chevron-left"></i>
                        </button>
                        <h4>{{ 'browseLayouts'|trans({}, 'customizer') }}</h4>
                    </div>
                    <div class="control-pane-content">
                        {% for item in predefinedChangesets %}
                            <h5>{{ item.label|trans({}, item.translationDomain) }}</h5>
                            {% if item.description %}
                                <p>{{ item.description|trans({}, item.translationDomain) }}</p>
                            {% endif %}
                            <button type="button" class="btn btn-primary btn-sm customizer-predefined-changeset-apply" data-changeset-id="{{ item.id }}">{{ 'apply'|trans }}</button>
                            <hr />
                        {% endfor %}
                        <h5>{{ 'resetCustomizerSettings'|trans({}, 'customizer') }}</h5>
                        <button class="btn btn-primary btn-sm btn-icon-left" type="button" data-bs-toggle="modal" data-bs-target="#modal-customizer-reset-settings">
                            <i class="btn-icon fas fa-eraser"></i> {{ 'resetCustomizerSettings'|trans({}, 'customizer') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="customizer-loader"><i class="fas fa-circle-notch fa-spin"></i><span>Loading...</span></div>
        </div>
        <div class="preview">
            <div class="preview-panel">
                <div class="panel-toggle">
                    <button type="button" class="customizer-panel-toggle active" data-panel="hide"><i class="fas fa-chevron-left"></i> {{ 'hide'|trans }}</button>
                    <button type="button" class="customizer-panel-toggle" data-panel="show"><i class="fas fa-chevron-right"></i> {{ 'show'|trans }}</button>
                </div>
                <button type="button" class="btn customizer-resolution-change active" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ 'desktop'|trans }}" data-resolution="full"><i class="fas fa-desktop"></i></button>
                <button type="button" class="btn customizer-resolution-change" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ 'tablet'|trans }}" data-resolution="tablet"><i class="fas fa-tablet-alt"></i></button>
                <button type="button" class="btn customizer-resolution-change" data-bs-toggle="tooltip" data-bs-placement="bottom" title="{{ 'smartphone'|trans }}" data-resolution="mobile"><i class="fas fa-mobile-alt"></i></button>
            </div>
            <div class="preview-loader"><div class="loader-icon"><i class="fas fa-circle-notch fa-spin"></i></div></div>
            <iframe class="customizer-preview"></iframe>
        </div>
    </div>
    <div class="modal fade" id="modal-customizer-reset-settings" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ 'resetCustomizerSettings'|trans({}, 'customizer') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>{{ 'useThisOptionToResetCustomizerConfigurationToDefaultValues'|trans({}, 'customizer')|raw }}</p>
                </div>
                <div class="modal-footer">
                    <a href="{{ path('backend.theme.customize.reset', { theme: theme.name, _token: csrf_token('theme.customizer.reset') }) }}" class="btn btn-danger customizer-drastic-operation-link">{{ 'resetSettings'|trans({}, 'customizer') }}</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modal-customizer-copy-settings" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">{{ 'copySettingsFromParentTheme'|trans({}, 'customizer') }}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Jeśli posiadasz ustawienia w szablonie nadrzędnym, możesz je skopiować do twojego szablonu potomnego i kontynuować konfigurację.</p>
                    <p>Pamiętaj jednak, że skopiowanie nadpisze wszystkie ustawienia, które dotychczas masz ustawione, <b>tej operacji nie można cofnąć!</b></p>
                </div>
                <div class="modal-footer">
                    <a href="{{ path('backend.theme.customize.copy_changeset_from_parent', { theme: theme.name, _token: csrf_token('theme.customizer.copy_changeset_from_parent') }) }}" class="btn btn-danger customizer-drastic-operation-link">{{ 'copySettings'|trans({}, 'customizer') }}</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ 'close'|trans }}</button>
                </div>
            </div>
        </div>
    </div>
    {% set predefinedChangesetsJson = {} %}
    {% for item in predefinedChangesets %}
        {% set predefinedChangesetsJson = predefinedChangesetsJson|merge({ (item.id): item.data }) %}
    {% endfor %}
    <script nonce="{{ csp_nonce() }}">
        let customizer = null;

        $(function () {
            let options = {
                paths: {
                    preview: '{{ previewUrl|raw }}',
                    save: '{{ path('backend.theme.customize.save', { changeset: changeset.id, theme: theme.name, _token: csrf_token('theme.customizer.save') })|raw }}'
                },
                changeset: '{{ changeset.id }}',
                predefinedChangesets: {{ predefinedChangesetsJson|json_encode|raw }},
                theme: '{{ theme.name }}',
                changed: {{ changeset.isEmpty ? 'false' : 'true' }},
                translations: {
                    cancelChangesQuestion: '{{ 'cancelChangesQuestion'|trans({}, 'customizer') }}',
                    areYouSureToCancelChanges: '{{ 'areYouSureToCancelChanges'|trans({}, 'customizer') }}',
                    saveChangesQuestion: '{{ 'saveChangesQuestion'|trans({}, 'customizer') }}',
                    areYouSureToSaveChanges: '{{ 'areYouSureToSaveChanges'|trans({}, 'customizer') }}',
                    yes: '{{ 'yes'|trans }}',
                    no: '{{ 'no'|trans }}',
                    multilingual: '{{ 'multilingual'|trans }}',
                    multilingualDescription: '{{ 'multilingualDescription'|trans({}, 'customizer') }}',
                    areYouSure: '{{ 'areYouSure'|trans }}',
                    thisOperationCannotBeUndone: '{{ 'thisOperationCannotBeUndone'|trans }}',
                },
            };

            customizer = new Customizer(options);
            customizer.init();
        });
    </script>
{% endblock %}
