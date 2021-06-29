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

        <div class="task-list__controls-container">

          <template v-if="!isManager">
            <field-checkbox
            v-model="filters.own_tasks"
            :data="['Свои задачи']"
            label="Свои задачи"
            class="task-list__checkbox"/>
          </template>

          <task-form
          @task-form:edit="editTask"
          @task-form:create="createTask"
          @task-form:delete="deleteTask"
          @task-form:reset="selectedTask = null"
          @task-form:project="getProjectEmployees"
          :task="selectedTask"
          :projects="projects"
          :statuses="statuses"
          :employees="propsData.employees"
          :project-devs="projectDevs"
          :project-testers="projectTesters"
          :is-manager="isManager"
          :own-tasks="filters.own_tasks"/>
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

        <div class="task-list__instruction">
          Выберите комментарий из списка, чтобы заполнить поля.
        </div>

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
          :disabled="!selectedComment"
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
          class="button button--negative"/>

          <a
          v-if="isManager === true"
          :href="`${baseUrl}/home/comments/export`"
          target="_blank"
          download="comments.csv"
          v-text="'Экспорт'"
          class="button button--neutral"/>
        </div>

        <data-table
        v-model="selectedComment"
        :header="commentsHeader"
        :body="comments"
        has-radio
        class="task-list__table"/>

      </template>

      <template v-if="selectedTab === 3">

        <div class="task-list__content">

          <div class="task-list__instruction">
            Выберите сотрудника из списка, чтобы получить статистику.
          </div>

        <div class="task-list__controls">
          <field-checkbox
          v-model="onlyEmpStats"
          :data="['Задачи сотрудника']"
          label="Задачи сотрудника"
          class="task-list__checkbox"/>

          <field-dropdown
          v-model="selectedEmpStat"
          :data="employees"
          :disabled="!onlyEmpStats"
          valueName="emp_id"
          labelName="emp_fio"
          placeholder="Сотрудник"
          class="task-form__dropdown"/>

          <a
          v-if="isManager === true"
          :href="`${baseUrl}/home/tasks/statistics/export`"
          target="_blank"
          download="statistics.csv"
          v-text="'Экспорт'"
          class="button button--neutral"/>
        </div>

          <data-table
          :header="statisticsHeader"
          :body="statistics"
          class="task-list__table"/>
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
import TaskForm from './TaskForm';

export default {
  name: "TaskList",
  components: { FieldTextarea, Tabs, FieldCheckbox, Task, DataTable, FieldDropdown, TaskForm },
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

      statisticsHeader: [
        { label: 'Тип', field: 'name' },
        { label: 'Количество', field: 'cnt' },
      ],

      tasks: this.propsData.tasks,
      comments: this.propsData.comments,
      employees: this.propsData.employees,
      statistics: this.propsData.statistics,
      projects: this.propsData.projects,

      filters: {
        own_tasks: false,
        own_comments: false,
      },

      taskDesc: null,
      commentText: '',

      isModalShown: false,

      modalTask: null,

      selectedTask: null,
      selectedComment: null,
      selectedTab: 1,
      selectedStatus: null,
      selectedEmpStat: null,

      taskComments: [],
      projectDevs: [],
      projectTesters: [],
      isManager: this.propsData.isManager,
      onlyEmpStats: false,
      isLoading: false,

      baseUrl: window.location.origin,
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

    async deleteTask(taskId) {
      if(!taskId) return;
      const params = { task_id: taskId };
      this.res = await api.post('/home/tasks/delete', params);

      await this.getTasks();
      this.closeModal();

      this.$notify({
        type: 'success',
        text: 'Задача успешно удалена'
      });
    },

    async editTask(taskObj) {
      if(!taskObj || !taskObj.task_id) return;

      await api.post('/home/tasks/edit', taskObj);

      await this.getTasks();
      this.closeModal();

      this.$notify({
        type: 'success',
        text: 'Задача успешно отредактирована'
      });
    },

    async createTask(taskObj = null) {
      if(!taskObj) return;
      await api.post('/home/tasks/create', taskObj);

      await this.getTasks();
      this.closeModal();

      this.$notify({
        type: 'success',
        text: 'Задача успешно создана'
      });
    },


    async getTasks() {
      this.tasks = await api.post('/home/tasks', this.filters);
      this.selectedTask = null;
    },

    async handleCreateComment(commentText){
      const params = { task_id: this.modalTask.task_id, comment_text: commentText };
      await api.post('/home/comments/create', params);

      this.closeModal();
      this.comments = await this.getComments(params);

      this.$notify({
        type: 'success',
        text: 'Комментарий успешно создан'
      });
    },

    async editComment() {
      if(!this.selectedComment) return;
      if(!this.commentText.length){
        this.$notify({
          type: 'warn',
          text: 'Комментарий не может быть пустым'
        });
        return;
      }
      const params = { comment_id: this.selectedComment.comment_id,
        comment_text: this.commentText || this.selectedComment.comment_comment };
      this.res = await api.post('/home/comments/edit', params);

      this.closeModal();
      this.comments = await this.getComments();

      this.$notify({
        type: 'success',
        text: 'Комментарий успешно отредактирован'
      });
    },

    async deleteComment() {
      if(!this.selectedComment) return;
      const params = { comment_id: this.selectedComment.comment_id };
      this.res = await api.post('/home/comments/delete', params);

      this.closeModal();
      this.comments = await this.getComments();

      this.$notify({
        type: 'success',
        text: 'Комментарий успешно удален'
      });
    },

    async getComments(params){
      if(!params) {
        const ownComments = this.isManager === true ? this.filters.own_comments : true;
        params = { own_comments: ownComments }
      }
      this.isLoading = true;
      const res = await api.post('/home/comments', params);
      this.isLoading = false;

      this.selectedComment = null;
      return res;
    },

    async getTaskStatistics(){
      this.isLoading = true;
      const params = {
        only_emp_stats: this.onlyEmpStats,
        emp_id: this.selectedEmpStat,
      };
      this.statistics = await api.post('/home/tasks/statistics', params);
      this.isLoading = false;
    },

    async getProjectEmployees(projectId){
      const taskProject = this.selectedTask ? this.selectedTask.task_project : projectId;

      if(!taskProject) return;

      const params = { task_project: taskProject}
      const employees = await api.post('/home/employees/by_project', params);
      this.projectDevs = employees.devs;
      this.projectTesters = employees.testers;
    }
  },

  computed: {
    tabs(){
      return this.isManager === true ?
      [{ 'tab_id': 1, 'tab_title': 'Задачи' },
        { 'tab_id': 2, 'tab_title': 'Комментарии' },
        { 'tab_id': 3, 'tab_title': 'Статистика' }] :

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
        this.selectedStatus = this.selectedTask ? this.selectedTask.task_status : null;
        this.taskDesc = this.selectedTask ? this.selectedTask.task_desc : null;
        this.getProjectEmployees();
      }
    },

    selectedComment: {
      handler(){
        this.commentText = this.selectedComment ? this.selectedComment.comment_comment : null;
      }
    },

    onlyEmpStats: {
      handler(){
        this.selectedEmpStat = null;
      }
    },

    selectedEmpStat: {
      handler(){
        this.getTaskStatistics();
      }
    }
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

  &__controls-container {
    align-items: flex-end;
  }

  &__controls {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: flex-end;
  }

  &__controls > * {
    width: auto;
    min-width: 250px;
    max-width: 600px;
  }

  &__controls > * + * {
    margin-left: 20px;
  }

  &__checkbox {
    min-width: unset;
    max-width: unset;
  }

}

</style>