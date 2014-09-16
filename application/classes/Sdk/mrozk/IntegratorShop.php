<?php
/**
 * Created by PhpStorm.
 * User: mrozk
 * Date: 15.05.14
 * Time: 23:14
 */

use DDelivery\Order\DDeliveryProduct;
use DDelivery\Order\DDStatusProvider;

class IntegratorShop extends \DDelivery\Adapter\PluginFilters
{

    public $request;

    public $settings;

    public $info;

    public function __construct( $request, $settings, $info = null )
    {
        if( $info != null ){
            $this->info = $info;
        }
        $this->request = $request;

        $this->settings = $settings;

        if( empty($this->settings->api) )
        {
            throw new  \DDelivery\DDeliveryException("Пустой api ключ");
        }
        if( empty( $this->settings->address ) ){
            $this->settings->address = array('street' => '','house' => '','corp' => '','flat' => '');
        }else{
            $this->settings->address = json_decode($this->settings->address, true);
        }

    }

    /**
     * Синхронизация локальных статусов
     * @var array
     */
    protected  $cmsOrderStatus = array( DDStatusProvider::ORDER_IN_PROGRESS => 'new',
                                        DDStatusProvider::ORDER_CONFIRMED => 'accepted',
                                        DDStatusProvider::ORDER_IN_STOCK => 'approved',
                                        DDStatusProvider::ORDER_IN_WAY => 'dispatched',
                                        DDStatusProvider::ORDER_DELIVERED => 'dispatched',
                                        DDStatusProvider::ORDER_RECEIVED => 'delivered',
                                        DDStatusProvider::ORDER_RETURN => 'declined',
                                        DDStatusProvider::ORDER_CUSTOMER_RETURNED => 'declined',
                                        DDStatusProvider::ORDER_PARTIAL_REFUND => 'declined',
                                        DDStatusProvider::ORDER_RETURNED_MI => 'declined',
                                        DDStatusProvider::ORDER_WAITING => 'declined',
                                        DDStatusProvider::ORDER_CANCEL => 'declined' );

    /**
     * Настройки базы данных
     * @return array
     */
    public function getDbConfig()
    {
        $config = Kohana::$config->load('database')->get('default');
        return array(
            'pdo' => new \PDO( $config['connection']['dsn'], $config['connection']['username'],
                               $config['connection']['password'], array(\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")),
            'prefix' => 'ddelivery_'
        );
    }
    public function onFinishResultReturn( $order, $resultArray ){
        $resultArray['street'] = '' ;
        $resultArray['house'] = '' ;
        $resultArray['corp'] = '' ;
        $resultArray['flat'] = '' ;
        $resultArray['type'] = $order->type ;
        $resultArray['userInfo']['toHousing'] = $order->toHousing;
        if( !empty( $this->settings->address['street'] ) ){
            $resultArray['street'] = $this->settings->address['street'];
        }

        if( !empty( $this->settings->address['house'] ) ){
            $resultArray['house'] = $this->settings->address['house'];
        }

        if( !empty( $this->settings->address['corp'] ) ){
            $resultArray['corp'] = $this->settings->address['corp'];
        }

        if( !empty( $this->settings->address['flat'] ) ){
            $resultArray['flat'] = $this->settings->address['flat'];
        }
        if( !empty( $order->cityName ) ){
            $resultArray['city_name'] = $order->cityName;
        }else{
            $resultArray['city_name'] = $order->cityName;
        }
        return $resultArray;
    }
    /**
     * Верните true если нужно использовать тестовый(stage) сервер
     * @return bool
     */
    public function isTestMode()
    {
        if ( $this->settings->rezhim  == '1')
        {
            return true;
        }
        elseif($this->settings->rezhim  == '2')
        {
            return false;
        }
        //return true;
    }

    /**
     * Возвращает товары находящиеся в корзине пользователя, будет вызван один раз, затем закеширован
     * @return DDeliveryProduct[]
     */
    protected function _getProductsFromCart()
    {
        $products = array();
        if( count( $this->info )){
            foreach( $this->info['cart'] as $product ){
                $products[] = new DDeliveryProduct(
                    $product['id'],	//	int $id id товара в системе и-нет магазина
                    $product['width'],	//	float $width длинна
                    $product['height'],	//	float $height высота
                    $product['length'],	//	float $length ширина
                    $product['weight'],	//	float $weight вес кг
                    $product['price'],	//	float $price стоимостьв рублях
                    $product['quantity'],	//	int $quantity количество товара
                    $product['title'],
                    $product['sku']	//	string $name Название вещи
                );
            }
        }

        return $products;

    }

    /**
     * Меняет статус внутреннего заказа cms
     *
     * @param $cmsOrderID - id заказа
     * @param $status - статус заказа для обновления
     *
     * @return bool
     */
    public function setInsalesOrderStatus($cmsOrderID, $status, $clientID)
    {
        $insales_user = ORM::factory('InsalesUser', array('id' => $clientID));

        if ( $insales_user->loaded() )
        {

            $insales_api =  new InsalesApi('ddelivery', $insales_user->passwd, $insales_user->shop );

            $pulet = '<order>
                            <id type="integer">' . $cmsOrderID . '</id>
                            <fulfillment-status>' . $status . '</fulfillment-status>
                      </order>';
            //echo strlen($pulet);
            //echo $cmsOrderID;
            $result = json_decode( $insales_api->api('PUT','/admin/orders/' . $cmsOrderID . '.json', $pulet) );
            return $result->id;
        }
            //echo $insales_user->id;
    }

    public function setCmsOrderStatus( $cmsOrderID, $status )
    {

    }

    public function isStatusToSendOrder( $cmsStatus )
    {
        if( $cmsStatus == $this->settings->status )
        {
            return true;
        }
    }
    /**
     * Возвращает API ключ, вы можете получить его для Вашего приложения в личном кабинете
     * @return string
     */
    public function getApiKey()
    {
        return $this->settings->api; //'73e402bc645d73e91721ecbc123e121d';
    }

    /**
     * Должен вернуть url до каталога с статикой
     * @return string
     */
    public function getStaticPath()
    {
        return '../html/';
    }

    /**
     * URL до скрипта где вызывается DDelivery::render
     * @return string
     */
    public function getPhpScriptURL(){
        $url = array();
        $url['client_name'] = $this->request->query('client_name');
        $url['client_phone'] = $this->request->query('client_phone');
        $url['token'] = $this->request->query('token');
        $url['shipping_address'] = $this->request->query('shipping_address');
        $url['type_of_window'] = $this->request->query('type_of_window');

        // Тоесть до этого файла
        return URL::base( $this->request ) . 'sdk/?' . http_build_query($url);
    }

    /**
     * Возвращает путь до файла базы данных, положите его в место не доступное по прямой ссылке
     * @return string
     */
    public function getPathByDB()
    {
        return __DIR__.'/../db/db.sqlite';
    }

    /**
     * Метод будет вызван когда пользователь закончит выбор способа доставки
     *
     * @param \DDelivery\Order\DDeliveryOrder $order
     * @return void
     */
    public function onFinishChange($order)
    {

    }

    /**
     * Какой процент от стоимости страхуется
     * @return float
     */
    public function getDeclaredPercent()
    {
        return $this->settings->declared;
        //return 100; // Ну это же пример, пускай будет случайный процент
    }

    /**
     * Должен вернуть те компании которые НЕ показываются в курьерке
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    public function filterCompanyPointCourier()
    {

        if(!empty($this->settings->cur_companies ) )
        {
            return explode(',', $this->settings->cur_companies);
        }
        return array();
        //
        // TODO: Implement filterCompanyPointCourier() method.
    }

    /**
     * Должен вернуть те компании которые НЕ показываются в самовывозе
     * см. список компаний в DDeliveryUI::getCompanySubInfo()
     * @return int[]
     */
    public function filterCompanyPointSelf()
    {

        if(!empty($this->settings->pvz_companies ) )
        {
            return explode(',', $this->settings->pvz_companies);
        }
        return array();
        /*

        */
        // TODO: Implement filterCompanyPointSelf() method.
    }

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Курьер
     * @return int
     */
    public function filterPointByPaymentTypeCourier()
    {
        return $this->settings->payment;
        return self::PAYMENT_POST_PAYMENT;
        // выбираем один из 3 вариантов(см документацию или комменты к констатам)
        return self::PAYMENT_POST_PAYMENT;
        return self::PAYMENT_PREPAYMENT;
        return self::PAYMENT_NOT_CARE;
        // TODO: Implement filterPointByPaymentTypeCourier() method.
    }

    /**
     * Возвращаем способ оплаты константой PluginFilters::PAYMENT_, предоплата или оплата на месте. Самовывоз
     * @return int
     */
    public function filterPointByPaymentTypeSelf()
    {
        return $this->settings->payment;
        return self::PAYMENT_POST_PAYMENT;
        // выбираем один из 3 вариантов(см документацию или комменты к констатам)
        return self::PAYMENT_POST_PAYMENT;
        return self::PAYMENT_PREPAYMENT;
        return self::PAYMENT_NOT_CARE;
        // TODO: Implement filterPointByPaymentTypeSelf() method.
    }

    /**
     * Если true, то не учитывает цену забора
     * @return bool
     */
    public function isPayPickup()
    {

        $zabor = (int)$this->settings->zabor;
        if( $zabor )
            return true;
        else
            return false;

    }

    /**
     * Метод возвращает настройки оплаты фильтра которые должны быть собраны из админки
     *
     * @return array
     */
    public function getIntervalsByPoint()
    {
        //return array();
        $interval1 = array();
        $interval2 = array();
        $interval3 = array();
        //$interval4 = array();
        if( ( isset( $this->settings->from1 ) && !empty($this->settings->from1) ) && ( isset( $this->settings->to1 ) && !empty($this->settings->to1)  ) && ( isset( $this->settings->sum1 ) ) )
        {
            $interval1 = array('min' => $this->settings->from1, 'max' => $this->settings->to1,
                               'type' => (int)$this->settings->val1, 'amount' => $this->settings->sum1);
        }

        if( ( isset( $this->settings->from2 ) && !empty($this->settings->from2) ) && ( isset( $this->settings->to2 ) && !empty($this->settings->to2) ) && ( isset( $this->settings->sum2 ) ) )
        {
            $interval2 = array('min' => $this->settings->from2, 'max' => $this->settings->to2,
                               'type' => (int)$this->settings->val2, 'amount' => $this->settings->sum2);
        }

        if( ( isset( $this->settings->from3 ) && !empty($this->settings->from3) ) && ( isset( $this->settings->to3 ) && !empty($this->settings->to3) ) && ( isset( $this->settings->sum3 ) ) )
        {
            $interval3 = array('min' => $this->settings->from3, 'max' => $this->settings->to3,
                               'type' => (int)$this->settings->val3, 'amount' => $this->settings->sum3);
        }
        return array($interval1, $interval2, $interval3);

    }

    /**
     * Тип округления
     * @return int
     */
    public function aroundPriceType()
    {

        switch ($this->settings->okrugl)
        {
            case '1': return self::AROUND_FLOOR;
            case '2': return self::AROUND_CEIL;
            case '3': return self::AROUND_ROUND;
            default : return self::AROUND_ROUND;
        }

        // return self::AROUND_ROUND; // self::AROUND_FLOOR, self::AROUND_CEIL
    }

    /**
     * Шаг округления
     * @return float
     */
    public function aroundPriceStep()
    {
        //return 0.5;

        if( !empty ( $this->settings->shag ) ){
            return $this->settings->shag; // До 50 копеек
        }
        else
        {
            return 0.5;
        }

        // TODO: Implement aroundPriceStep() method.
    }

    /**
     * описание собственных служб доставки
     * @return string
     */
    public function getCustomPointsString()
    {
        return '';
    }

    /**
     * Если вы знаете имя покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientFirstName() {
        $client_name = $this->request->query( 'client_name' );
        if( !empty( $client_name ) ){
            return $client_name;
        }
        return '';
    }

    /**lo
     * Если вы знаете фамилию покупателя, сделайте чтобы оно вернулось в этом методе
     * @return string|null
     */
    public function getClientLastName() {
        $client_name = $this->request->query( 'client_name' );
        if( !empty( $client_name ) ){
            $data = explode(' ', trim($client_name));
            if(isset($data[1])){
                return $data[1];
            }
        }
        return '';
    }
    /**
     * Вырезаем из номера телефона ненужные символы
     *
     * @param string $phone
     *
     * @return string
     */
    public function formatPhone( $phone )
    {
        return preg_replace( array('/-/', '/\(/', '/\)/', '/\+7/', '/\s\s+/'), '', $phone );
    }

    /**
     * Если вы знаете телефон покупателя, сделайте чтобы оно вернулось в этом методе. 11 символов, например 79211234567
     * @return string|null
     */
    public function getClientPhone() {
        $phone = $this->formatPhone( $this->request->query( 'client_phone' ) );
        $phone  = substr( $phone, -10);
        return '+7' . $phone;
    }

    /**
     * Верни массив Адрес, Дом, Корпус, Квартира. Если не можешь можно вернуть все в одном поле и настроить через get*RequiredFields
     * @return string[]
     */
    public function getClientAddress() {
        /// return array(1,2,3,4,5,6,7,8);

        $shipping_address = $this->request->query('shipping_address');
        $street = '';
        $house = '';
        $corp = '';
        $flat = '';
        if( !empty( $this->settings->address['street'] ) ){
            if(isset( $shipping_address['fields_values_attributes'][$this->settings->address['street']] )){
                $street = $shipping_address['fields_values_attributes'][$this->settings->address['street']]['value'];
            }
        }

        if( !empty( $this->settings->address['house'] ) ){
            if(isset( $shipping_address['fields_values_attributes'][$this->settings->address['house']] )){
                $house = $shipping_address['fields_values_attributes'][$this->settings->address['house']]['value'];
            }
        }

        if( !empty( $this->settings->address['corp'] ) ){
            if(isset( $shipping_address['fields_values_attributes'][$this->settings->address['corp']] )){
                $corp = $shipping_address['fields_values_attributes'][$this->settings->address['corp']]['value'];
            }
        }

        if( !empty( $this->settings->address['flat'] ) ){
            if(isset( $shipping_address['fields_values_attributes'][$this->settings->address['flat']] )){
                $flat = $shipping_address['fields_values_attributes'][$this->settings->address['flat']]['value'];
            }
        }

        return array( $street, $house, $corp, $flat );

        //return  array(1,2,3,4);
    }

    /**
     * Верните id города в системе DDelivery
     * @return int
     */
    public function getClientCityId()
    {
        // Если нет информации о городе, оставьте вызов родительского метода.
        return parent::getClientCityId();
    }

    /**
     * Возвращает бинарную маску обязательных полей для курьера
     * Если редактирование не включено, но есть обязательность то поле появится
     * Если редактируемых полей не будет то пропустим шаг
     * @return int
     */
    public function getCourierRequiredFields()
    {
        if( $this->settings->form == '0'){
            return    self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
                    | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE
                    | self::FIELD_EDIT_ADDRESS | self::FIELD_REQUIRED_ADDRESS
                    | self::FIELD_EDIT_ADDRESS_HOUSE | self::FIELD_REQUIRED_ADDRESS_HOUSE
                    | self::FIELD_EDIT_ADDRESS_HOUSING
                    | self::FIELD_EDIT_ADDRESS_FLAT | self::FIELD_REQUIRED_ADDRESS_FLAT;
        }else{
            return 0;
        }

        /*
        // ВВести все обязательно, кроме корпуса
        return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
        | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE
        | self::FIELD_EDIT_ADDRESS | self::FIELD_REQUIRED_ADDRESS
        | self::FIELD_EDIT_ADDRESS_HOUSE | self::FIELD_REQUIRED_ADDRESS_HOUSE
        | self::FIELD_EDIT_ADDRESS_HOUSING
        | self::FIELD_EDIT_ADDRESS_FLAT | self::FIELD_REQUIRED_ADDRESS_FLAT;
        */
    }

    /**
     * Возвращает бинарную маску обязательных полей для пунктов самовывоза
     * Если редактирование не включено, но есть обязательность то поле появится
     * Если редактируемых полей не будет то пропустим шаг
     * @return int
     */
    public function getSelfRequiredFields()
    {
        if($this->settings->form == '0'){
            return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
                   | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE;
        }else{
            return 0;
        }
        /*
        // Имя, фамилия, мобилка
        return self::FIELD_EDIT_FIRST_NAME | self::FIELD_REQUIRED_FIRST_NAME
        | self::FIELD_EDIT_PHONE | self::FIELD_REQUIRED_PHONE;
        */
    }

    /**
     * Возвращает поддерживаемые магазином способы доставки
     * @return array
     */
    public function getSupportedType(){

        $type_of_window = $this->request->query('type_of_window');

        switch ($type_of_window ){
            case 'onlyMap' :
                    return array(
                        \DDelivery\Sdk\DDeliverySDK::TYPE_SELF
                    );
                    break;
            case 'onlyCourier' :
                    return array(
                        \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER
                    );
                    break;
            default:
                    if( $this->settings->type == '1' ){
                        return array(
                            \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER,
                            \DDelivery\Sdk\DDeliverySDK::TYPE_SELF
                        );
                    }elseif($this->settings->type == '2'){
                        return array(
                            \DDelivery\Sdk\DDeliverySDK::TYPE_SELF
                        );
                    }elseif($this->settings->type == '3'){
                        return array(
                            \DDelivery\Sdk\DDeliverySDK::TYPE_COURIER,
                        );
                    }
        }
    }


    /**
     *
     * Тип кеширования, для централизированого подхода и для индивидуального решения
     * разные
     *
     * @return string
     */
    public function getCachingFormat(){
        // return DShopAdapter::CACHING_TYPE_CENTRAL;
        return \DDelivery\Adapter\DShopAdapter::CACHING_TYPE_CENTRAL;
        return DShopAdapter::CACHING_TYPE_CENTRAL;
    }

    /**
     * Получить доступные способы оплаты для Самовывоза ( можно анализировать содержимое order )
     * @param $order DDeliveryOrder
     * @return array
     */
    public function getSelfPaymentVariants( $order ){
        return array();
    }

    /**
     * Получить доступные способы оплаты для курьера ( можно анализировать содержимое order )
     * @param $order DDeliveryOrder
     * @return array
     */
    public function getCourierPaymentVariants( $order ){
        return array();
    }


    /**
     * Получить название шаблона для сдк ( разные цветовые схемы )
     *
     * @return string
     */
    public function getTemplate(){
        return $this->settings->theme;
    }


}