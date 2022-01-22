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
                <draggable class="ctb-sortable-fields" v-bind="dragOptions" :list="section.fields" handle=".ctb-sortable-handler" group="fields" @start="drag=true" @end="drag=false" ghost-class="ctb-draggable-ghost">
                    <transition-group type="transition" :name="!drag ? 'flip-list' : null" class="ctb-sortable-placeholder" tag="div" :data-label="translations.addNewField">
                        <Field
                            v-for="(field, key) in section.fields"
                            :key="field.code.value"
                            :field="field"
                            :translations="translations"
                        ></Field>
                    </transition-group>
                </draggable>
            </div>
            <div class="ctb-section-footer text-center">
                <button class="ctb-button" type="button" @click="$root.$emit('field:add', section.code)">{{ translations.addNewField }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Field from './Field';

export default {
    props: ['section', 'translations'],
    components: {
        draggable,
        Field
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
