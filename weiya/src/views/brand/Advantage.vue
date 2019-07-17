<template>
  <div>
    <mt-header title="联合品牌定制优势">
      <router-link
        to="/"
        slot="left"
      >
        <mt-button icon="back"></mt-button>
      </router-link>
    </mt-header>
    <div class="content">
      <div class="content-in">
        <img
          src="../../../static/images/Advantage.jpg"
          alt=""
        >
        <div>维雅联合品牌定制</div>
        <div>低门槛3万起定制品牌！</div>
        <ul>
          <li><img
              src="../../../static/images/icon_d.png"
              alt=""
            >下单犹如下餐馆点菜般简单方便</li>
          <li><img
              src="../../../static/images/icon_d.png"
              alt=""
            >O2O（线上或线下）进行下单操作</li>
          <li><img
              src="../../../static/images/icon_d.png"
              alt=""
            >7 x 24小时订单管理在线，专属客服响应</li>
        </ul>
      </div>
      <div class="img">
        <div><img
            src="../../../static/images/Advantage.jpg"
            alt=""
          ></div>
        <div><img
            src="../../../static/images/Advantage.jpg"
            alt=""
          ></div>
        <div><img
            src="../../../static/images/Advantage.jpg"
            alt=""
          ></div>
      </div>
    </div>
    <van-action-sheet
      v-model="show"
      :actions="actions"
      cancel-text="取消"
      @select="onSelect"
    />
    <div
      class="btn"
      @click="actionSheet"
    >推荐定制方案</div>

    <mt-actionsheet
      :actions="data"
      v-model="sheetVisible"
    >
    </mt-actionsheet>
  </div>
</template>

<script>
export default {
  name: 'hello',
  data () {
    return {
      // action sheet 选项内容
      data: [{
        name: '3万定制方案',
        method: this.getCamera	// 调用methods中的函数
      }, {
        name: '10万定制方案',
        method: this.getLibrary	// 调用methods中的函数
      }, {
        name: '20+万定制方案',
        method: this.getLibrary	// 调用methods中的函数
      }],
      // action sheet 默认不显示，为false。操作sheetVisible可以控制显示与隐藏
      sheetVisible: false
    }
  },
  methods: {
    actionSheet: function () {
      // 打开action sheet
      this.sheetVisible = true
    },
    getCamera: function () {
      console.log('打开照相机')
    },
    getLibrary: function () {
      console.log('打开相册')
    }
  }
}
</script>

<style lang="less" scoped>
  .mint-header {
    background: #fff;
    color: #333;
  }
  .content {
    background: #efefef;
    padding: 0.2rem;
    .content-in {
      background: #fff;
      > img {
        width: 100%;
        padding: 0.2rem 0.2rem 0;
      }
      > div:nth-of-type(1) {
        font-size: 0.4rem;
        color: #898989;
        margin: 0.2rem 0 0.1rem;
        padding-left: 0.3rem;
      }
      div:nth-of-type(2) {
        font-size: 0.4rem;
        color: #2392c3;
        font-weight: bold;
        margin: 0.2rem 0 0.1rem;
        padding-left: 0.3rem;
      }
      ul li {
        margin-top: 0.2rem;
        img {
          width: 0.4rem;
          height: 0.4rem;
          margin: 0 0.2rem 0 0.6rem;
        }
      }
    }
  }
  .img {
    padding: 0.5rem 0.1rem 2rem;
    display: flex;
    align-content: center;
    background: #fff;
    img {
      width: 100%;
    }
    div:nth-of-type(2) {
      margin: 0 0.1rem;
    }
  }
  .btn {
    background: #e60012;
    text-align: center;
    color: #fff;
    padding: 0.2rem;
    position: fixed;
    bottom: 0;
    width: 100%;
  }
</style>
