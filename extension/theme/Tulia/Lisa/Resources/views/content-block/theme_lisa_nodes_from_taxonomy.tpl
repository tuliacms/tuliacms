<div class="block block-nodes">
    <div class="container-xxl">
        <div class="row">
            <div class="col">
                <a href="{{ term_path(taxonomy|default) }}" class="btn btn-primary btn-lg btn-icon-right block-news-read-more-btn d-none d-md-block">
                    <span>{{ taxonomy_readmore|default('readMore'|trans({}, 'lisa-theme')) }}</span>
                    <i class="btn-icon {{ taxonomy_icon|default('fas fa-chevron-right') }}"></i>
                </a>
                <p class="lead">{{ intro|default|raw }}</p>
                <h2>{{ headline|default|raw }}</h2>
            </div>
        </div>
        {% set nodes = find_nodes({
            category : taxonomy|default(0),
            page     : 1,
            per_page : number_of_nodes|default(3)
        }) %}
        <div class="row">
            {% for node in nodes %}
                <div class="col-12 col-md-4">
                    <a href="{{ node_path(node) }}" class="block-node-item">
                        <div class="block-node-image">
                            {% if node.thumbnail %}
                                {{ image(node.thumbnail, { size: 'node-thumbnail' }) }}
                            {% else %}
                                <img src="{{ asset('/assets/theme/tulia/lisa/images/no-photo.node-thumbnail.png') }}" alt="" />
                            {% endif %}
                            <span class="block-node-date">{{ format_date(node.publishedAt) }}</span>
                        </div>
                        <div class="block-node-info">
                            <div class="block-node-title">{{ node.title }}</div>
                            <p class="block-node-description">{{ node.introduction }}</p>
                            <div class="block-node-read-more">{{ node_readmore|default('readMore'|trans({}, 'lisa-theme')) }}</div>
                        </div>
                    </a>
                </div>
            {% endfor %}
        </div>
        <div class="row d-md-none">
            <div class="col text-center">
                <a href="{{ term_path(taxonomy|default) }}" class="btn btn-primary btn-lg btn-icon-right block-news-read-more-btn">
                    <span>{{ taxonomy_readmore|default('readMore'|trans({}, 'lisa-theme')) }}</span>
                    <i class="btn-icon {{ taxonomy_icon|default('fas fa-chevron-right') }}"></i>
                </a>
            </div>
        </div>
    </div>
</div>
