<template>
    <div :class="{ 'cbb-block': true }">
        <div class="cbb-block-header">
            <div class="cbb-sortable-handler"><i class="fas fa-arrows-alt"></i></div>
            <div class="cbb-block-name" @click="$root.$emit('block:edit', block.id)">
                <span class="cbb-name">{{ block.name }}</span>
                <span class="cbb-type">{{ type.name }}</span>
            </div>
            <div class="cbb-block-options">
                <span @click="$root.$emit('block:edit', block.id)" v-bs-tooltip :title="translations.editBlock"><i class="fas fa-pen"></i></span>
                <span class="cbb-btn-hover-danger" @click="$root.$emit('block:remove', block.id)" v-bs-tooltip :title="translations.removeBlock"><i class="fas fa-trash"></i></span>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['block', 'type', 'translations']
}
</script>
