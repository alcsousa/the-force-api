<template>
    <BaseLayout>
        <ContentCard>
            <LoadingPlaceholder v-if="loading" />

            <div class="m-4" v-else>
                <div class="pb-4">
                    <span class="font-semibold">
                        {{ peopleDetails?.name }}
                    </span>
                </div>

                <div class="flex flex-row w-full gap-x-40">
                    <div class="w-1/2">
                        <span class="font-bold text-sm">Details</span>
                        <hr class="border border-gray-200 rounded-sm my-1">

                        <p>Birth Year: {{ peopleDetails?.birth_year }}</p>
                        <p>Gender: {{ peopleDetails?.gender }}</p>
                        <p>Eye Color: {{ peopleDetails?.eye_color }}</p>
                        <p>Hair Color: {{ peopleDetails?.hair_color }}</p>
                        <p>Height: {{ peopleDetails?.height }}</p>
                        <p>Mass: {{ peopleDetails?.mass }}</p>
                    </div>

                    <div class="w-1/2">
                        <span class="font-bold text-sm">Movies</span>
                        <hr class="border border-gray-200 rounded-sm my-1">

                        <ul class="flex flex-row flex-wrap gap-x-1 items-center text-sm">
                            <li v-for="(film, idx) in peopleDetails.films" :key="film.id" class="flex items-center">
                                <RouterLink
                                    :to="{ name: 'movieDetail', params: { id: film.id } }"
                                    class="text-blue-600 underline hover:text-blue-400"
                                >
                                    {{ film.title }}
                                </RouterLink>
                                <span v-if="idx < peopleDetails.films.length - 1">,</span>
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
import {getPeopleDetailsById} from "../../js/api/people/getPeopleDetailsById.js";
import LoadingPlaceholder from "../ui/placeholders/LoadingPlaceholder.vue";

const route = useRoute();
const router = useRouter();
const peopleDetails = ref({
    name: '',
    birth_year: '',
    gender: '',
    eye_color: '',
    hair_color: '',
    height: '',
    mass: '',
    films: []
});
const loading = ref(false);

const goToSearch = async () => {
    await router.push({ name: 'search' });
};

const fetchPersonDetails = async () => {
    try {
        loading.value = true;
        const id = route.params.id;
        const details = await getPeopleDetailsById(id);
        peopleDetails.value = details.data;
        loading.value = false;
    } catch (error) {
        console.error("Error fetching person details:", error);
    }
};

onMounted(() => {
    fetchPersonDetails();
});
</script>
