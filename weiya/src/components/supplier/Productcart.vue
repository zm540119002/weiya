<template>
  <div @click="hidePanel"  class="bg"></div>
</template>

<script>
export default {
  props: {
    panelShow: {
      type: Boolean
    }
  },
  methods: {
    aa () {
      console.log(1111)
    },
    hidePanel () {
      // 这里应该怎么修改父组件的值呢?这段代码会报错，
      // 提示子组件不能修改父组件的值
      // this.panelShow = false;  ✗
      this.$emit('hidePanel')
    }
  }
}
</script>

<style lang="less" scoped>
  .bg{
    width: 100vh;
    height: 100vh;
    background: #000;
    position: fixed;
    top: 0;
    z-index: 999;
    opacity: 0.6;
  }
</style>
