<template>
    <transition name="fade">
        <div class="loader">
            <div class="loader-progress">
                <div
                v-for="i in 3"
                :key=i
                :style="{ animationDelay: (i*250)+'ms' }" />
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'MainLoader',
    props: { },
    data() {
        return {

        }
    },
    methods: { },
    watch: { }
}
</script>

<style scoped>
.loader {
    position: absolute;
    width: inherit;
    height: inherit;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    background: rgba(78, 78, 78, 0.5);
    transition: all 1s ease-out;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 999999;
}

.loader-progress {
    width: 120px;
    height: 120px;
    display: flex;
    justify-content: center;
    align-items: center;
    position: relative;
}

.loader-progress > div {
    width: 12px;
    height: 12px;
    background: #999;
    border-radius: 3px;
    right: 10px;
    animation: circleKey 1s linear infinite;
    margin: 5px;
}

@keyframes circleKey {
    0% {
        background: #888;
        transform: scale(1);
    }
    50% {
        background: #f3f3f3;
        transform: scale(1.3);
    }
    100% {
        background: #888;
        transform: scale(1);
    }
}

.fade-enter-active, .fade-enter-active .busy-dialog,
.fade-leave-active, .fade-leave-active .busy-dialog {
    transition: all 0.2s ease-out;
}

.fade-enter {
    opacity: 0;
}

.fade-enter .busy-dialog {
    transform: scale(0.9);
}

.fade-leave-to {
    opacity: 0;
}

.fade-leave-to .busy-dialog {
    transform: scale(0.9);
}

</style>
