<template>
  <div>
    <div>     
     购物车
     <van-icon name="location-o" />
    </div>
    <Tabbar msg="Welcome to Your Vue.js App" />
  </div>
</template>

<script>
import Tabbar from '@/components/supplier/Tabbar.vue'
export default {
  name: 'home',
  components: {
    Tabbar
  }
}
</script>

<style lang="less" scoped>

</style>
