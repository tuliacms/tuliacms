<template>
    <div class="modal fade" id="cbb-import-json-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ translations.import }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea v-model="json" class="cbb-import-textarea form-control" :placeholder="translations.pasteJsonBlocks"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">{{ translations.close }}</button>
                    <button type="button" class="btn btn-success" @click="$emit('import', json)">{{ translations.import }}</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['translations'],
    data: function () {
        return {json: ''}
    },
    mounted() {
        this.$root.$on('import:modal:opened', () => {
            setTimeout(function () {
                $('.cbb-import-textarea').focus();
            }, 500);
        });
    }
}
</script>
