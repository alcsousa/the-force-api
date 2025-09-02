<template>
    <header class="w-full bg-white shadow-md flex items-center justify-center h-16">
        <span class="text-green-500 font-bold text-lg">SWStarter</span>
    </header>

    <main>
        <div class="container mx-auto mt-8">
            <slot/>
        </div>
    </main>
</template>

<style>
body {
    background-color: var(--light-gray);
}
</style>
