<template>
    <div>
        <div class="modal fade" id="ctb-edit-field-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translations.editField }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ctb-new-field-label" class="form-label">{{ translations.fieldLabel }}</label>
                            <input type="email" class="form-control" id="ctb-new-field-label" v-model="field.label" />
                        </div>
                        <div class="mb-3">
                            <label for="ctb-new-field-id" class="form-label">{{ translations.fieldId }}</label>
                            <input type="email" class="form-control" id="ctb-new-field-id" disabled readonly v-model="field.id" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="$emit('confirm')">{{ translations.save }}</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{  translations.cancel }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['field', 'translations'],
    methods: {
    }
}
</script>
