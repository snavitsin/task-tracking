<template>
  <div class="kanban-board">
    <div
    v-text="title || 'Kanban доска задач'"
    class="kanban-board__title" />

    <div class="kanban-board__content">
      <div
      v-for="group in mappedTasks"
      :key="group.title"
      class="kanban-board__column">
        <div class="kanban-board__column-container">
          <div
          v-text="group.title"
          class="kanban-board__column-title" />
          <draggable
          @change="handleDrag"
          :list="group.tasks"
          :options="{ disabled : !editable }"
          ghost-class="ghost"
          group="group"
          class="kanban-board__group">
            <div
            v-for="task in group.tasks"
            :key="task.task_id"
            class="kanban-board__task">
              <div
              v-text="task.task_priority_title"
              :style="getPriorityStyle(task)"
              class="kanban-board__task-priority" />
              <a
              v-text="getTaskUrlTitle(task)"
              :href="getTaskUrl(task.task_id)"
              target="_blank"
              class="kanban-board__task-number" />
              <div
              v-text="task.task_title"
              class="kanban-board__task-title" />
            </div>
          </draggable>
        </div>
      </div>
    </div>

    <modal
    v-if="isConfirmModalShown"
    @modal:close="isConfirmModalShown = false"
    class="kanban-board__modal">
      <div
      v-text="modalText"
      class="kanban-board__modal-text"/>

      <template #buttons>
        <div class="kanban-board__modal-controls">
          <button
          v-text="'Изменить статус'"
          @click="handleConfirmation()"
          class="button button--positive kanban-board__button"/>
          <button
          v-text="'Отмена'"
          @click="handleCancel()"
          class="button button--neutral kanban-board__button"/>
        </div>
      </template>
    </modal>

  </div>
</template>

<script>
import draggable from 'vuedraggable'
import Modal from './Modal';

export default {
  name: "KanbanBoard",
  components: { draggable, Modal },
  props: {
    title: { type: String, default: () => 'Kanban доска задач' },
    tasks: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    editable: { type: Boolean, default: () => true },
  },
  data() {
    return {
      tasksData: this.tasks,
      drag: false,
      filters: {},
      mappedTasks: [],
      selectedTask: null,
      newStatus: null,
      isConfirmModalShown: false,
    }
  },
  methods: {

    async handleDrag(event) {
      if(event.added) {
        const addedTask = event.added.element;
        this.mappedTasks.forEach(group => {
          if(group.tasks.find(task => task.task_id === addedTask.task_id)) {
            this.selectedTask = addedTask;
            this.newStatus = group.status_id;
            this.statusTitle = group.title;
            this.isConfirmModalShown = true;
          }
        });
      }
    },
    handleConfirmation() {
      this.updateTaskStatus(this.selectedTask.task_id, this.newStatus);
    },
    handleCancel() {
      this.mapTasks();
      this.isConfirmModalShown = false;
    },
    async updateTaskStatus(taskId, statusId) {
      this.$store.state.isLoading = true;
      const params = { task_id: taskId, status_id: statusId };
      const res = await this.$store.dispatch('fetchData', { url: '/tasks/update_status', params });

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
          await this.getTasks();
        }
      }
      this.$store.state.isLoading = false;
    },
    async getTasks() {
      const params = { };
      const res = await this.$store.dispatch('fetchData', { url: '/tasks/get', params });

      if(res?.errors) {
        const self = this;
        Object.keys(res.errors).forEach(function(key) {
          self.$notify({
            type: 'error',
            text: res.errors[key].shift(),
          });
        });
      } else {
        if(res.status === true) {
          this.tasksData = res.data.tasks;
          this.mapTasks();
          this.isConfirmModalShown = false;
        }
      }
    },

    mapTasks() {
      let groups = [];
      this.statuses.forEach(status => {
        groups.push({
          status_id: status.status_id,
          title: status.status_title,
          key: status.status_slug,
          tasks: this.tasksData.filter(task => task.task_status === status.status_id),
        });
      });

      this.mappedTasks = groups;
    },

    getTaskUrl(task_id) {
      return `/tasks/${task_id}`;
    },

    getTaskUrlTitle(task) {
      return `${task.task_project_code}#${task.task_id}`;
    },

    getPriorityStyle(task) {
      return task.task_priority_color ? `background-color: ${task.task_priority_color}` : null;
    }

  },
  computed: {
    modalText() {
      if(this.selectedTask) {
        const taskTtitle = this.selectedTask.task_title;
        return `Вы действительно хотите изменить статус задачи "${taskTtitle}" на "${this.statusTitle}"?`;
      }
    }
  },
  created() {
    this.mapTasks();
  }
}
</script>

<style lang="scss">
.kanban-board {

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
    grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
    grid-auto-rows: 1fr;
    gap: 20px;
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
    display: flex;
    justify-content: flex-start;
    flex-direction: column;;

    &-priority {
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
  }

  &__button {
    flex: 1 1 auto;
    margin: 10px;
    min-width: 200px;
  }
}
</style>