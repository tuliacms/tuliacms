<template>
    <div class="block block-what-we-do">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12 col-lg-6 order-lg-1">
                    <div class="block-content">
                        <p class="lead"><Contenteditable v-model="block.data.intro"></Contenteditable></p>
                        <h2><Contenteditable v-model="block.data.headline"></Contenteditable></h2>
                        <WysiwygEditor v-model="block.data.content"></WysiwygEditor>
                        <ul>
                            <li
                                v-for="item in contentList.collection"
                                :key="item.id"
                            >
                                <strong class="mb-2 d-block"><Contenteditable v-model="item.lead"></Contenteditable></strong>
                                <Contenteditable v-model="item.paragraph"></Contenteditable>
                                <Actions actions="moveUp,moveDown,remove" :collection="contentList" :item="item"></Actions>
                            </li>
                            <li>
                                <Actions actions="add" :collection="contentList"></Actions>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-12 col-lg-6 order-lg-0 block-images" style="padding-top:400px">
                    <BackgroundImage
                        class="block-image block-image-main"
                        v-model="block.data.image_above"
                        placement="above"
                    ></BackgroundImage>
                    <BackgroundImage
                        class="block-image block-image-sub"
                        v-model="block.data.image_under"
                        placement="under"
                    ></BackgroundImage>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const { defineProps, inject, watch } = require('vue');
const props = defineProps(['block']);
const block = inject('blocks.instance').editor(props);

const translator = inject('translator');
const BackgroundImage = block.extension('BackgroundImage');
const WysiwygEditor = block.extension('WysiwygEditor');
const Contenteditable = block.extension('Contenteditable');
const Collection = block.extension('Collection');
const Actions = block.extension('Collection.Actions');

const contentList = new Collection(block.data.content_list, {
    lead: 'Mauris tincidunt convallis',
    paragraph: 'Nunc ut dictum quam. Mauris tincidunt convallis lectus sed lacinia.',
});
</script>
