import { createRouter, createWebHistory } from 'vue-router'

import DashboardView from '../views/DashboardView.vue'
import JobsView from '../views/JobsView.vue'
import HistoryView from '../views/HistoryView.vue'
import LogsView from '../views/LogsView.vue'
import SettingsView from '../views/SettingsView.vue'
import AboutView from '../views/AboutView.vue'

const router = createRouter({

    history: createWebHistory(),

    routes: [

        {
            path: '/',
            component: DashboardView
        },

        {
            path: '/jobs',
            component: JobsView
        },

        {
            path: '/history',
            component: HistoryView
        },

        {
            path: '/logs',
            component: LogsView
        },

        {
            path: '/settings',
            component: SettingsView
        },

        {
            path: '/about',
            component: AboutView
        }

    ]

})

export default router