<template>
  <div class="task-form">
    <div class="task-form__content">

      <div class="task-form__instruction">
        Выберите задачу из списка задач, чтобы заполнить поля.
      </div>

      <div class="task-form__controls">

        <field-textarea
        v-model="taskObj.task_title"
        :title="'Заголовок задачи'"
        :data-vv-as="'Заголовок задачи'"
        :disabled="!!taskObj.task_id"
        name="taskTitle"
        class="task-form__textarea"/>

        <field-textarea
        v-model="taskObj.task_desc"
        :title="'Описание задачи'"
        :data-vv-as="'Описание задачи'"
        name="taskDesc"
        class="task-form__textarea"/>

        <date-picker
        v-model="taskObj.task_deadline"
        :disabled="!!taskObj.task_id"
        value-type="YYYY-MM-DD HH:mm:ss"
        format="YYYY-MM-DD HH:mm:ss"/>

        <field-dropdown
        :data="statuses"
        v-model="taskObj.task_status"
        placeholder="Статус"
        class="task-form__dropdown"/>

        <field-dropdown
        :data="priority"
        v-model="taskObj.task_priority"
        :disabled="!!taskObj.task_id"
        placeholder="Приоритет"
        class="task-form__dropdown"/>

        <field-dropdown
        :data="projects"
        v-model="taskObj.task_project"
        :disabled="!!taskObj.task_id"
        valueName="project_id"
        labelName="project_title"
        placeholder="Проект"
        class="task-form__dropdown"/>
      </div>


      <div class="task-form__buttons">
        <button
        @click="resetTask"
        v-text="'Сбросить'"
        class="button button--neutral"/>

        <button
        v-if="ownTasks || isManager"
        @click="editTask"
        v-text="'Редактировать'"
        class="button button--positive"/>

        <button
        v-if="isManager"
        @click="createTask"
        v-text="'Создать'"
        class="button button--positive"/>

        <button
        v-if="isManager"
        @click="deleteTask"
        v-text="'Удалить'"
        class="button button--negative"/>
      </div>
    </div>
  </div>
</template>

<script>

import FieldCheckbox from './FieldCheckbox';
import FieldTextarea from './FieldTextarea';
import FieldDropdown from './FieldDropdown';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';

export default {
  name: "TaskForm",
  components: {
    FieldCheckbox, FieldTextarea, FieldDropdown, DatePicker
  },
  props: {
    ownTasks: { type: Boolean, default: false },
    isManager: { type: Boolean, default: false },
    task: { type: Object, default: null },
    projects: { type: Array, default: [] },
    statuses: { type: Array, default: [] },
    employees: { type: Array, default: [] }
  },
  data(){
    const emptyTaskValues = {
      'task_title': null,
      'task_desc': null,
      'task_status': null,
      'task_priority': null,
      'task_deadline': null,
      'task_project': null,
    };

    return {

      emptyTask: emptyTaskValues,

      priority: [
        'Низкий', 'Средний', 'Важный'
      ],

      taskObj: this.task ? { ...this.task } : emptyTaskValues,
    }
  },
  methods: {
    editTask(){
      this.$emit('task-form:edit', this.taskObj);
    },

    createTask(){
      const deadlineDate = new Date(this.taskObj.task_deadline);
      const nowDate = new Date();

      if(nowDate > deadlineDate)
        console.log('error');

      return;
      this.$emit('task-form:create', this.taskObj);
    },

    deleteTask(){
      this.$emit('task-form:delete', this.taskObj.task_id);
    },

    editEmployees(){
      //this.$emit('task-form@create', this.taskObj);
    },

    resetTask(){
      this.taskObj = { ...this.emptyTask };
    }
  },
  computed: {

  },

  watch: {
    task: {
      handler(){
        this.taskObj = { ...this.task };
      }
    }
  },

  created(){

  }
}
</script>

<style lang="scss">

.task-form {

  &__content {
    > * + * {
      margin-top: 20px;
    }
  }

  &__content > * + * {
    margin-top: 20px
  }

  &__controls {
    display: grid;
    grid-template-columns: repeat(3, minmax(200px, auto));
    grid-gap: 20px;

    align-items: end;
  }

  &__controls > * {
    //margin-left: 20px;
    //width: auto;
    //min-width: 250px;
    //max-width: 600px;
  }

  &__buttons {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: flex-end;
  }

  &__buttons > * {
    width: auto;
    min-width: 250px;
    max-width: 600px;
  }

  &__buttons > * + * {
    margin-left: 20px;
  }

  &__checkbox {
    min-width: unset;
    max-width: unset;
  }

}

</style>