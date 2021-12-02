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
        <FieldCreator
            @confirm="createFieldUsingCreatorData"
            :data="creator.field.data"
            :translations="translations"
        ></FieldCreator>
    </div>
</template>

<script>
import FieldCreator from './components/FieldCreator';
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
            creator: {
                field: {
                    data: {
                        sectionId: null,
                        label: null,
                        id: null,
                    },
                    modal: null
                }
            },
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
                                {id: '1231423', label: '11'},
                                {id: '1231423', label: '22'},
                                {id: '1231423', label: '33'},
                                {id: '1231423', label: '44'},
                            ]
                        }
                    ]
                }
            }
        };
    },
    components: {
        FieldCreator,
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
    methods: {
        openCreateFieldModel: function (sectionId) {
            this.creator.field.data.sectionId = sectionId;
            this.creator.field.data.label = '';
            this.creator.field.data.id = '';
            this.creator.field.data.type = '';
            this.creator.field.modal.show();
        },
        createFieldUsingCreatorData: function () {
            let data = this.creator.field.data;
            let section = this._findSection(data.sectionId);

            if (this._findField(data.id)) {
                Tulia.Info.info({
                    title: this.translations.youCannotCreateTwoFieldsWithTheSameId,
                    type: 'warning'
                });
                return;
            }

            section.fields.push({
                id: data.id,
                label: data.label,
            });

            this.creator.field.modal.hide();
        },
        _findSection: function (id) {
            for (let s in this.layout.sidebar.sections) {
                if (this.layout.sidebar.sections[s].id === id) {
                    return this.layout.sidebar.sections[s];
                }
            }

            for (let s in this.layout.main.sections) {
                if (this.layout.main.sections[s].id === id) {
                    return this.layout.main.sections[s];
                }
            }
        },
        _findField: function (id) {
            for (let s in this.layout.sidebar.sections) {
                for (let f in this.layout.sidebar.sections[s].fields) {
                    if (this.layout.sidebar.sections[s].fields[f].id === id) {
                        return this.layout.sidebar.sections[s].fields[f];
                    }
                }
            }

            for (let s in this.layout.main.sections) {
                for (let f in this.layout.main.sections[s].fields) {
                    if (this.layout.main.sections[s].fields[f].id === id) {
                        return this.layout.main.sections[s].fields[f];
                    }
                }
            }
        }
    },
    mounted: function () {
        this.creator.field.modal = new bootstrap.Modal(document.getElementById('ctb-create-field-modal'));

        this.$root.$on('field:add', (sectionId) => {
            this.openCreateFieldModel(sectionId);
        });
        // Set default width form existing fields.
        /*for (let input of this.$el.querySelectorAll('input.form-control')) {
            this.resizeInput({
                target: input
            });
        }*/
    }
};
</script>

