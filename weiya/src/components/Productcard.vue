<template>
  <div class="product">
    <div>
      <img
        src="../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div @click="showPopup"><img
            src="../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <div>
      <img
        src="../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div><img
            src="../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <div>
      <img
        src="../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div><img
            src="../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <div>
      <img
        src="../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div><img
            src="../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <!-- 弹窗 -->
    <div>
      <van-popup
        v-model="show"
        position="bottom"
        round
        :style="{width:'100%',
      height: '30%'
      }"
      >
        <div class="top">
          <div><img
              src="../../static/images/pro1.png"
              alt=""
            ></div>
          <span>是非成败转头空青山依旧在惯看秋月春风一壶浊酒喜相</span>
        </div>
        <div class="price_add">
          <div>￥68.00</div>
          <div>
            <van-stepper v-model="value" />
          </div>
        </div>
        <div class="spec">
          <div>25ml*6片/盒</div>
          <div>体验限购10盒</div>
        </div>
      </van-popup>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      show: false,
      value: 1
    }
  },

  methods: {
    showPopup () {
      this.show = true
    }
  }
}
</script>

<style lang="less" scoped>
  .product {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    & > div {
      width: 49%;
      background: #fff;
      margin-bottom: 0.2rem;
      img {
        width: 100%;
      }
      .title {
        width: 100%;
        margin: 0.2rem 0 0.15rem;
        padding: 0 0.1rem;
      }
      .price {
        width: 100%;
        display: flex;
        justify-content: space-between;
        padding: 0 0.1rem;
        margin-bottom: 0.2rem;
        div:nth-of-type(1) {
          color: #ff6482;
        }
        div:nth-of-type(2) {
          width: 0.55rem;
        }
      }
    }
    div:nth-of-type(2n-1) {
      margin-right: 2%;
    }
    .top {
      display: flex;
      align-items: center;
      margin: 0.2rem 0 0 0.2rem;
      padding-bottom: 0.2rem;
      border-bottom: 1px solid #ccc;
      div {
        width: 1.4rem;
        height: 1.4rem;
        border: 1px solid #eee;
      }
      img {
        width: 100%;
      }
    }
    .price_add {
      padding: 0 0.2rem 0.2rem;
      margin: 0.2rem 0;
      display: flex;
      align-items: center;
      justify-content: space-between;
      div:nth-of-type(2) {
        .van-stepper {
          display: flex;
          align-items: center;
          width: 2.5rem;
          input{
            background: red;
          }
        }
      }
    }
    .spec{
      display: flex;
      justify-content: space-between;
      margin: 0.2rem;
      border-top: 1px solid #ccc;
      padding: 0.2rem 0;
    }
  }
</style>
