import axios from '../../node_modules/axios'
import { Message } from '../../node_modules/element-ui'

axios.defaults.timeout = 5000 // 请求超时
axios.defaults.baseURL = 'https://api.worldview.com.cn'

axios.interceptors.request.use(
  config => {
    // 在发送请求之前做些什么
    // 获取本地存储的token值
    let mytoken = localStorage.getItem('mytoken')

    // console.log(mytoken, 'request我是token值')
    if (mytoken) {
      // 让每个请求携带token-- ['token']为自定义key
      config.headers.Authorization = mytoken
      config.headers.Token = mytoken
    }
    return config
  },
  error => {
    console.log(error) // for debug
    return Promise.reject(error)
  }
)
// 拦截响应
axios.interceptors.response.use(
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
export default axios
