import request from '@/utils/request'

// 收货地址
export function addressHandle () {
  return request({
    url: '/ucenter/Address/getList',
    method: 'get',
    headers: { 'Content-Type': 'application/json;charset=utf8', 'organId': '1333333333' },
    data: { }
  })
}
