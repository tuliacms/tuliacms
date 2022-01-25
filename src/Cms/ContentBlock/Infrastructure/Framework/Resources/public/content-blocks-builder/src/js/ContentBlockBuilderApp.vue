<template>
    <div class="content-block-builder">
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
        <BlockCreator
            :block_types="block_types"
            :translations="translations"
            @confirm="createBlockFromModal"
        ></BlockCreator>
        <hr />
        <textarea :name="field_name" v-model="model_result"></textarea>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import Block from './components/Block';
import BlockCreator from './components/BlockCreator';

export default {
    name: 'ContentBlockBuilder',
    data() {
        let model = window.ContentBlockBuilder.field_value;
        model = null;

        if (! model) {
            model = {
                blocks: [
                    {
                        id: 'waertvsertyv',
                        type: 'text',
                        name: 'Wprowadzenie',
                        visible: true,
                        fields: {
                            tytul: ['Tytuł'],
                            tresc: ['<p><b>Moja</b> treść!</p>'],
                        }
                    },
                    {
                        id: 'dsfgsghdfgh',
                        type: 'image',
                        name: 'Zdjęcie w nagłówku',
                        visible: false,
                        fields: {
                            image: ['/path/to/image.jpg'],
                        }
                    },
                    {
                        id: 'sdfgsdfg',
                        type: 'text',
                        name: 'Treść główna',
                        visible: true,
                        fields: {
                            image: ['<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sollicitudin, mi ultrices vestibulum placerat, massa metus condimentum ipsum, tempus lacinia velit justo nec libero.</p>'],
                        }
                    },
                ]
            };
        }

        return {
            translations: window.ContentBlockBuilder.translations,
            block_types: window.ContentBlockBuilder.block_types,
            field_name: window.ContentBlockBuilder.field_name,
            model_result: JSON.stringify(model),
            view: {
                modal: {
                    block_creator: null,
                },
            },
            model: model,
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
        BlockCreator
    },
    methods: {
        save: function () {
            this.model_result = JSON.stringify(this.model);
        },
        removeBlock: function (blockId) {
            Tulia.Confirmation.warning().then((result) => {
                if (!result.value) {
                    return;
                }

                for (let i in this.model.blocks) {
                    if (this.model.blocks[i].id === blockId) {
                        this.model.blocks.splice(i, 1);
                    }
                }
            });
        },
        openCreateBlockModel: function () {
            this.view.modal.block_creator.show();

            this.$root.$emit('block:create:modal:opened');
        },
        createBlockFromModal: function (block) {
            this.model.blocks.push({
                id: _.uniq(),
                type: block.type,
                name: block.name,
                visible: true,
                fields: {}
            });

            this.view.modal.block_creator.hide();
            this.$forceUpdate();
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

        this.$root.$on('block:create', () => {
            this.openCreateBlockModel();
        });
        this.$root.$on('block:remove', (blockId) => {
            this.removeBlock(blockId);
        });
    }
};
</script>

