<template>
    <div>
        <div class="modal fade" id="ctb-create-field-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translations.addNewField }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div v-if="step === 1">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="ctb-new-field-label" class="form-label">{{ translations.fieldLabel }}</label>
                                    <input type="email" class="form-control" id="ctb-new-field-label" v-model="field.label" />
                                </div>
                                <div class="col mb-3">
                                    <label for="ctb-new-field-id" class="form-label">{{ translations.fieldId }}</label>
                                    <input type="email" class="form-control" id="ctb-new-field-id" v-model="field.id" />
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
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" v-model="field.multiple" id="ctb-multiple-field">
                                <label class="form-check-label" for="ctb-multiple-field">
                                    {{ translations.multipleField }}
                                </label>
                            </div>
                            <div class="form-text mb-3">{{ translations.multipleFieldInfo }}</div>
                            <div class="alert alert-info text-center">{{ translations.theseOptionsWillNotBeEditableAfterSave }}</div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-success btn-icon-right" @click="step = 2">{{ translations.nextStep }} <i class="btn-icon fas fa-chevron-right"></i></button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{  translations.cancel }}</button>
                        </div>
                    </div>
                    <div v-else>
                        <div class="modal-body">
                            asdads
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default btn-icon-left" @click="step = 1">{{ translations.previousStep }} <i class="btn-icon fas fa-chevron-left"></i></button>
                            <button type="button" class="btn btn-success" @click="$emit('confirm')">{{ translations.create }}</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{  translations.cancel }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['field', 'step', 'fieldTypes', 'translations'],
    methods: {
        changeFieldType: function (type) {
            this.field.type = type.id;
            // For somehow, Vue does not update preview when we change the field type
            // so we need to refresh it manually.
            this.$forceUpdate();
        }
    },
    mounted: function () {
        this.$root.$on('field:create:modal:opened', () => {
            this.step = 1;
        });
    }
}
</script>
