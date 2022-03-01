<template>
    <div class="content-block-builder">
        <div class="cbb-toolbar">
            <button type="button" class="btn btn-primary btn-icon-left" @click="openImportModal()">
                <i class="btn-icon fas fa-upload"></i>
                {{ translations.import }}
            </button>
            <button type="button" class="btn btn-primary btn-icon-left" @click="exportToJson()">
                <i class="btn-icon fas fa-download"></i>
                {{ translations.export }}
            </button>
        </div>
        <div class="cbb-blocks-list">
            <draggable class="cbb-sortable-fields" v-bind="dragOptions" :list="model.blocks" handle=".cbb-sortable-handler" group="fields" @start="drag=true" @end="drag=false" ghost-class="cbb-draggable-ghost">
                <transition-group type="transition" :name="!drag ? 'flip-list' : null" class="cbb-sortable-placeholder" tag="div" :data-label="translations.addNewBlock">
                    <Block
                        v-for="block in model.blocks"
                        :key="block.id"
                        :block="block"
                        :type="block_types[block.type]"
                        :translations="translations"
                    ></Block>
                </transition-group>
            </draggable>
        </div>
        <button type="button" class="btn btn-success btn-icon-left" @click="$root.$emit('block:create')">
            <i class="btn-icon fas fa-plus"></i>
            {{ translations.addBlock }}
        </button>
        <ExportJson
            :json="model_json"
            :translations="translations"
        ></ExportJson>
        <ImportJson
            :translations="translations"
            @import="importJson"
        ></ImportJson>
        <BlockCreator
            :block_types="block_types"
            :translations="translations"
            @confirm="createBlockFromModal"
        ></BlockCreator>
        <BlockEditor
            :block="view.form.block_editor"
            :block_types="block_types"
            :translations="translations"
            :settings="settings"
            @confirm="updateBlockFromModal"
        ></BlockEditor>
        <textarea :name="field_name" v-model="model_result" style="display:none;"></textarea>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Block from './components/Block';
import BlockCreator from './components/BlockCreator';
import BlockEditor from './components/BlockEditor';
import ExportJson from './components/ExportJson';
import ImportJson from './components/ImportJson';

export default {
    name: 'ContentBlockBuilder',
    data() {
        let model = {};
        let source = window.ContentBlockBuilder.field_value;

        if (source && source.indexOf('[content_block_render ') === 0) {
            let matches = source.match(/source="([^"]+)"/);

            if (matches[1]) {
                model = JSON.parse(decodeURIComponent(escape(atob(matches[1]))));
            }
        }

        if (! model.blocks) {
            model.blocks = [];
        }

        function decodeHTMLEntities (text) {
            return $('<textarea/>').html(text).text();
        }

        for (let i in window.ContentBlockBuilder.translations) {
            window.ContentBlockBuilder.translations[i] = decodeHTMLEntities(window.ContentBlockBuilder.translations[i]);
        }

        return {
            translations: window.ContentBlockBuilder.translations,
            block_types: window.ContentBlockBuilder.block_types,
            field_name: window.ContentBlockBuilder.field_name,
            settings: {
                field_name_pattern: window.ContentBlockBuilder.field_name_pattern,
                cors_domain: window.ContentBlockBuilder.cors_domain,
            },
            model_result: JSON.stringify(model),
            view: {
                modal: {
                    block_creator: null,
                    block_editor: null,
                    export_json: null,
                    import_json: null,
                },
                form: {
                    block_editor: {
                        name: null,
                        block_panel_url: null,
                        fields: {},
                    },
                },
            },
            model: model,
            model_json: '',
        };
    },
    computed: {
        dragOptions() {
            return {
                animation: 200,
                group: 'fields',
                disabled: false,
                ghostClass: 'cbb-draggable-ghost'
            };
        }
    },
    components: {
        draggable,
        Block,
        BlockCreator,
        BlockEditor,
        ExportJson,
        ImportJson,
    },
    methods: {
        save: function () {
            this.model_result = '[content_block_render source="' + btoa(unescape(encodeURIComponent(JSON.stringify(this.model)))) + '"]';
        },
        exportToJson: function () {
            this.model_json = JSON.stringify(this.model, undefined, 4);
            this.view.modal.export_json.show();

            this.$root.$emit('export:modal:opened');
        },
        openImportModal: function () {
            this.view.modal.import_json.show();

            this.$root.$emit('import:modal:opened');
        },
        importJson: function (json) {
            this.model = JSON.parse(json);
            this.view.modal.import_json.hide();
        },
        removeBlock: function (blockId) {
            Tulia.Confirmation.warning().then((result) => {
                if (! result.value) {
                    return;
                }

                for (let i in this.model.blocks) {
                    if (this.model.blocks[i].id === blockId) {
                        this.model.blocks.splice(i, 1);
                    }
                }
            });
        },
        duplicateBlock: function (blockId) {
            let block = this._findBlock(blockId);
            let newBlock = JSON.parse(JSON.stringify(block));
            newBlock.id = this.uniqueId();

            for (let i in this.model.blocks) {
                if (this.model.blocks[i].id === blockId) {
                    this.model.blocks.splice(i, 0, newBlock);
                    break;
                }
            }
        },
        openCreateBlockModel: function () {
            this.view.modal.block_creator.show();

            this.$root.$emit('block:create:modal:opened');
        },
        openEditBlockModel: function (blockId) {
            let block = this._findBlock(blockId);
            let type = this._findType(block.type);

            this.view.form.block_editor.id = block.id;
            this.view.form.block_editor.name = block.name;
            this.view.form.block_editor.type = block.type;
            this.view.form.block_editor.fields = block.fields;
            this.view.form.block_editor.block_panel_url = type.block_panel_url;
            this.view.modal.block_editor.show();

            this.$root.$emit('block:edit:modal:opened');
        },
        createBlockFromModal: function (block) {
            this.model.blocks.push({
                id: this.uniqueId(),
                type: block.type,
                name: block.name,
                visible: true,
                fields: {}
            });

            this.view.modal.block_creator.hide();
            this.$forceUpdate();
        },
        updateBlockFromModal: function (blockId, blockData) {
            let block = this._findBlock(blockId);

            for (let i in blockData.fields) {
                block.fields[i] = blockData.fields[i];
            }
            block.name = blockData.name;

            this.view.modal.block_editor.hide();
            this.$forceUpdate();
        },
        _findBlock: function (blockId) {
            for (let i in this.model.blocks) {
                if (this.model.blocks[i].id === blockId) {
                    return this.model.blocks[i];
                }
            }
        },
        _findType: function (typeCode) {
            return this.block_types[typeCode];
        },
        uniqueId: function () {
            const idExists = (id) => {
                for (let i in this.model.blocks) {
                    if (this.model.blocks[i].id === id) {
                        return true;
                    }
                }

                return false;
            };

            let id;

            do {
                id = _.uniqueId('block_')
            } while (idExists(id));

            return id;
        }
    },
    mounted: function () {
        let self = this;
        let id = $('.content-block-builder').closest('form').attr('id');

        $('[data-submit-form=' + id + ']').mousedown(function (e) {
            self.save();
            e.preventDefault();
            e.stopPropagation();
        });

        let creationModal = document.getElementById('cbb-create-block-modal');
        this.view.modal.block_creator = new bootstrap.Modal(creationModal);

        creationModal.addEventListener('shown.bs.modal', function () {
            $(creationModal).find('.cbb-autofocus').focus();
        });

        let EditionModal = document.getElementById('cbb-edit-block-modal');
        this.view.modal.block_editor = new bootstrap.Modal(EditionModal);

        EditionModal.addEventListener('shown.bs.modal', function () {
            $(EditionModal).find('.cbb-autofocus').focus();
        });

        this.view.modal.export_json = new bootstrap.Modal(document.getElementById('cbb-export-json-modal'));
        this.view.modal.import_json = new bootstrap.Modal(document.getElementById('cbb-import-json-modal'));

        this.$root.$on('block:create', () => {
            this.openCreateBlockModel();
        });
        this.$root.$on('block:edit', (blockId) => {
            this.openEditBlockModel(blockId);
        });
        this.$root.$on('block:duplicate', (blockId) => {
            this.duplicateBlock(blockId);
        });
        this.$root.$on('block:remove', (blockId) => {
            this.removeBlock(blockId);
        });
    }
};
</script>

