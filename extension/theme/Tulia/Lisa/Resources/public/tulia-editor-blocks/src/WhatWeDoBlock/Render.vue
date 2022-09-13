<template>
    <div class="block block-what-we-do">
        <div class="row">
            <div class="col-12 col-lg-6 order-lg-1">
                <div class="block-content">
                    <p class="lead">{{ block.data.intro }}</p>
                    <h2>{{ block.data.headline }}</h2>
                    <ul>
                        <li
                            v-for="(item, key) in block.data.content_list"
                            :key="key"
                        >
                            <strong>{{ item.lead }}</strong>
                            <br />
                            {{ item.paragraph }}
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-12 col-lg-6 order-lg-0 block-images">
                <div class="block-image block-image-main" :id="imageAbove.id"></div>
                <div class="block-image block-image-sub" :id="imageUnder.id"></div>
            </div>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, watch } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').render(props);

const BackgroundImage = block.extension('BackgroundImage');
const imageAbove = new BackgroundImage(block, () => block.data.image_above, 'above');
const imageUnder = new BackgroundImage(block, () => block.data.image_under, 'under');
</script>
