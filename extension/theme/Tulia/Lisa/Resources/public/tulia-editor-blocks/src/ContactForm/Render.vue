<template>
    <div class="block block-bg-lightgray block-contact">
        <div class="container-xxl">
            <div class="row">
                <div class="col">
                    <div class="tulia-container-max-width">
                        <p :class="{ 'lead': true, 'text-center': block.data.headline_justify === 'center', 'text-end': block.data.headline_justify === 'right' }">{{ block.data.intro }}</p>
                        <h2 :class="{ 'text-center': block.data.headline_justify === 'center', 'text-end': block.data.headline_justify === 'right' }">{{ block.data.headline }}</h2>
                        <div v-html="block.data.content"></div>

                        <div class="tued-dynamic-block">[contact_form id="{{ block.data.form_id }}"]</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);
</script>
