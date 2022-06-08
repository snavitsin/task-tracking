<template>
  <div class="field-radio">
    <div class="field-radio__inner">
      <label
      v-for="(item, index) in data"
      :key="index"
      class="field-radio__item"
      :class="getModifiers(item)">
        <input
        v-model="model"
        v-on="listeners"
        v-bind="$attrs"
        :value="getValue(item)"
        type="radio"
        class="field-radio__input"/>

        <div class="field-radio__icon"/>

        <slot
        v-if="hasLabel"
        name="label"
        :data="item">
          <div
          class="field-radio__label"
          v-text="getLabel(item)"/>
        </slot>

      </label>
    </div>
  </div>

</template>
<script>
export default {
  name: 'FieldRadio',
  inheritAttrs: false,
  props: {
    data: { type: [Array, Object], required: true },

    value: { type: null, required: true },
    valueName: { type: String },
    labelName: { type: String },

    hasLabel: { type: Boolean, default: true },

    isError: { type: Boolean, default: false },
  },

  computed: {
    model: {
      get() {
        return this.value
      },

      set(value) {
        this.$emit('input', value)
      }
    },

    listeners() {
      const { input, change, ...listeners } = this.$listeners;
      return listeners;
    }
  },

  methods: {

    isChecked(item) {
      return this.value === this.getValue(item);
    },

    getValue(item) {
      const { valueName } = this;
      return valueName ? item[valueName] : item;
    },

    getLabel(item) {
      const { labelName } = this;
      return labelName ? item[labelName] : item;
    },

    getModifiers(item) {
      return {
        'field-radio__item_active': this.isChecked(item),
        'field-radio__item_error': this.isError,
        'field-radio__item_disabled': 'disabled' in this.$attrs && this.$attrs.disabled !== false
      }
    }
  }
}
</script>

<style lang="scss">

.field-radio {
  // Custom Properties:
  // ==================
  //   --accent-color
  //   --field-radio-error-color
  //   --field-radio-size

  --field-radio-computed-accent-color: #906fe9;


  &__inner {
    > * + * {
      margin-top: 10px;
    }
  }

  &__item {
    display: flex;
    align-items: center;

    cursor: pointer;

    &:hover .field-radio__icon {
      border-color: var(--field-radio-computed-accent-color);
    }

    &_active {

      .field-radio__icon {
        border-width: calc(var(--field-radio-size, 20px) / 2 * 0.6);
        border-color: var(--field-radio-computed-accent-color);
      }
    }

    &_error {
      --field-radio-computed-accent-color: #eb3d00;
    }

    &_disabled {
      opacity: 0.6;
      pointer-events: none;
    }
  }

  &__input {
    flex: 0 0 auto;

    position: absolute;
    border: 0;
    margin: -1px;
    padding: 0;
    width: 1px;
    height: 1px;
    overflow: hidden;
    clip: rect(0px, 0px, 0px, 0px);

    &:focus + .field-radio__icon {
      border-color: var(--field-radio-computed-accent-color);
    }
  }

  &__icon {
    flex: 0 0 auto;

    width: var(--field-radio-size, 20px);
    height: var(--field-radio-size, 20px);
    border: 1px solid #D3D8DB;
    background-color: #fff;
    border-radius: 100%;
    transition: .2s;
    cursor: pointer;
  }

  &__label {
    margin-left: 10px;
  }
}
</style>