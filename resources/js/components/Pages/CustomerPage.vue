<template>
  <div class="customer-page">

    <div
    v-text="'Заказчик'"
    class="customer-page__title" />

    <div
    class="project-page__content">
      <div class="project-page__main">
        <div class="project-page__header">
          <div class="project-page__project-title">
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('customer_fio')"
            title="ФИО"
            class="customer-page__field">
              <field-input
              v-model="customerData.customer_fio"
              v-validate="'required'"
              :isError="veeErrors.has('customer_fio')"
              :data-vv-as="' '"
              :disabled="!editable"
              maxlength="255"
              name="customer_fio" />
            </field-container>
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('customer_email')"
            title="Email"
            class="customer-page__field">
              <field-input
              v-model="customerData.customer_email"
              v-validate="'required|email'"
              :isError="veeErrors.has('customer_email')"
              :data-vv-as="' '"
              :disabled="!editable"
              maxlength="255"
              placeholder="Email"
              name="customer_email" />
            </field-container>
            <field-container
            :is-required="true"
            :errors="veeErrors.collect('customer_phone')"
            title="Телефон"
            class="customer-page__field">
              <field-input
              v-model="customerData.customer_phone"
              v-validate="{ required: true, regex: /^\+7 \(\d{3}\) \d{3}-\d{2}-\d{2}$/ }"
              :isError="veeErrors.has('customer_phone')"
              :mask="{ value: '+7 (999) 999-99-99' }"
              placeholder="+7 (___) ___-__-__"
              :data-vv-as="' '"
              :disabled="!editable"
              maxlength="255"
              name="customer_phone" />
            </field-container>
          </div>
        </div>

        <div
        class="project-page__controls">
          <button
          @click="saveCustomer"
          v-text="saveButtonText"
          class="button button--positive customer-page__control"/>
          <button
          @click="cancelChanges"
          v-text="'Отменить изменения'"
          class="button button--neutral customer-page__control"/>
          <button
          v-if="!isNewCustomer"
          @click="isConfirmModalShown = true"
          v-text="'Удалить'"
          class="button button--negative customer-page__control"/>
        </div>
      </div>
    </div>

    <div
    v-if="customerData.customer_projects && customerData.customer_projects.length"
    v-text="'Проекты заказчика'"
    class="subdivision-page__projects-title" />
    <div class="subdivision-page__content">
      <div
      v-for="project in customerData.customer_projects"
      :key="project.project_id"
      class="subdivision-page__project">
        <div class="subdivision-page__project-title">
          <a
          v-text="project.project_title"
          :href="getProjectUrl(project)"
          target="_blank"
          class="subdivision-page__project-link" />
        </div>
      </div>
    </div>

    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="customer-page__modal">
      <div
      v-text="'Вы уверены, что хотите удалить заказчика?'"
      class="customer-page__modal-text"/>

      <template #buttons>
        <div class="customer-page__modal-controls">
          <button
          v-text="'Удалить'"
          @click="handleConfirmation()"
          class="button button--negative customer-page__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral customer-page__button"/>
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
import { cloneDeep } from "lodash";

export default {
  name: "CustomerPage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput, FieldTextarea, FieldDatepicker },
  props: {
    customer: { type: Object, default: () => {} },
    isNewCustomer: { type: Boolean, default: () => false },
  },
  data() {
    return {
      customerData: this.customer,
      isConfirmModalShown: false,
      emptyCustomer: {
        customer_fio: 'Иван Иванович Иванов',
        customer_email: 'mail@example.ru',
        customer_phone: '+7 (999) 999-99-99',
      },
    }
  },
  methods: {
    getProjectUrl(project) {
      return `/projects/${project.project_id}`;
    },

    async saveCustomer() {
      this.$store.state.isLoading = true;
      const params = { ...this.customerData };
      const res = await this.$store.dispatch('fetchData', { url: '/customers/save', params });

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
    async deleteCustomer() {
      this.$store.state.isLoading = true;
      const params = { customer_id: this.customerData.customer_id };
      const res = await this.$store.dispatch('fetchData', { url: '/customers/delete', params });

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
          window.location.href = `/customers/`;
        }
      }
      this.$store.state.isLoading = false;
    },


    cancelChanges() {
      this.customerData = this.isNewCustomer ? this.emptyCustomer : this.customer;
    },

    handleConfirmation() {
      this.deleteCustomer();
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
      return this.isNewCustomer === true ? 'Создать' : 'Сохранить';
    },
  },
  created() {
    if(this.isNewCustomer)
      this.customerData = cloneDeep(this.emptyCustomer);
  }
}
</script>

<style lang="scss">
.customer-page {

  --accent-color: #906fe9;

  > * + * {
    margin-top: 20px;
  }

  &__fields {
    display: flex;
    margin: -10px;

    > * {
      flex: 1 1 auto;
      margin: 10px;
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

    > * + * {
      margin-top: 20px;
    }
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

  &__project {
    padding: 15px;
    border: 3px solid var(--input-border-color, #906fe9);
    border-radius: 5px;
    box-shadow: 0 0 4px 4px rgb(154 161 177 / 15%), 0 4px 4px 1px rgb(91 94 105 / 15%), 0 4px 4px -2px rgb(91 94 105 / 15%);

    &-title {
      padding: 10px;
      background: #fff;
      border: 1px solid var(--input-border-color, #D3D8DB);
    }

    &-link {
      display: block;
      color: #1fe09e;
      cursor: pointer;
      font-weight: bold;

      &:hover {
        color: inherit;
      }
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

  &__button {
    flex: 1 1 auto;
    margin: 10px;
    min-width: 200px;
  }

  &__modal {
    &-text {
      margin: 10px;
      font-size: 18px;
    }

    &-controls {
      display: flex;
      justify-content: space-between;
    }
  }
}
</style>