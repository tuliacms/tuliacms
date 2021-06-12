<template>
    <div class="app">
        <h3>{{ translations.availableFields }}</h3>
        <p>{{ translations.fieldsBuilderInfo }}</p>
        <button
            type="button"
            class="btn btn-success mr-2"
            v-for="item in availableFields"
            v-on:click="addInput(item.name)"
        >{{ item.label }}</button>
        <hr />
        <h3>{{ translations.fieldsBuilder }}</h3>
        <div class="contact-form-fields-builder">
            <div
                v-for="(field, key) in fields"
                class="form-field-prototype"
                :data-field-name="field.type"
            >
                <input
                    type="hidden"
                    :name="'form[fields][' + key + '][type]'"
                    v-model="field.type"
                />
                <span v-on:click="removeField(key)" class="field-remove fas fa-window-close"></span>
                [{{ field.type }}<!--
                    --><span
                        v-for="(option, name) in availableFields[field.type].options"
                        v-bind:class="{ 'text-danger': fields[key]['options'][name].error !== null }"
                        :title="fields[key]['options'][name].error"
                        data-toggle="tooltip"
                    ><!--
                    -->&nbsp;<label v-bind:class="{ 'field-optional': !option.required }">{{ name }}="<input
                        type="text"
                        :data-option-name="name"
                        :data-option-key="key"
                        class="form-control"
                        :name="'form[fields][' + key + '][' + name + ']'"
                        v-model="fields[key]['options'][name].value"
                        @change="resizeInput"
                        @input="resizeInput"
                        @focus="showLegend(field.type, name)"
                        @blur="hideLegend(field.type, name)"
                    />"</label><!--
                --></span>]
            </div>
            <div class="card" v-if="fields.length === 0">
                <div class="card-body">
                    {{ translations.addAnyFieldsToCreateForm }}
                </div>
            </div>
        </div>
        <div class="form-field-option-legends">
            <div v-for="field in availableFields">
                <span v-for="(option, optionName) in field.options">
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
        }
    },
    methods: {
        addInput: function (type) {
            let field = {
                type: type,
                options: {}
            };

            for (let i in this.availableFields[type].options) {
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
        showLegend: function (type, option) {
            this.availableFields[type].options[option].focused = true;
        },
        hideLegend: function (type, option) {
            this.availableFields[type].options[option].focused = false;
        }
    },
    mounted: function () {
        // Set default width form existing fields.
        for (let input of this.$el.querySelectorAll('.form-control')) {
            this.resizeInput({
                target: input
            });
        }
    }
};
</script>

