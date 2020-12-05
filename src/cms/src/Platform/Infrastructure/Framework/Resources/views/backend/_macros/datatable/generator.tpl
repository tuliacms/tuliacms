{% macro generate(datatable, options) %}
    {% assets ['datatable_tulia'] %}

    {% set datatableId = 'tulia-datatable-' ~ uniqid() %}
    {% set front = datatable.generateFront({ actions_column: options.actions_column ?? true }) %}

    <div id="{{ datatableId }}"></div>

    <script nonce="{{ csp_nonce() }}">
        $(function () {
            new Tulia.DataTable('#{{ datatableId }}', {
                data_endpoint: '{{ options.data_endpoint ?? '#' }}',
                per_page_limit: {{ options.per_page_limit ?? 20 }},
                columns: {{ front.columns|json_encode|raw }},
                filters: {{ front.filters|json_encode|raw }},
                i18n: {
                    translations: {
                        'moreOptions': '{{ 'moreOptions'|trans }}',
                        'loadingDataInProgress': '{{ 'loadingDataInProgress'|trans }}',
                        'perPage': '{{ 'perPage'|trans }}',
                        'previous': '{{ 'previous'|trans }}',
                        'next': '{{ 'next'|trans }}',
                        'filter': '{{ 'doFilter'|trans }}',
                        'remove': '{{ 'remove'|trans }}',
                    }
                }
            });
        });
    </script>

    <style>
        .datatable-container {
            position: relative;
            min-height: 100px;
        }
        .datatable-container .dtbl-loader {
            position: absolute;
            z-index: 9000;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255,255,255,.5);
        }
        .datatable-container .dtbl-loader:hover {
            cursor: wait;
        }
        .datatable-container .dtbl-loader.hidden {
            display: none !important;
        }
        .datatable-container .dtbl-loader span {
            display: block;
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            padding: 10px 15px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 3px;
            text-align: center;
            box-shadow: 0 0 20px rgba(0,0,0,.1);
            pointer-events: none;
        }
        .datatable-container .dtbl-loader span i {
            font-size: 27px;
            margin-bottom: 6px;
        }
        .datatable-container .dtbl-column-sortable:hover {
            background-color: rgba(0,0,0,.07);
            cursor: pointer;
        }
        .datatable-container .dtbl-column-sortable .dtbl-columns-sortable-icon {
            opacity: .6;
            display: inline-block;
            padding-left: 7px;
        }
        .datatable-container .dtbl-filters .dtbl-filter-dropdown {
            display: inline-block;
            margin: 0 5px 5px 0;
        }
        .datatable-container .dtbl-filters .dtbl-filter-dropdown > .dropdown-menu {
            min-width: 250px;
        }
        .datatable-container .dtbl-filters .btn.btn-default.dropdown-toggle {
            border: 1px solid rgba(0,0,0,.1);
        }
        .datatable-container .dtbl-filters .btn.btn-default.dropdown-toggle:hover {
            border: 1px solid rgba(0,0,0,.2);
        }
        .datatable-container .dtbl-filters .btn.dropdown-toggle {
            padding: 0 12px;
            height: 32px;
            line-height: 30px;
        }
        .datatable-container .dtbl-filters .form-control {
            font-size: 12px;
            height: 29px;
            line-height: 29px;
        }
        .datatable-container .dtbl-filters .dtbl-filter-comparison-dropdown .btn {
            height: 29px;
            line-height: 30px;
        }
    </style>
{% endmacro %}
