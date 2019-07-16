import Vue from 'vue'
import Router from 'vue-router'
import Home from './views/supplier/Home.vue'
Vue.use(Router)
export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  linkActiveClass: 'mui-active', // 覆盖默认的路由高亮的 类
  routes: [{
    path: '/',
    name: 'home',
    component: Home
  },
  {
    path: '/about',
    name: 'about',
    component: () => import(/* webpackChunkName: "about" */ './views/supplier/About.vue')
  }, {
    path: '/factory',
    name: 'Factory',
    component: () => import(/* webpackChunkName: "factory" */ './views/supplier/Factory.vue')
  }, {
    path: '/cart',
    name: 'Cart',
    component: () => import(/* webpackChunkName: "cart" */ './views/supplier/Cart.vue')
  },
  {
    path: '/user',
    name: 'User',
    component: () => import(/* webpackChunkName: "user" */ './views/user/User.vue')
  }
  ]
})
