import { cube, foo, graph } from 'index';
graph.options = {
    color:'blue',
    thickness:'3px'
};
graph.draw();
console.log(cube(3)); // 27
console.log(foo);    // 4.555806215962888