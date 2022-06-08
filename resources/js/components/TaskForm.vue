<template>
  <div class="task-form">
    <div
    v-if="ownTasks === true || isManager === true"
    class="task-form__content">

      <div
      v-if="false"
      class="task-form__controls">

        <field-textarea
        v-model="taskObj.task_title"
        :title="'Заголовок задачи'"
        :data-vv-as="'Заголовок задачи'"
        :disabled="!!taskObj.task_id || !isManager"
        name="taskTitle"
        class="task-form__textarea"/>

        <field-textarea
        v-model="taskObj.task_desc"
        :title="'Описание задачи'"
        :data-vv-as="'Описание задачи'"
        name="taskDesc"
        class="task-form__textarea"/>

        <div class="task-form__field-container">
          <div
          v-text="'Срок исполнения'"
          class="task-form__datepicker-title"/>
          <date-picker
          v-model="taskObj.task_deadline"
          :disabled="!!taskObj.task_id || !isManager"
          value-type="YYYY-MM-DD HH:mm:ss"
          format="YYYY-MM-DD HH:mm:ss"/>
        </div>

        <div class="task-form__field-container">
          <div
          v-text="'Статус'"
          class="task-form__datepicker-title"/>

          <field-dropdown
          v-model="taskObj.task_status"
          :data="statuses"
          placeholder="Статус"
          class="task-form__dropdown"/>
        </div>

        <div class="task-form__field-container">
          <div
          v-text="'Приоритет'"
          class="task-form__datepicker-title"/>

          <field-dropdown
          v-model="taskObj.task_priority"
          :data="priority"
          :disabled="!!taskObj.task_id || !isManager"
          placeholder="Приоритет"
          class="task-form__dropdown"/>
        </div>


        <div class="task-form__field-container">
          <div
          v-text="'Проект'"
          class="task-form__datepicker-title"/>

          <field-dropdown
          @input="handleProjectInput"
          v-model="taskObj.task_project"
          :data="projects"
          :disabled="!!taskObj.task_id || !isManager"
          valueName="project_id"
          labelName="project_title"
          placeholder="Проект"
          class="task-form__dropdown"/>
        </div>

        <div class="task-form__field-container">
          <div
          v-text="'Разработчик'"
          class="task-form__datepicker-title"/>

          <field-dropdown
          v-model="taskObj.task_dev"
          :data="projectDevs"
          :disabled="!!taskObj.task_id || !isManager"
          valueName="emp_id"
          labelName="emp_fio"
          placeholder="Разработчик"
          class="task-form__dropdown"/>
        </div>


        <div class="task-form__field-container">
          <div
          v-text="'Тестировщик'"
          class="task-form__datepicker-title"/>

          <field-dropdown
          v-model="taskObj.task_tester"
          :data="projectTesters"
          :disabled="!!taskObj.task_id || !isManager"
          valueName="emp_id"
          labelName="emp_fio"
          placeholder="Тестировщик"
          class="task-form__dropdown"/>
        </div>
      </div>


      <div class="task-form__buttons"
      v-if="false">
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
        :disabled="!!taskObj.task_id"
        class="button button--positive"/>

        <button
        v-if="isManager"
        @click="deleteTask"
        v-text="'Удалить'"
        class="button button--negative"/>

        <a
        v-if="isManager === true"
        :href="`${baseUrl}/home/tasks/export`"
        target="_blank"
        download="tasks.csv"
        v-text="'Экспорт'"
        class="button button--neutral"/>
      </div>
    </div>
  </div>
</template>

<script>

import FieldCheckbox from './Fields/FieldCheckbox';
import FieldTextarea from './FieldTextarea';
import FieldDropdown from './Fields/FieldDropdown';
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
    employees: { type: Array, default: [] },
    projectDevs: { type: Array, default: [] },
    projectTesters: { type: Array, default: [] },
  },
  data(){
    const emptyTaskValues = {
      'task_title': null,
      'task_desc': null,
      'task_status': null,
      'task_priority': null,
      'task_deadline': null,
      'task_project': null,
      'task_dev': null,
      'task_tester': null,
    };

    return {

      emptyTask: { ...emptyTaskValues},

      priority: [
        'Низкий', 'Средний', 'Важный'
      ],

      taskObj: this.task ? { ...this.task } : { ...emptyTaskValues },

      baseUrl: window.location.origin,
    }
  },
  methods: {

    handleProjectInput(val){
      this.$emit('task-form:project', val)
    },

    editTask(){
      if(!this.taskObj.task_id) {
        this.$notify({
          type: 'warn',
          text: 'Необходимо выбрать задачу для редактирования'
        });
        return;
      }
      this.$emit('task-form:edit', this.taskObj);
      this.resetTask();
    },

    createTask(){
      const emptyFields = Object.keys(this.taskObj).filter( key => this.taskObj[key] === null );
      if(emptyFields.length){
        this.$notify({
          type: 'warn',
          text: 'Необходимо заполнить все поля для создания задачи'
        });
        return;
      }

      const deadlineDate = new Date(this.taskObj.task_deadline);
      const nowDate = new Date();

      if(!this.taskObj.task_id && nowDate > deadlineDate) {
        this.$notify({
          type: 'warn',
          text: 'Срок исполнения задачи не может быть раньше текущей даты'
        });
        return;
      }

      this.$emit('task-form:create', this.taskObj);
      this.resetTask();
    },

    deleteTask(){
      if(!this.taskObj.task_id) {
        this.$notify({
          type: 'warn',
          text: 'Необходимо выбрать задачу для удаления'
        });
        return;
      }
      this.$emit('task-form:delete', this.taskObj.task_id);
      this.resetTask();
    },

    editEmployees(){
      //this.$emit('task-form@create', this.taskObj);
    },

    resetTask(){
      this.$emit('task-form:reset');
      this.taskObj = { ...this.emptyTask };
    }
  },
  computed: {

  },

  watch: {
    task: {
      handler(){
        this.taskObj = { ...this.task };
      },
      deep: true,
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
    grid-template-columns: repeat(3, 325px);
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
    display: grid;
    grid-template-columns: repeat(3, 325px);
    align-items: flex-end;
    grid-gap: 20px;
  }

  &__checkbox {
    min-width: unset;
    max-width: unset;
  }

}

</style>