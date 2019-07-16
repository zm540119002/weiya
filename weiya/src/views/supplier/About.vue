<template>
  <div class="home">
    <div>
       从业人员
    </div>
    
    <Tabbar msg="Welcome to Your Vue.js App"/>
  </div>
</template>

<script>
// @ is an alias to /src
import Tabbar from '@/components/supplier/Tabbar.vue'

export default {
  name: 'home',
  components: {
    Tabbar
  }
}
</script>
