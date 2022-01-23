<template>
    <div class="modal fade" id="ctb-edit-field-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations.editField }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="ctb-edit-field-label" class="form-label">{{ translations.fieldLabel }}</label>
                            <input type="text" :class="{ 'form-control': true, 'ctb-autofocus': true, 'is-invalid': model.name.valid === false }" id="ctb-edit-field-label" v-model="model.name.value" @change="_validate()" />
                            <div v-if="model.name.valid === false" class="invalid-feedback">{{ model.name.message }}</div>
                            <div class="form-text">{{ translations.fieldLabelHelp }}</div>
                        </div>
                        <div class="col mb-3">
                            <label for="ctb-edit-field-id" class="form-label">{{ translations.fieldId }}</label>
                            <input type="text" :class="{ 'form-control': true, 'is-invalid': model.code.valid === false }" id="ctb-edit-field-id" v-model="model.code.value" disabled />
                            <div v-if="model.code.valid === false" class="invalid-feedback">{{ model.code.message }}</div>
                        </div>
                    </div>
                    <div v-if="showMultilingualOption">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="model.multilingual.value" id="ctb-edit-field-multilingual">
                            <label class="form-check-label" for="ctb-edit-field-multilingual">
                                {{ translations.multilingualField }}
                            </label>
                        </div>
                        <div class="form-text mb-3">{{ translations.multilingualFieldInfo }}</div>
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
                    <div v-if="model.constraints.length !== 0" class="card">
                        <div class="card-body pb-0">
                            <div class="ctb-field-constraints">
                                <div v-for="(constraint, id) in model.constraints" :key="id" class="ctb-field-constraint mb-3">
                                    <div class="form-check mb-1">
                                        <input class="form-check-input" type="checkbox" :disabled="constraint.required" :id="'ctb-edit-field-constraint-' + id" v-model="constraint.enabled">
                                        <label class="form-check-label" :for="'ctb-edit-field-constraint-' + id">
                                            {{ constraint.label }}
                                        </label>
                                        <div v-if="constraint.help_text" class="form-text">{{ constraint.help_text }}</div>
                                    </div>
                                    <div v-if="constraint.enabled && constraint.modificators.length !== 0" class="ctb-field-constraint-modificators row">
                                        <div v-for="modificator in constraint.modificators" :key="modificator.id" class="col-6 ctb-field-constraint mb-4">
                                            <FormControl
                                                :translations="translations"
                                                :field="modificator"
                                                :id="'ctb-edit-field-modificator-' + modificator.id"
                                            ></FormControl>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" @click="updateField()">{{ translations.save }}</button>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{translations.cancel }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FormControl from './FormControl';

export default {
    props: ['field', 'fieldTypes', 'translations', 'defaultMultilingualValue', 'showMultilingualOption'],
    components: {
        FormControl
    },
    data: function () {
        return {
            model: {
                code: { value: '', valid: true, message: null },
                name: { value: null, valid: true, message: null },
                multilingual: { value: false, valid: true, message: null },
                type: { value: false, valid: true, message: null },
                constraints: [],
                configuration: [],
            }
        };
    },
    methods: {
        updateField: function () {
            if (this._validate() === false) {
                return;
            }

            let model = {
                name: this.model.name.value,
                multilingual: !!this.model.multilingual.value,
                constraints: [],
                configuration: [],
            };

            for (let c in this.model.constraints) {
                let modificators = [];

                for (let m in this.model.constraints[c].modificators) {
                    modificators.push({
                        id: m,
                        value: this.model.constraints[c].modificators[m].value,
                    });
                }

                model.constraints.push({
                    id: this.model.constraints[c].id,
                    enabled: this.model.constraints[c].enabled,
                    modificators: modificators,
                });
            }

            for (let c in this.model.configuration) {
                model.configuration.push({
                    id: this.model.configuration[c].id,
                    value: this.model.configuration[c].value,
                });
            }

            this.$emit('confirm', model);
        },
        _validate: function () {
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

            for (let c in this.model.constraints) {
                for (let m in this.model.constraints[c].modificators) {
                    this.model.constraints[c].modificators[m].valid = true;
                    this.model.constraints[c].modificators[m].message = null;

                    if (this.model.constraints[c].modificators[m].required && (
                        this.model.constraints[c].modificators[m].value === null
                        || this.model.constraints[c].modificators[m].value === ''
                    )) {
                        this.model.constraints[c].modificators[m].valid = false;
                        this.model.constraints[c].modificators[m].message = this.translations.pleaseFillThisField;
                        status = false;
                    }
                }
            }

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
        _updateFieldTypeConstraints: function () {
            this.model.constraints = [];

            // Not all field types contain constraints
            if (! this.fieldTypes[this.model.type.value].constraints) {
                return;
            }

            let constraints = JSON.parse(JSON.stringify(this.fieldTypes[this.model.type.value].constraints));

            for (let c in constraints) {
                let newConstraint = constraints[c];
                newConstraint.id = c;

                for (let m in newConstraint.modificators) {
                    newConstraint.modificators[m].valid = true;
                    newConstraint.modificators[m].message = null;
                }

                for (let cs in this.field.constraints) {
                    let oldConstraint = this.field.constraints[cs];

                    if (oldConstraint.id === newConstraint.id && oldConstraint.enabled) {
                        newConstraint.enabled = oldConstraint.enabled;

                        for (let nm in newConstraint.modificators) {
                            let newModificator = newConstraint.modificators[nm];

                            for (let om in oldConstraint.modificators) {
                                let oldModificator = oldConstraint.modificators[om];

                                if (oldModificator.id === nm) {
                                    newModificator.value = oldModificator.value;
                                    newModificator.valid = oldModificator.valid;
                                    newModificator.message = oldModificator.message;
                                }
                            }
                        }
                    }
                }

                this.model.constraints.push(newConstraint);
            }
        },
        _updateFieldTypeConfiguration: function () {
            this.model.configuration = [];

            // Not all field types contain configurations
            if (! this.fieldTypes[this.model.type.value].configuration) {
                return;
            }

            let configuration = JSON.parse(JSON.stringify(this.fieldTypes[this.model.type.value].configuration));

            for (let nc in configuration) {
                configuration[nc].id = nc;
                configuration[nc].value = null;
                configuration[nc].valid = null;
                configuration[nc].message = null;

                for (let cc in this.field.configuration) {
                    let oldConfiguration = this.field.configuration[cc];

                    if (oldConfiguration.id === configuration[nc].id) {
                        configuration[nc].value = oldConfiguration.value;
                        configuration[nc].valid = oldConfiguration.valid;
                        configuration[nc].message = oldConfiguration.message;
                    }
                }

                this.model.configuration.push(configuration[nc]);
            }
        },
        _initiate: function () {
            this.model.code.value = this.field.code.value;
            this.model.code.valid = this.field.code.valid;
            this.model.code.message = this.field.code.message;
            this.model.name.value = this.field.name.value;
            this.model.name.valid = this.field.name.valid;
            this.model.name.message = this.field.name.message;
            this.model.multilingual.value = this.field.multilingual.value;
            this.model.multilingual.valid = this.field.multilingual.valid;
            this.model.multilingual.message = this.field.multilingual.message;
            this.model.type.value = this.field.type.value;
            this.model.type.valid = this.field.type.valid;
            this.model.type.message = this.field.type.message;

            this._updateFieldTypeConstraints();
            this._updateFieldTypeConfiguration();
        },
    },
    mounted: function () {
        this.$root.$on('field:edit:modal:opened', () => {
            this._initiate();
        });
    }
}
</script>
