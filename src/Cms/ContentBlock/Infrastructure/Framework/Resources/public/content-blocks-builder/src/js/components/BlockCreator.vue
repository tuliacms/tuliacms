<template>
    <div class="modal fade" id="cbb-create-block-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations.addBlock }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="cbb-new-field-label" class="form-label">{{ translations.blockName }}</label>
                            <input type="text" :class="{ 'form-control': true, 'cbb-autofocus': true, 'is-invalid': model.name.valid === false }" id="cbb-new-field-label" v-model="model.name.value" autocomplete="off" />
                            <div v-if="model.name.valid === false" class="invalid-feedback">{{ model.name.message }}</div>
                        </div>
                    </div>
                    <div class="cbb-block-type-list">
                        <div
                            :class="{ 'cbb-block-type': true, 'cbb-block-type-active': model.block_type.value === type.code, 'is-invalid': model.block_type.valid === false }"
                            v-for="type in block_types"
                            @click="setBlockType(type.code)"
                        >
                            <div class="cbb-block-type-inner">
                                <div class="cbb-block-type-icon" v-bind:style="{ backgroundImage: 'url(' + type.icon + ')' }"></div>
                                <div class="cbb-block-type-name">{{ type.name }}</div>
                            </div>
                        </div>
                        <div v-if="model.block_type.valid === false" class="invalid-feedback">{{ model.block_type.message }}</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="saveField()">{{ translations.createAndConfigure }}</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ translations.cancel }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['block_types', 'translations'],
    components: {
    },
    data: function () {
        return {
            model: {
                block_type: { value: null, valid: true, message: null },
                name:       { value: null, valid: true, message: null },
            }
        };
    },
    methods: {
        saveField: function () {
            if (this.validate() === false) {
                return;
            }

            this.$emit('confirm', {
                type: this.model.block_type.value,
                name: this.model.name.value,
            });
        },
        validate: function () {
            let valid = true;

            for (let i in this.model) {
                this.model[i].valid = true;
                this.model[i].message = null;
            }

            if (! this.model.name.value) {
                this.model.name.valid = false;
                this.model.name.message = this.translations.pleaseFillThisField;
                valid = false;
            }

            if (! this.model.block_type.value) {
                this.model.block_type.valid = false;
                this.model.block_type.message = this.translations.pleaseSelectBlockType;
                valid = false;
            }

            return valid;
        },
        setBlockType: function (type) {
            this.model.block_type.value = type;
            this.model.block_type.valid = true;
            this.model.block_type.message = null;
        },
        _initiate: function () {
            for (let i in this.model) {
                this.model[i].value = null;
                this.model[i].valid = true;
                this.model[i].message = null;
            }
        },
    },
    mounted: function () {
        this.$root.$on('block:create:modal:opened', () => {
            this._initiate();
        });
    }
}
</script>
