<?php
namespace Bonfix\DaliliSms\seeds;

use Bonfix\DaliliSms\Models\SmsMenu;
use Bonfix\DaliliSms\Models\SmsMenuOption;
use Bonfix\DaliliSms\SmsHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SmsMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //MENU_GROUP_MAIN
        $order = 1;
        $this->createMenu(SmsHelper::MENU_GROUP_MAIN, SmsHelper::MENU_MAIN_REGISTRATION,
            $order,
            "Welcome to Dalili.\nTo unsubscribe, text {{unsubscribe_key}} to {{short_code}}.",
            "Karibu Dalili.\nIli kujiondoa, tuma {{unsubscribe_key}} kwa {{short_code}}.");
        $this->createMenuLang($order); //2
        $this->createMenuUsername($order); //3
        $this->createMenuAreas($order);//4,5
        $this->createMainMenu($order);//6

        //MENU_GROUP_PROFILE
        $order = 1;
        $menu = $this->createMenu(SmsHelper::MENU_GROUP_PROFILE, SmsHelper::MENU_PROFILE_MAIN,
            $order, 'PROFILE: Change',
            'PROFAILI: Badilisha',
        true);
        $this->createMenuOption($menu->id, SmsHelper::MENU_MAIN_LANGUAGE, 1, 'Language ({{language}})', 'Lugha ({{language}})');
        $this->createMenuOption($menu->id, SmsHelper::MENU_MAIN_AREA, 2, 'Location ({{village_name}},{{region_name}})', 'Eneo  ({{village_name}},{{region_name}})');
        $this->createMenuOption($menu->id, SmsHelper::MENU_MAIN_USERNAME, 3,
            'Username ({{username}})',
            'Jina la matumizi ({{username}})');
        $this->createMenuOption($menu->id, SmsHelper::MENU_MAIN_UNSUBSCRIBE, 4,
            'Unsubscribe from Dalili',
            'Jiondoe kutoka Dalili');

        //MENU_GROUP_OFFERS
        $order = 1;
        $this->createMenu(SmsHelper::MENU_GROUP_OFFERS, SmsHelper::MENU_OFFERS_MAIN,
            $order,
            'This are products on offer, select an offer to view the price and the trader’s contact',
            'Hizi ni bidhaa zilizo kwa matoleo. Chagua bidhaa ndio uone bei na namba ya simu ya muuzaji');
        $this->createMenu(SmsHelper::MENU_GROUP_OFFERS, SmsHelper::MENU_OFFERS_RES,
            $order,
            'Items selected cost {{price}} from {{prev_price}}. Thank you for for shopping with shop: {{shop_name}}. Contact shop using {{shop_contact}}',
            'Bei ya bidhaa ulizochagua ni {{price}} kutoka {{prev_price}}. Asante kwa kwa kununua na duka: {{shop_name}}. Wasiliana na duka kutumia {{shop_contact}}');

        //MENU_COMMON_OPTIONS
        $order = 1;
        $this->createMenu(SmsHelper::MENU_GROUP_COMMON_OPTIONS, SmsHelper::MENU_COMMON_OPTIONS_MORE_MENU_CODE,
            $order, "More","Zaidi");
        $this->createMenu(SmsHelper::MENU_GROUP_COMMON_OPTIONS, SmsHelper::MENU_COMMON_OPTIONS_BACK_MENU_CODE,
            $order, "Back","Nyuma");
        $this->createMenu(SmsHelper::MENU_GROUP_COMMON_OPTIONS, SmsHelper::MENU_COMMON_OPTIONS_MAIN_MENU_CODE,
            $order, "Main menu","Menu kuu");

        //MENU not ordered
        $order = -100;
        $this->createMenu(SmsHelper::MENU_GROUP_MAIN, SmsHelper::MENU_MAIN_UNSUBSCRIBE,
            $order,
            "You have been unsubscribed from Dalili. To join Dalili again, text DALILI to {{short_code}}.\nDalili, more choice, more value!",
            "Umejiondoa kwenye Dalili. Kujiunga na Dalili tena, tuma neno DALILI kwa {{short_code}}.\nDalili, more choice, more value!");

    }

    private function createMainMenu(&$order)
    {
        $menu = $this->createMenu(SmsHelper::MENU_GROUP_MAIN,
            SmsHelper::MENU_MAIN_OPTIONS,
            $order, 'MAIN MENU: Select',
            'MENU KUU: Chagua', true);

        //create menu options
        $this->createMenuOption($menu->id, SmsHelper::MENU_SHOPS_MAIN, 1, 'Shops', 'Maduka');
        $this->createMenuOption($menu->id, SmsHelper::MENU_PRODUCTS_MAIN, 2, 'Products', 'Bidhaa');
        $this->createMenuOption($menu->id, SmsHelper::MENU_OFFERS_MAIN, 3, 'Offers', 'Matoleo');
        $this->createMenuOption($menu->id, SmsHelper::MENU_PROFILE_MAIN, 4, 'Profile', 'Profaili');
    }
    public function createMenuUsername(&$order){
        $this->createMenu(SmsHelper::MENU_GROUP_MAIN,
            SmsHelper::MENU_MAIN_USERNAME, $order, 'Please input a username',
            'Tafadhali, weka jina unalotaka kutumia');
    }

    public function createMenuAreas(&$order){
        $this->createMenu(SmsHelper::MENU_GROUP_MAIN,
            SmsHelper::MENU_MAIN_AREA,
            $order, 'Which region are you from?',
            'Unatoka eneo gani?');
        $this->createMenu(SmsHelper::MENU_GROUP_MAIN,
            SmsHelper::MENU_MAIN_SUB_AREA,
            $order, 'Please select your area in {{region_name}}',
            'Chagua eneo utokao katika {{region_name}}');
    }

    public function createMenuLang(&$order){
        $menu = $this->createMenu(SmsHelper::MENU_GROUP_MAIN, SmsHelper::MENU_MAIN_LANGUAGE,
            $order, 'Please select a language that you prefer',
        'Chagua lugha upendayo');

        //create menu options
        $this->createMenuOption($menu->id, 'en', 1, 'English', 'Kingereza');
        $this->createMenuOption($menu->id, 'sw', 2, 'Kiswahili', 'Kiswahili');
    }

    private function createMenuOption($menuId, $value, $order, $en, $sw)
    {
        $opt = SmsMenuOption::create([
            'menu_id' => $menuId,
            'value' => $value,
            'order' => $order,
            'en' => ['description' => $en],
        ]);
        $opt->translateOrNew('sw')->description = $sw;
        $opt->save();
        return $opt;
    }
    private function createMenu($group, $name, &$order, $en, $sw, $optionsNavigable=false)
    {
        $menu = SmsMenu::create([
            'name' => $name,
            'order' => $order++,
            'group' => $group,
            'optionsNavigable' => $optionsNavigable,
            'en' => ['description' => $en]
        ]);
        $menu->translateOrNew('sw')->description = $sw;
        $menu->save();
        return $menu;
    }
}
