<template>
  <div class="app">
    <app-header
    class="app__header container"/>

    <div class="app__content container">
      <slot/>
    </div>

    <div
    v-if="$store.state.isLoading"
    class="app__spinner">
      <main-loader
      text="Идет загрузка данных" />
    </div>

    <app-footer
    class="app__footer container"/>

    <portal-target
    name="modals"
    multiple />

    <notifications
    classes="notification-alert"/>
  </div>
</template>

<script>
import { forEach } from 'lodash';

import AppHeader from "./AppHeader";
import AppFooter from "./AppFooter";

export default {
  name: 'App',
  components: { AppFooter, AppHeader, },
  props: {
    vuexData: {type: Object},
  },
  methods: {
    setVuexData() {
      return forEach(this.vuexData, (value, path) =>
      this.$store.commit('setData', {path, value})
      );
    },
  },
  created() {
    this.setVuexData();
  },
  metaInfo() {
    return {
      title: 'Управление проектами'
    }
  },
}
</script>

<style lang="scss">
.app {
  display: flex;
  flex-direction: column;

  &__header {
    flex: 0 0 auto;
  }

  &__footer {
    flex: 0 0 auto;
  }
}
</style>
