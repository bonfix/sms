<?php


namespace Bonfix\DaliliSms;


use App\Models\Regions\Region;
use App\Models\System\Country;
use App\Models\Villages\Village;
use Bonfix\DaliliSms\Models\SmsIn;
use Bonfix\DaliliSms\Models\SmsMenu;
use Bonfix\DaliliSms\Models\SmsMenuOption;
use Bonfix\DaliliSms\Models\SmsOut;
use Bonfix\DaliliSms\Models\SmsUser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class DaliliSmsController
{
    private const DALILI_REG_KEY = 'DALILI';
    private const MAIN_MENU_KEY = '00';
    private const COUNTRY_CODE = 'KE';
    private const DEFAULT_LOCALE = 'en';
    private $user;
    private $nextMenuName;
    private $nextMenu;
    private $currentMenu;
    private $lastSmsOut;
    private $selectedOption;
    private $receivedSms;

    private function testMethod()
    {
        return "";//$this->getSelectedOption($this->getMenuByName(SmsHelper::MENU_MAIN_AREA), '0');
    }
    /**
     * smsIn : handles incoming sms from the SMS gateway
     * @param Request $request
     * @return Response
     */
    public function smsIn(Request $request)
    {
        Log::info($request);
        $reponseMsg = '';
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
                    $reponseMsg = $this->goToNextMenu();
                } else {
                    //if unsubscribing
                    if(strtoupper($this->receivedSms) == SmsHelper::KEY_DALILI_UNSUBSCRIBE){
                        $this->user->isActive = 0;
                        $this->user->save();
                        $reponseMsg = $this->goToNextMenu($this->getMenuByName(SmsHelper::MENU_MAIN_UNSUBSCRIBE));
                    }
                    //if has finished registration and has requested main menu
                    elseif ($this->user->village_id && (strtoupper($this->receivedSms) == self::DALILI_REG_KEY || strtoupper($this->receivedSms) == self::MAIN_MENU_KEY))
                    {
                        //show main menu
                        $this->saveInboundSms();
                        $reponseMsg = $this->goToNextMenu($this->getMenuByName(SmsHelper::MENU_MAIN_OPTIONS));
                    }else
                    {
                        $this->saveInboundSms(true);
                        $reponseMsg = SmsHelper::INVALID_OPTION_MSG[$this->getLocale()]."\n" .
                            $this->goToNextMenu($this->currentMenu);
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
                $reponseMsg = $this->goToNextMenu();
            } else
            {
                //ignore msg
                $reponseMsg = $this->testMethod();//"Not a Dalili user";

            }
        }

        //return \response()->json($reponseMsg);
        return response($reponseMsg, 200)
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

    public function saveInboundSms($isInvalid=false)
    {
        SmsIn::create([
            'user_id' => $this->user->id,
            'menu_id' => $this->currentMenu->id,
            'message' => $this->receivedSms,
            'option' => $this->selectedOption,
            'isInvalid' =>$isInvalid
        ]);
        $this->setOption();
    }

    public function saveOutboundSms($menu, $msg, $page = null)
    {
        if ($menu->name == SmsHelper::MENU_PROFILE_MAIN)
            $this->nextMenuName = SmsHelper::MENU_PROFILE_MAIN;
        SmsOut::create([
            'user_id' => $this->user->id,
            'menu_id' => $menu->id,
            'message' => $msg,
            'page' => $page,
            'next_menu' => $this->nextMenuName
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
        if(!$nextMenu)
            $nextMenu = $this->getNextMenu();
        if (!$nextMenu)
            return 'Item under construction! 🚧';
        $locale = $this->getLocale();
        $desc = '';
        if($this->currentMenu->name == SmsHelper::MENU_MAIN_REGISTRATION)
            $desc .= $this->currentMenu->getTranslation($locale)->description . "\n";
        //get return data
        $desc .= $nextMenu->getTranslation($locale)->description;
        $optionsMsg = "";
        if ($nextMenu->name == SmsHelper::MENU_MAIN_AREA || $nextMenu->name == SmsHelper::MENU_MAIN_SUB_AREA) {
            $optionsMsg = $this->getAreaOptions($nextMenu);
        } else {
            $options = $nextMenu->options()->get();
            foreach ($options as $key => $menuOpt) {
                if ($optionsMsg != "")
                    $optionsMsg .= "\n";
                $optionsMsg .= "$menuOpt->order. " .
                    $menuOpt->getTranslation($locale)->description;
            }
        }
        //if not main menu, add main menu option
        if($nextMenu->group != SmsHelper::MENU_GROUP_MAIN)
        {
            $comm = SmsMenu::where('name',
                SmsHelper::MENU_COMMON_OPTIONS_MAIN_MENU_CODE)->first();
            $optionsMsg .= "\n"."$comm->name. " .$comm->getTranslation($locale)->description;
        }
        $msg = $this->replacePlaceholders("$desc\n$optionsMsg");

        $this->saveOutboundSms($nextMenu, $msg);
        return $msg;
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
                $this->nextMenuName = $this->lastSmsOut->next_menu;
                return $this->getMenuByName($this->selectedOption);
            }
            elseif($currentMenu->name == SmsHelper::MENU_MAIN_AREA)
            {
                $this->nextMenuName = $this->lastSmsOut->next_menu;
                return $this->getMenuByName(SmsHelper::MENU_MAIN_SUB_AREA);
            }else
                return $this->getMenuByName($this->lastSmsOut->next_menu);
        }
        if($this->selectedOption && $currentMenu->optionsNavigable){
            return $this->getMenuByName($this->selectedOption);
        }
        $next = $currentMenu->order + 1;
        return SmsMenu::where('order', $next)->first();
    }

    private function getSelectedOption()
    {
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
        $val = null;
        $opt = SmsMenuOption::where(['menu_id' => $this->currentMenu->id, 'order' => $this->receivedSms])->first();
        if($opt)
            $val = $opt->value;
        return $val;
    }
}
