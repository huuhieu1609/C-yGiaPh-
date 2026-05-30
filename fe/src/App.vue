<template>
  <component :is="layout">
      <router-view></router-view>
  </component>
  <ScrollToTop />
</template>

<script>
import ScrollToTop from './components/ScrollToTop.vue';

const default_layout = "blank";
export default {
  components: {
    ScrollToTop
  },
  computed: {
    layout() {
      return (this.$route.meta.layout || default_layout) + "-layout";
    },
  },
};
</script>
<style>
</style>