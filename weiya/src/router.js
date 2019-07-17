import Vue from 'vue'
import Router from 'vue-router'
import Index from './views/index/Index.vue'
Vue.use(Router)
export default new Router({
  mode: 'history',
  base: process.env.BASE_URL,
  linkActiveClass: 'mui-active', // 覆盖默认的路由高亮的 类
  routes: [{
    path: '/',
    name: 'index',
    component: Index
  },
  {
    path: '/weiya',
    name: 'weiya',
    component: () => import(/* webpackChunkName: "about" */ './views/weiya/Weiya.vue') // 走进维雅
  }, {
    path: '/store',
    name: 'Store',
    component: () => import(/* webpackChunkName: "factory" */ './views/store/Store.vue') // 供应商
  }, {
    path: '/cart',
    name: 'Cart',
    component: () => import(/* webpackChunkName: "cart" */ './views/cart/Cart.vue') // 购物车
  },
  {
    path: '/user',
    name: 'User',
    component: () => import(/* webpackChunkName: "user" */ './views/user/User.vue') // 个人中心
  },
  {
    path: '/advantage',
    name: 'Advantage',
    component: () => import(/* webpackChunkName: "user" */ './views/brand/Advantage.vue') // 品牌优势
  }
  ]
})
