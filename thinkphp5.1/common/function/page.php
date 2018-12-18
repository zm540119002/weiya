<?php

/**分页查询
 */
function page_query($model, $where = "", $field = "", $order = '', $join = '', $group = "", $pageSize = 15,$alias =''){
    $_m = clone $model;

    if ($field)
        $model = $model->field($field);
    if ($order)
        $model = $model->order($order);
    if ($join)
        $model = $model->join($join);
    if ($alias){
        $_m = $_m->alias($alias);
        $model = $model->alias($alias);
    }

    if ($group) {
        $res = $model->group($group)->select();
        $count = count($res);
        $page = new web\all\Component\PageAjax($count, $pageSize);
        $data = array_slice($res, $page->firstRow, $page->listRows);
    } else {
        $count = $_m->where($where)->join($join)->count();
        $page = new web\all\Component\PageAjax($count, $pageSize);
        if ($count) {
            $data = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->select();
        } else {
            $data = array();
        }
    }

    $returnData = array(
        'data' => $data,
        'total' => $count,
        'pageList' => $page->show(),
    );

    if (APP_DEBUG) {
        $returnData['lastSQL'] = $_m->getLastSql();
    }

    return $returnData;
}