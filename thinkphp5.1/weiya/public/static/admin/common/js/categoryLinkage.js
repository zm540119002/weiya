$(document).ready(function(){
    //初始化一级菜单
    $('[name=category_id_1]').append($('#allCategoryListTemp').find('option[level=1]').clone());
    //一级菜单change
    $('[name=category_id_1]').change(function(){
        //首先清空二级菜单和三级菜单
        $('[name=category_id_2],[name=category_id_3]').find('option[value!=""]').remove();
        //然后二级菜单追加所选一级菜单的子菜单
        if($(this).val()){
            var childMenu = $('#allCategoryListTemp').find('option[level=2][parent_id_1='+$(this).val()+']');
            $('[name=category_id_2]').append(childMenu.clone());
        }
    });
    //二级菜单change
    $('[name=category_id_2]').change(function(){
        //首先清空三级菜单
        $('[name=category_id_3]').find('option[value!=""]').remove();
        //然后三级菜单追加所选二级菜单的子菜单
        if($(this).val()){
            var childMenu = $('#allCategoryListTemp').find('option[level=3][parent_id_2='+$(this).val()+']');
            $('[name=category_id_3]').append(childMenu.clone());
        }
    });
    var h = document.documentElement.clientHeight || document.body.clientHeight;
    $('.Hui-article-box').css('height',(h-50)+'px').addClass('scrollContainer');
});