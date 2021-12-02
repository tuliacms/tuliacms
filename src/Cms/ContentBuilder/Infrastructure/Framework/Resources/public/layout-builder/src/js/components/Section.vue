<template>
    <div>
        <div class="ctb-section">
            <div class="ctb-section-label">
                <span class="ctb-section-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
                <input type="text" v-model="section.label" class="ctb-section-label-input" />
                <div class="ctb-section-options">
                    <span @click="$emit('section:remove', section.id)"><i class="fas fa-trash"></i></span>
                </div>
            </div>
            <div class="ctb-section-fields-container">
                <draggable class="ctb-sortable-fields" :list="section.fields" group="fields" @start="drag=true" @end="drag=false" :data-label="translations.addNewField">
                    <Field v-for="field in section.fields" v-bind:key="field.id" v-bind:field="field"></Field>
                </draggable>
            </div>
            <div class="ctb-section-footer">
                <button class="btn btn-primary" type="button" @click="$root.$emit('field:add', section.id)">{{ translations.addNewField }}</button>
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
    }
}
</script>
