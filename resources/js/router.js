import Vue from "vue";
import Router from "vue-router";
Vue.use(Router);

import firstPage from "./components/pages/myFirstVuePage";
import newPage from "./components/pages/new-route";

// admin pages
import TagPage from "./admin/pages/tags";
import CategoryPage from "./admin/pages/category";

const routes = [
    // Project Routes..........
    {
        path: "/tags",
        component: TagPage
    },
    {
        path: "/category",
        component: CategoryPage
    },
    // Basic Test Routes ..............
    {
        path: "/my-new-vue-router",
        component: firstPage
    },
    {
        path: "/new-router",
        component: newPage
    }
];

export default new Router({
    mode: "history",
    routes
});
