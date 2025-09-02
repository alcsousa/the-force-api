<template>
    <BaseLayout>
        <div class="flex flex-row w-full">
            <div class="w-2/5">
                <ContentCard>
                    <span class="font-semibold">What are you searching for?</span>

                    <div class="mt-4 flex space-x-4">
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="searchType" value="people" class="form-radio" v-model="form.searchType" />
                            <small class="font-bold">People</small>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="radio" name="searchType" value="movies" class="form-radio" v-model="form.searchType" />
                            <small class="font-bold">Movies</small>
                        </label>
                    </div>

                    <div class="my-4">
                        <input
                            type="text"
                            class="block w-full p-2 text-gray-900 border border-gray-300 rounded-sm text-sm font-bold"
                            autocomplete="off"
                            v-model="form.keyword"
                            placeholder="e.g. Chewbacca, Yoda, Boba Fett"
                        >
                    </div>

                    <button
                        :class="buttonClass"
                        @click.prevent="handleSearch"
                    >
                        {{ searchButtonText }}
                    </button>
                </ContentCard>
            </div>

            <div class="w-3/5">
                <ContentCard>
                    <div class="min-h-80">
                        <span class="font-bold">Results</span>
                        <hr class="border border-gray-200 rounded-sm my-1">

                        <div
                            class="flex flex-col items-center min-h-80 justify-center h-full text-center text-gray-300 font-bold text-xs"
                            v-if="hasNoResults && !searching"
                        >
                            <span>There are zero matches.</span>
                            <span>Use the form to search for People or Movies.</span>
                        </div>

                        <div
                            class="flex flex-col items-center min-h-80 justify-center h-full text-center text-gray-300 font-bold text-xs"
                            v-if="searching"
                        >
                            <span>Searching...</span>
                        </div>

                        <div v-if="!hasNoResults">
                            <div v-for="result in results" :key="result.id" class="p-2 border-b border-gray-200 flex items-center justify-between">
                                <span class="font-semibold">{{ result.name }}</span>

                                <RouterLink
                                    :to="{ name: routeName, params: { id: result.id } }"
                                    class="btn hover:bg-emerald bg-green-teal text-white text-sm font-bold py-1 px-5 rounded-full"
                                >
                                    SEE DETAILS
                                </RouterLink>
                            </div>
                        </div>
                    </div>
                </ContentCard>
            </div>
        </div>
    </BaseLayout>
</template>

<script setup lang="ts">
import BaseLayout from "../layouts/BaseLayout.vue";
import ContentCard from "../ui/cards/ContentCard.vue";
import {searchPeople} from "../../js/api/people/searchPeople.js";
import {searchMovies} from "../../js/api/movies/searchMovies.js";
import {computed, ref} from "vue";

const form = ref({
    searchType: 'people',
    keyword: ''
});
const hasNoResults = ref(true);
const results = ref([]);
const routeName = ref('peopleDetail');
const searching = ref(false);

const handleSearch = async () => {
    try {
        if (form.value.keyword.trim().length === 0) {
            return;
        }

        searching.value = true;
        let searchResults = [];
        hasNoResults.value = true;

        if (form.value.searchType === 'people') {
            routeName.value = 'peopleDetail';
            const result = await searchPeople(form.value.keyword);
            searchResults = result.data.map((item) => ({
                id: item.id,
                name: item.name
            }));
        }

        if (form.value.searchType === 'movies') {
            routeName.value = 'movieDetail';
            const result = await searchMovies(form.value.keyword);
            searchResults = result.data.map((item) => ({
                id: item.id,
                name: item.title
            }));
        }

        searching.value = false;

        if (searchResults.length > 0) {
            results.value = searchResults;
            hasNoResults.value = false;
            return;
        }

        hasNoResults.value = true;
    } catch (e) {
        console.error("Search failed:", e);
    }
};

const searchButtonText = computed(() => {
    return searching.value === true ? "SEARCHING..." : "SEARCH";
});

const buttonClass = computed(() => {
    const baseClass = 'text-sm text-white font-bold py-2 px-4 rounded-full w-full';

    return form.value.keyword.length > 0
        ? `bg-green-teal hover:bg-emerald ${baseClass}`
        : `bg-gray-300 ${baseClass}`;
});
</script>
