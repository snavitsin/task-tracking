<template>
  <div class="popover">
    <div 
    class="popover__trigger" 
    ref="trigger">
      <slot 
      name="trigger" 
      :data="popoverInterface"/>
    </div>
    <div
    class="popover__popper"
    :style="{'max-height': `${popoverOptions.maxHeight || 250}px`, 'overflow': `${popoverOptions.overflow || 'auto'}`}"
    ref="popper">
      <slot :data="popoverInterface"/>
    </div>
  </div>
</template>
<script>
import { get, set } from 'lodash';
import tippy, { sticky } from 'tippy.js';

const sameWidth = {
  name: 'sameWidth',
  enabled: true,
  phase: 'beforeWrite',
  requires: ['computeStyles'],
  fn: ({ state }) => {
    const { width } = state.rects.reference;
    state.styles.popper.width = `${width}px`;
  },

  effect: ({ state }) => {
    const { clientWidth } = state.elements.reference;
    state.elements.popper.style.width = `${clientWidth}px`;
  },
};

export default {
  name: 'Popover',

  props: {
    popoverOptions: { type: Object, default: () => ({}) }
  },

  data: () => ({
    tippy: null
  }),

  computed: {
    defaults() {
      return {
        arrow: true,
        interactive: true,
        placement: 'bottom',
        theme: 'popover',
        trigger: 'click',
        maxWidth: '',
        appendTo: () => document.body,
        zIndex: 200,
      };
    },

    popoverInterface() {
      return {
        show: this.show,
        hide: this.hide,
        info: get(this.tippy, 'state') || {},
        update: this.update,
        tippy: this.tippy,
      };
    }
  },

  methods: {
    setupTippy() {
      if (!this.tippy) {
        const { trigger, popper } = this.$refs;
        let { hasSameWidth, maxHeight, overflow, ...tippyOptions } = this.popoverOptions;

        tippyOptions = {
          ...this.defaults,
          ...tippyOptions,
          plugins: [ sticky ],
          content: popper
        };

        if (hasSameWidth) set(tippyOptions, 'popperOptions.modifiers', [sameWidth]);

        this.tippy = tippy(trigger, tippyOptions);

        this.$emit('popover:init', this.popoverInterface);
      }
    },

    setSameWidth(data) {
      const { width, left, right } = data.offsets.reference;

      data.styles.width = width;
      data.offsets.popper.width = width;
      data.offsets.popper.left = left;
      data.offsets.popper.right = right;

      return data;
    },

    show() {
      if (this.tippy) this.tippy.show();
    },

    hide() {
      if (this.tippy) this.tippy.hide();
    },

    update() {
      if (this.tippy) this.tippy.popperInstance.update();
    }
  },

  mounted() {
    this.setupTippy();
    this.$on('hook:beforeDestroy', () => {
      this.tippy.destroy();
      this.tippy = null;
    });
  }
};
</script>
<style lang="scss">
.popover {
  &__trigger {
    outline: none;
    cursor: pointer;
  }

  &__popper {
    padding: 20px;
    outline: none;
    overflow: auto;
  }
}

.tippy-box[data-theme~='popover'] {
  color: #2A3E4C;
  text-align: left;

  box-shadow: 0 0 20px 4px rgba(154, 161, 177, 0.15),
    0 4px 80px -8px rgba(36, 40, 47, 0.25),
    0 4px 4px -2px rgba(91, 94, 105, 0.15);
  background-color: #fff;

  &[data-placement^='top'] > .tippy-arrow::before {
    border-top-color: #fff;
  }

  &[data-placement^='bottom'] > .tippy-arrow::before {
    border-bottom-color: #fff;
  }

  &[data-placement^='left'] > .tippy-arrow::before {
    border-left-color: #fff;
  }

  &[data-placement^='right'] > .tippy-arrow::before {
    border-right-color: #fff;
  }

  > .tippy-content {
    padding: 0;
  }

  > .tippy-backdrop {
    background-color: #fff;
  }

  > .tippy-svg-arrow {
    fill: #fff;
  }
}
</style>