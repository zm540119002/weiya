<template>
  <div>
    <div>
      <div
        class="top"
        @click="showPopup"
      >
        <div class="user">
          <van-icon name="manager-o" />
        </div>
        <div>注册/登录</div>
      </div>
      <van-grid
        clickable
        :column-num="4"
      >
        <van-grid-item
          icon="comment-o"
          text="我的消息"
          to="/cart"
        />
        <van-grid-item
          icon="star-o"
          text="商品收藏"
          to="/cart"
        />
        <van-grid-item
          icon="qr-invalid"
          text="分享码"
          to="/cart"
        />
        <van-grid-item
          icon="qr-invalid"
          text="收益账户"
          to="/cart"
        />
      </van-grid>
      <div class="line"></div>
      <van-grid
        clickable
        :column-num="4"
      >
        <van-grid-item
          icon="comment-o"
          text="定制须知"
          to="/cart"
        />
        <van-grid-item
          icon="shop-o"
          text="采购账户"
          to="/cart"
        />
        <van-grid-item
          icon="balance-o"
          text="我的品牌"
          to="/cart"
        />
        <van-grid-item
          icon="search"
          text="我的报告"
          to="/cart"
        />
      </van-grid>
      <div class="line"></div>
      <van-grid
        clickable
        :column-num="4"
      >
        <van-grid-item
          icon="shopping-cart-o"
          text="采购车"
          to="/cart"
        />
        <van-grid-item
          icon="star-o"
          text="收货地址"
          to="/cart"
        />
        <van-grid-item
          icon="search"
          text="待付款"
          to="/cart"
        />
        <van-grid-item
          icon="search"
          text="待收货"
          to="/cart"
        />
      </van-grid>
      <van-grid
        clickable
        :column-num="4"
      >
        <van-grid-item
          icon="comment-o"
          text="待评价"
          to="/cart"
        />
        <van-grid-item
          icon="sign"
          text="已完成"
          to="/cart"
        />
        <van-grid-item
          icon="search"
          text="售后服务"
          to="/cart"
        />
        <van-grid-item
          icon="notes-o"
          text="全部订单"
          to="/cart"
        />
      </van-grid>
      <van-popup
        class="card"
        v-model="show"
      >
        <div><img
            src="../../../static/images/ucenter_logo.png"
            alt=""
          ></div>
        <van-tabs>
          <van-tab title="登录">
            <van-cell-group>
              <van-field
                v-model="postData.mobile_phone"
                placeholder="请输入用户名"
              />
              <van-field
                v-model="postData.password"
                placeholder="密码"
              />
            </van-cell-group>
            <van-button
              class="btn"
              @click="login"
              type="info"
            >登录</van-button>
          </van-tab>
          <van-tab title="注册/重置密码">
            <van-cell-group>
              <van-field
                v-model="postData2.userName"
                placeholder="请输入用户名"
              />
              <van-field
                v-model="postData2.captcha"
                placeholder="请输入收到的验证码"
              />
              <van-field
                v-model="postData2.password"
                placeholder="设置密码"
              />
            </van-cell-group>
            <van-button
              class="btn"
              @click="registerHandle"
              type="info"
            >注册</van-button>
          </van-tab>
        </van-tabs>
      </van-popup>
    </div>
    <div class="msy">2014-2019 美尚云 网站备案号XXXXXXXXXXXX</div>
    <Tabbar msg="Welcome to Your Vue.js App" />
  </div>
</template>

<script>
import Tabbar from '@/components/supplier/Tabbar.vue'
import { loginHandle } from '@/api/util'
export default {
  name: 'home',
  components: {
    Tabbar
  },
  data () {
    return {
      show: false,
      postData: {
        mobile_phone: '',
        password: ''
      },
      postData2: {
        userName: '',
        password: '',
        captcha: ''
      }
    }
  },

  methods: {
    showPopup () {
      this.show = true
    },
    login () {
      var loginParams = {
        mobile_phone: this.postData.mobile_phone,
        password: this.postData.password
      }
      // loginHandle(this.postData)
      //   .then(r => console.log(r)) // 接口调用成功返回的数据
      //   .catch(err => console.log(err)) // 接口调用失败返回的数据
      loginHandle(loginParams).then(res => {
        console.log(res.data)
      })
      console.log(this.postData)
    },
    registerHandle () {
      console.log(this.postData2)
    }
  }

}
</script>

<style lang="less" scoped>
  .top {
    background-color: #c7aa53;
    padding: 0.6rem 0;
    text-align: center;
    color: #fff;
    .user {
      width: 1rem;
      height: 1rem;
      line-height: 1.2rem;
      font-size: 0.6rem;
      border-radius: 50%;
      margin: 0 auto 0.2rem;
      background-color: #fff;
      color: #c7aa53;
    }
  }
  .line {
    background-color: #f2f2f2;
    height: 0.3rem;
  }
  .card {
    width: 92%;
    border-radius: 10px;
    padding: 5% 0;
    div {
      text-align: center;
      img {
        margin-top: 20px;
      }
    }
  }
  .btn {
    width: 92%;
    margin-bottom: 5%;
    margin-top: 10px;
    background-color: #ff7bac;
    border: 1px solid #ff7bac;
    border-radius: 5px;
  }
  .van-field {
    padding-bottom: 0;
  }
  .msy {
    width: 100%;
    text-align: center;
    position: absolute;
    bottom: 50px;
    font-size: 0.2rem;
    color: #999;
  }
</style>
