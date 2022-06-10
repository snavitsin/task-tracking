<template>
  <div class="main-page">

    <kanban-board
    :tasks="tasks"
    :statuses="statuses"
    :editable="editable" />

  </div>
</template>

<script>

import KanbanBoard from './KanbanBoard';

export default {
  name: "MainPage",
  components: { KanbanBoard },
  props: {
    tasks: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    editable: { type: Boolean, default: () => true },
  },
  data() {
    return {
      filters: {

      },
    }
  },
  methods: {

  },

  computed: {

  }
}
</script>

<style lang="scss">
.main-page {

}

</style>