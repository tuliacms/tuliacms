<template>
    <div class="modal fade" id="ctb-create-field-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations.addNewField }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="ctb-new-field-label" class="form-label">{{ translations.fieldLabel }}</label>
                            <input type="text" :class="{ 'form-control': true, 'ctb-autofocus': true, 'is-invalid': model.name.valid === false }" id="ctb-new-field-label" v-model="model.name.value" @keyup="generateFieldId()" @change="_validateBasics()" />
                            <div v-if="model.name.valid === false" class="invalid-feedback">{{ model.name.message }}</div>
                            <div class="form-text">{{ translations.fieldLabelHelp }}</div>
                        </div>
                        <div class="col mb-3">
                            <label for="ctb-new-field-id" class="form-label">{{ translations.fieldId }}</label>
                            <input type="text" :class="{ 'form-control': true, 'is-invalid': model.code.valid === false }" id="ctb-new-field-id" v-model="model.code.value" @keyup="idFieldChanged = true" @change="_validateBasics()" />
                            <div v-if="model.code.valid === false" class="invalid-feedback">{{ model.code.message }}</div>
                            <div class="form-text">{{ translations.fieldIdHelp }}</div>
                        </div>
                    </div>
                    <div v-if="showMultilingualOption">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="model.multilingual.value" id="ctb-new-field-multilingual">
                            <label class="form-check-label" for="ctb-new-field-multilingual">
                                {{ translations.multilingualField }}
                            </label>
                        </div>
                        <div class="form-text mb-3">{{ translations.multilingualFieldInfo }}</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ translations.fieldType }}</label>
                        <div class="ctb-field-type-selector">
                            <div v-for="type in fieldTypes" :key="type.id" :class="{ 'ctb-active': type.id === model.type.value }" @click="changeFieldType(type)">
                                {{ type.label }}
                            </div>
                        </div>
                    </div>
                    <div v-if="model.configuration.length !== 0" class="card mb-3">
                        <div class="card-body pb-0">
                            <div class="ctb-field-constraints row">
                                <div v-for="(configuration, id) in model.configuration" :key="id" class="col-6 ctb-field-constraint mb-4">
                                    <FormControl
                                        :translations="translations"
                                        :field="configuration"
                                        :id="'ctb-new-field-configuration-' + id"
                                    ></FormControl>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info text-center mb-0">{{ translations.theseOptionsWillNotBeEditableAfterSave }}</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="saveField()">{{ translations.create }}</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ translations.cancel }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FormControl from './FormControl';

export default {
    props: ['fieldTypes', 'translations', 'defaultMultilingualValue', 'showMultilingualOption'],
    components: {
        FormControl
    },
    data: function () {
        return {
            idFieldChanged: false,
            model: {
                code: { value: '', valid: true, message: null },
                type: { value: 'text', valid: true, message: null },
                name: { value: null, valid: true, message: null },
                multilingual: { value: false, valid: true, message: null },
                configuration: [],
            }
        };
    },
    methods: {
        changeFieldType: function (type) {
            // Do not update if it's the same.
            if (this.model.type.value === type.id) {
                return;
            }

            this.model.type.value = type.id;
            this._updateFieldTypeConfiguration();

            // For somehow, Vue does not update preview when we change the field type
            // so we need to refresh it manually.
            this.$forceUpdate();
        },
        generateFieldId: function () {
            if (this.model.code.value === '') {
                this.idFieldChanged = false;
            }

            if (this.idFieldChanged) {
                return;
            }

            this.model.code.value = this.model.name.value.toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/is, '_');
        },
        saveField: function () {
            if (this._validate() === false) {
                //return;
            }

            let model = {
                code: this.model.code.value,
                type: this.model.type.value,
                name: this.model.name.value,
                multilingual: !!this.model.multilingual.value,
                configuration: [],
            };

            for (let c in this.model.configuration) {
                model.configuration.push({
                    id: this.model.configuration[c].id,
                    value: this.model.configuration[c].value,
                });
            }

            this.$emit('confirm', model);
        },
        _updateFieldTypeConfiguration: function () {
            this.model.configuration = [];

            // Not all field types contain configurations
            if (! this.fieldTypes[this.model.type.value].configuration) {
                return;
            }

            let configuration = JSON.parse(JSON.stringify(this.fieldTypes[this.model.type.value].configuration));

            for (let i in configuration) {
                configuration[i].id = i;
                configuration[i].value = null;
                configuration[i].valid = null;
                configuration[i].message = null;
                this.model.configuration.push(configuration[i]);
            }
        },
        _validate: function () {
            let basics = this._validateBasics();
            let configs = this._validateConfiguration();

            return basics && configs;
        },
        _validateBasics: function () {
            let status = true;

            this.model.name.valid = true;
            this.model.name.message = null;
            this.model.code.valid = true;
            this.model.code.message = null;

            if (! this.model.name.value) {
                status = false;
                this.model.name.valid = false;
                this.model.name.message = this.translations.pleaseFillThisField;
            }

            if (! this.model.code.value) {
                status = false;
                this.model.code.valid = false;
                this.model.code.message = this.translations.pleaseFillThisField;
            } else if (! /^[0-9a-z_]+$/g.test(this.model.code.value)) {
                status = false;
                this.model.code.valid = false;
                this.model.code.message = this.translations.fieldCodeMustContainOnlyAlphanumsAndUnderline;
            }

            return status;
        },
        _validateConfiguration: function () {
            let status = true;

            for (let c in this.model.configuration) {
                this.model.configuration[c].valid = true;
                this.model.configuration[c].message = null;

                if (this.model.configuration[c].required && (
                    this.model.configuration[c].value === null
                    || this.model.configuration[c].value === ''
                )) {
                    this.model.configuration[c].valid = false;
                    this.model.configuration[c].message = this.translations.pleaseFillThisField;
                    status = false;
                }
            }

            return status;
        },
        _initiate: function () {
            this.idFieldChanged = false;

            this.model.code = { value: '', valid: true, message: null };
            this.model.type = { value: 'text', valid: true, message: null };
            this.model.name = { value: null, valid: true, message: null };
            this.model.multilingual = { value: null, valid: true, message: null };
            this.model.configuration = [];

            this._updateFieldTypeConfiguration();
        },
    },
    mounted: function () {
        this.$root.$on('field:create:modal:opened', () => {
            this._initiate();
        });
    }
}
</script>
