<template>
    <div :class="{ 'cbb-block': true, 'cbb-block-type-not-found': !type }">
        <div class="cbb-block-header">
            <div class="cbb-sortable-handler"><i class="fas fa-arrows-alt"></i></div>
            <div class="cbb-block-name" @click="editBlock(block.id)">
                <span class="cbb-name">{{ block.name }}</span>
                <span v-if="type" class="cbb-type">{{ type.name }}</span>
                <span v-else class="cbb-type"><b>{{ translations.missingBlockType }}</b> | {{ block.type }}</span>
            </div>
            <div class="cbb-block-options">
                <span v-if="type" @click="$root.$emit('block:edit', block.id)" v-bs-tooltip :title="translations.editBlock"><i class="fas fa-pen"></i></span>
                <span class="cbb-btn-hover-danger" @click="$root.$emit('block:remove', block.id)" v-bs-tooltip :title="translations.removeBlock"><i class="fas fa-trash"></i></span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['block', 'type', 'translations'],
    methods: {
        editBlock: function (id) {
            if (this.type) {
                this.$root.$emit('block:edit', id);
            } else {
                Tulia.Info.info({
                    title: this.translations.cannotEditThisBlock,
                    text: this.translations.cannotEditBlockWhenContentTypeNotExists
                });
            }
        }
    }
}
</script>
