import request from '@/utils/request'

// 收货地址
export function addressHandle () {
  return request({
    url: '/ucenter/Address/getList',
    method: 'get',
    data: { }
  })
}
