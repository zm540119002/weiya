import axios from 'axios'
import { Message } from 'element-ui'

// import store from '../../node_modules/@/store'
// import { getStore } from '../../node_modules/@/utils'

// const baseURL = process.env.NODE_ENV === 'development' ? '/apis' : process.env.BASE_API
const baseURL = 'https://api.worldview.com.cn'
const service = axios.create({
  baseURL,
  timeout: 15 * 1000
})
// request拦截器==>对请求参数做处理
service.interceptors.request.use(
  config => {
    // 在发送请求之前做些什么
    // if (store.state.user.token) {
    //   // 让每个请求携带token-- ['token']为自定义key
    //   config.headers.Authorization = getStore('token')
    //   config.headers.Token = getStore('token')
    // }
    // config.headers['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8'
    // config.headers['Content-Type'] = 'multipart/form-data'
    // config.headers['Content-Type'] = 'application/json'
    return config
  },
  error => {
    console.log(error) // for debug
    Promise.reject(error)
  }
)
// request拦截器==>对响应参数做处理
service.interceptors.response.use(
  response => response,
  error => {
    console.log('err' + error) // for debug
    Message({
      message: '请求异常',
      type: 'error'
    })
    return Promise.reject(error)
  }
)

service.all = axios.all
service.spread = axios.spread

export default service
