Tulia.DataTable = class {
    constructor (selector, options) {
        this.options = options;

        let root = $(selector);
        root.addClass('datatable-container');

        this.container = new Tulia.DataTable.Container(root, this.options);

        this.init();
    }

    init () {
        this.container.get('event_dispatcher').dispatch('app.init');
        this.container.get('view').init();
        this.container.get('repository').fetch();
        this.container.get('event_dispatcher').dispatch('app.ready');
    }
};

Tulia.DataTable.View = class {
    constructor (eventDispatcher, root) {
        this.eventDispatcher = eventDispatcher;
        this.root = root;
    }

    init () {
        this.root.append('<div class="pane-body">\
                <div class="pages-list-header dtbl-filters"></div>\
            </div>\
            <div class="dtbl-table"></div>\
            <div class="pane-footer dtbl-footer">\
                <div class="pages-list-bottom">\
                    <div class="actions-box"></div>\
                    <div class="pagination-box"></div>\
                </div>\
            </div>'
        );

        this.eventDispatcher.dispatch('view.ready');
    }
};

Tulia.DataTable.View.Loader = class {
    constructor(translator, root) {
        this.root = root;
        this.translator = translator;

        this.init();
    }

    init () {
        this.loader = $('<div class="dtbl-loader"><span><i class="fas fa-circle-notch fa-spin"></i><br />' + this.translator.get('loadingDataInProgress') + '</span></div>');
        this.root.append(this.loader);
    }

    show () {
        this.loader.removeClass('hidden');
    }

    hide () {
        this.loader.addClass('hidden');
    }
};

Tulia.DataTable.View.Filters = class {
    constructor (eventDispatcher, repository, translator, root, options) {
        this.eventDispatcher = eventDispatcher;
        this.repository = repository;
        this.translator = translator;
        this.root = root;
        this.options = options;
        this.filters = {};

        this.filtersRenderer = new Tulia.DataTable.View.Filters.FilterTypeRenderer();

        let self = this;

        this.eventDispatcher.on('view.ready', function () {
            self.init();
        });
    }

    init () {
        let self = this;

        this.filtersRoot = this.root.find('.dtbl-filters');

        for (let name in this.options.filters) {
            this.renderFilter(name, this.options.filters[name]);
        }

        this.filtersRoot.on('click', '[data-apply-filter]', function () {
            self.applyFilter(
                $(this).closest('.dropdown-menu').find('.dtbl-filter').attr('data-filter-name')
            );

            self.closeFilters();
        });
        this.filtersRoot.on('click', '[data-remove-filter]', function () {
            self.removeFilter(
                $(this).closest('.dropdown-menu').find('.dtbl-filter').attr('data-filter-name')
            );

            self.closeFilters();
        });
        this.filtersRoot.on('click', '.dtbl-filter-comparison-dropdown > a', function (e) {
            e.stopPropagation();
            e.preventDefault();

            $(this).next('.dropdown-menu').toggleClass('show');
        });
        this.filtersRoot.on('click', '.dtbl-filter-comparison-dropdown [data-filter-comparison]', function (e) {
            e.stopPropagation();
            e.preventDefault();

            self.changeFilterComparison(
                $(this).closest('.dtbl-filter-dropdown').find('.dtbl-filter').attr('data-filter-name'),
                $(this).attr('data-filter-comparison')
            );

            $(this).closest('.dropdown-menu').removeClass('show');
        });
        this.filtersRoot.on('keydown', '[data-filter-autofocus]', function (e) {
            let enterKey = 13;

            if (e.which === enterKey) {
                self.applyFilter(
                    $(this).closest('.dtbl-filter').attr('data-filter-name')
                );

                self.closeFilters();
            }
        });
        this.filtersRoot.on('click', '.dtbl-filter-dropdown > .dropdown-toggle', function () {
            self.openFilter($(this).parent().attr('data-filter-name'));
        });
        /**
         * Prevents close dropdown when click inside the dropdown menu.
         */
        this.filtersRoot.on('click', '.dtbl-filter-dropdown', function (e) {
            e.stopPropagation();
        });
        /**
         * Closes dropdowns when click enywhere on the page.
         */
        $('body').click(function () {
            self.closeFilters();
        });
    }

    renderFilter (name, options) {
        let item = $('<div class="dropdown dtbl-filter-dropdown" data-filter-name="' + name + '" data-filter-comparison="HAS">\
            <button class="btn btn-default dropdown-toggle" type="button">\
                ' + options.label + '<span class="dtbl-filter-value-preview"></span>\
            </button>\
            <div class="dropdown-menu">\
                <div class="dtbl-filter px-2 py-1" data-filter-name="' + name + '" data-filter-comparison="HAS"></div>\
            </div>\
        </div>');

        this.filters[name] = this.filtersRenderer.renderFilter(name, options, item.find('.dtbl-filter'));

        if (! this.filters[name]) {
            return;
        }

        this.filters[name].render();

        item.find('.dropdown-menu').append('<div class="dropdown-divider"></div>\
            <div class="px-2 py-1">\
                <button type="submit" class="btn btn-primary btn-sm" data-apply-filter>' + this.translator.get('filter') + '</button>\
                <button type="submit" class="btn btn-primary btn-sm" data-remove-filter>' + this.translator.get('remove') + '</button>\
            </div>');

        item.find('.dtbl-filter-comparison-selector').append('<div class="dtbl-filter-comparison-dropdown">\
                <a href="#" class="btn btn-default">==</a>\
                <div class="dropdown-menu"></div>\
            </div>');

        let filtersDropdown = item.find('.dtbl-filter-comparison-dropdown .dropdown-menu');
        let availableFilters = this.filters[name].getAvailableComparisons();

        for (let name in availableFilters) {
            filtersDropdown.append('<a class="dropdown-item" href="#" data-filter-comparison="' + name +'">' + availableFilters[name].label + ' [' + availableFilters[name].code + ']</a>');
        }

        this.filtersRoot.append(item);
    }

    closeFilters (not) {
        let dropdowns = this.filtersRoot.find('.dtbl-filter-dropdown > .dropdown-menu');

        if (not) {
            dropdowns.not(not);
        }

        dropdowns.removeClass('show');
    }

    openFilter (name) {
        let dropdown = this.filtersRoot.find('.dtbl-filter-dropdown[data-filter-name=' + name + '] > .dropdown-menu');

        this.closeFilters(dropdown);

        dropdown.toggleClass('show');
        dropdown.find('[data-filter-autofocus]').trigger('focus').trigger('select');
    }

    applyFilter (name) {
        let filter = this.filters[name];

        this.repository.applyFilter(name, filter.getValue(), filter.getComparison());
        this.repository.fetch();

        filter.updatePreview(filter.getValue());
    }

    removeFilter (name) {
        let filter = this.filters[name];

        this.repository.applyFilter(name, null);
        this.repository.fetch();

        filter.setValue(null);
        filter.updatePreview(null);
    }

    changeFilterComparison (name, comparison) {
        let filter = this.filters[name];

        filter.changeComparison(comparison);
    }
};

Tulia.DataTable.View.Filters.FilterTypeRenderer = class {
    renderFilter(name, options, root) {
        if (options.type === 'text') {
            return new Tulia.DataTable.View.Filters.Text(name, options, root);
        }
        if (options.type === 'yes_no') {
            return new Tulia.DataTable.View.Filters.YesNo(name, options, root);
        }
        if (options.type === 'single_select') {
            return new Tulia.DataTable.View.Filters.SingleSelect(name, options, root);
        }

        return false;
    }
};

Tulia.DataTable.View.Filters.Filter = class {
    constructor(name, options, root) {
        this.name = name;
        this.options = options;
        this.root = root;
        this.comparison = 'HAS';
        this.availableComparisons = {
            HAS: { label: 'Contains', code: '==' },
            EQUAL: { label: 'Equal', code: '===' },
            LESS: { label: 'Less than', code: '<' },
            LESS_EQUAL: { label: 'Less or equal than', code: '<=' },
            MORE: { label: 'More than', code: '>' },
            MORE_EQUAL: { label: 'More or equal than', code: '>=' }
        };
    }

    render () {
    }

    getValue () {
    }

    setValue (value) {
    }

    getAvailableComparisons () {
        return this.availableComparisons;
    }

    getComparison () {
        return this.comparison;
    }

    changeComparison (comparison) {
        this.comparison = comparison;

        this.root.find('.dtbl-filter-comparison-dropdown > a').text(this.availableComparisons[comparison].code);
    }

    updatePreview (value) {
        let button = this.root.closest('.dtbl-filter-dropdown').find('button.dropdown-toggle');
        let preview = button.find('.dtbl-filter-value-preview');

        if (value) {
            button.addClass('btn-primary').removeClass('btn-default');
            preview.text(' ' + this.availableComparisons[this.comparison].code + ' ' + value);
        } else {
            button.addClass('btn-default').removeClass('btn-primary');
            preview.empty();
        }
    }
};

Tulia.DataTable.View.Filters.Filter = class {
    constructor(name, options, root) {
        this.name = name;
        this.options = options;
        this.root = root;
        this.comparison = 'HAS';
        this.availableComparisons = {
            HAS: { label: 'Contains', code: '==' },
            EQUAL: { label: 'Equal', code: '===' },
            LESS: { label: 'Less than', code: '<' },
            LESS_EQUAL: { label: 'Less or equal than', code: '<=' },
            MORE: { label: 'More than', code: '>' },
            MORE_EQUAL: { label: 'More or equal than', code: '>=' }
        };
    }

    render () {
    }

    getValue () {
    }

    setValue (value) {
    }

    getValuePreview (value) {
        return value;
    }

    getAvailableComparisons () {
        return this.availableComparisons;
    }

    getComparison () {
        return this.comparison;
    }

    changeComparison (comparison) {
        this.comparison = comparison;

        this.root.find('.dtbl-filter-comparison-dropdown > a').text(this.availableComparisons[comparison].code);
    }

    updatePreview (value) {
        let button = this.root.closest('.dtbl-filter-dropdown').find('button.dropdown-toggle');
        let preview = button.find('.dtbl-filter-value-preview');

        if (value) {
            button.addClass('btn-primary').removeClass('btn-default');
            preview.text(' ' + this.availableComparisons[this.comparison].code + ' ' + this.getValuePreview(value));
        } else {
            button.addClass('btn-default').removeClass('btn-primary');
            preview.empty();
        }
    }
};

Tulia.DataTable.View.Filters.Text = class extends Tulia.DataTable.View.Filters.Filter {
    constructor(name, options, root) {
        super(name, options, root);
    }

    render () {
        this.root.append('<div class="input-group">\
            <div class="input-group-prepend dtbl-filter-comparison-selector"></div>\
            <input type="text" class="form-control" data-filter-autofocus />\
        </div>');
        this.element = this.root.find('input');
    }

    getValue () {
        return this.element.val();
    }

    setValue (value) {
        this.element.val(value);
    }
};

Tulia.DataTable.View.Filters.Select = class extends Tulia.DataTable.View.Filters.Filter {
    constructor(name, options, root) {
        super(name, options, root);
    }

    render () {
        this.root.append('<select class="form-control">\
            <option value="">--- select ---</option>\
        </select>');

        this.element = this.root.find('select');

        for (let key in this.options.choices) {
            this.element.append(`<option value="${key}">${this.options.choices[key]}</option>`);
        }
    }

    getValue () {
        return this.element.val();
    }

    setValue (value) {
        this.element.val();
    }

    getValuePreview (value) {
        return this.options.choices[value] ?? value;
    }
};

Tulia.DataTable.View.Filters.YesNo = class extends Tulia.DataTable.View.Filters.Select {
    constructor(name, options, root) {
        super(name, options, root);
    }
};

Tulia.DataTable.View.Filters.SingleSelect = class extends Tulia.DataTable.View.Filters.Select {
    constructor(name, options, root) {
        super(name, options, root);
    }
};

Tulia.DataTable.View.Pagination = class {
    constructor (eventDispatcher, translator, repository, root, options) {
        this.eventDispatcher = eventDispatcher;
        this.translator = translator;
        this.repository = repository;
        this.root = root;
        this.maxPages = 0;

        let self = this;

        if (options.pagination === true) {
            this.eventDispatcher.on('view.ready', function () {
                self.init();
            });
            this.eventDispatcher.on('repository.data', function (data) {
                self.refreshPagination(data.meta);
            });
        } else {
            this.repository.setLimit(null);
        }
    }

    init () {
        this.pagination = this.root.find('.pagination-box');
        this.pagination.html('<div class="dropdown d-inline-block">\n' +
            '    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">\n' +
            '        ' + this.translator.get('perPage') + ': <span class="dtbl-per-page">20</span>\n' +
            '    </button>\n' +
            '    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">\n' +
            '        <a class="dropdown-item" href="#" data-per-page="10">10</a>\n' +
            '        <a class="dropdown-item" href="#" data-per-page="20">20</a>\n' +
            '        <a class="dropdown-item" href="#" data-per-page="50">50</a>\n' +
            '        <a class="dropdown-item" href="#" data-per-page="100">100</a>\n' +
            '    </div>\n' +
            '</div> ' +
            '<nav class="d-inline-block">\n' +
            '    <ul class="pagination justify-content-end"></ul>\n' +
            '</nav>');

        let self = this;

        this.pagination.on('click', '[data-page]', function (e) {
            e.preventDefault();
            self.openPage($(this).attr('data-page'));
        });

        this.pagination.on('click', '[data-prev-page]', function (e) {
            e.preventDefault();
            self.openPreviousPage();
        });

        this.pagination.on('click', '[data-next-page]', function (e) {
            e.preventDefault();
            self.openNextPage();
        });

        this.pagination.on('click', '[data-per-page]', function (e) {
            e.preventDefault();
            self.changeLimit($(this).attr('data-per-page'));
        });
    }

    refreshPagination (meta) {
        this.calculateMaxPagesCount(meta);

        this.pagination.find('.dtbl-per-page').html(meta.limit);

        let root = this.pagination.find('.pagination');
        root.empty();

        for (let i = 1; i <= this.maxPages; i++) {
            root.append('<li class="page-item"><a class="page-link" href="#" data-page="' + i + '">' + i + '</a></li>');
        }

        root.prepend('<li class="page-item"><a class="page-link" href="#" data-prev-page>' + this.translator.get('previous') + '</a></li>');
        root.append('<li class="page-item"><a class="page-link" href="#" data-next-page>' + this.translator.get('next') + '</a></li>');

        root.find('[data-page=' + meta.page + ']').parent().addClass('active');

        if (meta.page === 1) {
            root.find('[data-prev-page]').parent().addClass('disabled');
        }
        if (meta.page === this.maxPages) {
            root.find('[data-next-page]').parent().addClass('disabled');
        }
    }

    calculateMaxPagesCount (meta) {
        this.maxPages = Math.ceil(meta.total_rows / meta.limit);
    }

    openPage (page) {
        // Do not fetch when new and current page are the same.
        if (page == this.repository.page) {
            return;
        }

        this.repository.setPage(page);
        this.repository.fetch();
    }

    openPreviousPage () {
        let page = this.repository.page - 1;

        this.repository.setPage(page <= 0 ? 1 : page);
        this.repository.fetch();
    }

    openNextPage () {
        let page = this.repository.page + 1;

        this.repository.setPage(page >= this.maxPages ? this.maxPages : page);
        this.repository.fetch();
    }

    changeLimit (limit) {
        // Do not fetch when new and current limit are the same.
        if (limit == this.repository.limit) {
            return;
        }

        this.repository.setPage(1);
        this.repository.setLimit(limit);
        this.repository.fetch();
    }
};

Tulia.DataTable.View.Datatable = class {
    constructor (eventDispatcher, translator, root, options) {
        this.eventDispatcher = eventDispatcher;
        this.options = options;
        this.root = root;
        this.table = null;
        this.columnsRenderer = new Tulia.DataTable.View.Datatable.ColumnTypeDataRenderer(translator);

        let self = this;

        this.eventDispatcher.on('view.ready', function () {
            self.init();
        });
        this.eventDispatcher.on('repository.data', function (data) {
            self.refresh(data.data);
        });
    }

    init () {
        let self = this;

        this.table = $('<table class="table pages-list"><thead></thead><tbody></tbody></table>');

        this.renderHead();

        this.root.find('.dtbl-table').append(this.table);

        this.table.find('.dtbl-column-sortable').click(function (e) {
            e.preventDefault();
            self.eventDispatcher.dispatch('view.datatable.sortable', $(this).attr('data-dtbl-sortable-column'));
        });

        this.eventDispatcher.on('sortable.change', function (sortBy, sortDir) {
            self.updateSorting(sortBy, sortDir);
        });
    }

    renderHead () {
        let tr = this.table.find('thead').append('<tr />');
        let columns = this.options.columns;

        for (let name in columns) {
            let type = columns[name].type ?? 'text';
            let htmlAttrs = columns[name].html_attr ?? {};

            htmlAttrs.class = htmlAttrs.class ?? '';

            let col = '';

            if (type === 'actions') {
                col = `<th >${columns[name].label}</th>`;
                htmlAttrs.class += ' col-actions';
            } else if (type === 'uuid') {
                col = `<th>${columns[name].label}</th>`;
                htmlAttrs.class += ' text-center col-uuid';
            } else {
                col = `<th>${columns[name].label}</th>`;
            }

            col = $(col);

            this.applyAttributes(col, htmlAttrs);

            if (columns[name].sortable ?? false) {
                col
                    .addClass('dtbl-column-sortable')
                    .append('<span class="dtbl-columns-sortable-icon"><i class="fas fa-sort"></i></span>')
                    .attr('data-dtbl-sortable-column', name)
                ;
            }

            tr.append(col);
        }
    }

    updateSorting (sortBy, sortDir) {
        this.table.find('.dtbl-columns-sortable-icon').html('<i class="fas fa-sort"></i>');

        if (sortBy) {
            let column = this.table.find('[data-dtbl-sortable-column=' + sortBy + ']');

            column.find('.dtbl-columns-sortable-icon').html(
                sortDir === 'asc'
                    ? '<i class="fas fa-sort-down"></i>'
                    : '<i class="fas fa-sort-up"></i>'
            );
        }
    }

    refresh (data) {
        let body = this.table.find('tbody');
        let columns = this.options.columns;

        body.empty();

        for (let i in data) {
            let row = data[i];
            let tr = $('<tr></tr>');

            for (let name in columns) {
                let type = columns[name].type ?? 'text';
                let col = this.generateColumn(type);

                if (row[name]) {
                    let value = row[name];

                    this.applyValueClass(col, columns[name].value_class ?? {}, value);

                    value = this.translateValue(columns[name].value_translation ?? {}, value);

                    col.html(this.columnsRenderer.render(type, value));
                } else {
                    col.html('<!-- Missing data for this column -->');
                }

                this.applyAttributes(col, columns[name].html_attr ?? {});

                col.attr('data-label', columns[name].label);

                tr.append(col);
            }

            body.append(tr);
        }

        body.find('[data-toggle=tooltip]').tooltip();
    }

    applyAttributes (elm, attrs) {
        for (name in attrs) {
            if (name === 'class') {
                elm.addClass(attrs[name]);
            } else {
                elm.attr(name, attrs[name]);
            }
        }
    }

    generateColumn (type) {
        let col = $('<td></td>');

        if (type === 'actions') {
            col = $('<td class="col-actions"></td>');
        } else if (type === 'uuid') {
            col = $('<td class="text-center col-uuid"></td>');
        }

        return col;
    }

    translateValue (translations, value) {
        if (translations && translations[value]) {
            return translations[value];
        }

        return value;
    }

    applyValueClass (elm, classes, value) {
        if (classes) {
            if (classes[value]) {
                elm.addClass(classes[value]);
            }
        }
    }
};

Tulia.DataTable.View.Datatable.ColumnTypeDataRenderer = class {
    constructor(translator) {
        this.translator = translator;
    }

    render (type, data) {
        if (type === 'text') {
            return this.renderText(data);
        } else if (type === 'uuid') {
            return this.renderUuid(data);
        } else if (type === 'actions') {
            return this.renderActions(data);
        }

        return '<!-- Unsupported column type -->';
    }

    renderUuid (data) {
        let shortened = data.substring(0, 8);
        return '<span class="short-uuid-shower" data-copy-dblclick="' + data + '" data-toggle="tooltip" title="Doubleclick to copy">' + shortened + '</span>';
    }

    renderText (data) {
        return data;
    }

    renderActions (data) {
        if (data.length === 0) {
            return;
        }

        let actions = $('<div class="actions-box">\
            <div class="btn-group">\
                <div class="btn-group" role="group">\
                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown"></button>\
                    <div class="dropdown-menu dropdown-menu-right">\
                        <h6 class="dropdown-header">' + this.translator.get('moreOptions') + '</h6>\
                        <div class="dropdown-divider"></div>\
                    </div>\
                </div>\
            </div>');
        let dropdown = actions.find('.dropdown-menu');

        for (let i in data) {
            if (i === 'main') {
                continue;
            }

            dropdown.append(
                $(data[i]).addClass('dropdown-item')
            );
        }

        if (data.main) {
            actions.find('> .btn-group').prepend(
                $(data.main).addClass('btn-main-action')
            );
        }

        return actions;
    }
};

Tulia.DataTable.View.Actions = class {
    constructor (eventDispatcher, root) {
        this.eventDispatcher = eventDispatcher;
        this.root = root;
    }

    init () {
        let self = this;

        this.root.on('click', '[data-component=action]', function (e) {
            e.preventDefault();
            let settings = self.parseActionSettings($(this).attr('data-settings'));

            self.eventDispatcher.dispatch('view.action.' + settings.action, settings, $(this));
        });
    }

    parseActionSettings (data) {
        return JSON.parse(
            data.replace(/'/g, '"')
        );
    }
};

Tulia.DataTable.View.Actions.Delete = class {
    performAction (settings) {
        let self = this;

        Tulia.Confirmation.warning().then(function (v) {
            if (! v.value) {
                return;
            }

            Tulia.PageLoader.show();
            self.createForm(settings).submit();
        });
    }

    createForm (settings) {
        let form = $('<form>');

        form
            .append(this.createFields(settings.data))
            .append('<input type="hidden" name="_token" value="' + settings.csrf_token + '" />')
            .attr('method', 'POST')
            .attr('action', settings.url)
            .appendTo('body');

        return form;
    }

    createFields (data) {
        let fields = $('<div>');

        for (let name in data) {
            if (Array.isArray(data[name])) {
                for (let i in data[name]) {
                    fields.append('<input type="hidden" name="' + name + '[]" value="' + data[name][i] + '" />');
                }
            } else {
                fields.append('<input type="hidden" name="' + name + '" value="' + data[name] + '" />');
            }
        }

        return fields;
    }
};

Tulia.DataTable.Sortable = class {
    constructor(eventDispatcher, repository) {
        this.repository = repository;
        this.eventDispatcher = eventDispatcher;

        let self = this;

        this.eventDispatcher.on('view.datatable.sortable', function (column) {
            self.changeSorting(column);
        });
    }

    changeSorting (column) {
        let sortBy = column;
        let sortDir = this.repository.sortDir === 'asc' ? 'desc' : 'asc';

        if (this.repository.sortBy !== column) {
            sortDir = 'asc';
        } else {
            /**
             * First click - sort ASC
             * Second click - sort DESC
             * Third click (this case) - reset sorting.
             */
            if (this.repository.sortDir === 'desc') {
                sortBy = null;
                sortDir = null;
            }
        }

        this.repository.setSort(sortBy, sortDir);

        this.eventDispatcher.dispatch('sortable.change', sortBy, sortDir);

        this.repository.fetch();
    }
};

Tulia.DataTable.Repository = class {
    constructor(eventDispatcher, loader, options) {
        this.eventDispatcher = eventDispatcher;
        this.loader = loader;
        this.endpoint = options.data_endpoint;

        this.page = 1;
        this.limit = options.per_page_limit;
        this.sortBy = null;
        this.sortDir = null;
        this.filters = {};

        let self = this;

        this.eventDispatcher.on('repository.data', function (data) {
            self.limit = data.meta.limit;
        });
    }

    fetch () {
        let self = this;

        this.loader.show();

        $.ajax({
            type: 'GET',
            url: this.endpoint,
            data: {
                filter: this.filters,
                sort_by: this.sortBy,
                sort_dir: this.sortDir,
                page: this.page,
                limit: this.limit
            },
            dataType: 'json',
            success: function (data) {
                self.eventDispatcher.dispatch('repository.data', data);
                self.loader.hide();
            }
        });
    }

    setPage (page) {
        this.page = parseInt(page);
    }

    setLimit (limit) {
        this.limit = limit === null ? null : parseInt(limit);
    }

    setSort (sortBy, sortDir) {
        this.sortBy = sortBy;
        this.sortDir = sortDir;
    }

    applyFilter (name, value, comparison) {
        if (! value) {
            delete this.filters[name];
        } else {
            this.filters[name] = {
                value: value,
                comparison: comparison
            };
        }
    }
};

Tulia.DataTable.Container = class {
    constructor (root, options) {
        this.services = [];

        let eventDispatcher = new Tulia.DataTable.EventDispatcher();
        let translator = new Tulia.DataTable.Translator(options);
        let view = new Tulia.DataTable.View(eventDispatcher, root);
        let loader = new Tulia.DataTable.View.Loader(translator, root);
        let repository = new Tulia.DataTable.Repository(eventDispatcher, loader, options);
        let sortable = new Tulia.DataTable.Sortable(eventDispatcher, repository);
        let datatable = new Tulia.DataTable.View.Datatable(eventDispatcher, translator, root, options);
        let pagination = new Tulia.DataTable.View.Pagination(eventDispatcher, translator, repository, root, options);
        let filters = new Tulia.DataTable.View.Filters(eventDispatcher, repository, translator, root, options);
        let actions = new Tulia.DataTable.View.Actions(eventDispatcher, root);
        let deleteActions = new Tulia.DataTable.View.Actions.Delete();

        this.set('options', options);
        this.set('root', root);
        this.set('event_dispatcher', eventDispatcher);
        this.set('translator', translator);
        this.set('view', view);
        this.set('view.datatable', datatable);
        this.set('view.loader', loader);
        this.set('view.pagination', pagination);
        this.set('view.filters', filters);
        this.set('view.actions', actions);
        this.set('view.actions.delete', deleteActions);
        this.set('repository', repository);
        this.set('sortable', sortable);

        eventDispatcher.on('view.ready', function (...args) { actions.init(...args) });
        eventDispatcher.on('view.action.delete', function (...args) { deleteActions.performAction(...args) });
    }

    get (name) {
        if (this.services[name]) {
            return this.services[name];
        }

        throw new Error(`Service named '${name}' not exists in Container.`);
    }

    set (name, obj) {
        this.services[name] = obj;
    }
};

Tulia.DataTable.Translator = class {
    constructor (options) {
        this.translations = options.i18n.translations;
    }

    get (name) {
        return this.translations[name] ?? name;
    }
};

Tulia.DataTable.EventDispatcher = class {
    constructor() {
        this.events = [];
    }

    on (events, listener, priority) {
        events = events.split(',');
        priority = priority || 100;

        for (let i = 0; i < events.length; i++) {
            let name = events[i].trim();

            if (this.events[name]) {
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            } else {
                this.events[name] = [];
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            }

            this.events[name].sort(function (a, b) {
                return b.priority - a.priority;
            });
        }

        return this;
    }

    dispatch (name, ...args) {
        if (! this.events[name]) {
            return this;
        }

        args = args || [];

        let self = this;

        for (let i = 0; i < this.events[name].length; i++) {
            this.events[name][i].listener.apply(self, args);
        }

        return this;
    }
};
