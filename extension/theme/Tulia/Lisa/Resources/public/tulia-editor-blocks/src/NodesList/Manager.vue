<template>
    <TaxonomySelect v-model="block.data.taxonomy" :label="translator.trans('Category of nodes', 'TuliaLisa')"></TaxonomySelect>
    <Input v-model="block.data.number_of_nodes" :label="translator.trans('Number of nodes', 'TuliaLisa')"></Input>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');

const TaxonomySelect = block.control('TaxonomySelect');
const Input = block.control('Input');
</script>
