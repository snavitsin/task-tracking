<template>
  <div class="tabs">
    <div class="tabs__inner">
      <template v-for="(tab, index) in data">
        <slot :data="{tabsInterface, tab}">
          <div
          @click="select(tab)"
          :key="index"
          class="tabs__item"
          :class="{'tabs__item--active': value === getValue(tab)}">
            <slot name="label" :data="{tabsInterface, tab}">
              <span class="tabs__item_name" v-text="getLabel(tab)" />
            </slot>
            <slot name="count" :data="{tabsInterface, tab}">
                        <span
                        class="tabs__item_count"
                        v-text="getCount(tab)"
                        v-if="countName"/>
            </slot>
          </div>
        </slot>
      </template>
    </div>
  </div>
</template>

<script>
import { get, pick } from 'lodash';

export default {
  name: 'Tabs',

  props: {
    data: { type: Array, default: () => ([]) },
    value: { type: null },

    valueName: { type: String },
    labelName: { type: String },
    countName: { type: String },
  },

  computed:{
    tabsInterface(){
      return pick(this, ['getValue', 'getLabel', 'isActive', 'select', 'getCount']);
    }
  },

  methods: {
    getValue(item) {
      const { valueName } = this;
      return valueName ? get(item, valueName) : item;
    },

    getLabel(item) {
      const { labelName } = this;
      return labelName ? get(item, labelName) : item;
    },

    getCount(item) {
      const { countName } = this;
      return countName ? get(item, countName) : 0;
    },

    isActive(item) {
      return this.getValue(item) === this.value;
    },

    select(item) {
      this.$emit('input', this.getValue(item));
    }
  }
}
</script>
<style lang="scss">
.tabs {
  &__inner {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    border-bottom: 1px solid #D3D8DB;
  }

  &__item {
    flex: 0 0 auto;

    position: relative;

    padding-left: 10px;
    padding-right: 10px;
    font-size: 13px;
    line-height: 45px;
    font-weight: bold;
    color: #7B898D;

    cursor: pointer;

    &:hover {
      color: #2A3E4C;
    }

    &--active {
      position: relative;
      color: #2A3E4C;

      &::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -2px;
        height: 2px;
        background-color: currentColor;
      }
    }

    &_count{
      font-weight: normal;
      margin-left: 10px;
    }
  }
}
</style>
