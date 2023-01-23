<?php

// select
//   o.id as Bestellnummer,
//   fu.username as Kunde,
//   o.ctime as Bestelldatum,
//   ob.price as Bestellwert,
//   ob.costs as Versandkosten,
//   IF(LENGTH(SUBSTRING(GROUP_CONCAT(oba.countryid),4)) > 0,
//     SUBSTRING(GROUP_CONCAT(oba.countryid),4),
//     GROUP_CONCAT(oba.countryid)
//   ) as Lieferland,
//   obs2.code as Versandart,
//   obs1.code as Zahlungsart
// from mshop_order o
// join mshop_order_base ob on o.baseid=ob.id
// join mshop_order_base_address oba on ob.id=oba.baseid
// join mshop_order_base_service obs1 on obs1.baseid=ob.id and obs1.type='payment'
// join mshop_order_base_service obs2 on obs2.baseid=ob.id and obs2.type='delivery'
// join fe_users as fu on ob.customerid=fu.uid
// where o.statuspayment >= 5
// group by o.id, fu.username, o.ctime, ob.price, ob.costs, obs2.code, obs1.code order by o.id;

foreach( $this->get( 'orders', [] ) as $order )
{
    $base = $order->getBaseItem();
    $customer = $base->getCustomerItem();
    $price = $base->getPrice();
    $countryId = map( $base->getAddress( 'delivery' ) )->getCountryId()->first() ?: map( $base->getAddress( 'payment' ) )->getCountryId()->first();

    echo( '"'. join( '","', [
        $order->getId(),
        $customer->getCode(),
        $order->getStatusPayment(),
        $order->getTimeCreated(),
        $price->getValue(),
        $price->getCosts(),
        $countryId,
        map( $base->getService( 'delivery' ) )->getCode()->first(),
        map( $base->getService( 'payment' ) )->getCode()->first(),
    ] ) . '"' . PHP_EOL );
}