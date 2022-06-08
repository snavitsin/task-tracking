<template>
    <div class="field-input">
        <input
        v-bind="$attrs"
        v-on="listeners"
        :value="value"
        :type="type"
        :class="modifiers"
        v-mask="mask"
        class="input field-input__input"/>
    </div>
</template>
<script>
export default {
    name: 'FieldInput',
    inheritAttrs: false,
    props: {
        type: {
            type: String,
            default: 'text',
            validator: type => ['email', 'text', 'password', 'number'].includes(type)
        },
        value: { type: [Number, String] },
        isError: { type: Boolean, default: false }
    },

    computed: {
        listeners() {
            return {
                ...this.$listeners,
                input: event => {
                    this.$emit('input', event.target.value);
                }
            }
        },

        modifiers() {
            return {
                'field-input__input_error': this.isError,
                'field-input__input_disabled': 'disabled' in this.$attrs && this.$attrs.disabled !== false,
                'field-input__input_readonly': 'readonly' in this.$attrs && this.$attrs.readonly !== false
            }
        },

        mask(){
            let mask = null;
            if (this.$attrs.mask){
                mask = {autoUnmask: !!this.$attrs.mask.isUnmask};
                if (!!this.$attrs.mask.regex){
                    mask.regex = this.$attrs.mask.regex;
                }
                else {
                    mask.mask = this.$attrs.mask.value;
                }

                if (this.$attrs.mask.placeholder){
                    mask.placeholder = this.$attrs.mask.placeholder;
                }
            }
            return mask;
        }
    }
}
</script>
<style lang="scss">


.field-input {

    --field-input-computed-accent-color: var(--accent-color, #906fe9);

    &__input {

        &:focus,
        &:hover {
            border-color: var(--field-input-computed-accent-color);
        }

        &_error {
            --field-input-computed-accent-color: var(--field-input-error-color, #ff5555);
            --input-border-color: var(--field-input-computed-accent-color);
        }

        &_readonly {
            pointer-events: none;
        }

        &_disabled {
            background-color: var(--field-input-disabled-color, #F0F3F5);
            opacity: 0.6;
            pointer-events: none;
        }
    }
}
</style>
