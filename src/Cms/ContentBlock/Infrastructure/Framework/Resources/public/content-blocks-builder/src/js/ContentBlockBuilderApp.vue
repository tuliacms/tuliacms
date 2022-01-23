<template>
    <div>
        Builder
    </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
    name: 'ContentBlockBuilder',
    data() {
        return {
            //translations: window.ContentBuilderLayoutBuilder.translations,
        };
    },
    components: {
        draggable
    },
    methods: {

    },
    mounted: function () {
        /*let creationModal = document.getElementById('ctb-create-field-modal');
        this.view.modal.field_creator = new bootstrap.Modal(creationModal);

        creationModal.addEventListener('shown.bs.modal', function () {
            $(creationModal).find('.ctb-autofocus').focus();
        });

        this.view.modal.field_editor = new bootstrap.Modal(document.getElementById('ctb-edit-field-modal'));

        this.$root.$on('field:add', (sectionCode) => {
            this.openCreateFieldModel(sectionCode);
        });
        this.$root.$on('field:edit', (fieldCode) => {
            this.openEditFieldModel(fieldCode);
        });
        this.$root.$on('field:remove', (fieldCode) => {
            this.removeField(fieldCode);
        });*/
    }
};
</script>

