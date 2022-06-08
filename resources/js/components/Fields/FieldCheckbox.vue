<template>
  <div class="field-checkbox">
    <div class="field-checkbox__inner">
      <label
      v-for="(item, index) in data"
      :key="index"
      class="field-checkbox__item"
      :class="getModifiers(item)">

        <input
        v-model="model"
        v-on="listeners"
        v-bind="$attrs"
        :value="getValue(item)"
        type="checkbox"
        class="field-checkbox__input"/>

        <v-icon
        name="check"
        class="field-checkbox__icon"/>

        <slot
        v-if="hasLabel"
        name="label"
        :data="item">
          <div
          class="field-checkbox__label"
          v-text="getLabel(item)"/>
        </slot>

      </label>
    </div>
  </div>
</template>
<script>
export default {
  name: 'FieldCheckbox',
  inheritAttrs: false,
  props: {
    data: { type: [Array, Object], required: true },

    hasLabel: { type: Boolean, default: true },

    value: { type: null, required: true },
    valueName: { type: String },
    labelName: { type: String },

    trueValue: { type: [String, Number, Boolean], default: true },
    falseValue: { type: [String, Number, Boolean], default: false },

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
      const isMultiple = Array.isArray(this.value);
      item = this.getValue(item);

      return isMultiple ?
      this.value.includes(item) : this.value == this.trueValue;
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
        'field-checkbox__item_active': this.isChecked(item),
        'field-checkbox__item_error': this.isError,
        'field-checkbox__item_disabled': 'disabled' in this.$attrs && this.$attrs.disabled !== false
      }
    }
  }
}
</script>

<style lang="scss">

.field-checkbox {
  // Custom Properties:
  // ==================
  //   --accent-color
  //   --field-checkbox-error-color
  //   --field-checkbox-size

  --field-checkbox-computed-accent-color: #906fe9;

  &__inner {
    > * + * {
      margin-top: 10px;
    }
  }

  &__item {
    display: flex;
    align-items: center;

    cursor: pointer;

    &:hover .field-checkbox__icon {
      border-color: var(--field-checkbox-computed-accent-color);

    }

    &_active {

      .field-checkbox__icon {
        border-color: var(--field-checkbox-computed-accent-color);
        background-color: var(--field-checkbox-computed-accent-color);
        > * {
          visibility: visible;
        }
      }
    }

    &_error {
      --field-checkbox-computed-accent-color: #eb3d00;
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

    &:focus + .field-checkbox__icon {
      border-color: var(--field-checkbox-computed-accent-color);
    }
  }

  &__icon {
    flex: 0 0 auto;

    width: var(--field-checkbox-size, 20px);
    height: var(--field-checkbox-size, 20px);
    padding: calc(var(--field-checkbox-size, 20px) * 0.1);

    border: 1px solid #D3D8DB;
    background-color: #ffffff;
    color: #ffffff;

    border-radius: 2px;
    transition: .2s;
    cursor: pointer;

  }

  &__label {
    margin-left: 10px;
  }
}
</style>