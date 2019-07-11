<template>
  <div class="list">
    <div class="wenzi">是非成败转头空，青山依旧在，惯看秋月春风。一壶浊酒喜相逢，古今多少事，滚滚长江东逝水，浪花淘尽英雄。 几度夕阳红。浪花淘尽英雄。 几度夕阳红。白发渔樵江浪花淘尽英雄 几度夕阳...   全文 ></div>
    <div class="img">
      <div><img src="../../../static/images/img1.png"></div>
      <div><img src="../../../static/images/img1.png"></div>
      <div><img src="../../../static/images/img1.png"></div>
    </div>
    <ul class="ctrl">
      <li>20190318 12:12:20</li>
      <li>图文消息</li>
      <li><van-icon name="eye-o" />1111</li>
      <li><van-icon name="chat-o" />1111</li>
      <li><van-icon name="browsing-history-o" />1111</li>
      <li><van-icon name="browsing-history-o" />1111</li>
    </ul>
  </div>
</template>

<script>
export default {

}
</script>

<style lang="less" scoped>
 .list{
   padding: 0 0.2rem 0.3rem;
    .wenzi{
    margin-top: 0.2rem;
  }
  .img{
    display: flex;
    div{
      border: 1px solid #ccc;
      width: 32%;
      margin-right: 2%;
      img{
        width: 100%;
      }
    }
    div:nth-of-type(3){
      margin-right: 0;
    }
  }
  .ctrl{
    display: flex;
    align-items: center;
    margin-top: 0.2rem;
    li{
      margin-right:0.14rem;
    }
  }
 }
</style>
