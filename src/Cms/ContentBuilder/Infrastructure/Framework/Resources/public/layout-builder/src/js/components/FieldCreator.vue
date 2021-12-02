<template>
    <div>
        <div class="modal fade" id="ctb-create-field-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ translations.addNewField }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ctb-new-field-label" class="form-label">{{ translations.fieldLabel }}</label>
                            <input type="email" class="form-control" id="ctb-new-field-label" v-model="data.label" />
                        </div>
                        <div class="mb-3">
                            <label for="ctb-new-field-id" class="form-label">{{ translations.fieldCode }}</label>
                            <input type="email" class="form-control" id="ctb-new-field-id" v-model="data.id" />
                            <div class="form-text">{{ translations.fieldIdHelp }}</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" @click="$emit('confirm')">{{ translations.create }}</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{  translations.cancel }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['data', 'translations'],
    methods: {
    }
}
</script>
