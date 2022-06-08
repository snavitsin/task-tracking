<template>
  <div class="task">
    <div class="task__table">
      <data-table
      :header="header"
      :body="[task]"
      class="task-list__table"/>
    </div>

    <div class="task__table">
      <data-table
      :header="commentsHeader"
      :body="comments"
      class="task-list__table"/>
    </div>

    <div class="task__comment">
      <field-textarea
      v-model="commentText"
      :title="'Текст комментария'"
      :placeholder="'Введите текст комментария'"
      :data-vv-as="'Текст комментария'"
      name="commentText"
      class="task__textarea"/>

      <button
      @click="createTask"
      v-text="'Создать комментарий'"
      :disabled="!emptyText"
      class="button button--positive"/>
    </div>
  </div>
</template>

<script>
import DataTable from './DataTable';
import FieldTextarea from './FieldTextarea';

export default {
  name: "Task",
  components: { DataTable, FieldTextarea },

  props: {
    header: { type: Array, default: [] },
    task: { type: Object, default: null },
    commentsHeader: { type: Array, default: [] },
    comments: { type: Array, default: [] },
    isLoading: { type: Boolean, default: false },
  },
  data() {
    return {
      commentText: '',
    }
  },
  methods: {
    createTask(){
      this.$emit('comment:create', this.commentText);
    }
  },

  computed: {
    emptyText(){
      return this.commentText.length;
    }
  }
}
</script>

<style lang="scss">

.task {

  &__comment {
    margin-top: 20px;
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: flex-end;
  }

  &__comment > * {
    margin-left: 20px;
    width: auto;
    min-width: 250px;
    max-width: 600px;
  }

  &__textarea {
    min-width: 600px;
  }

}
</style>