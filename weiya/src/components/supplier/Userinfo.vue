<template>
  <div class="top">
    <div @click="creatorspace" class="left"></div>
    <div class="center">
      <div>杨凤</div>
      <div>详情详情</div>
    </div>
    <div class="right">
      <div>关注</div>
      <div>6666人</div>
     </div>
  </div>
</template>

<script>
export default {
  methods:{
     creatorspace(){
        this.$router.push({
        name: "Creatorspace"
      })
     },
   }
}
</script>

 <style lang="less" scoped>
    .top{
        display: flex;
        padding:0.3rem 0.2rem;
        border-top: 1px solid #ddd;
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
</style>

