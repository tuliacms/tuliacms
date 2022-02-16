<template>
    <div class="modal fade" id="cbb-edit-block-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations.editBlock }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="cbb-new-field-label" class="form-label">{{ translations.blockName }}</label>
                            <input type="text" :class="{ 'form-control': true, 'cbb-autofocus': true, 'is-invalid': model.name.valid === false }" id="cbb-new-field-label" v-model="model.name.value" />
                            <div v-if="model.name.valid === false" class="invalid-feedback">{{ model.name.message }}</div>
                        </div>
                    </div>
                    <div :class="{ 'cbb-block-editor-panel': true, 'cbb-block-editor-panel-loading' : view.block_panel_loading }">
                        <iframe src="about:blank" id="cbb-block-editor-panel-iframe" :style="{ 'height': view.iframe_height + 'px' }"></iframe>
                        <div class="cbb-block-editor-panel-loader"><span>{{ translations.loading }}</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="saveBlock()">{{ translations.save }}</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ translations.cancel }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['block', 'translations', 'settings'],
    components: {
    },
    data: function () {
        return {
            model: {
                block_type: { value: null, valid: true, message: null },
                name:       { value: null, valid: true, message: null },
            },
            view: {
                block_panel_loading: true,
                iframe: null,
                iframe_height: 60,
            },
        };
    },
    methods: {
        saveBlock: function () {
            this.view.block_panel_loading = true;
            this.view.iframe.contentWindow
                .postMessage({
                    action: 'validate-form'
                },
                this.settings.cors_domain);
        },
        updateModel: function (fields) {
            this.$emit('confirm', this.block.id, {
                name: this.model.name.value,
                fields: fields
            });
        },
        setBlockType: function (type) {
            this.model.block_type.value = type;
            this.model.block_type.valid = true;
            this.model.block_type.message = null;
        },
        _initiate: function () {
            this.view.iframe_height = 60;

            for (let i in this.model) {
                this.model[i].value = null;
                this.model[i].valid = true;
                this.model[i].message = null;
            }

            this.model.name.value = this.block.name;

            let form = [];

            form.push(`<form action="${this.block.block_panel_url}" method="POST">`);

            for (let i in this.block.fields) {
                let name = this.settings.field_name_pattern
                    .replace('%field%', i)
                    .replace('%block_type%', this.block.type);

                let value = this.block.fields[i][0] ? this._htmlEscape(this.block.fields[i][0]) : '';

                form.push(`<input type="text" name="${name}" value="${value}" />`);
            }

            form.push('</form>');

            let body = $(this.view.iframe).contents().find('body');

            body.html(form.join(''));
            body.find('form').submit();
        },
        _htmlEscape: function (str) {
            return str
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;');
        }
    },
    mounted: function () {
        this.view.iframe = document.querySelector('#cbb-block-editor-panel-iframe');
        this.$root.$on('block:edit:modal:opened', () => {
            this._initiate();
        });

        window.addEventListener('message', (event) => {
            if (event.data.action === 'loaded') {
                this.view.block_panel_loading = false;
            }

            if (event.data.action === 'form-valid') {
                this.updateModel(event.data.fields);
            }

            if (event.data.action === 'height-changed') {
                this.view.iframe_height = event.data.height;
            }
        }, false);
    }
}
</script>
