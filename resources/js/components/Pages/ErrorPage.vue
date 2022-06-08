<template>
  <div class="error-page">
    <div class="error-page__content">
      <div
      v-text="title"
      class="error-page__content-title" />
      <div
      v-text="code"
      class="error-page__content-code" />
    </div>
  </div>
</template>

<script>
import draggable from 'vuedraggable'
import Modal from '../Modal';

export default {
  name: "TaskPage",
  components: { draggable, Modal },
  props: {
    title: { type: String, default: () => ''},
    code: { type: String, default: () => ''},
  },
  data() {
    return { }
  },
  methods: { }
}
</script>

<style lang="scss">
.error-page {
  &__content {
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;

    &-title {
      font-size: 20px;
    }

    &-code {
      font-size: 24px;
      font-weight: bold;
    }
  }
}
</style>