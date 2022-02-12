<template>
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a :href="listingUrl" class="btn btn-secondary btn-icon-left"><i class="btn-icon fas fa-times"></i> Anuluj</a>
                <button class="btn btn-success btn-icon-left" type="button" @click="save()"><i class="btn-icon fas fa-save"></i> Zapisz</button>
            </div>
            <i class="pane-header-icon fas fa-box"></i>
            <h1 class="pane-title">{{ translations.pageTitle }}</h1>
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
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="ctb-form-type-icon" class="form-label">{{ translations.icon }}</label>
                                    <input type="email" class="form-control" id="ctb-form-type-icon" v-model="model.type.icon" />
                                </div>
                                <div class="col-6 mb-3">
                                    <label for="ctb-form-type-hierarchical" class="form-label">{{ translations.hierarchicalType }}</label>
                                    <chosen-select id="ctb-form-type-hierarchical" v-model="model.type.isHierarchical">
                                        <option value="1">{{ translations.yes }}</option>
                                        <option value="0">{{ translations.no }}</option>
                                    </chosen-select>
                                    <div class="form-text">{{ translations.hierarchicalTypeHelp }}</div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label for="ctb-form-type-routable" class="form-label">{{ translations.routableType }}</label>
                                    <chosen-select id="ctb-form-type-routable" v-model="model.type.isRoutable">
                                        <option value="1">{{ translations.yes }}</option>
                                        <option value="0">{{ translations.no }}</option>
                                    </chosen-select>
                                    <div class="form-text">{{ translations.routableTypeHelp }}</div>
                                </div>
                                <div class="col-6 mb-3" v-if="model.type.isRoutable === '1'">
                                    <label for="ctb-form-type-routing-strategy" class="form-label">{{ translations.routingStrategy }}</label>
                                    <chosen-select id="ctb-form-type-routing-strategy" v-model="model.type.routingStrategy">
                                        <option v-for="strategy in routingStrategies" :id="strategy.id" :value="strategy.id">{{ strategy.label }}</option>
                                    </chosen-select>
                                    <div class="form-text">{{ translations.routingStrategyHelp }}</div>
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
                :showMultilingualOption="true"
            ></FieldCreator>
            <FieldEditor
                @confirm="editFieldUsingCreatorData"
                :translations="translations"
                :field="view.form.field_editor"
                :fieldTypes="fieldTypes"
                :showMultilingualOption="true"
            ></FieldEditor>
        </div>
    </div>
</template>

<script>
import FieldCreator from './components/FieldCreator';
import FieldEditor from './components/FieldEditor';
import SectionsList from './components/SectionsList';
import draggable from 'vuedraggable';
import framework from './framework';

export default {
    name: 'ContentLayoutBuilder',
    data() {
        let model = window.ContentBuilderLayoutBuilder.model;
        let errors = window.ContentBuilderLayoutBuilder.errors;
        let typeValidation = {
            name: { valid: !this.$get(errors, 'type.name.0'), message: this.$get(errors, 'type.name.0') },
            code: { valid: !this.$get(errors, 'type.code.0'), message: this.$get(errors, 'type.code.0') },
            icon: { valid: !this.$get(errors, 'type.icon.0'), message: this.$get(errors, 'type.icon.0') },
            isRoutable: { valid: !this.$get(errors, 'type.isRoutable.0'), message: this.$get(errors, 'type.isRoutable.0') },
            isHierarchical: { valid: !this.$get(errors, 'type.isHierarchical.0'), message: this.$get(errors, 'type.isHierarchical.0') }
        };

        return {
            translations: window.ContentBuilderLayoutBuilder.translations,
            fieldTypes: window.ContentBuilderLayoutBuilder.fieldTypes,
            routingStrategies: window.ContentBuilderLayoutBuilder.routingStrategies,
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
                    icon: this.$get(model, 'type.icon', 'fas fa-boxes'),
                    isRoutable: this.$get(model, 'type.isRoutable', false) ? '1' : '0',
                    isHierarchical: this.$get(model, 'type.isHierarchical', false) ? '1' : '0',
                    routingStrategy: this.$get(model, 'type.routingStrategy', null),
                },
                layout: {
                    sidebar: {
                        sections: this.$get(model, 'layout.sidebar.sections', [])
                    },
                    main: {
                        sections: this.$get(model, 'layout.main.sections', [])
                    }
                }
            }
        };
    },
    components: {
        FieldCreator,
        FieldEditor,
        SectionsList,
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

