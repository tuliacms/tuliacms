<template>
    <div>
        <draggable group="sections" v-model="sections" handle=".ctb-section-sortable-handler" class="ctb-sections-container" :data-label="translations.addNewSection">
            <Section v-for="section in sections" v-bind:key="section.id" v-bind:translations="translations" v-bind:id="section.id" v-bind:label="section.label" v-bind:fields="section.fields"></Section>
        </draggable>
        <button type="button" class="btn btn-primary" @click="addSection()">{{ translations.addNewSection }}</button>
    </div>
</template>

<script>
import Section from './Section';
import draggable from 'vuedraggable';

export default {
    props: ['sections', 'translations'],
    components: {
        Section,
        draggable
    },
    methods: {
        addSection: function () {
            this.sections.push({
                id: 'content',
                label: 'Content',
                fields: []
            });
        },
    }
}
</script>
