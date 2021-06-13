<template>
    <div class="app">
        <h3>{{ translations.availableFields }}</h3>
        <p class="text-muted">{{ translations.availableFieldsInfo }}</p>
        <button
            type="button"
            class="btn btn-success mr-2"
            v-for="item in availableFields"
            v-on:click="addInput(item.alias)"
        >{{ item.label }}</button>
        <hr />
        <h3>{{ translations.fieldsBuilder }}</h3>
        <p class="text-muted">{{ translations.fieldsBuilderInfo }}</p>
        <div class="contact-form-fields-builder">
            <div
                v-for="(field, key) in fields"
                class="form-field-prototype"
                :data-field-name="field.alias"
            >
                <input
                    type="hidden"
                    :name="'form[fields][' + key + '][alias]'"
                    v-model="field.alias"
                    autocomplete="off"
                />
                <span
                    v-on:click="removeField(key)"
                    class="field-remove fas fa-window-close"
                    :title="translations.removeField"
                    data-toggle="tooltip"
                ></span>
                [{{ field.alias }}<!--
                    --><span
                        v-for="(option, name) in availableFields[field.alias].options"
                        v-bind:class="{ 'text-danger': fields[key]['options'][name].error !== null }"
                        :title="fields[key]['options'][name].error"
                        data-toggle="tooltip"
                    ><!--
                    -->&nbsp;<label v-bind:class="{ 'field-optional': !option.required }">{{ name }}="<input
                        type="text"
                        autocomplete="off"
                        :data-option-name="name"
                        :data-option-key="key"
                        class="form-control"
                        :name="'form[fields][' + key + '][' + name + ']'"
                        v-model="fields[key]['options'][name].value"
                        @change="resizeInput"
                        @input="resizeInput"
                        @focus="showLegend(field.alias, name)"
                        @blur="hideLegend(field.alias, name)"
                    />"</label><!--
                --></span>]
                <span
                    v-on:click="addFieldToTemplate(key)"
                    class="field-add-to-template fas fa-plus-square"
                    :title="translations.addFieldToTemplate"
                    data-toggle="tooltip"
                ></span>
            </div>
            <div class="card" v-if="fields.length === 0">
                <div class="card-body">
                    {{ translations.addAnyFieldsToCreateForm }}
                </div>
            </div>
        </div>
        <div class="form-field-option-legends">
            <div v-for="field in availableFields">
                <div v-for="(option, optionName) in field.options">
                    <div
                        class="form-field-option-legend"
                        :data-option-legend-name="field.name + '_' + optionName"
                        v-bind:class="{ 'd-block': option.focused }"
                    >
                        <div class="card">
                            <div class="card-header">{{ translations.controlOptionLabel }}</div>
                            <div class="card-body">
                                <span style="font-size:17px;">{{ option.name }}</span><br />
                                <i>{{ translations.name }}:</i> <code>{{ optionName }}</code> |
                                <span v-if="option.required"><i>{{ translations.required }}:</i> <code>yes</code> | </span>
<!--                                <span v-if="option.multilingual"><i>{{ translations.multilingual }}:</i> <code>{{ translations.yes }}</code> | </span>-->
                                <i>{{ translations.type }}:</i> <code>{{ option.type }}</code>
                                <div v-if="option.type === 'collection'">
                                    <br />{{ translations.valuesSeparatedByPipeAllowedFollowing }}<br />
                                    <span v-for="(val, key) in option.collection" class="pl-4">
                                        <b>{{ key }}</b> - {{ val }}<br />
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <h3>{{ translations.fieldsTemplate }}</h3>
        <p class="text-muted">{{ translations.fieldsTemplateInfo }}</p>
        <div class="card" v-if="fields.length === 0">
            <div class="card-body">
                {{ translations.addAnyFieldsToCreateForm }}
            </div>
        </div>
        <div class="form-group" v-else>
            <span class="invalid-feedback d-block" v-if="fieldsTemplate.error !== null">
                <span class="d-block">
                    <span class="form-error-icon badge badge-danger text-uppercase">Błąd</span>
                    <span class="form-error-message">{{ fieldsTemplate.error }}</span>
                </span>
            </span>
            <textarea
                id="form_form_template"
                name="form[fields_template]"
                v-bind:class="{ 'is-invalid': fieldsTemplate.error !== null }"
                style="height: 150px; font-family: monospace; font-size: 15px;"
                class="form-control"
                autocomplete="off"
                v-model="fieldsTemplate.value"
            ></textarea>
        </div>
    </div>
</template>

<script>
export default {
    name: "App",
    data() {
        let availableFields = window.ContactFormBuilder.availableFields;

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
        }
    },
    methods: {
        addInput: function (alias) {
            let field = {
                alias: alias,
                options: {}
            };

            for (let i in this.availableFields[alias].options) {
                field.options[i] = {
                    name: i,
                    value: '',
                    error: null,
                };
            }

            this.fields.push(field);
        },
        removeField: function (key) {
            this.fields.splice(key, 1);
        },
        resizeInput: function (event) {
            event.target.style.width = ((event.target.value.length + 0.2) * 8) + 'px';
        },
        showLegend: function (alias, option) {
            this.availableFields[alias].options[option].focused = true;
        },
        hideLegend: function (alias, option) {
            this.availableFields[alias].options[option].focused = false;
        },
        addFieldToTemplate: function (key) {
            let textarea = $('#form_form_template');
            let cursorPos = textarea.prop('selectionStart');
            let v = textarea.val();
            let textBefore = v.substring(0,  cursorPos );
            let textAfter  = v.substring( cursorPos, v.length );
            textarea.val(textBefore + '[' + this.fields[key].options.name.value + ']' + textAfter);
        }
    },
    mounted: function () {
        // Set default width form existing fields.
        for (let input of this.$el.querySelectorAll('input.form-control')) {
            this.resizeInput({
                target: input
            });
        }
    }
};
</script>

