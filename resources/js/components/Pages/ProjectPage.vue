<template>
  <div class="project-page">
    <div
    v-text="'Проект'"
    class="project-page__page-title" />

    <div class="project-page__content">
      <div class="project-page__main">
        <div class="project-page__header">
          <div class="project-page__task-title">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('project_title')"
            title="Заголовок"
            class="project-page__field project-page__title">
              <field-input
              v-model="projectData.project_title"
              v-validate="'required'"
              :isError="veeErrors.has('project_title')"
              :data-vv-as="' '"
              maxlength="255"
              name="project_title" />
            </field-container>
          </div>
          <div class="project-page__combos">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('project_subdiv')"
            title="Подразделение"
            class="project-page__field">
              <field-dropdown
              class="project-page__dropdown"
              v-model="projectData.project_subdiv"
              :data="subdivisions"
              :disabled="!isManager"
              searchable
              placeholder="Подразделение"
              value-name="subdiv_id"
              label-name="subdiv_title" />
            </field-container>

            <field-container
            :is-required="true"
            :errors="veeErrors.collect('project_customer')"
            title="Заказчик"
            class="project-page__field">
              <field-dropdown
              class="project-page__dropdown"
              v-model="projectData.project_customer"
              :data="customers"
              :disabled="!isNewProject"
              searchable
              placeholder="Заказчик"
              value-name="customer_id"
              label-name="customer_fio" />
            </field-container>
          </div>
        </div>

        <div
        class="project-page__controls">
          <button
          @click="saveProject"
          v-text="saveButtonText"
          class="button button--positive project-page__control"/>
          <button
          @click="cancelChanges"
          v-text="'Отменить изменения'"
          class="button button--neutral project-page__control"/>
          <button
          v-if="!isNewProject && isManager"
          @click="isConfirmModalShown = true"
          v-text="'Удалить'"
          class="button button--negative project-page__control"/>
        </div>
        <div class="project-page__description">
          <field-container
          :is-required="true"
          :errors="veeErrors.collect('project_desc')"
          title="Описание"
          class="report-answer-quality__field">
            <field-textarea
            v-model="projectData.project_desc"
            :data-vv-as="' '"
            :is-error="veeErrors.has('project_desc')"
            :disabled="!editable"
            placeholder="Описание"
            name="project_desc"
            max="300"
            class="project-page__textarea"/>
          </field-container>
        </div>
        <div
        v-if="projectData.project_tasks.length"
        class="project-page__tasks">
          <div
          v-text="'Задачи'"
          class="project-page__tasks-title" />
          <div class="project-page__tasks-content">
            <div
            v-for="task in projectData.project_tasks"
            :key="task.task_id"
            class="project-page__task">
              <div class="project-page__task-header">
                <a
                v-text="`#${task.task_id}`"
                :href="getTaskUrl(task.task_id)"
                target="_blank"
                class="project-page__task-number" />
                <div
                v-text="task.task_priority_title"
                :style="getPriorityStyle(task)"
                class="project-page__task-priority" />
              </div>
              <div
              v-text="task.task_title"
              class="project-page__task-title" />
            </div>
          </div>
        </div>
      </div>

      <div class="project-page__side">
        <field-container
        v-if="!isNewProject"
        :errors="veeErrors.collect('project_dev_start')"
        title="Дата начала разработки"
        class="project-page__field project-page__datepicker">
          <field-datepicker
          v-model="projectData.project_dev_start"
          v-validate="'required'"
          :data-vv-as="'Дата начала разработки'"
          formatter="dd.MM.yyyy"
          readonly
          :disabled="true" />
        </field-container>

        <field-container
        :is-required="true"
        :errors="veeErrors.collect('project_dev_deadline')"
        title="Срок выполнения"
        class="project-page__field project-page__datepicker">
          <field-datepicker
          v-model="projectData.project_dev_deadline"
          v-validate="'required'"
          :data-vv-as="'Срок выполнения'"
          formatter="dd.MM.yyyy"
          :readonly="isManager"/>
        </field-container>
      </div>
    </div>

    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="project-page__modal">
      <div
      v-text="'Вы уверены, что хотите удалить проект?'"
      class="project-page__modal-text"/>

      <template #buttons>
        <div class="project-page__modal-controls">
          <button
          v-text="'Удалить'"
          @click="handleConfirmation()"
          class="button button--negative project-page__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral project-page__button"/>
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
  name: "ProjectPage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput, FieldTextarea, FieldDatepicker },
  props: {
    project: { type: Object, default: () => {} },
    customers: { type: Array, default: () => [] },
    subdivisions: { type: Array, default: () => [] },
    isNewProject: { type: Boolean, default: () => false },
  },
  data() {
    return {
      projectData: this.project,
      drag: false,
      filters: {},
      isConfirmModalShown: false,
      emptyProject: {
        project_title: 'Новый проект',
        project_desc: 'Описание'
      }
    }
  },
  methods: {

    async saveProject() {
      this.$store.state.isLoading = true;
      const params = { ...this.projectData };
      const res = await this.$store.dispatch('fetchData', { url: '/projects/save', params });

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

    async deleteProject() {
      this.$store.state.isLoading = true;
      const params = { project_id: this.projectData.project_id };
      const res = await this.$store.dispatch('fetchData', { url: '/projects/delete', params });

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
          window.location.href = `/projects`;
        }
      }
      this.$store.state.isLoading = false;
    },

    cancelChanges() {
      this.projectData = this.isNewProject ? this.emptyProject : this.project;
    },

    getTaskUrl(task_id) {
      return `/tasks/${task_id}`;
    },

    getPriorityStyle(task) {
      return task.task_priority_color ? `background-color: ${task.task_priority_color}` : null;
    },

    handleConfirmation() {
      this.deleteProject();
      this.isConfirmModalShown = false;
    },
    handleCancel() {
      this.isConfirmModalShown = false;
    },

  },
  computed: {
    isManager() {
      return this.$store.getters.checkRole('manager');
    },
    editable() {
      return this.isManager || this.isTaskOperator;
    },
    saveButtonText() {
      return this.isNewProject === true ? 'Создать' : 'Сохранить';
    },
    projectCustomer() {
      const customer = this.customers.find(customer => customer.id == 3);
      return customer ? customer.customer_fio : null;
    }
  },
  created() {
    if(this.isNewProject)
      this.projectData = this.emptyProject;
  }
}
</script>

<style lang="scss">
.project-page {

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

  &__tasks {
    &-content {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      grid-auto-rows: 1fr;
      gap: 20px;
    }
  }

  &__task {
    &-header {
      display: flex;
      justify-content: flex-start;

      > * + * {
        margin-left: 10px;
      }
    }

    &-priority {
      //background-color: #f7f7f8;
      padding: 5px;
      border-radius: 5px;
    }

    &-number {
      color: #1fe09e;
      background-color: #f7f7f8;
      padding: 5px;
      border-radius: 5px;

      &:hover {
        //color: #1fe09e;
        //text-decoration: underline;
      }
    }

    &-title {
      background-color: #f7f7f8;
      padding: 5px;
      border-radius: 5px;
      //border: 1px solid var(--input-border-color, #906fe9);
    }

    cursor: pointer;
    //background-color: #e7def9;
    background: rgb(144,111,233);
    background: linear-gradient(180deg, rgba(144,111,233,0.3) 0%, rgba(231,222,249,1) 100%);
    padding: 10px;
    border-radius: 5px;

    > * + * {
      margin-top: 10px;
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