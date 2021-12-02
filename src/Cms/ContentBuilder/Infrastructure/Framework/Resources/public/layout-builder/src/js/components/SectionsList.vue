<template>
    <div>
        <draggable group="sections" :list="sections" handle=".ctb-section-sortable-handler" class="ctb-sections-container" :data-label="translations.addNewSection">
            <Section
                v-for="section in sections"
                :key="section.id"
                v-bind:section="section"
                v-bind:translations="translations"
                v-on:section:remove="removeSection"
            ></Section>
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
                id: _.uniqueId('section-'),
                label: 'New section...',
                fields: []
            });
        },
        removeSection: function (id) {
            let self = this;

            Tulia.Confirmation.warning().then(function (result) {
                if (result.value) {
                    for (let i in self.sections) {
                        if (self.sections[i].id === id) {
                            self.sections.splice(i, 1);
                        }
                    }
                }
            });
        }
    }
}
</script>
