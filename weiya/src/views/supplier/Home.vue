<template>
  <div class="home">
    <div class="top_img"><img src="../../../static/images/home-banner1.jpg"></div>
    <div class="content">

      <!--轮播图-->
      <van-swipe :autoplay="3000">
        <van-swipe-item v-for="(image, index) in images" :key="index">
          <img v-lazy="image" />
        </van-swipe-item>
      </van-swipe>
      <!-- 快报 -->
      <div class="news">
        <div class="top">
          <div>维雅快报</div>
          <div><span>最新</span>是非成败转头空，青山依旧在，惯看风</div>
          <div>|更多</div>
        </div>
        <div class="bottom">
          <div><span>[客户昵称]</span><span>[手机号码]</span><span>[下单日期]</span><span>[发货日期]</span></div>
          <div><span>[客户昵称]</span><span>[手机号码]</span><span>[下单日期]</span><span>[发货日期]</span></div>
          <div><span>[客户昵称]</span><span>[手机号码]</span><span>[下单日期]</span><span>[发货日期]</span></div>
          <div><span>[客户昵称]</span><span>[手机号码]</span><span>[下单日期]</span><span>[发货日期]</span></div>
        </div>
      </div>
      <!-- 二十款明星单品 -->
      <div class="single">
        <div class="top">
          <div>二十款明星单品</div>
          <div>新零售社交电商爆款</div>
        </div>
      </div>

        <!-- 二十款明星单品 -->
      <div class="project">
        <div class="top">
          <div>十大热门项目</div>
          <div>热推门店科技美肤项目</div>
        </div>
      </div>
      <!-- 科技美肤精选 -->
      <div class="selected">
          <div class="title"  @click="showPanel"><span></span>科技美肤精选<span></span></div>
          <Productcard />
      </div>

    </div>
    <Productcart @hidePanel=hidePanel v-show="panelShow" :panelShow="panelShow" />
    <Tabbar/>
  </div>
</template>

<script>
// tabbar
import Tabbar from '@/components/supplier/Tabbar.vue'
// product card
import Productcard from '@/components/supplier/Productcard.vue'
// product cart
import Productcart from '@/components/supplier/Productcart.vue'

import img from '../../../static/images/banner-1.jpg'
import img0 from '../../../static/images/banner-0.jpg'

export default {
  name: 'home',
  components: {
    Tabbar,
    Productcard,
    Productcart
  },
  data () {
    return {
      active: 1,
      images: [
        img,
        img0
      ],
      panelShow: false
    }
  },
  methods: {
    // productcard(){
    //   // console.log(this.$refs.c1)
    //   this.$refs.c1.aa()
    // }
    showPanel () {
      this.panelShow = true
    },
    hidePanel () {
      this.panelShow = false
    }
  }

}
</script>
<style lang="less" scoped>
 .top_img img{
      width: 100%;
    }
  .content{
    padding: 0.2rem;
    background: #efefef;
    margin-bottom: 80px;
    .van-swipe{
      .van-swipe-item{
          width: 100%;
            img{
              width: 100%;
            }
          }
      }
      .news{
        background: #fff;
        margin-top: 0.2rem;
        padding: 0.2rem;
        .top{
          display: flex;
          justify-content: space-between;
          padding-bottom: 0.1rem;
          border-bottom: 1px solid #eee;
          span{
            color: red;
          }
        }
        .bottom{
          color: #c7aa53;
          background: #fff;
          div{
            display: flex;
            justify-content: space-between;
            margin: 0.1rem 0;
          }
        }
      }
      .single{
        background: #fff;
        margin-top: 0.2rem;
      }
      .project{
        background: #fff;
        margin-top: 0.2rem;
      }
      .selected{
        .title{
          text-align: center;
          margin: 0.3rem 0 0.1rem;
          span{
            width: 0.8rem;
            height: 0.2rem;
            display: inline-block;
          }
          span:nth-of-type(1){
            background: url('../../../static/images/icon_left.png') no-repeat;
            background-size: 0.5rem auto;
          }
          span:nth-of-type(2){
            background: url('../../../static/images/icon_right.png') no-repeat;
            background-size: 0.5rem auto;
            margin-left: 0.3rem;
          }
        }

      }

  }
</style>
