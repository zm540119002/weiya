<template>
  <div>
    <van-nav-bar
      title="收货地址"
      left-text=""
      right-text="新增"
      left-arrow
      @click-left="onClickLeft"
      @click-right="onClickRight"
    />
    <div class="line"></div>
    <div
      class="details"
      v-for="item in list"
    >
      <div class="address">
        <div><img
            src="../../../static/images/weizhi.png"
            alt=""
          ></div>
        <div>
          <div><span>{{item.consignee}}</span><span>{{item.mobile}}</span></div>
          <div><span class="color">(默认)</span><span>{{item.detail_address}}</span></div>
        </div>
      </div>
      <div class="edit">编辑</div>
    </div>
  </div>
</template>

<script>
import { addressHandle } from '@/api/address'
export default {
  data () {
    return {
      list: [],
      chosenAddressId: '1'
    }
  },
  created () {
    this.addressHandle()
  },
  methods: {
    // 获取收货地址
    addressHandle () {
      console.log('00000000000')
      addressHandle().then(res => {
        // console.log(res.data)
        let { data } = { ...res.data }
        // console.log(data)
        this.list = data
        console.log(this.list)
      })
    },
    // 导航栏
    onClickLeft () {
      console.log('返回')
      this.$router.go(-1)
    },
    onClickRight () {
      console.log('新增地址')
    }
  }
}
</script>

<style lang="less" scoped>
  .line {
    background-color: #f2f2f2;
    height: 0.2rem;
  }
  .details {
    padding: 0.2rem 0.3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-bottom: 1px solid #eee;
    .address {
      display: flex;
      align-items: center;
      > div:nth-of-type(1) {
        width: 0.5rem;
        height: 0.5rem;
        margin-right: 0.2rem;
        img {
          width: 100%;
        }
      }
      >div:nth-of-type(2){
        >span:nth-of-type(1){
          margin-right: 0.2rem
        }
      }
      .color{
        color: #eb6100;
      }
    }
    .edit {
      border-left: 1px solid #ccc;
      padding: 0.2rem 0 0.2rem 0.3rem;
    }
  }
</style>
