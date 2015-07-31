<?php
/* *****************************************************************
 * @Author: wushuiyong
 * @Created Time : 一  7/20 15:52:01 2015
 *
 * @File Name: components/Controller.php
 * @Description:
 * *****************************************************************/

namespace app\components;

use yii;

class Controller extends yii\web\Controller {

    /**
     * json渲染. PS:调用此方法之前若有输出将会出错
     *
     * @param mixed     $data
     * @param int       $code 0成功 非0错误
     * @param string    $msg  错误信息
     * @param int       $option json_encode options
     */
    public static function renderJson($data, $code = 0, $msg = '', $option = 0) {
        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;
        $ret = [
            'code' => (int)$code,
            'msg'  => $msg,
            'data' => $data,
        ];
        echo json_encode($ret, $option);
        Yii::$app->end();
    }

    /**
     * 获取参数（post/get）的值, 优先级：post > get > default
     *
     * @param string $name 参数名字
     * @param mixed  $default 默认值
     * @return mixed
     */
    public static function getParam($name, $default = null) {
        $post = Yii::$app->request->post($name);
        $get  = Yii::$app->request->get($name);
        return isset($_POST[$name]) ? $post : (isset($_GET[$name]) ? $get : $default);
    }
}

