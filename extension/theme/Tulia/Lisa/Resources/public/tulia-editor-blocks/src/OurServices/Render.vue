<template>
    <div class="block block-services block-bg-dark">
        <div class="container-xxl">
            <div class="row">
                <div class="col-12 col-lg-6">
                    <p class="lead">{{ block.data.intro }}</p>
                    <h2>{{ block.data.headline }}</h2>
                </div>
                <div class="col-12 col-lg-6">
                    <p class="services-slogan">{{ block.data.short_text }}</p>
                </div>
            </div>
            <div class="services-collection">
                <div class="row">
                    <div
                        class="col-12 col-sm-12 col-md-6 col-lg-4"
                        v-for="(service, key) in block.data.services"
                        :key="key"
                    >
                        <a v-if="service.link" :href="service.link" class="service-item service-item-hoverable">
                            <div class="service-icon"><i :class="service.icon"></i></div>
                            <h3>{{ service.title }}</h3>
                            <p class="mb-3">{{ service.content }}</p>
                        </a>
                        <div v-else class="service-item service-item-hoverable">
                            <div class="service-icon"><i :class="service.icon"></i></div>
                            <h3>{{ service.title }}</h3>
                            <p class="mb-3">{{ service.content }}</p>
                        </div>
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
