<?php

namespace Models;
use Models\Agency;

class Address {

    public function getAddressesShopping() {

        $token = get_option('wpmelhorenvio_token');
        $params = array(
            'headers'           =>  [
                'Content-Type'  => 'application/json',
                'Accept'        => 'application/json',
                'Authorization' => 'Bearer '.$token,
            ],
            'timeout'=> 10,
            'method' => 'GET'
        );

        $urlApi = 'https://www.melhorenvio.com.br';
        if(WP_ENV !== null && WP_ENV == 'develop') {
            $urlApi = 'https://sandbox.melhorenvio.com.br';
        } 
        $response =  json_decode(wp_remote_retrieve_body(wp_remote_request($urlApi . '/api/v2/me/addresses', $params)));
        $selectedAddress = get_option('melhorenvio_address_selected_v2');
        // $agencies = (new Agency())->getAgencies();

        $addresses = [];
        foreach ($response->data as $address) {

            $agenciesJadlog = [];
            // foreach ($agencies['agencies'] as $agency) {
            //     if ($agency['address']['city'] == $address->city->city && $agency['address']['state'] == $address->city->state->state_abbr ) {
            //         $agenciesJadlog[] = $agency;
            //     }
            // }

            $addresses[] = [
                'id' => $address->id,
                'address' => $address->address,
                'complement' => $address->complement,
                'label' => $address->label,
                'postal_code' => $address->postal_code,
                'number' => $address->number,
                'district' => $address->district,
                'city' => $address->city->city,
                'state' => $address->city->state->state_abbr,
                'country' => $address->city->state->country->id,
                'selected' => ($selectedAddress == $address->id) ? true : false,
                'jadlog' => $agenciesJadlog
            ];
        }

        return [
            'success' => true,
            'addresses' => $addresses
        ];
    }

    public function setAddressShopping($id) {
        
        $addressDefault = get_option('melhorenvio_address_selected_v2');
        if  (empty($addressDefault)) {
            add_option('melhorenvio_address_selected_v2', $id);
            return [
                'success' => true,
                'id' => $id
            ];
        }
        update_option('melhorenvio_address_selected_v2', $id);
        return [
            'success' => true,
            'id' => $id
        ];
    }

    public function getAddressFrom() {

        $addresses =$this->getAddressesShopping();
        $address = null;
        foreach($addresses['addresses'] as $item) {
            if($item['selected']) {
                $address = $item;
            }
        }
        
        if ($address == null && !empty($addresses['addresses'])) {
            return end($addresses['addresses']);
        }

        return $address;
    }
}