<template>
  <div class="statistics-page">
    <div
    v-text="'Статистика'"
    class="statistics-page__page-title" />

    <div class="statistics-page__content">
      <div class="statistics-page__stat">
        <div
        v-text="'Статистка проекта'"
        class="statistics-page__subtitle" />

        <div class="statistics-page__stat-content">
          <div class="statistics-page__stat-fields">
            <field-container
            title="Проект"
            class="task-page__field">
              <field-dropdown
              class="task-page__dropdown"
              v-model="selectedProject"
              :data="projects"
              searchable
              placeholder="Проект"
              value-name="project_id"
              label-name="project_title" />
            </field-container>

            <field-container
            title="ID проекта"
            class="task-page__field">
              <field-input
              v-model="selectedProject"
              :data-vv-as="' '"
              type="number"
              name="project_id" />
            </field-container>
          </div>
          <div class="statistics-page__controls">
            <a
            v-show="selectedProject"
            v-text="'Экспорт'"
            :href="projectStatUrl"
            target="_blank"
            class="button button--positive statistics-page__control"/>
          </div>
        </div>
      </div>

      <div class="statistics-page__stat">
        <div
        v-text="'Статистка сотрудника'"
        class="statistics-page__subtitle" />

        <div class="statistics-page__stat-content">
          <div class="statistics-page__stat-fields">
            <field-container
            title="Сотрудник"
            class="task-page__field">
              <field-dropdown
              class="task-page__dropdown"
              v-model="selectedEmp"
              :data="emps"
              searchable
              placeholder="Сотрудник"
              value-name="emp_id"
              label-name="emp_fio" />
            </field-container>

            <field-container
            title="ID сотрудника"
            class="task-page__field">
              <field-input
              v-model="selectedEmp"
              :data-vv-as="' '"
              type="number"
              name="project_id" />
            </field-container>


          </div>
          <div class="statistics-page__controls">
            <a
            v-show="selectedEmp"
            v-text="'Экспорт'"
            :href="empStatUrl"
            target="_blank"
            class="button button--positive statistics-page__control"/>
          </div>
        </div>
      </div>

    </div>

  </div>
</template>

<script>

import Modal from '../Modal';

import FieldContainer from "../Fields/FieldContainer";
import FieldDropdown from "../Fields/FieldDropdown";
import FieldInput from "../Fields/FieldInput";

export default {
  name: "ManagementPage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput },
  props: {
    emps: { type: Array, default: () => [] },
    projects: { type: Array, default: () => [] },
  },
  data() {
    return {
      selectedProject: null,
      selectedEmp: null,
    }
  },
  methods: {
    exportProjStat() {

    }
  },
  computed: {
    projectStatUrl() {
      return `/statistics/project/${this.selectedProject}`;
    },
    empStatUrl() {
      return `/statistics/emp/${this.selectedEmp}`;
    }
  },
}
</script>

<style lang="scss">
.statistics-page {

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

  &__stat {

    &-content {
      > * + * {
        margin-top: 20px;
      }
    }

    &-fields {
      display: flex;
      margin: -10px;

      > * {
        max-width: 300px;
        flex: 1 1 auto;
        margin: 10px;
      }

    }

    &-controls {

    }

    > * + * {
      margin-top: 10px;
    }
  }

  &__control {
    max-width: 200px;

    &:hover {
      color: #ccc;
    }
  }

  &__subtitle {
    font-weight: bold;
    font-size: 20px;
    color: #1fe09e;
  }

  &__header {
    > * + * {
      margin-top: 20px;
    }
  }


}
</style>