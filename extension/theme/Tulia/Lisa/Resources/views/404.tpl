{% extends 'theme' %}

{% block content %}
    <div class="px-4 py-5 my-5 text-center">
        <h1 class="display-5 fw-bold">Page not found :(</h1>
        <div class="col-lg-6 mx-auto">
            <p class="lead mb-4">Sorry, but we cannot find page You are looking for.</p>
            <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                <a href="{{ path('homepage') }}" class="btn btn-primary btn-lg px-4 gap-3">Go to homepage</a>
            </div>
        </div>
    </div>
{% endblock %}
