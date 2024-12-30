<?php
    //dump($order->orderProducts[0]->productOption->product->name);
?>
<html>
<head>
    <script src="https://cdn.iamport.kr/v1/iamport.js"></script>
    <script>
        function openPayment() {
            IMP.init('{{config("iamport.imp_code")}}'); // 예: 'imp00000000'
            //IMP.agency("고객사 식별코드", "티어코드"); // 예: 'imp00000000', '123'
            IMP.request_pay(
                {
                    //channelKey: "{콘솔 내 연동 정보의 채널키}",
                    /*pg: 'html5_inicis', // version 1.1.0부터 지원.
                    pay_method: "card",
                    merchant_uid: `payment-${crypto.randomUUID()}`, // 주문 고유 번호
                    name: "노르웨이 회전 의자",
                    amount: 64900,
                    buyer_email: "gildong@gmail.com",
                    buyer_name: "홍길동",
                    buyer_tel: "010-4242-4242",
                    buyer_addr: "서울특별시 강남구 신사동",
                    buyer_postcode: "01181",*/
                    pg: "{{$order->pay_method_pg}}", // version 1.1.0부터 지원.
                    pay_method: "{{$order->pay_method_method}}",
                    merchant_uid: "{{$order->merchant_uid}}",
                    name: "{{ $order->orderProducts[0]->productOption->product->name }}",
                    amount: {{ $order->price}},
                    buyer_email: "{{ $order->buyer_email }}",
                    buyer_name: "{{ $order->buyer_name }}",
                    buyer_tel: "{{ $order->buyer_contact }}",
                    buyer_addr: "{{ $order->buyer_address }} {{$order->buyer_address_detail}}",
                    buyer_postcode: "{{ $order->buyer_address_zipcode }}",
                },
                function (response) {
                    //console.log(response);
                    if (response.success) {
                        console.log(response.imp_uid, response.merchant_uid);
                        sendComplete(response.imp_uid, response.merchant_uid);
                    }
                }
            );
        }

        function sendComplete(imp_uid, merchant_uid) {
            fetch("http://localhost:8000/api/orders/complete", {
                method: "POST",
                headers: {"Content-Type": "application/json"},
                body: JSON.stringify({imp_uid, merchant_uid}),
            }).then(response => {

            }).then(data => {
                console.log("응답:", data);
            }).catch(error => {
                console.error("오류:", error);
            });
        }

        //openPayment();
        sendComplete('imp_034497587575', 'ORD-00000010')
        //show();

        function show ()
        {
            fetch("http://localhost:8000/api/orders/10", {
                method: "GET",
                headers: {"Content-Type": "application/json"},
            }).then(response => {
                console.log(response);
            }).then(data => {
                console.log("응답:", data);
            }).catch(error => {
                console.error("오류:", error);
            });
        }


    </script>
</head>
<body>
</body>
</html>
