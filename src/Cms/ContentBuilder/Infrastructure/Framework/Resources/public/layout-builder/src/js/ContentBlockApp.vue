<template>
    <div class="pane pane-lead content-builder-content-block-type">
        <form method="POST" id="ctb-form" style="display:none">
            <textarea name="node_type" id="ctb-form-field-node-type"></textarea>
            <input type="text" name="_token" :value="csrfToken"/>
        </form>
        <div class="pane-header">
            <div class="pane-buttons">
                <a :href="listingUrl" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> Anuluj</a>
                <button class="btn btn-success btn-icon-left" type="button" @click="save()"><i class="btn-icon fas fa-save"></i> Zapisz</button>
            </div>
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ translations.pageTitle }}</h1>
        </div>
        <div class="pane-body p-0">
            <div class="page-form">
                <div class="page-form-content">
                    <div class="page-form-header">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="ctb-node-type-name">{{ translations.contentTypeName }}</label>
                                        <input type="text" :class="{ 'form-control': true, 'is-invalid': view.form.type_validation.name.valid === false }" id="ctb-node-type-name" v-model="model.type.name" @keyup="generateTypeCode()" @change="validate()" />
                                        <div class="form-text">{{ translations.contentTypeNameInfo }}</div>
                                        <div v-if="view.form.type_validation.name.valid === false" class="invalid-feedback">{{ view.form.type_validation.name.message }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="ctb-node-type-code">{{ translations.contentTypeCode }}</label>
                                        <input type="text" :disabled="view.creation_mode !== true" :class="{ 'form-control': true, 'is-invalid': view.form.type_validation.code.valid === false }" id="ctb-node-type-code" v-model="model.type.code" @change="validate()" />
                                        <div class="form-text">{{ translations.contentTypeCodeHelp }}</div>
                                        <div v-if="view.form.type_validation.code.valid === false" class="invalid-feedback">{{ view.form.type_validation.code.message }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content ctb-section-main-tabs-contents">
                        <div class="ctb-sections-container">
                            <div class="ctb-section-fields-container">
                                <Fields
                                    :translations="translations"
                                    :fields="model.layout.main.sections[0].fields"
                                    :section="model.layout.main.sections[0]"
                                    :group="'fields'"
                                    :parent_field="null"
                                ></Fields>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <FieldCreator
                @confirm="createFieldUsingCreatorData"
                :translations="translations"
                :fieldTypes="fieldTypes"
                :showMultilingualOption="view.is_multilingual"
            ></FieldCreator>
            <FieldEditor
                @confirm="editFieldUsingCreatorData"
                :translations="translations"
                :field="view.form.field_editor"
                :fieldTypes="fieldTypes"
                :showMultilingualOption="view.is_multilingual"
            ></FieldEditor>
        </div>
    </div>
</template>

<script>
import FieldCreator from './components/FieldCreator';
import FieldEditor from './components/FieldEditor';
import SectionsList from './components/SectionsList';
import Fields from './components/Fields';
import draggable from 'vuedraggable';
import framework from './framework';

export default {
    name: 'ContentLayoutBuilder',
    data() {
        let model = window.ContentBuilderLayoutBuilder.model;
        let errors = window.ContentBuilderLayoutBuilder.errors;
        let sections = this.$get(model, 'layout.main.sections', []);
        let typeValidation = {
            name: { valid: !this.$get(errors, 'type.name.0'), message: this.$get(errors, 'type.name.0') },
            code: { valid: !this.$get(errors, 'type.code.0'), message: this.$get(errors, 'type.code.0') },
        };

        /**
         * Vue needs at least one section. Creating new type of Content Block we dont'have any sections,
         * so we need to create one, default section when no sections comes fromt backend.
         */
        if (sections.length === 0) {
            sections.push({
                code: _.uniqueId('section_'),
                name: {
                    value: 'Section',
                    valid: true,
                    message: null
                },
                fields: []
            });
        }

        return {
            translations: window.ContentBuilderLayoutBuilder.translations,
            fieldTypes: window.ContentBuilderLayoutBuilder.fieldTypes,
            listingUrl: window.ContentBuilderLayoutBuilder.listingUrl,
            csrfToken: window.ContentBuilderLayoutBuilder.csrfToken,
            view: {
                modal: {
                    field_creator: null,
                    field_editor: null,
                },
                errors: errors,
                form: {
                    code_field_changed: false,
                    field_creator_section_code: null,
                    field_creator_parent_field: null,
                    field_editor: {
                        code: {value: null, valid: true, message: null},
                        type: {value: null, valid: true, message: null},
                        name: {value: null, valid: true, message: null},
                        multilingual: {value: null, valid: true, message: null},
                        constraints: [],
                        configuration: [],
                    },
                    type_validation: typeValidation
                },
                creation_mode: window.ContentBuilderLayoutBuilder.creationMode,
                is_multilingual: window.ContentBuilderLayoutBuilder.multilingual,
            },
            model: {
                type: {
                    name: this.$get(model, 'type.name'),
                    code: this.$get(model, 'type.code'),
                    icon: '',
                    isRoutable: '0',
                    isHierarchical: '0',
                },
                layout: {
                    main: {
                        sections: sections
                    }
                }
            }
        };
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
    },
    components: {
        FieldCreator,
        FieldEditor,
        SectionsList,
        Fields,
        draggable
    },
    methods: framework.methods,
    mounted: function () {
        let creationModal = document.getElementById('ctb-create-field-modal');
        this.view.modal.field_creator = new bootstrap.Modal(creationModal);

        creationModal.addEventListener('shown.bs.modal', function () {
            $(creationModal).find('.ctb-autofocus').focus();
        });

        this.view.modal.field_editor = new bootstrap.Modal(document.getElementById('ctb-edit-field-modal'));

        this.$root.$on('field:add', (sectionCode, parentField) => {
            this.openCreateFieldModel(sectionCode, parentField);
        });
        this.$root.$on('field:edit', (fieldCode) => {
            this.openEditFieldModel(fieldCode);
        });
        this.$root.$on('field:remove', (fieldCode) => {
            this.removeField(fieldCode);
        });
    }
};
</script>

