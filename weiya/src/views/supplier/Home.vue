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
          <div>是非成败转头空，青山依旧在，惯看风</div>
          <div>|更多</div>
        </div>
      </div>
    </div>
    <Tabbar/>
  </div>
</template>

<script>
// @ is an alias to /src
import Tabbar from '@/components/supplier/Tabbar.vue'
import img from '../../../static/images/banner-1.jpg'
import img0 from '../../../static/images/banner-0.jpg'

export default {
  name: 'home',
  components: {
    Tabbar
  },
   data() {
    return {
      active: 1,
      images: [
        img,
        img0
      ]
    };
  },
   methods:{
     
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
        }
      } 
      
  }
</style>
