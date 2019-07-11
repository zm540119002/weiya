<template>
   <div class="details">
        <Userinfo />
        <div class="wenzi">
          文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字文字
        </div>
        <div class="img">
          <div><img src="../../../static/images/pro1.png" alt=""></div>
          <div><img src="../../../static/images/pro1.png" alt=""></div>
          <div><img src="../../../static/images/pro1.png" alt=""></div>
          <div><img src="../../../static/images/pro1.png" alt=""></div>
          <div><img src="../../../static/images/pro1.png" alt=""></div>
          <div><img src="../../../static/images/pro1.png" alt=""></div>
        </div>
      </div> 
</template>

<script>
import Userinfo from '@/components/supplier/Userinfo.vue'
export default {
name: 'list1',
  components: {
    Userinfo
  },
}
</script>

<style lang="less" scoped>
 .details{
      margin-bottom: 0.3rem;      
      .top{
        display: flex;
        padding-top: 0.2rem;
        .left{
          width: 1.2rem;
          height: 1.2rem;
          border-radius: 50%;
          background: #999;
          margin-right: 0.3rem;
          background-image: url(../../../static/images/11.png)
        }
        .center{
          flex: 1;
          div:nth-of-type(2){
            color: #ccc;
          }
        }
        .right{
          text-align: center;
          div:nth-of-type(1){
            padding: 0.06rem 0.4rem;
            background: red;
            color: #fff;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 0.06rem;
          }
        }
      }
      .wenzi{
        margin-top: 0.2rem;
        padding: 0 0.2rem;
      }
      .img{
          display: flex;
          flex-direction: row;
          flex-wrap:wrap;
          margin-top: 0.2rem;
          padding: 0 0.2rem;
        div{
          width: 32%;
          margin-right: 2%;
          img{
            width: 100%;
            
          }
        }
        div:nth-of-type(3n){
          margin-right: 0;
        }
        div:nth-of-type(-n+3){
          margin-bottom: 2%;
        }
      }
    }
</style>
