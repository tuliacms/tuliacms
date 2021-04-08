{% import '@backend/_macros/form/bootstrap/badge.tpl' as badge %}

<ul class="nav nav-tabs">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#settings-username-tab">
            {{ 'username'|trans }}
            {{ badge.errors_count(form, [ 'username_min_length' ]) }}
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings-password-tab">
            {{ 'password'|trans }}
            {{ badge.errors_count(form, [ 'password_min_length', 'password_min_digits', 'password_min_special_chars', 'password_min_big_letters', 'password_min_small_letters' ]) }}
        </a>
    </li>
</ul>
<div class="tab-content">
    <div class="tab-pane fade show active" id="settings-username-tab">
        <div class="form-controls-terminator">
            {{ form_row(form.username_min_length) }}
        </div>
    </div>
    <div class="tab-pane fade" id="settings-password-tab">
        <div class="form-controls-terminator">
            {{ form_row(form.password_min_length) }}
            {{ form_row(form.password_min_digits) }}
            {{ form_row(form.password_min_special_chars) }}
            {{ form_row(form.password_min_big_letters) }}
            {{ form_row(form.password_min_small_letters) }}
        </div>
    </div>
</div>
