<template>
    <div>
        <div class="ctb-section">
            <div class="ctb-section-label">
                <span class="ctb-section-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
                <input type="text" v-model="section.label" class="ctb-section-label-input" />
                <div class="ctb-section-options">
                    <span @click="$emit('section:remove', section.id)" v-bs-tooltip :title="translations.removeSection"><i class="fas fa-trash"></i></span>
                </div>
            </div>
            <div class="ctb-section-fields-container">
                <draggable class="ctb-sortable-fields" v-bind="dragOptions" :list="section.fields" handle=".ctb-sortable-handler" group="fields" @start="drag=true" @end="drag=false" ghost-class="ctb-draggable-ghost">
                    <transition-group type="transition" :name="!drag ? 'flip-list' : null" class="ctb-sortable-placeholder" tag="div" :data-label="translations.addNewField">
                        <Field
                            v-for="(field, key) in section.fields"
                            :key="field.id"
                            :field="field"
                            :translations="translations"
                            :errors="$get(errors, `fields.${key}`, {})"
                        ></Field>
                    </transition-group>
                </draggable>
            </div>
            <div class="ctb-section-footer text-center">
                <button class="ctb-button" type="button" @click="$root.$emit('field:add', section.id)">{{ translations.addNewField }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Field from './Field';

export default {
    props: ['section', 'errors', 'translations'],
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
