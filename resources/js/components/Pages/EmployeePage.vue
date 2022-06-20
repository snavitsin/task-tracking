<template>
  <div class="employee-page">
    <div
    v-text="'Сотрудник'"
    class="employee-page__page-title" />

    <div class="employee-page__content">
      <div class="employee-page__main">
        <div class="employee-page__header">
          <div class="employee-page__fields">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('emp_name')"
            title="Имя"
            class="employee-page__field employee-page__title">
              <field-input
              v-model="empData.emp_name"
              v-validate="'required'"
              :isError="veeErrors.has('emp_name')"
              :data-vv-as="' '"
              maxlength="255"
              name="emp_name" />
            </field-container>
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('emp_patroname')"
            title="Отчество"
            class="employee-page__field employee-page__title">
              <field-input
              v-model="empData.emp_patroname"
              v-validate="'required'"
              :isError="veeErrors.has('emp_patroname')"
              :data-vv-as="' '"
              maxlength="255"
              name="emp_patroname" />
            </field-container>
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('emp_surname')"
            title="Фамилия"
            class="employee-page__field employee-page__title">
              <field-input
              v-model="empData.emp_surname"
              v-validate="'required'"
              :isError="veeErrors.has('emp_surname')"
              :data-vv-as="' '"
              maxlength="255"
              name="emp_surname" />
            </field-container>
          </div>
          <div class="employee-page__combos">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('emp_subdiv')"
            title="Подразделения"
            class="employee-page__field">
              <field-dropdown
              class="employee-page__dropdown"
              v-model="empData.emp_subdiv"
              :data="subdivisions"
              :disabled="!isManager"
              searchable
              placeholder="Подразделение"
              value-name="subdiv_id"
              label-name="subdiv_title" />
            </field-container>

            <field-container
            :is-required="true"
            :errors="veeErrors.collect('emp_position')"
            title="Должность"
            class="employee-page__field">
              <field-dropdown
              class="employee-page__dropdown"
              v-model="empData.emp_position"
              :data="positions"
              :disabled="!isManager"
              searchable
              placeholder="Должность"
              value-name="emp_position_id"
              label-name="emp_position" />
            </field-container>
          </div>
        </div>

        <div
        class="employee-page__controls">
          <button
          @click="saveEmp"
          v-text="saveButtonText"
          class="button button--positive employee-page__control"/>
          <button
          @click="cancelChanges"
          v-text="'Отменить изменения'"
          class="button button--neutral employee-page__control"/>
          <button
          v-if="!isNewEmp && isManager"
          @click="isConfirmModalShown = true"
          v-text="'Удалить'"
          class="button button--negative employee-page__control"/>
        </div>

      </div>

      <kanban-board
      v-if="empData.emp_tasks && empData.emp_tasks.length"
      :tasks="empData.emp_tasks"
      :title="'Kanban доска задач сотрудника'"
      :statuses="statuses"
      :editable="false" />

    </div>

    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="employee-page__modal">
      <div
      v-text="'Вы уверены, что хотите удалить сотрудника?'"
      class="employee-page__modal-text"/>

      <template #buttons>
        <div class="employee-page__modal-controls">
          <button
          v-text="'Удалить'"
          @click="handleConfirmation()"
          class="button button--negative employee-page__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral employee-page__button"/>
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
import KanbanBoard from '../KanbanBoard';

import Modal from '../Modal';
import { cloneDeep } from "lodash";
import { format } from "date-fns";

export default {
  name: "EmployeePage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput, FieldTextarea, FieldDatepicker, KanbanBoard },
  props: {
    emp: { type: Object, default: () => {} },
    subdivisions: { type: Array, default: () => [] },
    positions: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    priority: { type: Array, default: () => [] },

    isNewEmp: { type: Boolean, default: () => false },

  },
  data() {
    return {
      empData: this.emp,
      isConfirmModalShown: false,
      emptyEmp: {
        emp_name: 'Иван',
        emp_patroname: 'Иванович',
        emp_surname: 'Иванов',
        emp_login: 'login',
        emp_mail: 'mail@example.ru',
      },
    }
  },
  methods: {

    async saveEmp() {
      this.$store.state.isLoading = true;

      const params = { ...this.empData };
      const res = await this.$store.dispatch('fetchData', { url: '/employees/save', params });

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
      }
      this.$store.state.isLoading = false;
    },

    async deleteEmp() {
      this.$store.state.isLoading = true;
      const params = { task_id: this.taskData.task_id };
      const res = await this.$store.dispatch('fetchData', { url: '/employees/delete', params });

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
          window.location.href = `/employees/`;
        }
      }
      this.$store.state.isLoading = false;
    },

    cancelChanges() {
      this.empData = this.isNewEmp ? cloneDeep(this.emptyEmp) : cloneDeep(this.emp);
    },

    handleConfirmation() {
      this.deleteEmp();
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
    saveButtonText() {
      return this.isNewEmp === true ? 'Создать' : 'Сохранить';
    },
    isManager() {
      return this.$store.getters.checkRole('chief') || this.$store.getters.checkRole('manager');
    },
  },
  created() {
    this.empData = this.isNewEmp ? cloneDeep(this.emptyEmp) : cloneDeep(this.empData);
  }
}
</script>

<style lang="scss">
.employee-page {

  --accent-color: #906fe9;
  --input-height: 60px;

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
    > * + * {
      margin-top: 20px;
    }
  }

  &__header {
    > * + * {
      margin-top: 20px;
    }
  }


  &__fields {
    display: flex;
    margin: -10px;

    > * {
      flex: 1 1 auto;
      margin: 10px;
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