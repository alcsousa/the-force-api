<template>
    <div class="custom-card">
        <slot />
    </div>
</template>

<style scoped>
.custom-card {
    padding: 15px;
    border-radius: 2px;
    box-shadow: rgba(60, 64, 67, 0.3) 0 1px 2px 0, rgba(60, 64, 67, 0.15) 0 1px 3px 1px;
    margin: 0 10px;
    background-color: white;
}
</style>
