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
                        <draggable class="ctb-sortable-fields" v-bind="dragOptions" :list="model.layout.main.sections[0].fields" handle=".ctb-sortable-handler" group="fields" @start="drag=true" @end="drag=false" ghost-class="ctb-draggable-ghost">
                            <transition-group type="transition" :name="!drag ? 'flip-list' : null" class="ctb-sortable-placeholder" tag="div" :data-label="translations.addNewField">
                                <Field
                                    v-for="(field, key) in model.layout.main.sections[0].fields"
                                    :key="field.code.value"
                                    :field="field"
                                    :translations="translations"
                                ></Field>
                            </transition-group>
                        </draggable>
                        <div class="ctb-section-footer text-center">
                            <button class="ctb-button" type="button" @click="$root.$emit('field:add', model.layout.main.sections[0].code)">{{ translations.addNewField }}</button>
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
import Field from './components/Field';
import draggable from 'vuedraggable';

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
        Field,
        draggable
    },
    methods: {
        save: function () {
            if (this.validate() === false) {
                return;
            }

            let data = {
                layout: this.model.layout,
                type: this.model.type
            };

            data.type.icon = '';
            data.type.isRoutable = '0';
            data.type.isHierarchical = '0';

            $('#ctb-form-field-node-type').val(JSON.stringify(data));
            $('#ctb-form').submit();
        },
        validate: function () {
            let status = true;

            this.view.form.type_validation.name.valid = true;
            this.view.form.type_validation.name.message = null;
            this.view.form.type_validation.code.valid = true;
            this.view.form.type_validation.code.message = null;

            if (! this.model.type.name) {
                status = false;
                this.view.form.type_validation.name.valid = false;
                this.view.form.type_validation.name.message = this.translations.pleaseFillThisField;
            }

            if (! this.model.type.code) {
                status = false;
                this.view.form.type_validation.code.valid = false;
                this.view.form.type_validation.code.message = this.translations.pleaseFillThisField;
            } else if (! /^[0-9a-z_]+$/g.test(this.model.type.code)) {
                status = false;
                this.view.form.type_validation.code.valid = false;
                this.view.form.type_validation.code.message = this.translations.fieldCodeMustContainOnlyAlphanumsAndUnderline;
            }

            return status;
        },
        removeField: function (fieldCode) {
            Tulia.Confirmation.warning().then((result) => {
                if (! result.value) {
                    return;
                }

                for (let s in this.model.layout.main.sections) {
                    for (let f in this.model.layout.main.sections[s].fields) {
                        if (this.model.layout.main.sections[s].fields[f].code.value === fieldCode) {
                            this.model.layout.main.sections[s].fields.splice(f, 1);
                        }
                    }
                }
            });
        },
        openCreateFieldModel: function (sectionCode) {
            this.view.form.field_creator_section_code = sectionCode;
            this.view.modal.field_creator.show();

            this.$root.$emit('field:create:modal:opened');
        },
        openEditFieldModel: function (fieldCode) {
            let field = this._findField(fieldCode);

            if (! field) {
                throw new Error('Cannot open edit modal, field not exists.');
            }

            this.view.form.field_editor.code = field.code;
            this.view.form.field_editor.type = field.type;
            this.view.form.field_editor.name = field.name;
            this.view.form.field_editor.multilingual = field.multilingual;
            this.view.form.field_editor.constraints = field.constraints;
            this.view.form.field_editor.configuration = field.configuration;
            this.view.modal.field_editor.show();

            this.$root.$emit('field:edit:modal:opened');
        },
        createFieldUsingCreatorData: function (data) {
            let section = this._findSection(this.view.form.field_creator_section_code);

            if (this._findField(data.id)) {
                Tulia.Info.info({
                    title: this.translations.youCannotCreateTwoFieldsWithTheSameId,
                    type: 'warning'
                });
                return;
            }

            section.fields.push({
                metadata: { has_errors: false },
                code: { value: data.code, valid: true, message: null },
                name: { value: data.name, valid: true, message: null },
                type: { value: data.type, valid: true, message: null },
                multilingual: { value: data.multilingual, valid: true, message: null },
                configuration: data.configuration,
                constraints: [],
            });

            this.view.modal.field_creator.hide();
            this.openEditFieldModel(data.code);
            this.$forceUpdate();
        },
        editFieldUsingCreatorData: function (data) {
            let field = this._findField(this.view.form.field_editor.code.value);

            if (! field) {
                return;
            }

            field.metadata.has_errors = false;
            field.code.message = null;
            field.code.valid = true;
            field.name.value = data.name;
            field.name.message = null;
            field.name.valid = true;
            field.multilingual.value = data.multilingual;
            field.multilingual.message = null;
            field.multilingual.valid = true;
            field.configuration = data.configuration;
            field.constraints = data.constraints;

            this.view.modal.field_editor.hide();
            this.$forceUpdate();
        },
        generateTypeCode: function () {
            if (this.view.creation_mode === false) {
                return;
            }

            if (this.model.type.code === '') {
                this.code_field_changed = false;
            }

            if (this.code_field_changed) {
                return;
            }

            this.model.type.code = this.model.type.name.toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/is, '_');
        },
        _findSection: function (code) {
            for (let s in this.model.layout.main.sections) {
                if (this.model.layout.main.sections[s].code === code) {
                    return this.model.layout.main.sections[s];
                }
            }
        },
        _findField: function (code) {
            for (let s in this.model.layout.main.sections) {
                for (let f in this.model.layout.main.sections[s].fields) {
                    if (this.model.layout.main.sections[s].fields[f].code.value === code) {
                        return this.model.layout.main.sections[s].fields[f];
                    }
                }
            }
        }
    },
    mounted: function () {
        let creationModal = document.getElementById('ctb-create-field-modal');
        this.view.modal.field_creator = new bootstrap.Modal(creationModal);

        creationModal.addEventListener('shown.bs.modal', function () {
            $(creationModal).find('.ctb-autofocus').focus();
        });

        this.view.modal.field_editor = new bootstrap.Modal(document.getElementById('ctb-edit-field-modal'));

        this.$root.$on('field:add', (sectionCode) => {
            this.openCreateFieldModel(sectionCode);
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

