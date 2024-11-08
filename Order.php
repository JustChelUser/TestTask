<?php
include_once "Database.php";
function getBarcode()
{
    $currentTime = microtime(true);
    $randomDigits = rand(100000, 999999);
    $barcode = str_replace('.', '', $currentTime . $randomDigits);
    return $barcode;
}

function bookOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $barcode)
{
    //Обращение как будто к (https://api.site.com/book)
    $responses = [
        ["message" => "order successfully booked"],
        ["error" => "barcode already exists"]
    ];
    return $responses[array_rand($responses)];
}

function approveOrder($barcode)
{
    //Обращение как будто к https://api.site.com/approve
    $responses = [
        ["message" => "order successfully approved"],
        ["error" => "event cancelled"],
        ["error" => "no tickets"],
        ["error" => "no seats"],
        ["error" => "fan removed"]
    ];
    return $responses[array_rand($responses)];
}

function newOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity)
{
    $barcode = getBarcode();
    while (true) {
        $bookResponse = bookOrder($event_id, $event_date, $ticket_adult_price, $ticket_adult_quantity, $ticket_kid_price, $ticket_kid_quantity, $barcode);
        if (isset($bookResponse["error"])) {
            $barcode = getBarcode();
            continue;
        }
        if (isset($bookResponse["message"])) {
            $approveResponse = approveOrder($barcode);
            if (isset($approveResponse["message"])) {
                $pdo = ConnectDB();
                $data = [
                    'event_id'=>$event_id,
                    'event_date'=>$event_date,
                    'ticket_adult_price'=>$ticket_adult_price,
                    'ticket_adult_quantity'=>$ticket_adult_quantity,
                    'ticket_kid_price'=>$ticket_kid_price,
                    'ticket_kid_quantity'=>$ticket_kid_quantity,
                    'barcode'=>$barcode,
                    'user_id'=>1,
                    ];
                $stmt = $pdo->prepare('INSERT INTO Orders (event_id,event_date,ticket_adult_price,ticket_adult_quantity,ticket_kid_price,ticket_kid_quantity,barcode,user_id) 
values (:event_id, :event_date, :ticket_adult_price,:ticket_adult_quantity, :ticket_kid_price, :ticket_kid_quantity,:barcode,:user_id)');
                $stmt->execute($data);
                echo "Order was successfully booked and approved\n";
                break;
            } else {
                echo "Order was not approved. Error: ".$approveResponse["error"]."\n";
                break;
            }
        }
    }
}
newOrder(1,"2020-11-11",100,1,100,1);
newOrder(2,"2020-11-11",200,1,100,1);