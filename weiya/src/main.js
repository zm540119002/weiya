import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import axios from 'axios'

// 导入初始化文件
import '../static/common/reset.css'

// 导入mui的样式
import '../static/css/mui.min.css'
import '../static/css/icons-extra.css'

// import '../static/js/mui'
// Vue.prototype.mui = mui
// Vue.use(mui)

import Vant from 'vant'
import 'vant/lib/index.css'

import MintUI from 'mint-ui'
import 'mint-ui/lib/style.css'
// //可以给axios的ajax请求设置统一的主机和端口号
axios.defaults.baseURL = 'https://hss.meishangyun.com'
// //将axios这个对象添加到Vue的原型对象中，在使用的时候就只需要使用this.对象名就可以了
Vue.prototype.$http = axios

Vue.use(Vant)
Vue.use(MintUI)

Vue.config.productionTip = false

// router.beforeEach((to, from, next) => {
//   var userInfo = window.localStorage.getItem('token') // to.matched.some(m=>m.meta.auth)获取浏览器缓存的用户信息
//   // if(userInfo){
//   alert(window.localStorage.isLogin)
//   if (window.localStorage.isLogin === '1') {
//     alert(2)
//     next()
//   } else {
//     if (to.path == '/user') {
//       alert(3)
//       // Vue.prototype.$message.warning('你还没登录，请先登录再操作')
//       Vue.prototype.$notify('提示文案')
//       next()
//     } else {
//       next('/user')
//     }
//   }
//   // else{
//   //     alert('不跑我这里')
//   // }
// })

new Vue({
  router,
  store,
  render: h => h(App)
}).$mount('#app')
