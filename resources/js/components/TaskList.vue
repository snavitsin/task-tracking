<template>
  <div class="task-list">
    <div class="task-list__content">

      <tabs
      v-model="selectedTab"
      :data="tabs"
      value-name="tab_id"
      label-name="tab_title"
      class="task-list__tabs"/>

      <template v-if="selectedTab === 1">

        <div class="task-list__controls">

          <template v-if="!isManager">
            <field-checkbox
            v-model="filters.own_tasks"
            :data="['Свои задачи']"
            label="Свои задачи"
            class="task-list__checkbox"/>
          </template>

          <field-dropdown
          v-if="filters.own_tasks === true || isManager === true"
          :data="statuses"
          v-model="selectedStatus"
          class="task-list__dropdown"/>

          <field-textarea
          v-if="filters.own_tasks === true || isManager === true"
          v-model="taskDesc"
          :title="'Описание задачи'"
          :data-vv-as="'Описание задачи'"
          name="taskDesc"
          class="task-list__textarea"/>

          <button
          v-if="filters.own_tasks === true || isManager === true"
          @click="editTask"
          v-text="'Редактировать'"
          class="button button--positive"/>

          <button
          v-if="isManager"
          @click="deleteTask"
          v-text="'Удалить'"
          class="button button--positive"/>
        </div>

        <data-table
        v-model="selectedTask"
        @table:select-row="handleRowSelect"
        :header="taskHeader"
        :body="tasks"
        :has-radio="isManager === true || filters.own_tasks"
        class="task-list__table"/>

      </template>

      <template v-if="selectedTab === 2">

        <div class="task-list__controls">

          <template v-if="isManager === true">
            <field-checkbox
            v-model="filters.own_comments"
            :data="['Свои комментарии']"
            label="Свои комментарии"
            class="task-list__checkbox"/>
          </template>

          <field-textarea
          v-model="commentText"
          :title="'Текст комментария'"
          :data-vv-as="'Текст комментария'"
          name="commentText"
          class="task-list__textarea"/>

          <button
          v-if="!isManager || (isManager === true && filters.own_comments === true)"
          @click="editComment"
          v-text="'Редактировать'"
          class="button button--positive"/>

          <button
          @click="deleteComment"
          v-text="'Удалить'"
          class="button button--positive"/>
        </div>

        <data-table
        v-model="selectedComment"
        :header="commentsHeader"
        :body="comments"
        has-radio
        class="task-list__table"/>

      </template>

      <template v-if="selectedTab === 3">

        <div class="task-list__controls">

          создание задачи
        </div>
      </template>

      <template v-if="selectedTab === 4">
        <div class="task-list__controls">

          статистика
        </div>

      </template>
    </div>

    <modal
    v-if="isModalShown"
    :title="'Задача'"
    @modal:close="closeModal"
    class-list="task-list__modal">
      <task
      @comment:create="handleCreateComment"
      :task="modalTask"
      :header="taskHeader"
      :comments="taskComments"
      :comments-header="commentsHeader"
      :is-loading="isLoading"/>
    </modal>

  </div>
</template>

<script>

import Tabs from './Tabs';
import api from '../api/api'
import Task from './Task';
import DataTable from './DataTable';
import FieldCheckbox from './FieldCheckbox';
import FieldDropdown from './FieldDropdown';
import FieldTextarea from './FieldTextarea';

export default {
  name: "TaskList",
  components: { FieldTextarea, Tabs, FieldCheckbox, Task, DataTable, FieldDropdown },
  props: {
    propsData: { type: Object, default: null },
  },
  data() {
    return {


      statuses: [
        'В тестировании', 'Необходимо сделать', 'В работе', 'Тех. долг', 'Выполнено'
      ],

      commentsHeader: [
        { label: 'Комментарий', field: 'comment_comment' },
        { label: 'Имя', field: 'emp_name' },
        { label: 'Фамилия', field: 'emp_surname' },
      ],

      tasks: this.propsData.tasks,
      comments: this.propsData.comments,

      filters: {
        own_tasks: false,
        own_comments: false,
      },

      selectedStatus: null,
      taskDesc: null,
      commentText: null,

      isModalShown: false,

      modalTask: null,
      selectedTask: null,
      selectedComment: null,
      selectedTab: 1,

      taskComments: [],
      isManager: this.propsData.isManager,
      isLoading: false,
    }
  },

  methods: {

    async handleRowSelect(row){
      this.modalTask = row;
      this.isModalShown = true;
      const params = { task_id: row.task_id };

      this.taskComments = await this.getComments(params);
    },

    closeModal(){
      this.modalTask = null;
      this.isModalShown = false;
    },

    async deleteTask() {
      if(!this.selectedTask) return;
      const params = { task_id: this.selectedTask.task_id };
      this.res = await api.post('/home/tasks/delete', params);

      await this.getTasks();
      this.closeModal();
    },

    async editTask() {
      if(!this.selectedTask) return;
      const params = { task_id: this.selectedTask.task_id,
        task_desc: this.taskDesc || this.selectedTask.task_desc,
        task_status: this.selectedStatus || this.selectedTask.task_status };
      this.res = await api.post('/home/tasks/edit', params);

      await this.getTasks();
      this.closeModal();
    },


    async getTasks() {
      this.tasks = await api.post('/home/tasks', this.filters);
      this.selectedTask = null;
    },

    async handleCreateComment(commentText){
      const params = { task_id: this.modalTask.task_id, comment_text: commentText };
      await api.post('/home/comments/create', params);

      this.taskComments = await this.getComments(params);
    },

    async editComment() {
      if(!this.selectedComment) return;
      const params = { comment_id: this.selectedComment.comment_id,
        comment_text: this.commentText || this.selectedComment.comment_comment };
      this.res = await api.post('/home/comments/edit', params);

      this.comments = await this.getComments();
    },

    async deleteComment() {
      if(!this.selectedComment) return;
      const params = { comment_id: this.selectedComment.comment_id };
      this.res = await api.post('/home/comments/delete', params);

      this.comments = await this.getComments();
      this.closeModal();
    },

    async getComments(params){
      if(!params) {
        const ownComments = this.isManager === true ? this.filters.own_comments : true;
        params = {own_comments: ownComments}
      }
      this.isLoading = true;
      const res = await api.post('/home/comments', params);
      this.isLoading = false;

      this.selectedComment = null;
      return res;
    },
  },

  computed: {
    tabs(){
      return this.isManager === true ?
      [{ 'tab_id': 1, 'tab_title': 'Задачи' },
        { 'tab_id': 2, 'tab_title': 'Комментарии' },
        { 'tab_id': 3, 'tab_title': 'Создание задачи' },
        { 'tab_id': 4, 'tab_title': 'Статистика' }] :

      [{ 'tab_id': 1, 'tab_title': 'Задачи' },
        { 'tab_id': 2, 'tab_title': 'Комментарии' }]
    }
    ,

    taskHeader(){
      return this.filters.own_tasks === true ?
      [
        { label: 'Заголовок', field: 'task_title' },
        { label: 'Описание', field: 'task_desc' },
        { label: 'Приоритет', field: 'task_priority' },
        { label: 'Статус', field: 'task_status' },
        { label: 'Проект', field: 'project_title' },
        { label: 'Роль', field: 'ref_task_emp_role' },
        { label: 'Дата создания', field: 'task_created' },
        { label: 'Срок исполнения', field: 'task_deadline' },
      ] :
      [
        { label: 'Заголовок', field: 'task_title' },
        { label: 'Описание', field: 'task_desc' },
        { label: 'Приоритет', field: 'task_priority' },
        { label: 'Статус', field: 'task_status' },
        { label: 'Проект', field: 'project_title' },
        { label: 'Дата создания', field: 'task_created' },
        { label: 'Срок исполнения', field: 'task_deadline' },
      ];
    },

  },

  watch: {
    filters: {
      deep: true,
      async handler() {
        this.comments = await this.getComments();
        await this.getTasks();
      }
    },
    selectedTask: {
      handler(){
        this.selectedStatus = this.selectedTask.task_status;
        this.taskDesc = this.selectedTask.task_desc;
      }
    },
    selectedComment: {
      handler(){
        this.commentText = this.selectedComment ? this.selectedComment.comment_comment : null;
      }
    },
  },
}
</script>

<style lang="scss">

.task-list {

  &__content {
    > * + * {
      margin-top: 20px;
    }
  }

  &__controls {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: flex-end;
  }

  &__controls > * {
    margin-left: 20px;
    width: auto;
    min-width: 250px;
    max-width: 600px;
  }

  &__checkbox {
    min-width: unset;
    max-width: unset;
  }

}

</style>