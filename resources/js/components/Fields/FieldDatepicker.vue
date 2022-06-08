<template>
  <div class="field-datepicker">
    <popover
    :popover-options="popoverOptions"
    class="field-datepicker__inner"
    :class="modifiers">
      <template #trigger>
        <input
        type="text"
        placeholder="Выберите дату"
        class="input field-datepicker__input"
        :value="date"
        readonly/>
      </template>

      <template #default="{ data: { hide } }">
        <datepicker
        v-bind="$attrs"
        v-on="listeners"
        @input="select($event, hide)"
        :value="value"
        class="field-datepicker__datepicker"/>
      </template>
    </popover>
  </div>
</template>
<script>
import { format, parse } from 'date-fns';
import ruLocale from 'date-fns/locale/ru';

import Popover from './Popover';
import Datepicker from './Datepicker';

export default {
  name: 'FieldDatepicker',
  components: { Popover, Datepicker },
  inheritAttrs: false,

  props: {
    isError: { type: Boolean },
    inputFormatter: { type: String },
    value:  { type: [String, Array, Date] }
  },

  computed: {
    date() {
      const { formatter, type } = this.$attrs;
      const isRange = type === 'range';
      
      if(!this.value || (isRange && !this.value.filter(Boolean).length)) return '';

      const isShowTime = 'showTime' in this.$attrs && this.$attrs.showTime !== false;

      const defaultFormatter = isShowTime ?
          'dd MMMM yyyy HH:mm' : 'dd MMMM yyyy';

      const iterator = value => {
        const date = formatter ?
          parse(value, formatter, new Date()) : value;

        const token = this.inputFormatter || defaultFormatter;

        return format(date, token, {locale: ruLocale});
      };

      return isRange ?
          this.value.map(iterator).join(' – ') : iterator(this.value);
    },

    listeners() {
      const { input, ...listeners } = this.$listeners;
      return listeners;
    },

    modifiers() {
      return {
        'field-datepicker__inner_error': this.isError,
        'field-datepicker__inner_disabled': this.$attrs.disabled
      }
    },

    popoverOptions() {
      const popperOptions = {
        modifiers: {
          setArrow: {
            enabled: true,
            order: 849,
            fn: data => {
              data.offsets.arrow.left = 20;
              return data;
            }
          }
        }
      }
      return {
        popperOptions,
        placement: 'bottom-start',
        maxHeight: 500
      }
    }
  },

  methods: {
    select(date, hide) {
      this.$emit('input', date);
      hide();
    }
  }
}
</script>
<style lang="scss">

.field-datepicker {
  // Custom Properties:
  // ==================
  //   --accent-color
  //   --field-datepicker-input-error-color
  //   --field-datepicker-input-disabled-color

  //   --input-height
  //   --input-padding-left
  //   --input-padding-right
  //   --input-border-radius

  --field-datepicker-accent-color: var(--accent-color, #906fe9);

  &__input {
    &:focus,
    &:hover {
      border-color: var(--field-datepicker-accent-color);
    }
  }

  &__inner_error {
    --field-datepicker-accent-color: var(--field-datepicker-input-error-color, #ff5555);
    --input-border-color: var(--field-datepicker-input-error-color, #ff5555);
  }

  &__inner_disabled {
    opacity: 0.6;
    pointer-events: none;

    .field-datepicker__input {
      background-color: var(--field-datepicker-input-disabled-color, #F0F3F5);
    }
  }
}
</style>
