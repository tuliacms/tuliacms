<template>
    <DynamicBlock type="theme_lisa_nodes_from_taxonomy" :data="block.data"></DynamicBlock>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
const DynamicBlock = block.extension('DynamicBlock');
</script>
