<template>
    <div class="app">
        <div class="page-form" id="node-form">
            <div class="page-form-sidebar">
                <SectionsList v-bind:translations="translations" v-bind:sections="layout.sidebar.sections"></SectionsList>
            </div>
            <div class="page-form-content">
                <div class="page-form-header">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label required">{{ translations.title }}</label>
                                    <input type="text" disabled="disabled" class="form-control">
                                </div>
                            </div>
                            <div class="col">
                                <div class="mb-3">
                                    <label class="form-label required">{{ translations.slug }}</label>
                                    <input type="text" disabled="disabled" class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-content ctb-section-main-tabs-contents">
                    <SectionsList v-bind:translations="translations" v-bind:sections="layout.main.sections"></SectionsList>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import SectionsList from './components/SectionsList';
import Field from './components/Field';
import draggable from 'vuedraggable';

export default {
    name: "App",
    data() {
        /*let availableFields = window.ContactFormBuilder.availableFields;

        for (let t in availableFields) {
            for (let o in availableFields[t].options) {
                availableFields[t].options[o].focused = false;
            }
        }

        return {
            fields: window.ContactFormBuilder.fields,
            availableFields: availableFields,
            translations: window.ContactFormBuilder.translations,
            fieldsTemplate: window.ContactFormBuilder.fieldsTemplate,
        }*/

        return {
            translations: window.ContentBuilderLayoutBuilder.translations,
            layout: {
                sidebar: {
                    sections: [
                        {
                            id: 'system-status',
                            label: 'System status',
                            fields: []
                        }
                    ]
                },
                main: {
                    sections: [
                        {
                            id: 'introduction',
                            label: 'Introduction',
                            fields: [
                                {id: '1231423', name: '11'},
                                {id: '1231423', name: '22'},
                                {id: '1231423', name: '33'},
                                {id: '1231423', name: '44'},
                            ]
                        }
                    ]
                }
            }
        };
    },
    components: {
        SectionsList,
        Field,
        draggable
    },
    watch: {
        layout: {
            handler: function (val, oldVal) {
                console.log('1', JSON.stringify(val));
            },
            deep: true
        }
    },
    mounted: function () {
        // Set default width form existing fields.
        /*for (let input of this.$el.querySelectorAll('input.form-control')) {
            this.resizeInput({
                target: input
            });
        }*/
    }
};
</script>

