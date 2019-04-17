Vue.component('button-counter', {
    data: function () {
        return {
            count: 0
        }
    },
    template: '<button v-on:click="count++">You clicked me {{ count }} times.</button>'
});
Vue.component('blog-post', {
    props: ['title'],
    template: '<h3>{{ title }}</h3>'
});
console.log(123);
import { cube, foo, graph } from 'index';
graph.options = {
    color:'blue',
    thickness:'3px'
};
graph.draw();
console.log(cube(3)); // 27
console.log(foo);    // 4.555806215962888