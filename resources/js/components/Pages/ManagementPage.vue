<template>
  <div class="management-page">
    <div
    v-text="'Управление'"
    class="management-page__page-title" />

    <div class="management-page__content">
      <tabs
      v-model="selectedTab"
      :data="tabs"
      value-name="tab_id"
      label-name="tab_title"
      class="task-list__tabs"/>

      <template v-if="selectedTab === 1">
        <task-page
        :customers="customers"
        :subdivisions="subdivisions"
        :priority="priority"
        :statuses="statuses"
        :projects="projects"
        :developers="developers"
        :testers="testers"
        :is-new-task="true"/>
      </template>
      <template v-else-if="selectedTab === 2">
        <project-page
        :customers="customers"
        :subdivisions="subdivisions"
        :is-new-project="true"/>
      </template>
      <template v-else-if="selectedTab === 3">
        <subdivision-page
        :is-new-subdiv="true"/>
      </template>
      <template v-else-if="selectedTab === 4">
        <customer-page
        :is-new-customer="true"/>
      </template>

      </div>

  </div>
</template>

<script>

import Modal from '../Modal';
import Tabs from '../Tabs';
import ProjectPage from './ProjectPage';
import TaskPage from './TaskPage';
import SubdivisionPage from './SubdivisionPage';
import CustomerPage from './CustomerPage';

export default {
  name: "ManagementPage",
  components: { Modal, ProjectPage, TaskPage, SubdivisionPage, CustomerPage, Tabs },
  props: {
    project: { type: Object, default: () => {} },
    customers: { type: Array, default: () => [] },
    priority: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    developers: { type: Array, default: () => [] },
    testers: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
    subdivisions: { type: Array, default: () => [] },
    isNewProject: { type: Boolean, default: () => false },
  },
  data() {
    return {
      selectedTab: 1,
    }
  },
  methods: {

  },
  computed: {
    tabs() {
      let tabs = [
        { 'tab_id': 1, 'tab_title': 'Новая задача' },
        { 'tab_id': 2, 'tab_title': 'Новый проект' }
      ];
      if(this.$store.getters.checkRole('chief')) {
        tabs = tabs.concat([
          { 'tab_id': 3, 'tab_title': 'Новое подразделение' },
          { 'tab_id': 4, 'tab_title': 'Новый заказчик' },
          //{ 'tab_id': 5, 'tab_title': 'Роли пользователей' },
        ]);
      }
      return tabs;
    },
  },
}
</script>

<style lang="scss">
.management-page {

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
    > * + * {
      margin-top: 20px;
    }
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