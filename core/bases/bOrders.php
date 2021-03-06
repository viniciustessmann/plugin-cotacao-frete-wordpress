<?php

namespace Bases;

use Interfaces\iOrders;

class bOrders implements iOrders {

    private $id;

    private $status;
    
    private $from;
    
    private $to;
    
    private $products;
    
    private $package;
    
    private $options;

    /**
     * @param Array $attributes
     * @return void
     */
    public function setAttributes($attributes) 
    {
        //  code
    }

    /**
     * @return object
     */
    public function retrieveOne() : Array
    {
        $post = get_post(['post_id' => $id, 'post_type' => 'shop_order']);
        
        return [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@retrieveOne',
            'data' => $post
        ];
    }

    /**
     * @param Array $filters
     * @return Array
     */
    public static function retrieveMany($filters = NULL) 
    {
        $defaults = array(
            'numberposts' => 5,
            'post_type' => 'shop_order'
        );

        return get_posts($defaults);
    }


    /**
     * @param Array $data
     * @return object
     */
    public function update($data) 
    {
        return [
            'message' => 'Testing OOP WP',
            'method' => 'OrdersModel@update'
        ];
    }
}