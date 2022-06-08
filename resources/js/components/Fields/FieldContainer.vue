<template>
    <div
    class="field-container">
        <div
        class="field-container__inner"
        :class="{'field-container__inner_required': isRequired}">
            <div
            v-if="title || $slots.title"
            class="field-container__title">
                <slot name="title">
                    <div
                    v-html="title"
                    class="field-container__title-content" />
                </slot>

                <slot
                v-if="helpTitle || $slots.helpTitle"
                name="helpTitle">
                    <icon
                    v-tippy="tooltipOptions"
                    name="action/help"
                    class="field-container__title-help" />
                </slot>
            </div>

            <div
            v-if="description || $slots.description"
            class="field-container__description">
                <slot name="description">
                    {{ description }}
                </slot>
            </div>

            <div class="field-container__content">
                <slot />
            </div>

            <div
            v-if="errors && errors.length"
            class="field-container__errors">
                <div
                v-for="error in errors"
                v-text="error"
                :key="error"
                class="field-container__error" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'FieldContainer',
    props: {
        title: { type: String, default: '' },
        description: { type: String, default: '' },
        errors: { type: Array, default: () => [] },
        helpTitle: { type: String, default: '' },

        isRequired: { type: Boolean, default: true },
    },

    computed: {
        tooltipOptions() {
            return {
                trigger: 'click',
                content: this.helpTitle,
                interactive: true,
                allowHTML: true,
                appendTo: document.body,
            };
        },
    },
};
</script>
<style lang="scss">

.field-container {

    &__inner {
        > * + * {
            margin-top: 10px;
        }
    }

    &__title {
        font-size: 16px;
        line-height: 20px;
        display: flex;

        > * + * {
            margin-left: 10px;
        }
    }

    &__title-content {
        flex: 0 1 auto;

    }

    &__title-help {
        flex: 0 0 auto;

        color: rgba(#7e888c, .4);

        cursor: pointer;
        outline: none;

        &:hover {
            color: #7e888c;
        }
    }

    &__description {
        font-size: 16px;
        line-height: 20px;

        color: #7E888C;
    }

    &__errors {
        > * + * {
            margin-top: 5px;
        }
    }

    &__error {
        font-size: 12px;
        line-height: 15px;
        color: #ff5555;
    }

    &__inner_required > &__title > &__title-content::after {
        content: '\A0*';
        color: #ff5555;
    }
}
</style>
