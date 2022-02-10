const createHierarchicalFields = function (model) {
    const recursiveHierarchicalFields = function (parent, fields) {
        let result = [];

        for (let field of fields) {
            if (field.parent === parent) {
                field.children = recursiveHierarchicalFields(field.code.value, fields);
                result.push(field);
            }
        }

        return result;
    };

    for (let k in model.layout) {
        for (let s in model.layout[k].sections) {
            model.layout[k].sections[s].fields = recursiveHierarchicalFields(null, model.layout[k].sections[s].fields);
        }
    }

    return model;
};

const flattenFields = function (model) {
    const recursiveFlattenFields = function (fields) {
        let result = [];

        for (let field of fields) {
            field.parent = null;
            result.push(field);

            if (field.children.length) {
                for (let subfield of recursiveFlattenFields(field.children)) {
                    subfield.parent = field.code.value;
                    result.push(subfield);
                }
                field.children = [];
            }
        }

        return result;
    };

    for (let k in model.layout) {
        for (let s in model.layout[k].sections) {
            model.layout[k].sections[s].fields = recursiveFlattenFields(model.layout[k].sections[s].fields);
        }
    }

    return model;
};

export default {
    createHierarchicalFields: createHierarchicalFields,
    methods: {
        save: function () {
            if (this.validate() === false) {
                return;
            }

            let model = flattenFields(JSON.parse(JSON.stringify(this.model)));

            $('#ctb-form-field-node-type').val(JSON.stringify({
                layout: model.layout,
                type: model.type
            }));
            $('#ctb-form').submit();
        },
        validate: function () {
            let status = true;

            this.view.form.type_validation.name.valid = true;
            this.view.form.type_validation.name.message = null;
            this.view.form.type_validation.code.valid = true;
            this.view.form.type_validation.code.message = null;

            if (!this.model.type.name) {
                status = false;
                this.view.form.type_validation.name.valid = false;
                this.view.form.type_validation.name.message = this.translations.pleaseFillThisField;
            }

            if (!this.model.type.code) {
                status = false;
                this.view.form.type_validation.code.valid = false;
                this.view.form.type_validation.code.message = this.translations.pleaseFillThisField;
            } else if (!/^[0-9a-z_]+$/g.test(this.model.type.code)) {
                status = false;
                this.view.form.type_validation.code.valid = false;
                this.view.form.type_validation.code.message = this.translations.fieldCodeMustContainOnlyAlphanumsAndUnderline;
            }

            return status;
        },
        removeField: function (fieldCode) {
            Tulia.Confirmation.warning().then((result) => {
                if (!result.value) {
                    return;
                }

                this._removeField(fieldCode);
            });
        },
        openCreateFieldModel: function (sectionCode, parentField) {
            this.view.form.field_creator_section_code = sectionCode;
            this.view.form.field_creator_parent_field = parentField;
            this.view.modal.field_creator.show();

            this.$root.$emit('field:create:modal:opened');
        },
        openEditFieldModel: function (fieldCode) {
            let field = this._findField(fieldCode);

            if (!field) {
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

            let newField = {
                metadata: {has_errors: false},
                code: {value: data.code, valid: true, message: null},
                name: {value: data.name, valid: true, message: null},
                type: {value: data.type, valid: true, message: null},
                multilingual: {value: data.multilingual, valid: true, message: null},
                configuration: data.configuration,
                constraints: [],
                children: [],
            };

            if (this.view.form.field_creator_parent_field) {
                let parent = this._findField(this.view.form.field_creator_parent_field);

                if (!parent) {
                    alert('ERROR: Parent field not exists. Cannot create this field.');
                    return;
                }

                parent.children.push(newField);
            } else {
                section.fields.push(newField);
            }

            this.view.modal.field_creator.hide();
            this.openEditFieldModel(data.code);
            this.$forceUpdate();
        },
        editFieldUsingCreatorData: function (data) {
            let field = this._findField(this.view.form.field_editor.code.value);

            if (!field) {
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
            for (let k in this.model.layout) {
                for (let s in this.model.layout[k].sections) {
                    if (this.model.layout[k].sections[s].code === code) {
                        return this.model.layout[k].sections[s];
                    }
                }
            }
        },
        _findField: function (code) {
            for (let k in this.model.layout) {
                for (let s in this.model.layout[k].sections) {
                    let field = this._findInFields(this.model.layout[k].sections[s].fields, code);

                    if (field) {
                        return field;
                    }
                }
            }
        },
        _findInFields: function (fields, code) {
            for (let f in fields) {
                if (fields[f].code.value === code) {
                    return fields[f];
                }

                let fieldFromChildren = this._findInFields(fields[f].children, code);

                if (fieldFromChildren) {
                    return fieldFromChildren;
                }
            }
        },
        _removeField: function (code) {
            for (let k in this.model.layout) {
                for (let s in this.model.layout[k].sections) {
                    this._removeFieldFromList(this.model.layout[k].sections[s].fields, code);
                }
            }
        },
        _removeFieldFromList: function (fields, code) {
            for (let f in fields) {
                if (fields[f].code.value === code) {
                    fields.splice(f, 1);
                    return;
                }

                this._removeFieldFromList(fields[f].children, code);
            }
        }
    }
};
