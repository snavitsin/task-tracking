<template>
  <div class="app-header">
    <a
    v-text="'Управление проектами'"
    href="/"
    class="app-header__logo"/>

    <div
    v-if="$store.getters.isAuth"
    class="app-header__login">
      <popover
      class="app-header__popover-trigger"
      :popover-options="{ trigger: 'click', maxWidth: '300px', interactive: true }">
        <template
        #trigger>
          <span
          v-text="'account_circle'"
          class="app-header__login-icon material-icons"/>
          <div
          v-text="$store.state.user.emp_name"
          class="app-header__button"/>
        </template>
        <template #default>
          <div class="app-header__popover">

            <a
            v-for="item in userLinks"
            :key="item.link"
            v-text="item.title"
            :href="item.link"
            class="app-header__popover-item" />
            <div
            v-text="'Выйти'"
            @click="logout"
            class="app-header__popover-item" />
          </div>
        </template>
      </popover>
    </div>

    <div
    v-if="false"
    @click="showLoginSidebar"
    class="app-header__login">
            <span
            v-text="'account_circle'"
            class="app-header__login-icon material-icons"/>
      <div
      v-text="'Вход'"
      class="app-header__button"/>
    </div>

  </div>
</template>

<script>

import Popover from './Fields/Popover';

export default {
  name: "AppHeader",
  components: { Popover },
  data() {
    return {

    }
  },
  methods: {
    async logout() {
      this.$store.state.isLoading = true;
      const res = await this.$store.dispatch('fetchData', { url: '/logout' });
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

    showLoginSidebar() {
      this.$store.state.isLoginSidebarShown = !this.$store.state.isLoginSidebarShown;
    },
  },
  computed: {
    userLinks() {
      const links = [
        { 'title': 'Мои комментарии', 'link': '/comments/my' },
        { 'title': 'Мои задачи', 'link': '/' },
        { 'title': 'Все задачи', 'link': '/tasks/all' },
      ]
      if(this.$store.getters.checkRole('manager')) {
        links.concat([
          { 'title': 'Создание задачи', 'link': '/task/create' },
          { 'title': 'Статистка', 'link': '/statistics' },
        ]);
      }
      return links;
    }
  }
}
</script>

<style lang="scss">

.app-header {
  //display: grid;
  display: flex;
  padding-top: 30px;
  padding-bottom: 30px;
  height: 120px;
  justify-content: space-evenly;
  //grid-template-columns: auto auto;
  //grid-template-rows: auto;
  align-items: center;
  //background-color: #fff;
  color: #1fe09e;

  &__logo {
    text-decoration: none;
    font-size: 30px;
    font-weight: bold;

    &:hover {
    }
  }

  &__button {
    font-weight: bold;
    font-size: 16px;
  }

  &__login {
    display: flex;
    flex-direction: column;
    justify-content: center;
    text-align: center;
    cursor: pointer;
    transition: .2s;

    &-icon {
      font-size: 36px;
    }

    &:hover {
      opacity: .8;
    }
  }
  
  &__popover {

    > * + * {
      margin-top: 10px;
    }

    &-item {
      display: block;
      background: #f7f7f8;
      color: #1fe09e;
      border-radius: 5px;
      padding: 10px;
      cursor: pointer;

      &:hover {
        color: inherit;
      }
    }

    &-trigger {
      position: relative;
      border: none;
      background: none;
      text-align: center;
    }
  }
}
</style>