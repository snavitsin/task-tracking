<template>
  <div class="task-page">
    <div
    v-text="'Задача'"
    class="task-page__page-title" />

    <div class="task-page__content">
      <div class="task-page__main">
        <div class="task-page__header">
          <div class="task-page__task-title">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('task_title')"
            title="Заголовок"
            class="task-page__field task-page__title">
              <field-input
              v-model="taskData.task_title"
              v-validate="'required'"
              :isError="veeErrors.has('task_title')"
              :data-vv-as="' '"
              maxlength="255"
              name="task_title" />
            </field-container>
          </div>
          <div class="task-page__combos">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('task_priority')"
            title="Приоритет"
            class="task-page__field">
              <field-dropdown
              class="task-page__dropdown"
              v-model="taskData.task_priority"
              :data="priority"
              :disabled="!isManager"
              searchable
              placeholder="Приоритет"
              value-name="priority_id"
              label-name="priority_title" />
            </field-container>

            <field-container
            :is-required="true"
            :errors="veeErrors.collect('task_status')"
            title="Статус"
            class="task-page__field">
              <field-dropdown
              class="task-page__dropdown"
              v-model="taskData.task_status"
              :data="statuses"
              :disabled="!editable"
              searchable
              placeholder="Статус"
              value-name="status_id"
              label-name="status_title" />
            </field-container>
          </div>
        </div>

        <div
        class="task-page__controls">
          <button
          @click="saveTask"
          v-text="saveButtonText"
          class="button button--positive task-page__control"/>
          <button
          @click="cancelChanges"
          v-text="'Отменить изменения'"
          class="button button--neutral task-page__control"/>
          <button
          v-if="!isNewTask && isManager"
          @click="isConfirmModalShown = true"
          v-text="'Удалить'"
          class="button button--negative task-page__control"/>
        </div>
        <div class="task-page__description">
          <field-container
          :is-required="true"
          :errors="veeErrors.collect('task_desc')"
          title="Описание"
          class="report-answer-quality__field">
            <field-textarea
            v-model="taskData.task_desc"
            :data-vv-as="' '"
            :is-error="veeErrors.has('task_desc')"
            :disabled="!editable"
            placeholder="Описание"
            name="task_desc"
            max="300"
            class="task-page__textarea"/>
          </field-container>
        </div>
        <div class="task-page__resolution">
          <field-container
          :is-required="false"
          :errors="veeErrors.collect('task_resolution')"
          title="Решение"
          class="report-answer-quality__field">
            <field-textarea
            v-model="taskData.task_resolution"
            :data-vv-as="' '"
            :is-error="veeErrors.has('task_resolution')"
            :disabled="!canEditResolution"
            placeholder="Решение"
            name="task_resolution"
            max="5000"
            class="task-page__textarea"/>
          </field-container>
        </div>
        <div
        v-if="comments.length"
        class="task-page__comments">
          <div
          v-text="'Комментарии'"
          class="task-page__comments-title" />
          <div
          v-for="comment in comments"
          :key="comment.comment_id"
          class="task-page__comment">
            <div
            v-text="comment.comment_author"
            class="task-page__comment-author" />
            <div
            v-text="comment.comment_comment"
            class="task-page__comment-text" />
          </div>
        </div>
      </div>

      <div class="task-page__side">
        <field-container
        :is-required="true"
        :errors="veeErrors.collect('task_project')"
        title="Проект"
        class="task-page__field">
          <field-dropdown
          class="task-page__dropdown"
          v-model="taskData.task_project"
          :data="projects"
          :disabled="!isNewTask"
          searchable
          placeholder="Проект"
          value-name="project_id"
          label-name="project_title" />
        </field-container>

        <div class="task-page__operators">
          <field-container
          title="Автор задачи"
          class="task-page__field">
            <field-input
            v-model="taskAuthor"
            :data-vv-as="' '"
            readonly
            name="task_title" />
          </field-container>

          <field-container
          :is-required="true"
          :errors="veeErrors.collect('task_dev')"
          title="Разработчик"
          class="task-page__field">
            <field-dropdown
            class="task-page__dropdown"
            v-model="taskData.task_dev"
            :data="developers"
            :disabled="!isManager"
            searchable
            placeholder="Разработчик"
            value-name="emp_id"
            label-name="emp_fio" />
          </field-container>

          <field-container
          :is-required="true"
          :errors="veeErrors.collect('task_tester')"
          title="Тестировщик"
          class="task-page__field">
            <field-dropdown
            class="task-page__dropdown"
            v-model="taskData.task_tester"
            :data="testers"
            :disabled="!isManager"
            searchable
            placeholder="Тестировщик"
            value-name="emp_id"
            label-name="emp_fio" />
          </field-container>
        </div>
        <div class="task-page__info">

          <field-container
          v-if="!isNewTask"
          :errors="veeErrors.collect('task_created')"
          title="Дата создания"
          class="task-page__field task-page__datepicker">
            <field-datepicker
            v-model="taskData.task_created"
            v-validate="'required'"
            :data-vv-as="'Дата создания'"
            formatter="dd.MM.yyyy"
            readonly
            :disabled="true" />
          </field-container>

          <field-container
          :is-required="true"
          :errors="veeErrors.collect('task_deadline')"
          title="Срок выполнения"
          class="task-page__field task-page__datepicker">
            <field-datepicker
            v-model="taskData.task_deadline"
            v-validate="'required'"
            :data-vv-as="'Срок выполнения'"
            formatter="dd.MM.yyyy"
            :readonly="isManager"/>
          </field-container>
        </div>

      </div>
    </div>

    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="task-page__modal">
      <div
      v-text="'Вы уверены, что хотите удалить задачу?'"
      class="task-page__modal-text"/>

      <template #buttons>
        <div class="task-page__modal-controls">
          <button
          v-text="'Удалить'"
          @click="handleConfirmation()"
          class="button button--negative task-page__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral task-page__button"/>
        </div>
      </template>
    </modal>

  </div>
</template>

<script>
import FieldContainer from "../Fields/FieldContainer";
import FieldDropdown from "../Fields/FieldDropdown";
import FieldInput from "../Fields/FieldInput";
import FieldTextarea from "../Fields/FieldTextarea";
import FieldDatepicker from "../Fields/FieldDatepicker";

import Modal from '../Modal';

export default {
  name: "TaskPage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput, FieldTextarea, FieldDatepicker },
  props: {
    task: { type: Object, default: () => {} },
    taskEmps: { type: Array, default: () => [] },
    comments: { type: Array, default: () => [] },
    priority: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    developers: { type: Array, default: () => [] },
    testers: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    isManager: { type: Boolean, default: () => false },
    isTaskOperator: { type: Boolean, default: () => false },
    isNewTask: { type: Boolean, default: () => false },

  },
  data() {
    return {
      taskData: this.task,
      drag: false,
      filters: {},
      mappedTasks: [],
      selectedTask: null,
      newStatus: null,
      isConfirmModalShown: false,
      emptyTask: {
        task_title: 'Новая задача',
        task_desc: 'Описание'
      }
    }
  },
  methods: {

    async saveTask() {
      this.$store.state.isLoading = true;
      const params = { ...this.taskData };
      const res = await this.$store.dispatch('fetchData', { url: '/tasks/save', params });

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
          //await this.getTasks();
        }
      }
      this.$store.state.isLoading = false;
    },

    async deleteTask() {
      this.$store.state.isLoading = true;
      const params = { task_id: this.taskData.task_id };
      const res = await this.$store.dispatch('fetchData', { url: '/tasks/delete', params });

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
          window.location.href = `/`;
        }
      }
      this.$store.state.isLoading = false;
    },

    cancelChanges() {
      this.taskData = this.isNewTask ? this.emptyTask : this.task;
    },

    handleConfirmation() {
      this.deleteTask();
      this.isConfirmModalShown = false;
    },
    handleCancel() {
      this.isConfirmModalShown = false;
    },

  },
  computed: {
    editable() {
      return this.isManager || this.isTaskOperator;
    },
    canEditResolution () {
      return this.isManager || (this.isTaskOperator && this.$store.getters.checkRole('developer'));
    },
    saveButtonText() {
      return this.isNewTask === true ? 'Создать' : 'Сохранить';
    },
    taskAuthor() {
      const author = this.taskEmps.find(emp => emp.emp_position == 3);
      return author ? author.emp_fio : null;
    }
  },
  created() {
    if(this.isNewTask)
      this.taskData = this.emptyTask;
  }
}
</script>

<style lang="scss">
.task-page {

  --accent-color: #906fe9;


  > * + * {
    margin-top: 20px;
  }

  &__page{
    &-title {
      color: #906fe9;
      font-weight: bold;
      font-size: 24px;
    }
  }

  &__content {
    display: flex;
    justify-content: space-between;
  }

  &__header {
    > * + * {
      margin-top: 20px;
    }
  }

  &__combos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    grid-auto-rows: 1fr;
    gap: 20px;
  }

  &__title {
    --input-height: 60px;
  }

  &__controls {
    display: flex;
    margin-left: -10px;
    margin-right: -10px;
    margin-top: 10px;

    @media (max-width: 480px) {
      flex-wrap: wrap;
    }
  }

  &__resolution {
    .textarea {
      font-size: 15px;
    }
  }

  &__control {
    flex: 1 1 auto;
    margin: 10px;
    min-width: 200px;
  }

  &__side {
    flex: 0 1 auto;
    margin-left: 100px;

    > * + * {
      margin-top: 20px;
    }
  }

  &__project {

  }

  &__operators {
    > * + * {
      margin-top: 10px;
    }
  }

  &__operator {
    padding: 20px;
    background-color: #fff;
    border: 1px solid var(--field-dropdown-border-color, #D3D8DB);

    &-title {

    }

    &-user {

    }
  }

  &__info {
    > * + * {
      margin-top: 10px;
    }
  }

  &__field {

  }

  &__datepicker {
    .popover {
      position: inherit;
    }
  }

  &__date {
    padding: 10px;
    background-color: #fff;
    border: 1px solid var(--field-dropdown-border-color, #D3D8DB);
  }

  &__main {
    flex: 1 1 auto;

    > * + * {
      margin-top: 20px;
    }
  }




  &__column {
    padding: 10px;
    border: 3px solid var(--input-border-color, #906fe9);
    border-radius: 5px;
    box-shadow: 0 0 4px 4px rgb(154 161 177 / 15%), 0 4px 4px 1px rgb(91 94 105 / 15%), 0 4px 4px -2px rgb(91 94 105 / 15%);

    &-container {
      display: flex;
      height: 100%;
      flex-direction: column;

      > * + * {
        margin-top: 10px;
      }
    }

    &-title {
      font-weight: bold;
    }

    > * + * {
      margin-top: 10px;
    }
  }

  &__group {
    flex: 1 0 auto;

    > * + * {
      margin-top: 10px;
    }
  }

  &__task {
    &-number {
      float: left;
      color: #1fe09e;
      background-color: #f7f7f8;
      padding: 5px;
      border-radius: 5px;
      margin-right: 10px;

      &:hover {
        //color: #1fe09e;
        //text-decoration: underline;
      }
    }

    &-title {
      padding: 5px;
    }

    cursor: pointer;
    //background-color: #e7def9;
    background: rgb(144,111,233);
    background: linear-gradient(180deg, rgba(144,111,233,0.3) 0%, rgba(231,222,249,1) 100%);
    padding: 10px;
    border-radius: 5px;
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

  &__comments {
    &-title {
      font-weight: bold;
    }

    > * + * {
      margin-top: 10px;
    }
  }

  &__comment {
    padding: 10px;
    background: #fff;
    border: 1px solid #D3D8DB;
  }
}
</style>