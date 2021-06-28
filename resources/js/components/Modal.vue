<template>
  <portal to="modals">
    <div
    class="modal"
    :key="id"
    :class="classList"
    @click.self="close('overlay')">

      <div class="modal__body">

        <div class="modal__close-container"
        @click="close('icon')">
          <v-icon
          @click="close('icon')"
          name="x"
          class="modal__close"/>
        </div>

        <slot
        name="content"
        :data="modalInterface">

          <div class="modal__content">

            <slot
            v-if="title || $scopedSlots.title"
            :data="modalInterface"
            name="title">
              <div
              v-text="title"
              class="modal__title"/>
            </slot>

            <slot
            v-if="text || $scopedSlots.default"
            :data="modalInterface">
              <div
              v-text="text"
              class="wysiwyg modal__text"/>
            </slot>

            <div
            v-if="buttons || $scopedSlots.buttons"
            class="modal__buttons">
              <slot
              :data="modalInterface"
              name="buttons">
                <button
                v-for="button in buttons"
                v-text="button.label"
                @click="click(button)"
                class="button modal__button"
                :class="button.classList"
                type="button"/>
              </slot>
            </div>
          </div>
        </slot>
      </div>

    </div>
  </portal>
</template>
<script>
import { uniqueId, pick, isFunction } from 'lodash';

export default {
  name: 'Modal',

  props: {
    title: { type: String },
    text: { type: String },
    buttons: { type: Array },
    classList: { type: null }
  },

  data() {
    return {
      id: uniqueId('modal-')
    };
  },

  computed: {
    modalInterface() {
      return pick(this, ['close']);
    }
  },

  methods: {
    close(target) {
      this.$emit('modal:close', target);
    },

    click(button) {
      return button.handler && isFunction(button.handler)
      ? button.handler()
      : this.close(button.value);
    },

    init() {
      const handler = event =>
      event.code === 'Escape'
      ? event.stopPropagation() && this.close('icon')
      : false;

      window.addEventListener('keydown', handler);

      this.$once('hook:destroyed', () => {
        window.removeEventListener('keydown', handler);
      });

      document.activeElement.blur();

      this.$emit('modal:init', this.modalInterface);
    }
  },

  mounted() {
    this.init();
  }
};
</script>
<style lang="scss">

.modal {
  display: block;
  position: fixed;
  left: 0;
  right: 0;
  top: 0;
  bottom: 0;
  z-index: 199;

  padding: 20px 55px;
  padding-bottom: 0;

  overflow: auto;

  background-color: rgba(240, 243, 245, 0.96);

  text-align: center;

  &:after {
    content: '';
    display: inline-block;
    vertical-align: middle;
    height: 100%;
  }

  &__body {
    position: relative;
    display: inline-block;
    vertical-align: middle;

    width: 100%;
    max-width: var(--modal-max-width, 1200px);

    padding: 20px;
    margin-bottom: 20px;

    text-align: left;

    background-color: #fff;
    border-radius: 10px;
    box-shadow: 0 13px 26px rgba(0, 0, 0, 0.05);
  }

  &__close {

  }

  &__close-container {
    float: right;

    width: 20px;
    height: 20px;

    margin-top: 10px;
    margin-bottom: 20px;
    margin-left: 20px;

    color: #7b898d;

    cursor: pointer;
    transition: 0.2s;

    &:hover {
      color: #2a3e4c;
    }
  }

  &__content {
    > * + * {
      margin-top: 20px;
    }
  }

  &__title {
    font-size: 20px;
    line-height: 30px;

    color: #2a3e4c;
    font-weight: 500;
  }

  &__text {
    font-size: 15px;
    line-height: 24px;
  }

  &__buttons {
    display: flex;
    flex-wrap: wrap;
    margin-left: -10px;
    margin-right: -10px;
    margin-top: 10px;
  }

  &__button {
    flex: 0 0 auto;
    width: auto;
    min-width: 100px;

    font-size: 11px;
    line-height: 38px;
    margin: 10px;
  }

  @media (max-width: 760px) {
    padding: 20px;
  }
}
</style>