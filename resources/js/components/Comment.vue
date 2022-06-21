<template>
  <div class="comment">
    <div class="comment__body">
      <field-container
      :is-required="false"
      v-if="!isNewComment"
      title="Автор"
      class="comment-page__field">
        <field-input
        v-model="author"
        :data-vv-as="' '"
        readonly/>
      </field-container>
      <field-textarea
      v-model="commentData.comment_comment"
      v-validate="'required'"
      :data-vv-as="' '"
      placeholder="Комментарий"
      max="300" />
    </div>
    <div class="comment__controls">
      <button
      v-if="isCommentOwner"
      :disabled="!commentData.comment_comment"
      @click="saveComment"
      v-text="saveButtonText"
      class="button button--positive project-page__control"/>
      <button
      v-if="isCommentOwner"
      v-text="'Отменить изменения'"
      @click="cancelChanges"
      class="button button--neutral project-page__control"/>
      <button
      v-if="!isNewComment && isCommentOwner"
      @click="isConfirmModalShown = true"
      v-text="'Удалить'"
      class="button button--negative task-page__control"/>
    </div>

    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="comment__modal">
      <div
      v-text="'Вы уверены, что хотите удалить комментарий?'"
      class="comment__modal-text"/>

      <template #buttons>
        <div class="comment__modal-controls">
          <button
          v-text="'Удалить'"
          @click="handleConfirmation()"
          class="button button--negative comment__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral comment__button"/>
        </div>
      </template>
    </modal>
  </div>
</template>

<script>
import {format} from "date-fns";
import FieldTextarea from "./Fields/FieldTextarea";
import Modal from "./Modal";
import FieldContainer from "./Fields/FieldContainer";
import FieldInput from "./Fields/FieldInput";
import {cloneDeep} from "lodash";

export default {
  name: "Comment",
  components: { Modal, FieldContainer, FieldInput, FieldTextarea },
  props: {
    comment: { type: Object, default: () => {} },
    isNewComment: { type: Boolean, default: () => false },
    taskId: { type: Number, default: () => null },
  },
  data() {
    return {
      commentData: this.comment,
      isConfirmModalShown: false,
      emptyComment: {
        comment_comment: 'Текст комментария',
        comment_task: this.taskId,
      },
    }
  },
  methods: {
    async saveComment() {

      this.$store.state.isLoading = true;
      const params = { ...this.commentData };
      const res = await this.$store.dispatch('fetchData', { url: '/comments/save', params });

      if(res?.errors) {
        const self = this;
        Object.keys(res.errors).forEach(function(key) {
          self.$notify({
            type: 'error',
            text: res.errors[key].shift(),
          });
        });
      } else {
        this.$notify({
          type: res.type,
          text: res.message
        });

        if(res.status === true) {
          window.location.reload();
        }
      }
      this.$store.state.isLoading = false;
    },
    async deleteComment() {
      this.$store.state.isLoading = true;
      const params = { comment_id: this.commentData.comment_id };
      const res = await this.$store.dispatch('fetchData', { url: '/comments/delete', params });

      if(res?.errors) {
        const self = this;
        Object.keys(res.errors).forEach(function(key) {
          self.$notify({
            type: 'error',
            text: res.errors[key].shift(),
          });
        });
      } else {
        this.$notify({
          type: res.type,
          text: res.message
        });

        if(res.status === true) {
          window.location.reload();
        }
      }
      this.$store.state.isLoading = false;
    },
    cancelChanges() {
      this.commentData = this.isNewComment ? cloneDeep(this.emptyComment) : cloneDeep(this.comment);
    },
    handleConfirmation() {
      this.deleteComment();
      this.isConfirmModalShown = false;
    },
    handleCancel() {
      this.isConfirmModalShown = false;
    },
  },
  computed: {
    author() {
      if(this.isNewComment) return ``;
      return this.commentData.comment_author;
    },
    isCommentOwner() {
      if(this.isNewComment) return true;
      return this.commentData.comment_emp === this.$store.state.user.emp_id;
    },
    saveButtonText() {
      return this.isNewComment === true ? 'Создать' : 'Сохранить';
    }
  },
  created() {
    this.commentData = this.isNewComment ? cloneDeep(this.emptyComment) : cloneDeep(this.commentData);
  }
}
</script>

<style lang="scss">
.comment {

  --input-height: 60px;
  --textarea-height: 120px;

  &__body {
    > * + * {
      margin-top: 10px;
    }
  }

  &__field {

  }

  &__controls {
    display: flex;
    margin-left: -10px;
    margin-right: -10px;
  }

  &__modal {
    &-text {
      margin: 10px;
      font-size: 18px;
    }

    &-controls {
      display: flex;
      justify-content: space-between;

      @media (max-width: 480px) {
        flex-wrap: wrap;
      }
    }

    &-button {
      flex: 1 1 auto;
      margin: 10px;
      min-width: 200px;
    }
  }

  &__button {
    flex: 1 1 auto;
    margin: 10px;
    min-width: 200px;
  }
}
</style>