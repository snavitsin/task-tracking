<template>
  <div class="projects-page">
    <div
    v-text="'Проекты'"
    class="projects-page__title" />
    <div class="projects-page__content">
      <div
      v-for="project in projectsData"
      :key="project.project_id"
      class="projects-page__project">


        <div class="projects-page__project-title">
          <a
          v-text="project.project_title"
          :href="getProjectUrl(project)"
          target="_blank"
          class="projects-page__project-link" />
        </div>
        <div
        v-text="getProjectTasksCountText(project)"
        class="projects-page__project-count" />
        <div
        v-text="project.project_desc"
        class="projects-page__project-desc" />
      </div>
    </div>
  </div>
</template>

<script>


export default {
  name: "ProjectsPage",
  components: { },
  props: {
    projects: { type: Array, default: () => [] },
  },
  data() {
    return {
      projectsData: this.projects,
    }
  },
  methods: {
    getProjectTasksCountText(project) {
      return `Задачи: ${project.project_tasks.length}`;
    },

    getProjectUrl(project) {
      return `/projects/${project.project_id}`;
    },

  },
}
</script>

<style lang="scss">
.projects-page {

  --accent-color: #906fe9;

  > * + * {
    margin-top: 20px;
  }

  &__title {
    color: #906fe9;
    font-weight: bold;
    font-size: 24px;
  }

  &__content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    grid-auto-rows: 1fr;
    gap: 20px;
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

    &-desc {
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