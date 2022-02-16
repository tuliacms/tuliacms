<template>
    <div>
        <draggable group="sections" :list="sections" v-bind="dragOptions" handle=".ctb-section-sortable-handler" class="ctb-sections-container">
            <transition-group type="transition" :name="!drag ? 'flip-list' : null" class="ctb-sortable-placeholder" tag="div" :data-label="translations.addNewSection">
                <Section
                    v-for="(section, id) in sections"
                    :key="section.code"
                    :section="section"
                    :translations="translations"
                    :errors="$get(errors, id, {})"
                    @section:remove="removeSection"
                ></Section>
            </transition-group>
        </draggable>
        <div class="text-center">
            <div class="ctb-new-section-button" @click="addSection()">
                <i class="fa fa-plus"></i>
                {{ translations.addNewSection }}
            </div>
        </div>
    </div>
</template>

<script>
import Section from './Section';
import draggable from 'vuedraggable';

export default {
    props: ['sections', 'errors', 'translations'],
    components: {
        Section,
        draggable
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: 'sections',
                disabled: false,
                ghostClass: 'ctb-draggable-ghost'
            };
        }
    },
    methods: {
        addSection: function () {
            this.sections.push({
                code: _.uniqueId('section_'),
                name: {
                    value: 'New section...',
                    valid: true,
                    message: null
                },
                fields: []
            });
        },
        removeSection: function (code) {
            Tulia.Confirmation.warning().then((result) => {
                if (result.value) {
                    for (let i in this.sections) {
                        if (this.sections[i].code === code) {
                            this.sections.splice(i, 1);
                        }
                    }
                }
            });
        }
    }
}
</script>
