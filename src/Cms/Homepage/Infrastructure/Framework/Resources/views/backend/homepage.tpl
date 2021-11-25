{% extends 'backend' %}
{% assets [ 'masonry' ] %}

{% block title %}
    Dashboard
{% endblock %}

{% block breadcrumbs %}
    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
{% endblock %}

{% block content %}
    <div class="page-content">
        <div class="page">
            <div class="dashboard">
                {#<div class="dashboard-widgets loading">#}
                <div class="dashboard-widgets">
                    {{ dashboard_widgets('backend.dashboard') }}
                    {#
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="https://analytics.google.com/analytics/web/" target="_blank" rel="noopener noreferrer"><i class="fab fa-google dropdown-icon"></i> Otwórz Google Analytics</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-cogs dropdown-icon"></i> Ustawienia</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-chart-line"></i>
                                    <div class="pane-title">Statystyki odwiedzin</div>
                                </div>
                                <div class="pane-body p-0">
                                    <div class="statistics-widget">
                                        <div class="statistics-canvas-container">
                                            <div id="timeline-chart"></div>
                                            <div class="ct-chart ct-perfect-fourth"></div>
                                            <!-- <canvas id="myChart" width="400" height="120"></canvas> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="pane-footer py-1">
                                    <p class="text-muted mb-1"><small>Google Analytics by Tulia CMS</small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-cogs dropdown-icon"></i> Ustawienia</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-clock dropdown-icon"></i> Stwórz kopię teraz</a>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-archive dropdown-icon"></i> Otwórz listę kopii</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-archive"></i>
                                    <div class="pane-title">Kopia zapasowa</div>
                                </div>
                                <div class="pane-body">
                                    <div class="backup-widget">
                                        <p class="mb-0">Zaleca się wykonać kopię za: <b>2</b> dni</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#" data-toggle="modal" data-target="#widget-gestione-news-settings"><i class="fas fa-cogs dropdown-icon"></i> Ustawienia</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-newspaper"></i>
                                    <div class="pane-title">Aktualności</div>
                                </div>
                                <div class="pane-body">
                                    <div class="gestione-news-widget">
                                        <div class="news-item">
                                            <div class="news-date">2 maja, 2019</div>
                                            <a href="#">Lorem ipsum dolor sit amit, elit enim at minim veniam quis nostrud</a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">12 czerwca, 2019</div>
                                            <a href="#">Aiusmdd tempor incididunt ut labore et dolore magna elit </a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">30 października, 2019</div>
                                            <a href="#">Lorem ipsum veniam quis nostrud</a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">2 maja, 2019</div>
                                            <a href="#">Lorem ipsum dolor sit amit, consectetur eiusmdd tempor incididunt ut labore et dolore magna elit enim at minim veniam quis nostrud</a>
                                        </div>
                                        <div class="news-item">
                                            <div class="news-date">22 stycznia, 2019</div>
                                            <a href="#">Lorem ipsum dolor sit amit, consectetur eiusmdd tempor incididunt ut labore et dolore magna elit enim at minim veniam quis nostrud</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="pane-footer py-1">
                                    <p class="mb-1"><small><a href="#" class="text-muted">Blog Tulia CMS &nbsp; <i class="fas fa-external-link-square-alt"></i></a></small></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-comments"></i>
                                    <div class="pane-title">Ostatnie komentarze</div>
                                </div>
                                <div class="pane-body p-0">
                                    <div class="last-comments-widget">
                                        <div class="comments-list">
                                            <div class="comment-item">
                                                <div class="comment-short-info">
                                                    <span class="comment-author">Adam Banaszkiewicz</span>
                                                    - <span class="comment-date">2019-08-25 13:54</span>,
                                                    na stronie <a href="#">Lorem ipsum dolor sit amet...</a>
                                                </div>
                                                <div class="comment-content">
                                                    Vivamus elementum tortor justo, in hendrerit augue auctor quis. Sed tempor fermentum risus, in sagittis purus gravida vitae. Nulla vitae condimentum dui. Nunc eros ex, pulvinar a tempus sodales.
                                                </div>
                                            </div>
                                            <div class="comment-item">
                                                <div class="comment-short-info">
                                                    <span class="comment-author">Adam Banaszkiewicz</span>
                                                    - <span class="comment-date">2019-08-25 13:54</span>,
                                                    na stronie <a href="#">Morbi accumsan auctor ultricies</a>
                                                </div>
                                                <div class="comment-content">
                                                    Maecenas tempus posuere ante ac aliquam. Morbi accumsan auctor ultricies. Cras suscipit nisl ut dolor pharetra, vitae condimentum risus fermentum.
                                                </div>
                                            </div>
                                            <div class="comment-item">
                                                <div class="comment-short-info">
                                                    <span class="comment-author">Adam Banaszkiewicz</span>
                                                    - <span class="comment-date">2019-08-25 13:54</span>,
                                                    na stronie <a href="#">enean sit amet blandit nunc. Ut fringilla ipsum id enim posuere congue.</a>
                                                </div>
                                                <div class="comment-content">
                                                    Proin dolor nibh, sodales nec tellus vitae, aliquam iaculis elit. Etiam dapibus ut quam malesuada vehicula. Aenean sit amet blandit nunc. Ut fringilla ipsum id enim posuere congue. Cras condimentum lectus porta ligula maximus dictum.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="widget">
                        <div class="widget-inner">
                            <div class="pane">
                                <div class="pane-header">
                                    <div class="pane-buttons">
                                        <div class="dropdown">
                                            <button class="btn btn-icon-only" type="button" data-bs-toggle="dropdown">
                                                <i class="btn-icon fas fa-ellipsis-v"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-right">
                                                <a class="dropdown-item dropdown-item-with-icon" href="#"><i class="fas fa-eye-slash dropdown-icon"></i> Ukryj ten widget</a>
                                            </div>
                                        </div>
                                    </div>
                                    <i class="pane-header-icon fas fa-charging-station"></i>
                                    <div class="pane-title">Aktualizacje systemu</div>
                                </div>
                                <div class="pane-body">
                                    <div class="system-update-widget">
                                        <div class="status-icon"><i class="fas fa-check"></i></div>
                                        <p>System jest zaktualizowany do najnowszej wersji.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    #}
                </div>
            </div>
            <div class="modal fade" id="widget-gestione-news-settings" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Ustawienia wiadomości widgetu Tulia News</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <fieldset class="form-group">
                                <label>Wybierz język wiadomości</label>
                                <select class="form-control custom-select">
                                    <option value="">Polski</option>
                                    <option value="1">English</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Zamknij</button>
                            <button type="button" class="btn btn-primary">Zapisz</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script nonce="{{ csp_nonce() }}">
        $(function () {
            var masonry = $('.dashboard-widgets').masonry({
                itemSelector: '.widget',
                percentPosition: true
            });

            masonry.on('layoutComplete', function () {
                masonry.removeClass('loading');
            });

            var masonryLayoutTimeout = null;
            var masonryLayoutCall = function () {
                masonry.masonry('layout');
            };
            var masonryLayoutReset = function () {
                clearTimeout(masonryLayoutTimeout);
                masonryLayoutTimeout = setTimeout(masonryLayoutCall, 100);
            };
            masonryLayoutCall();
        });
    </script>
{% endblock %}
