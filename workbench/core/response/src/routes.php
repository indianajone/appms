<?php

Route::get('/core', function() {
    $array_msg = Array
        (
        'total' => 500,
        '0' => Array
            (
            'student' => Array
                (
                'id' => 1,
                'name' => 'abc',
                'address' => Array
                    (
                    'city' => 'Pune',
                    'zip' => '411006'
                )
            )
        ),
        '1' => Array
            (
            'student' => Array
                (
                'id' => 1,
                'name' => 'abc',
                'address' => Array
                    (
                    'city' => 'Pune',
                    'zip' => '411006'
                )
            )
        )
    );
    
    //RESPONSE
    //return $array_msg;
    //return Response::xml($array_msg,'root');
    
    $status['code'] = 200;
    $status['message'] = 'success';
    //ERROR
    return Response::error($status);
});