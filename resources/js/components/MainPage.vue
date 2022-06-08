<template>
  <div class="main-page">

    <kanban-board
    :tasks="tasks"
    :statuses="statuses"
    :editable="editable" />

  </div>
</template>

<script>

import KanbanBoard from './KanbanBoard';
// import NewPinForm from "./NewPinForm";
// import MainMap from "./MainMap";
// import CheckBoxInput from "./CheckBoxInput";
import { findIndex, cloneDeep } from 'lodash';

export default {
  name: "MainPage",
  components: { KanbanBoard },
  props: {
    tasks: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    editable: { type: Boolean, default: () => true },
  },
  data() {
    return {
      filters: {

      },
    }
  },
  methods: {

  },

  computed: {

  }
}
</script>

<style lang="scss">
.main-page {

  &__map {
    position: absolute;
  }

  &__sidebar {
    &-content {
      padding: 30px;
      display: flex;
      height: 100%;
      flex-direction: column;
      justify-content: space-evenly;

      > * + * {
        margin-top: 20px;
      }
    }

    &--hidden {
      width: 0;

      .main-page__sidebar-content {
        display: none;
      }
    }
  }

  &__arrow {
    color: #fff;
    font-size: 35px;
    position: absolute;
    top: 5px;

    &-container {
      position: absolute;
      top: 50px;
      right: -60px;
      width: 40px;
      height: 50px;
      background-color: #4d4d4d;
      border-radius: 5px;
      cursor: pointer;
    }
  }

  &__search {

    &-block {
      flex: 0 1 auto;
    }

    &-container{
      display: flex;
      padding: 10px;
      justify-content: space-evenly;
      background-color: #fff;

      .card__input {
        padding: 0;

      }
    }

    &-icon {
      margin-left: 10px;
      cursor: pointer;
    }
  }

  &__close {
    &-icon {
      margin-left: 10px;
      cursor: pointer;
    }
  }

  &__recycling-types {
    flex: 1 1 auto;
  }

}

</style>