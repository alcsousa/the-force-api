<template>
    <BaseLayout>
        <ContentCard>
            <LoadingPlaceholder v-if="loading" />

            <div class="m-4" v-else>
                <div class="pb-4">
                    <span class="font-semibold">
                        {{ movieDetails?.title }}
                    </span>
                </div>

                <div class="flex flex-row w-full gap-x-40">
                    <div class="w-1/2">
                        <span class="font-bold text-sm">Opening Crawl</span>
                        <hr class="border border-gray-200 rounded-sm my-1">

                        <p>{{ movieDetails?.opening_crawl }}</p>
                    </div>

                    <div class="w-1/2">
                        <span class="font-bold text-sm">Movies</span>
                        <hr class="border border-gray-200 rounded-sm my-1">

                        <ul class="flex flex-row flex-wrap gap-x-1 items-center text-sm">
                            <li v-for="(character, idx) in movieDetails.characters" :key="character.id" class="flex items-center">
                                <RouterLink
                                    :to="{ name: 'peopleDetail', params: { id: character.id } }"
                                    class="text-blue-600 underline hover:text-blue-400"
                                >
                                    {{ character.name }}
                                </RouterLink>
                                <span v-if="idx < movieDetails.characters.length - 1">,</span>
                            </li>
                        </ul>

                    </div>
                </div>

                <div class="mt-20">
                    <a
                        class="btn bg-green-teal hover:bg-emerald text-white text-sm font-bold py-2 px-5 rounded-full"
                        href="#"
                        @click.prevent="goToSearch"
                    >
                        BACK TO SEARCH
                    </a>
                </div>
            </div>
        </ContentCard>
    </BaseLayout>
</template>

<script setup>
import BaseLayout from "../layouts/BaseLayout.vue";
import ContentCard from "../ui/cards/ContentCard.vue";
import {useRoute, useRouter} from "vue-router";
import {onMounted, ref} from "vue";
import {getMovieDetailsById} from "../../js/api/movies/getMovieDetailsById.js";
import LoadingPlaceholder from "../ui/placeholders/LoadingPlaceholder.vue";

const route = useRoute();
const router = useRouter();
const movieDetails = ref({
    title: '',
    opening_crawl: '',
    characters: []
});
const loading = ref(false);

const goToSearch = async () => {
    await router.push({ name: 'search' });
};

const fetchMovieDetails = async () => {
    try {
        loading.value = true;
        const id = route.params.id;
        const details = await getMovieDetailsById(id);
        movieDetails.value = details.data;
        loading.value = false;
    } catch (error) {
        console.error('Error fetching movie details:', error);
    }
};

onMounted(() => {
    fetchMovieDetails();
});
</script>
