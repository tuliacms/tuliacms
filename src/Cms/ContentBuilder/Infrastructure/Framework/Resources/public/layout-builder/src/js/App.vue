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
            :field="creator.field.data"
            :translations="translations"
            :fieldTypes="fieldTypes"
            :step="1"
        ></FieldCreator>
        <FieldEditor
            @confirm="editFieldUsingCreatorData"
            :field="editor.field.data"
            :translations="translations"
        ></FieldEditor>
    </div>
</template>

<script>
import FieldCreator from './components/FieldCreator';
import FieldEditor from './components/FieldEditor';
import SectionsList from './components/SectionsList';
import Field from './components/Field';
import draggable from 'vuedraggable';

export default {
    name: 'ContentLayoutBuilder',
    data() {
        return {
            translations: window.ContentBuilderLayoutBuilder.translations,
            fieldTypes: window.ContentBuilderLayoutBuilder.fieldTypes,
            creator: {
                field: {
                    data: {
                        sectionId: null,
                        label: null,
                        id: null,
                        multilingual: false,
                        multiple: false,
                        constraints: [],
                    },
                    modal: null,
                }
            },
            editor: {
                field: {
                    data: {
                        fieldId: null,
                        label: null,
                        id: null,
                        multilingual: false,
                        multiple: false,
                        constraints: [],
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
                                {id: '11', label: '11'},
                                {id: '22', label: '22'},
                                {id: '33', label: '33'},
                                {id: '44', label: '44'},
                            ]
                        }
                    ]
                }
            }
        };
    },
    components: {
        FieldCreator,
        FieldEditor,
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
        removeField: function (fieldId) {
            Tulia.Confirmation.warning().then((result) => {
                if (! result.value) {
                    return;
                }

                for (let s in this.layout.sidebar.sections) {
                    for (let f in this.layout.sidebar.sections[s].fields) {
                        if (this.layout.sidebar.sections[s].fields[f].id === fieldId) {
                            this.layout.sidebar.sections[s].fields.splice(f, 1);
                            return;
                        }
                    }
                }

                for (let s in this.layout.main.sections) {
                    for (let f in this.layout.main.sections[s].fields) {
                        if (this.layout.main.sections[s].fields[f].id === fieldId) {
                            this.layout.main.sections[s].fields.splice(f, 1);
                            return;
                        }
                    }
                }
            });
        },
        openCreateFieldModel: function (sectionId) {
            this.creator.field.data.sectionId = sectionId;
            this.creator.field.data.label = '';
            this.creator.field.data.id = '';
            this.creator.field.data.type = 'text';
            this.creator.field.data.multilingual = false;
            this.creator.field.data.multiple = false;
            this.creator.field.data.constraints = [];
            this.creator.field.modal.show();

            this.$root.$emit('field:create:modal:opened');
        },
        openEditFieldModel: function (fieldId) {
            let field = this._findField(fieldId);

            this.editor.field.data.fieldId = fieldId;
            this.editor.field.data.label = field.label;
            this.editor.field.data.id = field.id;
            this.editor.field.data.type = field.type;
            this.editor.field.data.multilingual = false;
            this.editor.field.data.multiple = false;
            this.editor.field.data.constraints = [];
            this.editor.field.modal.show();

            this.$root.$emit('field:edit:modal:opened');
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
        editFieldUsingCreatorData: function () {
            let data = this.editor.field.data;
            let section = this._findSection(data.sectionId);

            section.fields.push({
                label: data.label,
            });

            this.editor.field.modal.hide();
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
        let creationModal = document.getElementById('ctb-create-field-modal');
        this.creator.field.modal = new bootstrap.Modal(creationModal);

        creationModal.addEventListener('shown.bs.modal', function () {
            $(creationModal).find('.ctb-autofocus').focus();
        });

        this.editor.field.modal = new bootstrap.Modal(document.getElementById('ctb-edit-field-modal'));

        this.$root.$on('field:add', (sectionId) => {
            this.openCreateFieldModel(sectionId);
        });
        this.$root.$on('field:edit', (fieldId) => {
            this.openEditFieldModel(fieldId);
        });
        this.$root.$on('field:remove', (fieldId) => {
            this.removeField(fieldId);
        });
    }
};
</script>

