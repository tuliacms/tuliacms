<template>
    <div>
        <div class="ctb-section">
            <div class="ctb-section-label">
                <span class="ctb-section-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
                <input
                    type="text"
                    v-model="section.name.value"
                    :class="{ 'ctb-section-label-input': true, 'ctb-section-label-input-has-error': ! section.name.valid }"
                    v-bs-tooltip
                    :title="section.name.message"
                />
                <div class="ctb-section-options">
                    <span @click="$emit('section:remove', section.code)" v-bs-tooltip :title="translations.removeSection"><i class="fas fa-trash"></i></span>
                </div>
            </div>
            <div class="ctb-section-fields-container">
                <Fields
                    :translations="translations"
                    :fields="section.fields"
                    :section="section"
                    :group="'fields'"
                    :parent_field="null"
                ></Fields>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Fields from './Fields';

export default {
    props: ['section', 'translations'],
    components: {
        draggable,
        Fields
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: 'fields',
                disabled: false,
                ghostClass: 'ctb-draggable-ghost'
            };
        }
    }
}
</script>
