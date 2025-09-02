import {createRouter, createWebHistory} from "vue-router";
import SearchView from "../components/views/SearchView.vue";
import PeopleDetailView from "../components/views/PeopleDetailView.vue";
import MovieDetailView from "../components/views/MovieDetailView.vue";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'search',
            component: SearchView
        },
        {
            path: '/people/:id',
            name: 'peopleDetail',
            component: PeopleDetailView
        },
        {
            path: '/movies/:id',
            name: 'movieDetail',
            component: MovieDetailView
        },
    ]
});

export default router
