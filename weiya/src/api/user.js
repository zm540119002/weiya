import request from '@/utils/request'

// // 重置密码发送验证码 get请求
// export function sendIdentifyingCode () {
//   return request({
//     url: 'https://easy-mock.com/mock/5ca40e906f29eb7d233dafdc/province',
//     method: 'get',
//     data: {}
//   })
// }

// // 保存商品图片  需要传参的post请求
// export function saveGoodsImageList (goodsId, images, imgDelIds) {
//   return request({
//     url: '/goodsManage/tGoodsImagesRelsOPerate.int.do',
//     method: 'post',
//     data: {
//       goodsId,
//       images,
//       imgDelIds
//     }
//   })
// }
// // post请求
// export function saveUser (data) {
//   return request({
//     url: 'ucenter/UserCenterApi/login',
//     method: 'post',
//     data
//   })
// }
// // 首页  不需要传参的post请求
// export function homeStatistical () {
//   return request({
//     url: '/homePage/statisticalData',
//     method: 'post',
//     data: {}
//   })
// }

// 登录请求
export function loginHandle (data) {
  return request({
    url: '/ucenter/User/login',
    method: 'post',
    data: { data }
  })
}

// 验证码
export function sendSmsHandle (data) {
  return request({
    url: '/ucenter/User/sendSms',
    method: 'post',
    data: { data }
  })
}

// 用户注册 修改密码
export function registerHandle (data) {
  return request({
    url: '/ucenter/User/register',
    method: 'post',
    data: { data }
  })
}

// 退出登录
export function logoutHandle () {
  return request({
    url: '/ucenter/User/logout',
    method: 'post',
    data: { }
  })
}
