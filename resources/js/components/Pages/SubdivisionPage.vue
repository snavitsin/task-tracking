<template>
  <div class="subdivision-page">
    <div class="subdivision-page__fields">

      <div class="subdivision-page__fields-header">
        <field-container
        :is-required="true"
        :errors="veeErrors.collect('subdiv_title')"
        title="Название"
        class="subdivision-page__field">
          <field-input
          v-model="subdivData.subdiv_title"
          v-validate="'required'"
          :isError="veeErrors.has('subdiv_title')"
          :data-vv-as="' '"
          :disabled="!editable"
          maxlength="255"
          name="subdiv_title" />
        </field-container>

        <div
        v-if="editable"
        class="subdivision-page__controls">
          <button
          @click="saveSubdiv"
          v-text="saveButtonText"
          class="button button--positive subdivision-page__control"/>
          <button
          @click="cancelChanges"
          v-text="'Отменить изменения'"
          class="button button--neutral subdivision-page__control"/>
          <button
          v-if="!isNewSubdiv"
          @click="isConfirmModalShown = true"
          v-text="'Удалить'"
          class="button button--negative subdivision-page__control"/>
        </div>
      </div>

      <field-container
      :is-required="true"
      :errors="veeErrors.collect('subdiv_desc')"
      title="Описание"
      class="subdivision-page__field">
        <field-textarea
        v-model="subdivData.subdiv_desc"
        :data-vv-as="' '"
        :is-error="veeErrors.has('subdiv_desc')"
        :disabled="!editable"
        placeholder="Описание"
        name="subdiv_desc"
        max="300"
        class="subdivision-page__textarea"/>
      </field-container>
    </div>
    <div
    v-text="'Сотрудники подразделения'"
    class="subdivision-page__emps-title" />
    <div class="subdivision-page__content">
      <div
      v-for="emp in subdivData.subdiv_emps"
      :key="emp.emp_id"
      class="subdivision-page__emp">
        <div
        v-text="emp.emp_fio"
        class="subdivision-page__emp-fio" />
        <div
        v-text="emp.emp_position_title"
        class="subdivision-page__emp-position" />
      </div>
    </div>


    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="subdivision-page__modal">
      <div
      v-text="'Вы уверены, что хотите удалить подразделение?'"
      class="subdivision-page__modal-text"/>

      <template #buttons>
        <div class="subdivision-page__modal-controls">
          <button
          v-text="'Удалить'"
          @click="handleConfirmation()"
          class="button button--negative subdivision-page__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral subdivision-page__button"/>
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
  name: "subdivisionPage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput, FieldTextarea, FieldDatepicker },
  props: {
    subdiv: { type: Object, default: () => {} },
    isNewSubdiv: { type: Boolean, default: () => false },
  },
  data() {
    return {
      subdivData: this.subdiv,
      isConfirmModalShown: false,
      emptySubdiv: {
        subdiv_title: 'Новое подразделение',
        subdiv_desc: 'Описание'
      },
    }
  },
  methods: {
    getSubdivCountText(subdiv) {
      return `Сотрудники: ${subdiv.subdiv_emp_count}`;
    },

    getSubdivUrl(subdiv) {
      return `/subdivision/${subdiv.subdiv_id}`;
    },

    async saveSubdiv() {
      this.$store.state.isLoading = true;
      const params = { ...this.subdivData };
      const res = await this.$store.dispatch('fetchData', { url: '/subdivisions/save', params });

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
      const params = { subdiv_id: this.subdivData.subdiv_id };
      const res = await this.$store.dispatch('fetchData', { url: '/subdivisions/delete', params });

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


    cancelChanges() {
      this.subdivData = this.isNewSubdiv ? this.emptySubdiv : this.subdiv;
    },

    handleConfirmation() {
      this.deleteSubdiv();
      this.isConfirmModalShown = false;
    },
    handleCancel() {
      this.isConfirmModalShown = false;
    },

  },
  computed: {
    editable() {
      return this.$store.getters.checkRole('manager');
    },
    saveButtonText() {
      return this.isNewSubdiv === true ? 'Создать' : 'Сохранить';
    },
  },
}
</script>

<style lang="scss">
.subdivision-page {

  --accent-color: #906fe9;

  > * + * {
    margin-top: 20px;
  }

  &__fields {
    > * + * {
      margin-top: 20px;
    }
  }

  &__controls {
    display: flex;
    margin-left: -10px;
    margin-right: -10px;
    margin-top: 10px;
  }

  &__control{
    flex: 1 1 auto;
    margin: 10px;
    min-width: 200px;
  }

  &__content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(100px, 400px));
    grid-auto-rows: 1fr;
    gap: 20px;
  }

  &__emp {
    padding: 15px;
    border: 3px solid var(--input-border-color, #906fe9);
    border-radius: 5px;
    box-shadow: 0 0 4px 4px rgb(154 161 177 / 15%), 0 4px 4px 1px rgb(91 94 105 / 15%), 0 4px 4px -2px rgb(91 94 105 / 15%);

    &-fio {
      padding: 10px;
      background: #fff;
      border: 1px solid var(--input-border-color, #D3D8DB);
    }

    &-position {
      padding: 10px;
      background: #fff;
      border: 1px solid var(--input-border-color, #D3D8DB);
    }

    > * + * {
      margin-top: 10px;
    }
  }

  &__title {
    color: #906fe9;
    font-weight: bold;
    font-size: 24px;
  }
}
</style>