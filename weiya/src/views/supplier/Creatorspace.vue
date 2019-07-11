<template>
  <div>
    <div class="header">
      <div @click="back" class="back"><van-icon name="arrow-left" /></div>
      <Userinfo/>
    </div>
    <div class="details">
      <van-tabs v-model="active">
        <van-tab title="所有">
          <List/>
        </van-tab>
        <van-tab title="消息">内容 2</van-tab>
        <van-tab title="小视频">内容 3</van-tab>
        <van-tab title="视频">内容 4</van-tab>
        <van-tab title="文章">内容 4</van-tab>
        <van-tab title="云店">内容 4</van-tab>
      </van-tabs>
    </div>
  </div>
</template>

<script>
import List from '@/components/supplier/List.vue'
import Userinfo from '@/components/supplier/Userinfo.vue'
export default {
  name: 'Creatorspace',
  components: {
    List,
    Userinfo
  },
 data() {
    return {
      active: 0
    };
  },
  methods:{
     back(){
         this.$router.back(-1)
     }
   }
}
</script>

<style lang="less" scoped>
  .header{
    background-color: #666;
    color: #fff;
    padding: 0.25rem;
    .back{
      margin-bottom: 0.4rem;
    }
   .top{
        border-top: none;
      }  
  }
  .details{
   .van-tab{
     background-color: red;
   }
  }
</style>