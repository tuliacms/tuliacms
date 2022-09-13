<template>
    <Select v-model="block.data.headline_justify" :choices="choices" :label="translator.trans('Headline align', 'TuliaLisa')"></Select>
    <FormSelect v-model="block.data.form_id" :label="translator.trans('Form', 'TuliaLisa')"></FormSelect>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').manager(props);
const translator = inject('translator');

const Select = block.control('Select');
const FormSelect = block.control('FormSelect');
const choices = {
    left: 'Left',
    center: 'Center',
    right: 'Right',
};

block.on('created', () => {
    block.expectsFullWidthSection();
});
</script>
