<template>
    <div>
        <div class="modal fade" id="ctb-create-field-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header pb-0">
                        <ul class="nav nav-tabs mb-0">
                            <li class="nav-item">
                                <button :class="{ 'nav-link': true, 'active': this.step === 1 }" @click="showStep(1)" type="button">{{ translations.fieldDetails }}</button>
                            </li>
                            <li class="nav-item">
                                <button :class="{ 'nav-link': true, 'active': this.step === 2, 'disabled': field.configuration && field.configuration.length === 0 }" @click="showStep(2)" type="button">{{ translations.fieldTypeConfiguration }}</button>
                            </li>
                            <li class="nav-item">
                                <button :class="{ 'nav-link': true, 'active': this.step === 3 }" @click="showStep(3)" type="button">{{ translations.fieldTypeConstraints }}</button>
                            </li>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div v-if="step === 1">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="ctb-new-field-label" class="form-label">{{ translations.fieldLabel }}</label>
                                    <input type="text" :class="{ 'form-control': true, 'ctb-autofocus': true, 'is-invalid': validation.label.valid === false }" id="ctb-new-field-label" v-model="field.label" @keyup="updateFieldId()" />
                                    <div v-if="validation.label.valid === false" class="invalid-feedback">{{ validation.label.message }}</div>
                                    <div class="form-text">{{ translations.fieldLabelHelp }}</div>
                                </div>
                                <div class="col mb-3">
                                    <label for="ctb-new-field-id" class="form-label">{{ translations.fieldId }}</label>
                                    <input type="text"  :class="{ 'form-control': true, 'is-invalid': validation.id.valid === false }" id="ctb-new-field-id" v-model="field.id" @keyup="idFieldChanged = true" />
                                    <div v-if="validation.id.valid === false" class="invalid-feedback">{{ validation.id.message }}</div>
                                    <div class="form-text">{{ translations.fieldIdHelp }}</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ translations.fieldType }}</label>
                                <div class="ctb-field-type-selector">
                                    <div v-for="type in fieldTypes" :key="type.id" :class="{ 'ctb-active': type.id === field.type }" @click="changeFieldType(type)">
                                        {{ type.label }}
                                    </div>
                                </div>
                            </div>
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" v-model="field.multilingual" id="ctb-multilingual-field">
                                <label class="form-check-label" for="ctb-multilingual-field">
                                    {{ translations.multilingualField }}
                                </label>
                            </div>
                            <div class="form-text mb-3">{{ translations.multilingualFieldInfo }}</div>
                            <div class="alert alert-info text-center">{{ translations.theseOptionsWillNotBeEditableAfterSave }}</div>
                        </div>
                    </div>
                    <div v-else-if="step === 2">
                        <div class="modal-body">
                            <div v-if="field.configuration.length !== 0" class="ctb-field-constraints">
                                <div v-for="(configuration, id) in field.configuration" :key="id" class="ctb-field-constraint mb-4">
                                    <label class="form-label" :for="'ctb-field-configuration-' + id">{{ configuration.label }}</label>
                                    <input type="text" :id="'ctb-field-configuration-' + id" :class="{ 'form-control': true, 'form-control-sm': true }" v-model="configuration.value" />
                                </div>
                            </div>
                            <div v-else>
                                <div class="alert alert-info">{{ translations.thisFieldDoesNotHaveConfiguration }}</div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="step === 3">
                        <div class="modal-body">
                            <div class="ctb-field-constraints">
                                <div v-for="(constraint, id) in field.constraints" :key="id" class="ctb-field-constraint mb-4">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" :disabled="constraint.required" :id="'ctb-field-constraint-' + id" v-model="constraint.enabled">
                                        <label class="form-check-label" :for="'ctb-field-constraint-' + id">
                                            {{ constraint.label }}
                                        </label>
                                        <div v-if="constraint.help_text" class="form-text">{{ constraint.help_text }}</div>
                                    </div>
                                    <div v-if="constraint.enabled && constraint.modificators.length !== 0" class="ctb-field-constraint-modificators">
                                        <div v-for="modificator in constraint.modificators" :key="modificator.name" class="ctb-field-constraint-modificator mb-2">
                                            <label class="form-label">{{ modificator.label }}</label>
                                            <input type="text" :class="{ 'form-control': true, 'form-control-sm': true }" v-model="modificator.value" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="saveField()">{{ translations.create }}</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{  translations.cancel }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['field', 'step', 'fieldTypes', 'translations'],
    data: function () {
        return {
            validation: {
                label: {
                    valid: true,
                    message: null,
                },
                id: {
                    valid: true,
                    message: null,
                }
            },
            idFieldChanged: false,
            constraints: [],
        };
    },
    methods: {
        changeFieldType: function (type) {
            // Do not update if it's the same.
            if (this.field.type === type.id) {
                return;
            }

            this.field.type = type.id;
            this._updateFieldTypeConstraints();
            this._updateFieldTypeConfiguration();

            // For somehow, Vue does not update preview when we change the field type
            // so we need to refresh it manually.
            this.$forceUpdate();
        },
        showStep: function (step) {
            if (step !== 1) {
                if (this._validateBasicConfiguration() === false) {
                    return;
                }
            }

            this.step = step;
        },
        updateFieldId: function () {
            if (this.idFieldChanged) {
                return;
            }

            this.field.id = this.field.label.toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/is, '_');
        },
        saveField: function () {
            if (this._validateBasicConfiguration() === false) {
                return;
            }

            this.$emit('confirm');
        },
        _validateBasicConfiguration: function () {
            let status = true;

            this.validation.label.valid = true;
            this.validation.label.message = null;
            this.validation.id.valid = true;
            this.validation.id.message = null;

            if (! this.field.label) {
                status = false;
                this.validation.label.valid = false;
                this.validation.label.message = this.translations.pleaseFillThisField;
            }

            if (! this.field.id) {
                status = false;
                this.validation.id.valid = false;
                this.validation.id.message = this.translations.pleaseFillThisField;
            } else if (! /^[0-9a-z_]+$/g.test(this.field.id)) {
                status = false;
                this.validation.id.valid = false;
                this.validation.id.message = this.translations.fieldIdMustContainOnlyAlphanumsAndUnderline;
            }

            return status;
        },
        _initiate: function () {
            this.step = 1;
            this.idFieldChanged = false;
            this._updateFieldTypeConstraints();
            this._updateFieldTypeConfiguration();
        },
        _updateFieldTypeConstraints: function () {
            this.field.constraints = [];

            // Not all field types contain custom constraints.
            if (! this.fieldTypes[this.field.type].constraints) {
                return;
            }

            let constraints = JSON.parse(JSON.stringify(this.fieldTypes[this.field.type].constraints));

            for (let i in constraints) {
                let constraint = constraints[i];
                constraint.enabled = constraint.required === true;

                this.field.constraints.push(constraint);
            }
        },
        _updateFieldTypeConfiguration: function () {
            this.field.configuration = [];

            // Not all field types contain configurations
            if (! this.fieldTypes[this.field.type].configuration) {
                return;
            }

            let configuration = JSON.parse(JSON.stringify(this.fieldTypes[this.field.type].configuration));

            for (let i in configuration) {
                this.field.configuration.push(configuration[i]);
            }
        }
    },
    mounted: function () {
        this.$root.$on('field:create:modal:opened', () => {
            this._initiate();
        });
    }
}
</script>
