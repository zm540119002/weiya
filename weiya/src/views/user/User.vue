<template>
  <div>
    <div>
      <div
        v-if="!status"
        class="top"
        @click="showPopup"
      >
        <div class="user">
          <van-icon name="manager-o" />
        </div>
        <div>注册/登录</div>
      </div>
      <!-- 已登录 -->
      <div
        v-if="status==='true'"
        class="top"
      >
        <div class="login-user">
          <div class="login-info">
            <div><img
                src="../../../static/images/pro1.png"
                alt=""
              ></div>
            <div>{{postData.mobile_phone}}{{postData2.mobile_phone}}</div>
          </div>
          <div @click="showPopupTop">设置 ></div>
        </div>
      </div>
      <!-- 弹窗 -->
      <van-popup
        position="top"
        :style="{ height: '46%' }"
        v-model="show1"
      >
        <van-nav-bar
          title="设置"
          left-text=""
          right-text="确定"
          left-arrow
          @click-left="onClickLeft"
          @click-right="onClickRight"
        />
        <div class="line"></div>
        <div class="user-info">
          <div class="user-info-header">
            <div>头像</div>
            <div>
              <van-uploader
                v-model="fileList"
                multiple
                :max-count="1"
              />
            </div>
          </div>
          <div class="user-info-name">
            <div>昵称<span>李思思</span></div>
            <div>
              <div>></div>
            </div>
          </div>
          <div class="user-info-name">
            <div>设置钱包账户安全密码</div>
            <div>></div>
          </div>
          <div class="user-info-name no-line">
            <div @click="logout">退出登录</div>
            <div>></div>
          </div>
        </div>

      </van-popup>
      <!-- 已登录 -->
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
          @click="address"
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
                v-model="postData2.mobile_phone"
                placeholder="请输入用户名"
              />
              <div class="code">
                <div>
                  <input
                    type="text"
                    v-model="postData2.captcha"
                    placeholder="请输入收到的验证码"
                  >
                </div>
                <div @click="sendSms">获取验证码</div>
              </div>
              <van-field
                v-model="postData2.password"
                placeholder="设置密码"
              />
            </van-cell-group>
            <van-button
              class="btn"
              type="info"
              @click="register"
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
import Tabbar from '@/components/Tabbar.vue'
import { loginHandle, sendSmsHandle, logoutHandle, registerHandle } from '@/api/user'
import { setTimeout } from 'timers'
export default {
  name: 'user',
  components: {
    Tabbar
  },
  data () {
    return {
      status: false,
      show: false, // tab切换
      show1: false, // 顶部划出遮罩
      fileList: [], // 上传头像
      postData: {
        mobile_phone: '13679898380',
        password: '111111'
      },
      postData2: {
        mobile_phone: '',
        password: '',
        captcha: ''
      }
    }
  },
  created () {
    // 获取本地存储数据
    this.status = localStorage.getItem('status')
    console.log(this.status, '获取状态值11111')
  },
  methods: {
    // tab切换
    showPopup () {
      this.show = true
    },
    // 从上滑出遮罩
    showPopupTop () {
      this.show1 = true
    },
    // 登录
    login () {
      var loginParams = {
        mobile_phone: this.postData.mobile_phone,
        password: this.postData.password
      }
      // console.log(loginParams)
      // console.log(this.postData.mobile_phone, this.postData.password)
      // loginHandle(this.postData)
      //   .then(r => console.log(r)) // 接口调用成功返回的数据
      //   .catch(err => console.log(err)) // 接口调用失败返回的数据
      loginHandle(loginParams).then(res => {
        console.log(res.data)
        console.log(res.data.data.token)

        if (res.data.code === '1') {
          // 存储token值
          localStorage.setItem('mytoken', res.data.data.token)
          localStorage.setItem('status', 'true')
          // 登录成功跳转
          setTimeout(() => {
            this.$router.go(0)
          }, 1000)
        }
      })
    },
    // 验证码
    sendSms () {
      var sendSmsParams = {
        mobile_phone: this.postData2.mobile_phone
      }
      sendSmsHandle(sendSmsParams).then(res => {
        console.log(res.data)
      })
      console.log(this.postData2)
    },
    onClickLeft () {
      this.$router.go(0)
    },
    onClickRight () {
      console.log('确定')
    },
    // 用户注册，修改密码
    register () {
      var registerParams = {
        mobile_phone: this.postData2.mobile_phone,
        password: this.postData2.password,
        captcha: this.postData2.captcha
      }
      registerHandle(registerParams).then(res => {
        console.log(this.postData2.captcha)
        console.log(res.data)
        if (res.data.code === '1') {
          // 存储token值
          localStorage.setItem('mytoken', res.data.data.token)
          localStorage.setItem('status', 'true')
          this.$router.go(0)
        }
      })
    },
    // 退出登录
    logout () {
      logoutHandle().then(res => {
        console.log(res.data)
        if (res.data.code === '1') {
          // 清除本地用户信息
          localStorage.clear()
          // 跳到登录页
          setTimeout(() => {
            // 刷新当前页面
            this.$router.go(0)
          }, 300)
        }
      })
    },
    // 收货地址
    address () {
      this.$router.push({
        name: 'Address'
      })
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
    .login-user {
      width: 80%;
      margin: 0 auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      .login-info {
        display: flex;
        align-items: center;
        div:nth-of-type(1) {
          width: 1rem;
          height: 1rem;
          img {
            width: 100%;
          }
        }
        div:nth-of-type(2) {
          margin-left: 0.2rem;
        }
      }
    }
  }
  .user-info {
    padding: 0 0.5rem;
    // margin-top: 0.2rem;
    .user-info-header,
    .user-info-name {
      display: flex;
      align-items: center;
      padding: 0.4rem 0 0.2rem;
      border-bottom: 1px solid #eee;
      div:nth-of-type(2) {
        margin-left: 0.7rem;
      }
    }
    .user-info-name {
      justify-content: space-between;
      div:nth-of-type(1) {
        span {
          margin-left: 0.7rem;
        }
      }
      div:nth-of-type(2) {
        margin-left: 0.7rem;
        display: flex;
        justify-content: space-between;
      }
    }
    .no-line {
      border-bottom: none;
    }
  }
  .line {
    background-color: #f2f2f2;
    height: 0.2rem;
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
  .code {
    display: flex;
    margin-top: 0.2rem;
    padding: 0 0.3rem;
    align-items: center;
    div:nth-of-type(1) {
      flex: 8;
    }
    div:nth-of-type(2) {
      flex: 3;
      background: #ff7bac;
      margin-left: 0.2rem;
      height: 40px;
      line-height: 40px;
      border-radius: 3px;
      color: #fff;
    }
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
  .aa {
    background: red;
  }
</style>
