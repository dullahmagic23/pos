<script>
export default {
    name: "NotificationComponent",
    props: {
        message: String,
        type:{
            type: String,
            default: 'info'
        }

    }
}
</script>

<template>
    <div :class="`toast align-items-center text-bg-${type} border-0`" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-header">
                <i class="fa fa-info"></i>
                <strong class="me-auto">Notification</strong>
            </div>
            <div class="toast-body">
               {{message}}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</template>

<style scoped>

</style>
