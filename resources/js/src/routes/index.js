import { createRouter, createWebHistory } from "vue-router";

import Home from "../pages/Home.vue";
import Login from "../pages/Login.vue";
import Register from "../pages/Register.vue";
import Checkout from "../pages/Checkout.vue";
import NotFound from "../pages/NotFound.vue";
import Transaction from "../pages/Transaction.vue";
import Setting from "../pages/Setting.vue";
import PaymentSuccess from "../pages/PaymentSuccess.vue";
import PaymentFail from "../pages/PaymentFail.vue";
import ThreeDForm from "../pages/ThreeDForm.vue";

const routes = [
    {
        'path': '/',
        'component': Home,
        'name': 'home'
    },
    {
        'path': '/login',
        'component': Login,
        'name': 'login',
        'meta': {
            'guest': true
        }
    },
    {
        'path': '/register',
        'component': Register,
        'name': 'register',
        'meta': {
            'guest': true
        }
    },
    {
        'path': '/checkout',
        'component': Checkout,
        'name': 'checkout',
        'meta': {
            'auth': true
        }
    },
    {
        'path': '/transactions',
        'component': Transaction,
        'name': 'transaction',
        'meta': {
            'auth': true
        }
    },
    {
        'path': '/settings',
        'component': Setting,
        'name': 'setting',
        'meta': {
            'auth': true,
            'roles': ['admin', 'support']
        }
    },
    {
        'path': '/payment-success',
        'component': PaymentSuccess,
        'name': 'payment-success',
        'meta': {
            'auth': true
        }
    },
    {
        'path': '/payment-fail',
        'component': PaymentFail,
        'name': 'payment-fail',
        'meta': {
            'auth': true
        }
    },
    {
        'path': '/three-d-secure',
        'component': ThreeDForm,
        'name': 'three-d-secure',
        'meta': {
            'auth': true,
            'layout': 'blank'
        }
    },
    {
        'path': '/:pathMatch(.*)*',
        'component': NotFound,
        'name': 'not-found'
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    if (to.meta.auth && !localStorage.getItem('token')) {
        return next({name: 'login'});
    }

    if (to.meta.guest && localStorage.getItem('token')) {
        return next({name: 'home'});
    }

    next();
});

router.beforeEach((to, from, next) => {
    if (to.meta.roles) {
        const user = JSON.parse(localStorage.getItem('user'));
        if (!user) {
            return next({name: 'home'});
        }

        const userRoles = user.roles.map(role => role.name);
        if (!to.meta.roles.some(role => userRoles.includes(role))) {
            return next({name: 'home'});
        }
    }

    next();
})

export default router;
