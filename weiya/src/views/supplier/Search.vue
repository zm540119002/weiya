<template>
  <div>
    <div class="top">
      <van-icon @click="back" name="arrow-left" />
      <input type="text" placeholder="搜消息、小视频、视频或文章"> 
    </div>
  </div>
</template>

<script>
export default {
 methods:{
     back(){
         this.$router.back(-1)
          }
       }
}
</script>

<style lang="less" scoped>
.top{
  background-color: #ff6482;
  color: #fff;
  display: flex;
  align-items: center;
  padding: 0.15rem 0.2rem;
  input{
    height: 30px;
    border-radius: 4px;
    margin-left: 0.3rem;
  }
}
</style>
