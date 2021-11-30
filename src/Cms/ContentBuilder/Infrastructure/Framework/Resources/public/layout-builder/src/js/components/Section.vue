<template>
    <div>
        <div class="ctb-section">
            <div class="ctb-section-label">
                <span class="ctb-section-sortable-handler"><i class="fas fa-arrows-alt"></i></span>
                {{ label }}
            </div>
            <div class="ctb-section-fields-container">
                <draggable class="ctb-sortable-fields" v-model="fields" group="fields" @start="drag=true" @end="drag=false" :data-label="translations.addNewField">
                    <Field v-for="field in fields" v-bind:key="field.id" v-bind:id="field.id" v-bind:name="field.name"></Field>
                </draggable>
            </div>
            <div class="ctb-section-footer">
                <button class="btn btn-primary" type="button" v-on:click="addField()">{{ translations.addNewField }}</button>
            </div>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Field from './Field';

export default {
    props: ['id', 'label', 'fields', 'translations'],
    components: {
        draggable,
        Field
    },
    methods: {
        addField: function () {
            this.fields.push({
                id: '234asdf',
                name: 'q23rwqerw4w4'
            });
        }
    }
}
</script>
