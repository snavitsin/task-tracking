<template>
  <div
  class="field-textarea">

    <div
    class="field-textarea__inner"
    :class="modifiers">

      <textarea
      v-bind="$attrs"
      v-on="listeners"
      :value="value"
      class="textarea field-textarea__textarea"/>

      <div
      v-if="max"
      class="field-textarea__max">Осталось символов: {{symbolsLeft}}</div>

    </div>
  </div>
</template>
<script>
export default {
  name: 'FieldTextarea',
  inheritAttrs: false,
  props: {
    isError: { type: Boolean, default: false },
    max: { type: [String, Number] },
    value: { type: null }
  },

  computed: {

    listeners() {
      return {
        ...this.$listeners,
        input: event => {
          let { value } = event.target;

          if(this.max) {
            value = value.slice(0, this.max);
            event.target.value = value;
          }

          this.$emit('input', value);
        }
      }
    },

    modifiers() {
      return {
        'field-textarea__inner_error': this.isError,
        'field-textarea__inner_disabled': 'disabled' in this.$attrs && this.$attrs.disabled !== false
      }
    },

    symbolsLeft() {
      if(!this.max) return '';

      const { max, value } = this;
      const length = value ? value.length : 0;
      return max - length;
    }
  }
}
</script>
<style lang="scss">

.field-textarea {
  --field-textarea-computed-accent-color: var(--accent-color, #906fe9);

  &__inner {
    position: relative;

    &_error {
      --field-textarea-computed-accent-color: var(--field-textarea-error-color, #ff5555);
      --textarea-border-color: var(--field-textarea-computed-accent-color);
    }

    &_disabled {
      opacity: 0.6;
      pointer-events: none;

      .field-textarea__textarea {
        background-color: var(--field-textarea-disabled-color, #F0F3F5);
      }
    }
  }

  &__textarea:hover,
  &__textarea:focus {
    border-color: var(--field-textarea-computed-accent-color);
  }

  &__max {
    position: absolute;
    left: 2px;
    bottom: 2px;

    padding: 0 20px;

    font-size: 13px;
    line-height: 30px;

    color: #3D3D3D;
    background-color: rgba(#fff, .8);
  }

}
</style>
