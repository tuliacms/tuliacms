<footer>
    <div class="cms-status">
        <i class="status-icon fas fa-stopwatch"></i>
        <span class="status-text">Zapisano automatycznie szkic 2 minuty temu.</span>
    </div>
    <div class="cms-version"><a href="#" target="_blank" title="Tulia CMS" rel="noopener">Tulia CMS {{ constant('Tulia\\Cms\\Platform\\Version::VERSION') }}</a></div>
</footer>

<div class="search-anything-container" id="search-anything">
    <div class="search-head">
        <div class="closer"></div>
        <div class="search-input">
            <input type="text" placeholder="{{ 'startTyping'|trans }}" class="query" />
        </div>
    </div>
    <div class="search-body">
        <div class="search-info">
            <div class="pane pane-lead">
                <div class="pane-header">
                    <i class="pane-header-icon fas fa-search"></i>
                    <h1 class="pane-title">{{ 'searchAnything'|trans }}</h1>
                </div>
                <div class="pane-body">
                    <div class="search-info-wrapper">
                        <div class="hl">{{ 'searchInWholeAdmin'|trans }}</div>
                        <div class="search-in-list">
                            <ul>
                                <li><i class="icn fas fa-file-powerpoint"></i> {{ 'searchInContents'|trans }}</li>
                                <li><i class="icn fas fa-folder-open"></i> {{ 'searchInTaxonomies'|trans }}</li>
                                <li><i class="icn fas fa-cogs"></i> {{ 'searchInSettings'|trans }}</li>
                                <li><i class="icn fas fa-tools"></i> {{ 'searchInTools'|trans }}</li>
                                <li><i class="icn fas fa-dice-d6"></i> {{ 'searchInSystem'|trans }}</li>
                                <li><i class="icn fas fa-question-circle"></i> {{ 'searchInHelp'|trans }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="search-results-wrapper d-none">
            <div class="pane pane-lead">
                <div class="pane-header">
                    <i class="pane-header-icon tsa-loading-show fas fa-circle-notch fa-spin d-none"></i>
                    <i class="pane-header-icon tsa-loading-hide fas fa-search"></i>
                    <h1 class="pane-title">{{ 'searchResultsForQuery'|trans({ query: '<span class="tsa-query-preview"></span>' })|raw }}</h1>
                </div>
                <div class="pane-body">
                    <div class="search-results"></div>
                    <div class="search-loader">
                        {{ 'searchingInProgress'|trans({ query: '<b><span class="tsa-query-preview"></span></b>' })|raw }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
