<template>
  <div class="subdivisions-page">
    <div
    v-text="'Подразделения'"
    class="subdivisions-page__title" />
    <div class="subdivisions-page__content">
      <div
      v-for="subdiv in subdivsData"
      :key="subdiv.subdiv_id"
      class="subdivisions-page__subdiv">


        <div class="subdivisions-page__subdiv-title">
          <a
          v-text="subdiv.subdiv_title"
          :href="getSubdivUrl(subdiv)"
          class="subdivisions-page__subdiv-link" />
        </div>
        <div
        v-text="getSubdivCountText(subdiv)"
        class="subdivisions-page__subdiv-count" />
        <div
        v-text="subdiv.subdiv_desc"
        class="subdivisions-page__subdiv-desc" />
      </div>
    </div>
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
  name: "SubdivisionsPage",
  components: { Modal, FieldContainer, FieldDropdown, FieldInput, FieldTextarea, FieldDatepicker },
  props: {
    subdivs: { type: Array, default: () => [] },
  },
  data() {
    return {
      subdivsData: this.subdivs,
    }
  },
  methods: {
    getSubdivCountText(subdiv) {
      return `Сотрудники: ${subdiv.subdiv_emp_count}`;
    },

    getSubdivUrl(subdiv) {
      return `/subdivisions/${subdiv.subdiv_id}`;
    },

  },
}
</script>

<style lang="scss">
.subdivisions-page {

  --accent-color: #906fe9;

  > * + * {
    margin-top: 20px;
  }

  &__content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    grid-auto-rows: 1fr;
    gap: 20px;
  }

  &__subdiv {
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