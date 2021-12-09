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
                            <input type="text" :class="{ 'form-control': true, 'ctb-autofocus': true, 'is-invalid': model.label.valid === false }" id="ctb-edit-field-label" v-model="model.label.value" @change="_validate()" />
                            <div v-if="model.label.valid === false" class="invalid-feedback">{{ model.label.message }}</div>
                            <div class="form-text">{{ translations.fieldLabelHelp }}</div>
                        </div>
                        <div class="col mb-3">
                            <label for="ctb-edit-field-id" class="form-label">{{ translations.fieldId }}</label>
                            <input type="text" class="form-control" id="ctb-edit-field-id" v-model="model.id.value" disabled />
                        </div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="model.multilingual.value" id="ctb-edit-field-multilingual">
                        <label class="form-check-label" for="ctb-edit-field-multilingual">
                            {{ translations.multilingualField }}
                        </label>
                    </div>
                    <div class="form-text mb-3">{{ translations.multilingualFieldInfo }}</div>
                    <div v-if="model.constraints.length !== 0" class="card mb-3">
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
                                    <div v-if="constraint.enabled && constraint.modificators.length !== 0" class="ctb-field-constraint-modificators">
                                        <div v-for="modificator in constraint.modificators" :key="modificator.name" class="ctb-field-constraint-modificator mb-2">
                                            <label class="form-label">{{ modificator.label }}</label>
                                            <input type="text" :class="{ 'form-control': true, 'is-invalid': modificator.valid === false }" v-model="modificator.value" />
                                            <div v-if="modificator.valid === false" class="invalid-feedback">{{ modificator.message }}</div>
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
export default {
    props: ['field', 'fieldTypes', 'translations'],
    data: function () {
        return {
            model: {
                id: { value: '' },
                label: { value: null, valid: true, message: null },
                multilingual: { value: false, valid: true, message: null },
                constraints: [],
            }
        };
    },
    methods: {
        updateField: function () {
            if (this._validate() === false) {
                return;
            }

            let model = {
                label: this.model.label.value,
                multilingual: !!this.model.multilingual.value,
                constraints: [],
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

            this.$emit('confirm', model);
        },
        _validate: function () {
            let status = true;

            this.model.label.valid = true;
            this.model.label.message = null;
            this.model.id.valid = true;
            this.model.id.message = null;

            if (! this.model.label.value) {
                status = false;
                this.model.label.valid = false;
                this.model.label.message = this.translations.pleaseFillThisField;
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
                                }
                            }
                        }
                    }
                }

                this.model.constraints.push(newConstraint);
            }
        },
        _initiate: function () {
            this.model.id = { value: this.field.id, valid: true, message: null };
            this.model.label = { value: this.field.label, valid: true, message: null };
            this.model.multilingual = { value: this.field.multilingual, valid: true, message: null };
            this.model.type = { value: this.field.type };

            this._updateFieldTypeConstraints();
        },
    },
    mounted: function () {
        this.$root.$on('field:edit:modal:opened', () => {
            this._initiate();
        });
    }
}
</script>
