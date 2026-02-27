import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import App from './App.vue';
import Dashboard from './components/Dashboard.vue';
import './style.css';

// Create router
const router = createRouter({
    history: createWebHistory('/admin'),
    routes: [
        {
            path: '/',
            name: 'dashboard',
            component: Dashboard
        }
    ]
});

// Create app
const app = createApp(App);
app.use(router);
app.mount('#app');
