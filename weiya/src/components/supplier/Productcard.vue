<template>
  <div class="product">
    <div>
      <img
        src="../../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div @click="showPopup"><img
            src="../../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <div>
      <img
        src="../../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div><img
            src="../../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <div>
      <img
        src="../../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div><img
            src="../../../static/images/shopping-cart-icons.png"
            alt=""
          ></div>
      </div>
    </div>
    <div>
      <img
        src="../../../static/images/product.jpg"
        alt=""
      >
      <div class="title">标题标题标题标题标题标题标题标题标题标题标题</div>
      <div class="price">
        <div>￥1000.00</div>
        <div><img
            src="../../../static/images/shopping-cart-icons.png"
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
    >内容</van-popup>
    </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      show: false
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
    &>div {
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
  }
</style>
