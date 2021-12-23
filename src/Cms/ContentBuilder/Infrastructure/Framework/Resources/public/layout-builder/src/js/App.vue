<template>
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a :href="listingUrl" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> Anuluj</a>
                <button class="btn btn-success btn-icon-left" type="button" @click="save()"><i class="btn-icon fas fa-save"></i> Zapisz</button>
            </div>
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ translations.createNodeType }}</h1>
        </div>
        <div class="pane-body p-0">
            <div class="page-form" id="node-form">
                <div class="page-form-sidebar">
                    <form method="POST" id="ctb-form" style="display:none">
                        <textarea name="node_type" id="ctb-form-field-node-type"></textarea>
                        <input type="text" name="_token" :value="csrfToken"/>
                    </form>
                    <div class="ctb-sections-container">
                        <div class="ctb-section ctb-section-internal-fields">
                            <div class="ctb-section-label">
                                {{ translations.internalFields }}
                            </div>
                            <div class="ctb-section-fields-container">
                                <div class="ctb-sortable-fields mb-3">
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.title }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.slug }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.publishedAt }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.publicationStatus }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.author }}</span></div>
                                    <div class="ctb-field"><span class="ctb-field-label">{{ translations.flags }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <SectionsList
                        :translations="translations"
                        :sections="model.layout.sidebar.sections"
                        :errors="$get(view.errors, 'layout.sidebar.sections', [])"
                    ></SectionsList>
                </div>
                <div class="page-form-content">
                    <div class="page-form-header">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label" for="ctb-node-type-name">{{ translations.nodeTypeName }}</label>
                                        <input type="text" :class="{ 'form-control': true, 'is-invalid': view.form.type_validation.name.valid === false }" id="ctb-node-type-name" v-model="model.type.name" @keyup="generateTypeCode()" @change="validate()" />
                                        <div class="form-text">{{ translations.nodeTypeNameInfo }}</div>
                                        <div v-if="view.form.type_validation.name.valid === false" class="invalid-feedback">{{ view.form.type_validation.name.message }}</div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="mb-3">
                                        <label class="form-label" for="ctb-node-type-code">{{ translations.nodeTypeCode }}</label>
                                        <input type="text" :class="{ 'form-control': true, 'is-invalid': view.form.type_validation.code.valid === false }" id="ctb-node-type-code" v-model="model.type.code" @change="validate()" />
                                        <div class="form-text">{{ translations.nodeTypeCodeHelp }}</div>
                                        <div v-if="view.form.type_validation.code.valid === false" class="invalid-feedback">{{ view.form.type_validation.code.message }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="ctb-form-type-icon" class="form-label">{{ translations.icon }}</label>
                                    <input type="email" class="form-control" id="ctb-form-type-icon" v-model="model.type.icon" />
                                </div>
                                <div class="col mb-3">
                                    <label for="ctb-form-type-routable" class="form-label">{{ translations.routableType }}</label>
                                    <chosen-select id="ctb-form-type-routable" v-model="model.type.isRoutable">
                                        <option value="1">{{ translations.yes }}</option>
                                        <option value="0">{{ translations.no }}</option>
                                    </chosen-select>
                                    <div class="form-text">{{ translations.routableTypeHelp }}</div>
                                </div>
                            </div>
                            <div class="row" v-if="view.form.has_taxonomy_field">
                                <div class="col mb-3">
                                    <label for="ctb-form-type-hierarchical" class="form-label">{{ translations.hierarchicalType }}</label>
                                    <chosen-select id="ctb-form-type-hierarchical" v-model="model.type.isHierarchical">
                                        <option value="1">{{ translations.yes }}</option>
                                        <option value="0">{{ translations.no }}</option>
                                    </chosen-select>
                                    <div class="form-text">{{ translations.hierarchicalTypeHelp }}</div>
                                </div>
                                <div class="col mb-3">
                                    <label for="ctb-form-type-taxonomy-field" class="form-label">{{ translations.taxonomyField }}</label>
                                    <chosen-select id="ctb-form-type-taxonomy-field" v-model="model.type.taxonomyField">
                                        <option v-for="field in view.form.taxonomy_fields" :key="field.value" :value="field.value">{{ field.label }}</option>
                                    </chosen-select>
                                    <div class="form-text">{{ translations.taxonomyFieldHelp }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-content ctb-section-main-tabs-contents">
                        <SectionsList
                            :translations="translations"
                            :sections="model.layout.main.sections"
                            :errors="$get(view.errors, 'layout.main.sections', [])"
                        ></SectionsList>
                    </div>
                </div>
            </div>
            <FieldCreator
                @confirm="createFieldUsingCreatorData"
                :translations="translations"
                :fieldTypes="fieldTypes"
            ></FieldCreator>
            <FieldEditor
                @confirm="editFieldUsingCreatorData"
                :translations="translations"
                :field="view.form.field_editor"
                :fieldTypes="fieldTypes"
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

const getFrom = function (object, path, defaultValue = null) {
    let result = object;

    for (let piece of path.split('.')) {
        result = result[piece];

        if (! result) {
            break;
        }
    }

    return result || defaultValue;
};

export default {
    name: 'ContentLayoutBuilder',
    data() {
        let model = window.ContentBuilderLayoutBuilder.model;
        let errors = window.ContentBuilderLayoutBuilder.errors;
        let typeValidation = {
            name: { valid: !getFrom(errors, 'type.name.0'), message: getFrom(errors, 'type.name.0') },
            code: { valid: !getFrom(errors, 'type.code.0'), message: getFrom(errors, 'type.code.0') },
            icon: { valid: !getFrom(errors, 'type.icon.0'), message: getFrom(errors, 'type.icon.0') },
            isRoutable: { valid: !getFrom(errors, 'type.isRoutable.0'), message: getFrom(errors, 'type.isRoutable.0') },
            isHierarchical: { valid: !getFrom(errors, 'type.isHierarchical.0'), message: getFrom(errors, 'type.isHierarchical.0') },
            taxonomyField: { valid: !getFrom(errors, 'type.taxonomyField.0'), message: getFrom(errors, 'type.taxonomyField.0') },
        };

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
                    has_taxonomy_field: false,
                    taxonomy_fields: [],
                    field_creator_section_id: null,
                    field_editor: {
                        id: null,
                        type: null,
                        label: null,
                        multilingual: null,
                        constraints: [],
                        configuration: [],
                    },
                    type_validation: typeValidation
                }
            },
            model: {
                type: {
                    name: getFrom(model, 'type.name'),
                    code: getFrom(model, 'type.code'),
                    icon: getFrom(model, 'type.icon', 'fas fa-boxes'),
                    isRoutable: getFrom(model, 'type.isRoutable', '1'),
                    isHierarchical: getFrom(model, 'type.isHierarchical', '1'),
                    taxonomyField: getFrom(model, 'type.taxonomyField'),
                },
                layout: {
                    sidebar: {
                        sections: getFrom(model, 'layout.sidebar.sections', [])
                    },
                    main: {
                        sections: getFrom(model, 'layout.main.sections', [])
                    }
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
    methods: {
        save: function () {
            if (this.validate() === false) {
                return;
            }

            $('#ctb-form-field-node-type').val(JSON.stringify({
                layout: this.model.layout,
                type: this.model.type
            }));
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
                this.view.form.type_validation.code.message = this.translations.fieldIdMustContainOnlyAlphanumsAndUnderline;
            }

            return status;
        },
        removeField: function (fieldId) {
            Tulia.Confirmation.warning().then((result) => {
                if (! result.value) {
                    return;
                }

                for (let s in this.model.layout.sidebar.sections) {
                    for (let f in this.model.layout.sidebar.sections[s].fields) {
                        if (this.model.layout.sidebar.sections[s].fields[f].id === fieldId) {
                            this.model.layout.sidebar.sections[s].fields.splice(f, 1);
                        }
                    }
                }

                for (let s in this.model.layout.main.sections) {
                    for (let f in this.model.layout.main.sections[s].fields) {
                        if (this.model.layout.main.sections[s].fields[f].id === fieldId) {
                            this.model.layout.main.sections[s].fields.splice(f, 1);
                        }
                    }
                }

                this._detectTaxonomyFieldExistence();
            });
        },
        openCreateFieldModel: function (sectionId) {
            this.view.form.field_creator_section_id = sectionId;
            this.view.modal.field_creator.show();

            this.$root.$emit('field:create:modal:opened');
        },
        openEditFieldModel: function (fieldId) {
            let field = this._findField(fieldId);

            this.view.form.field_editor.id = field.id;
            this.view.form.field_editor.type = field.type;
            this.view.form.field_editor.label = field.label;
            this.view.form.field_editor.multilingual = field.multilingual;
            this.view.form.field_editor.constraints = field.constraints;
            this.view.form.field_editor.configuration = field.configuration;
            this.view.form.field_editor.errors = field.errors;
            this.view.modal.field_editor.show();

            this.$root.$emit('field:edit:modal:opened');
        },
        createFieldUsingCreatorData: function (data) {
            let section = this._findSection(this.view.form.field_creator_section_id);

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
                type: data.type,
                multilingual: data.multilingual,
                configuration: data.configuration,
            });

            this._detectTaxonomyFieldExistence();

            this.view.modal.field_creator.hide();
            this.openEditFieldModel(data.id);
        },
        editFieldUsingCreatorData: function (data) {
            let field = this._findField(this.view.form.field_editor.id);

            field.label = data.label;
            field.multilingual = data.multilingual;
            field.configuration = data.configuration;
            field.constraints = data.constraints;

            this.view.modal.field_editor.hide();
        },
        generateTypeCode: function () {
            if (this.model.type.code === '') {
                this.code_field_changed = false;
            }

            if (this.code_field_changed) {
                return;
            }

            this.model.type.code = this.model.type.name.toLowerCase().replace(/[^a-z0-9_]+/g, '_').replace(/_+/is, '_');
        },
        _findSection: function (id) {
            for (let s in this.model.layout.sidebar.sections) {
                if (this.model.layout.sidebar.sections[s].id === id) {
                    return this.model.layout.sidebar.sections[s];
                }
            }

            for (let s in this.model.layout.main.sections) {
                if (this.model.layout.main.sections[s].id === id) {
                    return this.model.layout.main.sections[s];
                }
            }
        },
        _findField: function (id) {
            for (let s in this.model.layout.sidebar.sections) {
                for (let f in this.model.layout.sidebar.sections[s].fields) {
                    if (this.model.layout.sidebar.sections[s].fields[f].id === id) {
                        let field = this.model.layout.sidebar.sections[s].fields[f];
                        field.errors = this.$get(this.view.errors, `layout.sidebar.sections.${s}.fields.${f}`);
                        return field;
                    }
                }
            }

            for (let s in this.model.layout.main.sections) {
                for (let f in this.model.layout.main.sections[s].fields) {
                    if (this.model.layout.main.sections[s].fields[f].id === id) {
                        let field = this.model.layout.main.sections[s].fields[f];
                        field.errors = this.$get(this.view.errors, `layout.main.sections.${s}.fields.${f}`);
                        return field;
                    }
                }
            }
        },
        _detectTaxonomyFieldExistence: function () {
            this.view.form.has_taxonomy_field = false;
            this.view.form.taxonomy_fields = [];

            for (let s in this.model.layout.sidebar.sections) {
                for (let f in this.model.layout.sidebar.sections[s].fields) {
                    if (this.model.layout.sidebar.sections[s].fields[f].type === 'taxonomy') {
                        this.view.form.has_taxonomy_field = true;
                        this.view.form.taxonomy_fields.push({
                            value: this.model.layout.sidebar.sections[s].fields[f].id,
                            label: this.model.layout.sidebar.sections[s].fields[f].label
                        });
                    }
                }
            }

            for (let s in this.model.layout.main.sections) {
                for (let f in this.model.layout.main.sections[s].fields) {
                    if (this.model.layout.main.sections[s].fields[f].type === 'taxonomy') {
                        this.view.form.has_taxonomy_field = true;
                        this.view.form.taxonomy_fields.push({
                            value: this.model.layout.sidebar.sections[s].fields[f].id,
                            label: this.model.layout.sidebar.sections[s].fields[f].label
                        });
                    }
                }
            }

            // Reset taxonomy field model if field was removed.
            if (this.view.form.has_taxonomy_field === false) {
                this.model.type.taxonomyField = null;
            } else {
                // Set taxonomy field if model is empty and if any of taxonomy fields are created.
                if (this.model.type.taxonomyField === null) {
                    this.model.type.taxonomyField = this.view.form.taxonomy_fields[0].value;
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

        this._detectTaxonomyFieldExistence();

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

