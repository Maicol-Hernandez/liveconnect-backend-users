<?php

namespace App\helpers;

use Api\Maicoldev\Exceptions\HttpException;

class Pagination
{

    private static array $data;
    private static string $limit, $page;


    /**
     * 
     */
    public static function getData(): array
    {
        return self::$data;
    }

    /**
     * 
     */
    public static function setData(array $data)
    {
        self::$data = $data;
    }

    /**
     * 
     */
    public static function getLimit(): int
    {
        return self::$limit;
    }

    /**
     * 
     */
    public static function setLimit(int $limit)
    {

        self::$limit = $limit;
    }

    /**
     * @return int page
     */
    public static function getPage(): int
    {

        return self::$page;
    }

    /**
     * 
     */
    public static function setPage(int $page)
    {
        self::$page = $page;
    }

    public function __construct(
        // array $data
    )
    {
        // $this->data = $data;
    }

    /**
     * paginated data
     * @param array data
     * @param array query params
     * @return array data
     */
    public static function pagination(array $data, array $fields): array
    {
        // params
        $limit = (int)($_GET['limit'] ?? 10);
        $page = (int)($_GET['page'] ?? 1);

        // global properties 
        self::setLimit($limit);
        self::setPage($page);
        self::setData($data);

        $aux_is_params = [];

        foreach ($fields as $field) {
            // name param
            $aux_is_params["{$field}"]['name'] = $field;
            // value param
            $param = $_GET[$field] ?? null;
            $aux_is_params["{$field}"]['value'] = ($param === '') ? null : $param;
        }

        if (!is_numeric($limit) || $limit < 1) {
            // exception
            throw new HttpException('Invalid query param "limit", must be a number greather or equal 1');
        }

        if (!is_numeric($page) || $page < 1) {
            // exception
            throw new HttpException('Invalid query param "page", must be a number greather or equal 1');
        }

        if ($limit > 25) {
            // exception
            throw new HttpException('The limit of users per request is 25');
        }

        // echo json_encode($aux_is_params);

        $is_params = self::validateQueryParams($aux_is_params);

        return self::configureArrayMap($is_params);
    }

    /**
     * @param array params query
     */
    private static function validateQueryParams(array $params): array
    {

        $aux_is_param = [];
        foreach ($params as $is_param) {
            # code...
            if ($is_param['value'] === 'true') {
                # code...
                $aux_is_param["{$is_param['name']}"]["{$is_param['name']}"] = "&{$is_param['name']}={$is_param['value']}";
                $aux_is_param["{$is_param['name']}"]["value"] = true;
            } elseif ($is_param['value'] === 'false') {
                $aux_is_param["{$is_param['name']}"]["{$is_param['name']}"] = "&{$is_param['name']}={$is_param['value']}";
                $aux_is_param["{$is_param['name']}"]["value"] = false;
            } else {

                // $aux_is_param["{$is_param['name']}"]["{$is_param['name']}"];
                $aux_is_param["{$is_param['name']}"]["value"] = $is_param['value'];
            }
        }

        // echo json_encode($aux_is_param);

        return $aux_is_param;
    }

    /**
     * 
     */
    private static function configureArrayMap(array $params): array
    {

        $keys = [];

        $data_map = [
            'page' => self::getPage(),
            'limit' => self::getLimit(),
            'filters' => [
                //     'is_admin' => "",
                //     'is_active' => ""
            ],
            'pagination' => [
                'total' => 0,
                'data_per_page' => 0,
                'previous' => '',
                'next' => ''
            ],
            'data' => []
        ];

        // echo "params", json_encode($params), "\n";

        foreach ($params as $key => $value) {
            // echo "key ", json_encode($key) . "\n";
            // echo "value ", json_encode($value) . "\n";
            $keys["{$key}"] = $value['value'];
        }

        // echo json_encode($keys);

        $data_map['filters'] = $keys;
        $data_map['pagination']['total'] = count(self::countData($params));

        $limit = self::getLimit();
        $page = self::getPage();

        if (self::getPage() === 1) {
            $data_map['data'] = array_slice(self::$data, 0, self::$limit);
            if (count(self::$data) > self::$limit) {
                $data_map['pagination']['next'] = self::getUrl() . "?page=2&limit={$limit}";
            }
        } else {
            $page -= 1;
            $data_map['data'] = array_slice(self::$data, $page * $limit, $limit);
            $data_map['pagination']['previous'] = self::getUrl() . "?page={$page}&limit={$limit}";

            if (count(self::$data) > (($page * $limit) + $limit)) {
                $aux_page = $page + 2;
                $data_map['pagination']['next'] = self::getUrl() . "?page={$aux_page}&limit={$limit}";
            }
        }

        $data_map['pagination']['data_per_page'] = count($data_map['data']);

        return $data_map;
    }

    /**
     * @param array $data
     * @param array $params
     * @return array
     */
    private static function countData(array $params): array
    {
        // echo "params ", json_encode($params), "\n";



        $found = [];

        // array_filter(
        //     $data,
        //     function ($info) {
        //         // echo "is_admin ", json_encode($info['is_admin']), "\n";
        //         // echo "is_active ", json_encode($info['is_active']), "\n";
        //         return $info['is_admin'] === true && $info['is_active'];
        //     }
        // );

        foreach ($params as $key => $value) {
            # code...
            // echo "key ", json_encode($key), "\n";
            // echo "value ", json_encode($value), "\n";

        }
        foreach (self::$data as $user) {
            # code...
            if ($user[$key] && $user[$key]) {
                # code...
                array_push($found, $user);
            }
        }




        echo "found ", json_encode($found), "\n";

        return self::$data;
    }


    /**
     * @return string url 
     */
    private static function getUrl(): string
    {

        $http = 'http://';
        if (isset($_SERVER['HTTPS'])) {
            $http = 'https://';
        }

        $path = $_SERVER['REQUEST_URI'];
        foreach (['?', '#'] as $char) {
            if (strpos($path, $char) !== false) {
                # code...
                $path = strstr($path, $char, true);
            }
        }

        return "{$http}{$_SERVER['HTTP_HOST']}{$path}";
    }
}



        // $is_active = $_GET['is_active'] ?? null;
        // $is_admin = $_GET['is_admin'] ?? null;

        // // is active
        // $is_active = ($is_active === '') ? null : $is_active;
        // // is admin
        // $is_admin = ($is_admin === '') ? null : $is_admin;