<template>
  <div class="login-page">
    <div class="login-page__form">
      <field-container
      :errors="veeErrors.collect('emp_login')"
      :title="'Логин'"
      class="login-page__field"
      isRequired>
        <field-input
        v-model="login"
        v-validate="'required'"
        :isError="veeErrors.has('emp_login')"
        :data-vv-as="' '"
        maxlength="255"
        name="emp_login" />
      </field-container>

      <field-container
      :errors="veeErrors.collect('emp_password')"
      :title="'Пароль'"
      class="login-page__field"
      isRequired>
        <field-input
        v-model="password"
        v-validate="'required'"
        :isError="veeErrors.has('emp_password')"
        :data-vv-as="' '"
        maxlength="255"
        type="password"
        name="emp_password" />
      </field-container>
    </div>
    <div class="login-page__controls">
      <button
      @click="sendForm"
      v-text="'Войти'"
      class="button button--positive login-page__control"/>
    </div>
  </div>
</template>

<script>

import FieldContainer from "../Fields/FieldContainer";
import FieldInput from "../Fields/FieldInput";

//import Sidebar from './Sidebar';
// import NewPinForm from "./NewPinForm";
// import MainMap from "./MainMap";
// import CheckBoxInput from "./CheckBoxInput";
import {findIndex, cloneDeep} from 'lodash';

export default {
  name: "MainPage",
  components: { FieldContainer, FieldInput },
  data() {
    return {
      login: null,
      password: null,
    }
  },
  methods: {
    async sendForm() {
      this.$store.state.isLoading = true;
      const params = { emp_login: this.login, emp_password: this.password };
      const res = await this.$store.dispatch('fetchData', {url: '/login', params});
      this.$store.state.isLoading = false;

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

    },
  },
}
</script>

<style lang="scss">
.login-page {
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;

  &__form {
    > * + * {
      margin-top: 20px;
    }
  }

  &__controls {

  }

  &__control {
    width: 150px;
  }

  > * + * {
    margin-top: 20px;
  }
}

</style>