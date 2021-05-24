<?php


namespace Bonfix\DaliliSms;

use App\Http\Resources\CompareShopCollection;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\PromotionCollection;
use App\Http\Resources\ShopCollection;
use App\Models\Regions\Region;
use App\Models\Shops\Shop;
use App\Models\System\Country;
use App\Models\Villages\Village;
use App\Services\v2\Shops\ShopService;
use Bonfix\DaliliSms\Models\SmsIn;
use Bonfix\DaliliSms\Models\SmsMenu;
use Bonfix\DaliliSms\Models\SmsMenuOption;
use Bonfix\DaliliSms\Models\SmsOut;
use Bonfix\DaliliSms\Models\SmsUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class DaliliSmsController
{
    private $request;
    private const DALILI_REG_KEY = 'DALILI';
    private const COUNTRY_CODE = 'KE';
    private const COUNTRY_CURRENCY = 'Ksh';
    private const ITEMS_PER_PAGE = 6;
    private const DEFAULT_LOCALE = 'en';
    private $user;
    private $menuAfterProcessing;
    private $nextMenu;
    private $currentMenu;
    private $currentPage = 1;
    private $lastSmsOut;
    private $selectedOption;
    private $receivedSms;
    protected $shopService;
    private $_countryId;
    private $_shop;
    private $arrayToCache;
    private $arrayFromCache;
    private $isMoreMenu = false;
    private $village;
    /**
     * @var mixed
     */
    private $tempOpt;
    private $nextSmsOut;

    //TEST
    private $replySms = false;
    private $returnSmsContentResponse = true;
    /**
     * @var bool
     */
    private $isInvalidChoice = false;
    /**
     * @var bool
     */
    private $invalidShop = false;

    public function __construct(ShopService $shopService)
    {
        $this->shopService = $shopService;
    }

    public function testMethod()
    {
       /* $this->request->per_page = 10;
        $this->request->country = 3;
        $offers = $this->menuShops($this->getMenuByName(SmsHelper::MENU_SHOPS_MAIN));//->toArray($this->request);
        //dd($offers);*/
        //dd(phpinfo());
        //$res = $this->sendSms('254793076592', 'TEst-Msg');
        dd("TEST");
    }

    private function isApiKeyValid($key){
       return ($key && $key === env('SMS_DALILI_API_KEY'));
    }

    /**
     * smsIn : handles incoming sms from the SMS gateway
     * @param Request $request
     * @return Response
     */
    public function smsIn(Request $request)
    {
        $this->request = $request;
        if(!$this->isApiKeyValid($request->get('api')))
        {
            return response("Error! API key is not given or is invalid!", 401)
                ->header('Content-Type', 'text/plain');
        }
        Log::info($request);
        $reponseMsgs = [];
        $phone = $request->msisdn;
        $this->receivedSms = trim($request->message);
        //check if a dalili user
        $this->user = SmsUser::where('phone', $phone)->first();
        if ($this->user && $this->user->isActive)
        {
            //check last menu item
            $this->lastSmsOut = SmsOut::where(['user_id' => $this->user->id])
                ->orderBy('id', 'DESC')->first();
            //get menu
            if ($this->lastSmsOut) {
                $this->currentMenu= SmsMenu::find($this->lastSmsOut->menu_id);
                //check if selected option is valid
                $this->selectedOption = $this->getSelectedOption();
                if ($this->selectedOption) {
                    //save sms in
                    $this->saveInboundSms();
                    //in case of errors, go to main menu
//                    try
                    {
                        $reponseMsgs = $this->goToNextMenu();
                    }
//                    catch(\Exception $e)
//                    {
//                        $reponseMsgs = $this->goToNextMenu($this->getMenuByName(SmsHelper::MENU_MAIN_OPTIONS));
//                        if(count($reponseMsgs)>0){
//                            $reponseMsgs[0] = SmsHelper::ERROR_MSG[$this->getLocale()] ."\n". $reponseMsgs[0];
//                        }
//                    }
                } else {
                    //if unsubscribing
                    if(strtoupper($this->receivedSms) == SmsHelper::KEY_DALILI_UNSUBSCRIBE){
                        $this->user->isActive = 0;
                        $this->user->save();
                        $reponseMsgs = $this->goToNextMenu($this->getMenuByName(SmsHelper::MENU_MAIN_UNSUBSCRIBE));
                    }
                    //if has finished registration and has requested main menu
                    elseif ($this->user->village_id &&
                        (strtoupper($this->receivedSms) == self::DALILI_REG_KEY ||
                            strtoupper($this->receivedSms) === SmsHelper::MENU_COMMON_OPTIONS_MAIN_MENU_CODE))
                    {
                        //show main menu
                        $this->saveInboundSms();
                        $reponseMsgs = $this->goToNextMenu($this->getMenuByName(SmsHelper::MENU_MAIN_OPTIONS));
                    }else
                    {
                        //INVALID
                        $this->isInvalidChoice = true;
                        //get next menu
                        $this->nextMenu = SmsMenu::find($this->lastSmsOut->menu_id);
                        $this->saveInboundSms(true);
                        //resend current menu
                        $this->resendOutboundSms();
                        $reponseMsgs[] = SmsHelper::INVALID_OPTION_MSG[$this->getLocale()]."\n" .
                            $this->lastSmsOut->message;
                    }
                }
            }

        } else
         { //new user
            //check if Dalili registration
            if (strtoupper($this->receivedSms) == self::DALILI_REG_KEY)
            {
                //check if previously registered - reset
                if ($this->user){
                    $this->user->isActive = 1;
                    $this->user->village_id = null;
                    $this->user->region_id = null;
                    $this->user->language_code = null;
                    $this->user->save();
                }
                else{
                    //register user
                    $this->user = SmsUser::create([
                        'phone' => $phone,
                        'country_code' => self::COUNTRY_CODE,
                        'last_request_time' => Carbon::now()
                    ]);
                }
                //store msg
                $this->currentMenu = $this->getMenuByName(SmsHelper::MENU_MAIN_REGISTRATION);
                $this->saveInboundSms();
                $reponseMsgs = $this->goToNextMenu();
            } else
            {
                //ignore msg
                //$reponseMsg = [];//$this->testMethod();//"Not a Dalili user";

            }
        }

        //check if send sms $returnSmsContentResponse
        foreach ($reponseMsgs as $msg)
        {
            if(!$this->isInvalidChoice)
                $this->saveOutboundSms($msg);
            if($this->replySms) {
                $res = $this->sendSms($phone, $msg);
                //dd($res->getStatusCode());
                if($res->getStatusCode() == 200) {
                    $this->nextSmsOut->is_sent = true;
                    $this->nextSmsOut->save();
                }
            }
        }

        $reponseText = 'Ok';
        if($this->returnSmsContentResponse)
            $reponseText = join("\n", $reponseMsgs);
        //return \response()->json($reponseMsg);
        return response($reponseText, 200)
            ->header('Content-Type', 'text/plain');
    }

    private function setOption()
    {
        $option = $this->selectedOption;
        $isChanged = true;
        if ($this->currentMenu->name == SmsHelper::MENU_MAIN_LANGUAGE)
            $this->user->language_code = $option;
        else if ($this->currentMenu->name == SmsHelper::MENU_MAIN_USERNAME)
            $this->user->username = $this->receivedSms;
        else if ($this->currentMenu->name == SmsHelper::MENU_MAIN_AREA)
            $this->user->region_id = $option;
        else if ($this->currentMenu->name == SmsHelper::MENU_MAIN_SUB_AREA)
            $this->user->village_id = $option;
        else if ($this->currentMenu->name == SmsHelper::MENU_PROFILE_MAIN &&
            $option == SmsHelper::MENU_MAIN_UNSUBSCRIBE)
            $this->user->isActive = 0;
        else
            $isChanged = false;
        if ($isChanged)
            $this->user->save();
    }
    private function getSelectedIds(){
        $selection = $this->selectedOption;
        if($selection && is_array($selection))
        {
            //get selected item ids
            $ids = [];
            foreach ($selection as $item)
            {
                $ids[] = $item->id;
            }
            $selection = join(',', $ids);
        }
        return $selection;
    }
    public function saveInboundSms($isInvalid=false)
    {
        SmsIn::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->currentMenu->id,
            'message' => $this->receivedSms,
            'option' => $this->getSelectedIds(),
            'isInvalid' =>$isInvalid
        ]);
        $this->setOption();
    }
    public function resendOutboundSms(){
        $this->nextSmsOut = SmsOut::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->lastSmsOut->menu_id,
            'message' => $this->lastSmsOut->message,
            'page' => $this->lastSmsOut->page,
            'next_menu' => $this->lastSmsOut->next_menu,
            'cache' => $this->lastSmsOut->cache,
            'prev_item' => $this->lastSmsOut->prev_item
        ]);
        //clear cache of prev
        $this->clearRequestCache();
    }
    private function clearRequestCache(){
        if($this->lastSmsOut && $this->lastSmsOut->cache)
        {
            $this->lastSmsOut->cache = null;
            $this->lastSmsOut->save();
        }
    }
    public function saveOutboundSms($msg, $page = null)
    {
        $page = $page ? $page : $this->currentPage;
        //dd($msg);
//        if($this->isInvalidChoice){
//
//        }
        if ($this->nextMenu->name == SmsHelper::MENU_PROFILE_MAIN)
            $this->menuAfterProcessing = SmsHelper::MENU_PROFILE_MAIN;
        $this->nextSmsOut = SmsOut::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->nextMenu->id,
            'message' => $msg,
            'page' => $page,
            'next_menu' => $this->menuAfterProcessing,
            'cache' => $this->arrayToCache ? json_encode($this->arrayToCache) : null,
            'prev_item' => $this->isMoreMenu ? $this->lastSmsOut->prev_item : $this->getSelectedIds()
        ]);
    }

    private function getLocale(): string
    {
        if ($this->user && $this->user->language_code)
            return $this->user->language_code;
        return self::DEFAULT_LOCALE;
    }

    public function goToNextMenu($nextMenu=null)
    {
        $responseMessages = [];
        $locale = $this->getLocale();
        if($nextMenu == null)
            $nextMenu = $this->getNextMenu();
        $this->nextMenu = $nextMenu;
        if (!$nextMenu)
        {
            $commonMainMenuOption = SmsMenu::where('name',
                SmsHelper::MENU_COMMON_OPTIONS_MAIN_MENU_CODE)->first();
            //clear cache of prev
            $this->clearRequestCache();
            return "🚧 Item under construction! 🚧\n\n"."$commonMainMenuOption->name. " .$commonMainMenuOption->getTranslation($locale)->description;;
        }
        $desc = '';
        if($this->currentMenu->name == SmsHelper::MENU_MAIN_REGISTRATION)
            $responseMessages[] = $this->replacePlaceholders($this->currentMenu->getTranslation($locale)->description . "\n");
        //get return data
        $desc .= $nextMenu->getTranslation($locale)->description;
        $optionsMsg = "";
        if ($nextMenu->name == SmsHelper::MENU_MAIN_AREA || $nextMenu->name == SmsHelper::MENU_MAIN_SUB_AREA)
        {
            $optionsMsg = $this->getAreaOptions($nextMenu);
        }
        elseif ($nextMenu->name == SmsHelper::MENU_OFFERS_MAIN)
        {
            $this->setRequestParams();
            $optionsMsg .= $this->menuOffers($nextMenu);
        }
        elseif ($nextMenu->group == SmsHelper::MENU_GROUP_PRODUCTS)
        {
            $this->setRequestParams();
            $optionsMsg .= $this->menuProducts($nextMenu);
        }
        elseif ($nextMenu->group == SmsHelper::MENU_GROUP_SHOPS)
        {
            $this->setRequestParams();
            $optionsMsg .= $this->menuShops($nextMenu);
        }
        else
        {
            $options = $nextMenu->options()->get();
            foreach ($options as $key => $menuOpt) {
                if ($optionsMsg != "")
                    $optionsMsg .= "\n";
                $optionsMsg .= "$menuOpt->order. " .
                    $menuOpt->getTranslation($locale)->description;
            }
        }
        //if has cache, add more menu option
        if($this->arrayToCache && count($this->arrayToCache)>=self::ITEMS_PER_PAGE)
        {
            $comm = SmsMenu::where('name',
                SmsHelper::MENU_COMMON_OPTIONS_MORE_MENU_CODE)->first();
            $optionsMsg .= "\n"."$comm->name. " .$comm->getTranslation($locale)->description;
        }

        //if not main menu, add main menu option
        if($nextMenu->group != SmsHelper::MENU_GROUP_MAIN)
        {
            $comm = SmsMenu::where('name',
                SmsHelper::MENU_COMMON_OPTIONS_MAIN_MENU_CODE)->first();
            $optionsMsg .= "\n"."$comm->name. " .$comm->getTranslation($locale)->description;
        }

        //replacePlaceholders
        $responseMessages[] = $this->replacePlaceholders("$desc\n$optionsMsg");
        //in case of errors, go to main menu
        /*try
        {
        }
        catch(\Exception $e)
        {
            return $this->goToNextMenu($this->getMenuByName(SmsHelper::MENU_MAIN_OPTIONS));
        }*/
        //clear cache of prev
        $this->clearRequestCache();
        return $responseMessages;
    }

    private function setRequestParams(){
        $this->request->per_page = self::ITEMS_PER_PAGE;
        $this->request->page = $this->currentPage;
        $this->request['page'] = $this->currentPage;
        $this->request->country = $this->getCountryId();
        $this->request['language'] = $this->request->language = $this->getLocale();
        if(!$this->village)
            $this->village = Village::find($this->user->village_id);
        $this->request->latitude = $this->village->latitude;
        $this->request->longitude = $this->village->longitude;
    }



    private function menuShops($nextMenu)
    {
        $this->request->order = 'price';
        $options = '';
        $count = 1;
        if ($nextMenu->name == SmsHelper::MENU_SHOPS_MAIN)
        {
            $this->arrayToCache = $this->getShops()['data'];
            foreach ($this->arrayToCache as $k=>$offer)
            {
                if($options != '')
                    $options .= "\n";
                $options .= ($count++).". {$offer['name']}";
            }
            return  $options;//html_entity_decode($options, ENT_NOQUOTES); <del>
        }
        if ($nextMenu->name == SmsHelper::MENU_SHOPS_ITEMS)
        {
            $from = ($this->currentPage-1) * $this->request->per_page;
            $res = $this->getShopProducts(1);
            if($res)
            {
                $this->arrayToCache = $res['products']->slice($from, $this->request->per_page);
                foreach ($this->arrayToCache as $k=>$item)
                {
                    //dd($item->toArray());
                    if($options != '')
                        $options .= "\n";
                    $options .= ($count++).". {$item['description']} @{$item['pivot']['price']}";
                }
            }
            else{
                $this->invalidShop = true;
                $options = 'Sorry, shop not found!';
            }

            return  $options;
        }
    }

    private function getShops()
    {
        $data = $this->shopService->getShopsForSms();
        $data = new ShopCollection($data);
        return $data->toArray($this->request);
    }

    private function getShopProducts($page)
    {
        $id = null;
        if($this->isMoreMenu)
            $id = $this->lastSmsOut->prev_item;
        else
            $id = $this->selectedOption[0]->id;

        $data = $this->shopService->getShopDetails($id);//->slice(0, $this->request->per_page);
        if(is_array($data))
        {
            return null;
        }
        $data = new \App\Http\Resources\Shop($data);
        return $data->toArray($this->request);
    }

    private function menuProducts($nextMenu)
    {
        $this->request->order = 'price';
        $options = '';
        $count = 1;
        if ($nextMenu->name == SmsHelper::MENU_PRODUCTS_MAIN)
        {
            $this->arrayToCache = $this->getProducts()['data'];
            foreach ($this->arrayToCache as $k=>$offer)
            {
                if($options != '')
                    $options .= "\n";
                $options .= ($count++).". {$offer['description']}";
            }
            return  $options;//html_entity_decode($options, ENT_NOQUOTES); <del>
        }
        if ($nextMenu->name == SmsHelper::MENU_PRODUCTS_SHOPS)
        {
            $this->arrayToCache = $this->getCompareProducts(1)['data'];
            foreach ($this->arrayToCache as $item)
            {
                if($options != '')
                    $options .= "\n";
                $phone = Shop::find($item['id'])->phone_number;
                $options .= ($count++).". {$item['name']} @{$item['basket_price']}";
                $options .= $phone ? " ($phone)" : '';
            }
            return  $options;
        }
    }

    private function getCompareProducts($page)
    {
        $ids = null;
        if($this->isMoreMenu)
            $ids = explode(',', $this->lastSmsOut->prev_item);
        else
            $ids = $this->selectedOption;
        //set product ids and quantity
        $this->request->products = [];
        foreach ($ids as $k=>$item){
            if($this->isMoreMenu)
                $this->request->products[] = ['id'=>$item, 'quantity'=>1];
            else
            $this->request->products[] = ['id'=>$item->id, 'quantity'=>1];
        }
        $from = ($this->currentPage-1) * $this->request->per_page;
        $data = $this->shopService->compareProducts()->slice($from, $this->request->per_page);
        $data = new CompareShopCollection($data);
        return $data->toArray($this->request);
    }

    private function getProducts()
    {
        $data = $this->shopService->getProducts();
        $data = new ProductCollection($data);
        return $data->toArray($this->request);
    }

    private function menuOffers($nextMenu)
    {
        if ($nextMenu->name == SmsHelper::MENU_OFFERS_MAIN)
        {
            $options = '';
            $count = 1;
            $this->arrayToCache = $this->getOffers(1)['data'];
            foreach ($this->arrayToCache as $k=>$offer)
            {
                if($options != '')
                    $options .= "\n";
                $options .= ($count++).". {$offer['description']} @{$offer['price']}";
            }
            return  $options;//html_entity_decode($options, ENT_NOQUOTES); <del>
        }/*else{
            //get selected item ids
            $totalCost = 0;
            $original = 0;
            foreach ($this->selectedOption as $k=>$item)
            {
                $totalCost += $item['price'];
                $original += $item['original_price'];
            }

            return null;
        }*/
    }

    private function getOffers($page)
    {
        $offers = $this->shopService->getPromotions();
        $offers = new PromotionCollection($offers);
        return $offers->toArray($this->request);
    }

    private function replacePlaceholders($str)
    {
        return preg_replace_callback('/\{\{(\w+)}}/',
            [$this, '_replacePlaceholder'], $str);
    }

    private function _replacePlaceholder($match)
    {
        list(,$name) = $match;
        switch ($name){
            case 'village_name':
                return Village::find($this->user->village_id)->getTranslation(self::DEFAULT_LOCALE)->name;
            case 'region_name':
                return Region::find($this->user->region_id)->getTranslation(self::DEFAULT_LOCALE)->name;
            case 'language':
                return SmsMenuOption::where(['menu_id' => $this->getMenuByName(SmsHelper::MENU_MAIN_LANGUAGE)->id,
                    'value' => $this->getLocale()])->first()->getTranslation($this->getLocale())->description;
            case 'unsubscribe_key':
                return SmsHelper::KEY_DALILI_UNSUBSCRIBE;
            case 'short_code':
                return SmsHelper::DALILI_SHORTCODE;
            case 'username':
                return $this->user->username;
            case 'shop_name':
                return $this->selectedOption[0]->shop->name;
            case 'shop_contact':
                return Shop::find($this->selectedOption[0]->shop->id)->phone_number;
            case 'offer_price':
                return self::COUNTRY_CURRENCY."".$this->selectedOption[0]->price;
            case 'original_price':
                return $this->selectedOption[0]->original_price;
                //products
            case 'products_shop_name':
                return $this->selectedOption[0]->name;
            case 'products_shop_contact':
                return Shop::find($this->selectedOption[0]->id)->phone_number;
            case 'products_basket_price':
                return self::COUNTRY_CURRENCY." ".$this->selectedOption[0]->basket_price;
            default:
                if(strpos($name, "shops_") === 0)
                    return $this->getShopDetails($name);
                return '';
        }
    }
    private function getShopDetails($key)
    {
        if(!$this->_shop)
        {
            if($this->isMoreMenu)
                $id = $this->lastSmsOut->prev_item;
            else
                $id = $this->selectedOption[0]->id;
            $this->_shop = Shop::find($id);
        }
        //dd($this->_shop->toArray());
        switch ($key)
        {
            case 'shops_shop_name': return $this->_shop->name;
            case 'shops_shop_contact': return $this->_shop->phone_number;
            case 'shops_basket_price':
                $total = 0;
                foreach ($this->selectedOption as $item){
                    $total += $item->pivot->price;
                }
                return self::COUNTRY_CURRENCY." ".$total;
        }
    }

    private function getAreaOptions($menu)
    {
        $areas = [];
        $areaOptions = '';
        $areas = $this->getRegions($menu->name);
        foreach ($areas as $k => $area) {
            if ($areaOptions != '')
                $areaOptions .= "\n";
            $areaOptions .= ($k + 1) . ". ".$area['name'];
        }
        return $areaOptions;
    }

    private function getRegions($menuName): array
    {
        $res = [];
        if ($menuName == SmsHelper::MENU_MAIN_AREA)
        {
            $countryId = Country::where('iso2', self::COUNTRY_CODE)->first()->id;
            $regions = Region::where('country_id', $countryId)->orderBy('id')->get();
        }
        else
        {
            $regions = Village::where('region_id', $this->user->region_id)->orderBy('id')->get();
        }
        foreach ($regions as $k => $region) {
            $res[] = ['name' => $region->getTranslation(self::DEFAULT_LOCALE)->name, 'id'=>$region->id];
        }
        return $res;
    }

    public function getMenuByName($name)
    {
        return SmsMenu::where('name', $name)->first();
    }

    private function getNextMenu($currentMenu=null)
    {
        if(!$currentMenu)
            $currentMenu = $this->currentMenu;
        if($this->lastSmsOut && $this->lastSmsOut->next_menu){
            if($this->selectedOption && $currentMenu->optionsNavigable)
            {
                $this->menuAfterProcessing = $this->lastSmsOut->next_menu;
                return $this->getMenuByName($this->selectedOption);
            }
            elseif($currentMenu->name == SmsHelper::MENU_MAIN_AREA)
            {
                $this->menuAfterProcessing = $this->lastSmsOut->next_menu;
                return $this->getMenuByName(SmsHelper::MENU_MAIN_SUB_AREA);
            }else
                return $this->getMenuByName($this->lastSmsOut->next_menu);
        }
//        if ($this->currentMenu->name == SmsHelper::MENU_OFFERS_MAIN) {
//            return $this->getMenuByName(SmsHelper::MENU_OFFERS_RES);
//        }
        if($this->selectedOption && $currentMenu->optionsNavigable){
            return $this->getMenuByName($this->selectedOption);
        }
        if($this->lastSmsOut && $this->lastSmsOut->cache &&
            $this->selectedOption === SmsHelper::MENU_COMMON_OPTIONS_MORE_MENU_CODE){
                return $currentMenu;
        }
        $next = $currentMenu->order + 1;
        return SmsMenu::where(['order'=>$next, 'group'=>$currentMenu->group])->first();
    }

    private function getSelectedOption()
    {
        if($this->receivedSms === SmsHelper::MENU_COMMON_OPTIONS_MAIN_MENU_CODE)
            return null;
        if ($this->currentMenu->name == SmsHelper::MENU_MAIN_USERNAME)
            return $this->receivedSms;
        if ($this->currentMenu->name == SmsHelper::MENU_MAIN_AREA || $this->currentMenu->name == SmsHelper::MENU_MAIN_SUB_AREA) {
            $regions = $this->getRegions($this->currentMenu->name);
            if(is_numeric($this->receivedSms) && array_key_exists($this->receivedSms-1, $regions)){
                return $regions[$this->receivedSms-1]['id'];
            }
            else
                return null;
        }
        /*if ($this->currentMenu->name == SmsHelper::MENU_OFFERS_MAIN) {
            $regions = $this->getOffers(1);
            if(is_numeric($this->receivedSms) && array_key_exists($this->receivedSms-1, $regions)){
                return $this->receivedSms;
            }
            else
                return null;
        }*/
        if($this->lastSmsOut->cache){
            if($this->receivedSms === SmsHelper::MENU_COMMON_OPTIONS_MORE_MENU_CODE)
            {
                $this->isMoreMenu  = true;
                $this->currentPage += $this->lastSmsOut->page ? $this->lastSmsOut->page : 1;
                return $this->receivedSms;
            }
            //items with no selection
            if ($this->currentMenu->name == SmsHelper::MENU_PRODUCTS_SHOPS ||
                $this->currentMenu->name == SmsHelper::MENU_SHOPS_ITEMS){
                return null;
            }
            $this->arrayFromCache = json_decode($this->lastSmsOut->cache);
            //dd($this->arrayFromCache);
            $choices = explode(',', $this->receivedSms);
            if(count($choices) > 1 && $this->currentMenu->allowMultiple == false)
                return null;
            $selectedItems = [];
            foreach ($choices as $choice)
            {
                if(is_numeric($choice) &&
                    array_key_exists(--$choice, $this->arrayFromCache))
                {
                    $selectedItems[] = $this->arrayFromCache[$choice];
                }
                else
                    return null;
            }
            return $selectedItems;
        }
        $val = null;
        $opt = SmsMenuOption::where(['menu_id' => $this->currentMenu->id, 'order' => $this->receivedSms])->first();
        if($opt)
            $val = $opt->value;
        return $val;
    }

    private function getCountryId()
    {
        if(!$this->_countryId)
            $this->_countryId = Country::where('iso2', self::COUNTRY_CODE)->first()->id;
        return $this->_countryId;
    }

    private function sendSms($phone, $textMessage): ?ResponseInterface
    {
        $client = new Client(['headers' => ['AccessKey' => env('SMS_ACCESS_KEY')]]);
        //$client->setDefaultOption('verify', false);
        // Set a single header using path syntax
        //$client->setDefaultOption('headers/AccessKey', env('SMS_ACCESS_KEY'));
        $response = null;
        try {
            $response = $client->post(env('SMS_API_SEND_URL'), [
                'json' => [
                    'SenderId' => env('SMS_SENDER_ID'),
                    "ApiKey" => env('SMS_API_KEY'),
                    "ClientId" => env('SMS_CLIENT_ID'),
                    "MessageParameters" => [
                        [
                            "Number" => $phone,
                            "Text" => $textMessage
                        ]
                    ]
                ]
            ]);
        }catch(\Exception $exception){
        }
        return $response;
    }
}
